<script type="text/javascript">
// <!-- ======================= 编译模块 ======================= 
window.AutoBuilderWrapper.on("OnInit", function(){
	$("#btn_compile").button().hide().click(function(){
		// 可选选项
		var compile_opt_dom = $(".compile_options");
		var compile_opt_str = "";
		$.each(compile_opt_dom, function(i){
			compile_opt_str += " " + $(compile_opt_dom[i]).val();
		});
		compile_opt_str = $.trim(compile_opt_str);
		
		var param = ({
			cmd: 'compile',
			ver: $("#compile_versions").val(),
			cmdopt: compile_opt_str
		});
		var opts = ({
			onSuccess: function(obj){
				if (!obj || obj.retCode != 0)
					seed_alert(obj.msg, '执行指令失败');
				else
					window.AutoBuilder.loadPackageInfo({ cache: false }); // 刷新已编译包列表
				}
		});
		// 触发OnCompileCmdSend事件
		window.AutoBuilderWrapper.trigger("OnCompileCmdSend", [param, opts]);
		
		window.AutoBuilder.execBuilder(param, opts);
		setTimeout(function(){ auto_builder_loadprocess(); }, 500);
	});
	window.AutoBuilderWrapper.on("OnVersionInfoLoaded", function(evt, data){
		var container = $("#compile_versions");
		container.empty();
		var filter_func = (function(src, start, ori_map){
			var begin = src.indexOf('repo dir list begin', start);
			if (begin < 0)
				return begin;

			begin += 'repo dir list begin'.length;
			var end = src.indexOf('repo dir list end', begin);
			if (end < 0)
				end = src.length;

			// 置为不可用
			ori_map = ori_map || ({});
			$.each(ori_map, function(i){
				ori_map[i] = false;
			});
			
			var strDirs = data.msg.substr(begin, end - begin);
			var dirs = strDirs.split('\n');
			$.each(dirs, function(i){
				if ($.trim(dirs[i])) {
					dirs[i] = $.trim(dirs[i]);
					var version_name = dirs[i].charAt(dirs[i].length - 1) == '/'? dirs[i].substr(0, dirs[i].length - 1): dirs[i];
					ori_map[version_name] = true;
				}
			});
			
			return end + 'repo dir list end'.length;
		});
		
		var available_branch = ({}), available_branch_arr = [];
		var begin = 0;
		while (begin >= 0)
		{
			begin = filter_func(data.msg, begin, available_branch);
		}

		$.each(available_branch, function(i){
			if (available_branch[i])
				available_branch_arr.push(i);
		});
		
		if (available_branch_arr.length <= 0) {
			container.html('<option selected="selected">--无可用版本--</option>');
			return;
		}

		$.each(available_branch_arr, function(i){
			var option = $('<option></option>');
			option.attr({ value: available_branch_arr[i] }).html(available_branch_arr[i]);

			// git master branch
			if ("master" == available_branch_arr[i]) {
				option.attr("selected", "selected");
			}
			container.append(option);
		});

		$("#btn_compile").show();

		// 克隆数据
		$("#publish_versions_container").empty()
			.append('<span class="input-group-addon" id="tabs-operator-publish-version-text">版本:</span>')
			.append(container.clone().attr({
				id: "publish_versions",
				"aria-describedby": "tabs-operator-publish-version-text"
			}));
	});
	
	// 更新编译机脚本
	$("#btn_update_scripts").click(function(){
		window.AutoBuilder.execBuilder({ cmd: 'updatescript' });
		
		setTimeout(function(){ auto_builder_loadprocess(); }, 500);
	}).button();
});
// -->
</script>