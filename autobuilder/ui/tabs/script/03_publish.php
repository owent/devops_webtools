<script type="text/javascript">
// <!-- ======================= 发布模块 ======================= 
window.AutoBuilderWrapper.on("OnInit", function(){
	$("#btn_publish").button().hide().click(function(){
		// 可选选项
		var publish_opt_dom = $(".publish_options");
		var publish_opt_str = "";
		$.each(publish_opt_dom, function(i){
			publish_opt_str += " " + $(publish_opt_dom[i]).val();
		});
		publish_opt_str = $.trim(publish_opt_str);
		
		var param = ({ 
			cmd: 'publish',
			ver: $("#publish_versions").val(),
			verno: $("#btn_publish_verno").val(),
			targetid: $("#btn_publish_install_target").val(),
			pubopt: publish_opt_str
		});
		var opts = ({});

		// 触发OnPublishCmdSend事件
		window.AutoBuilderWrapper.trigger("OnPublishCmdSend", [param, opts]);
		
		window.AutoBuilder.execBuilder(param, opts);
		setTimeout(function(){ auto_builder_loadprocess(); }, 500);
	});
	
	window.AutoBuilderWrapper.on("OnServerInfoLoaded", function(evt, data){
		var container = $("#btn_publish_install_target");
		container.empty();
		var zone_list = $("zone", data);
		if (zone_list.length == 0){
			container.html('<option selected="selected">--无可用目标--</option>');
			return;
		}

		$.each(zone_list, function(i){
			var zone_data = $(zone_list[i]);
			if (!zone_data.attr("id"))
				return;
			
			var option = $('<option></option>');
			option.attr({ value: zone_data.attr("id") }).html(zone_data.attr("name"));
			container.append(option);
		});

		$("#btn_publish").show();
		
		// 克隆数据
		$("#manager_server_list_container").empty()
			.append('<span class="input-group-addon" id="tabs-operator-manager-target-server-text">目标服务器：</span>')
			.append(container.clone().attr({
				id: "manager_target_server",
				"aria-describedby": "tabs-operator-manager-target-server-text"
			}));
		$("#custom_server_list_container").empty()
			.append('<span class="input-group-addon" id="tabs-operator-custom-target-server-text">目标服务器：</span>')
			.append(container.clone().attr({
				id: "custom_target_server",
				"aria-describedby": "tabs-operator-custom-target-server-text"
			}));
	});

	// 初始化自动完成功能
	$("#btn_publish_verno").autocomplete({source: [], minLength: 0});
	$("#btn_publish_verno").dblclick(function(){
		$("#btn_publish_verno").autocomplete("search", "");
	});
	
	// 填入已编译包版本的自动完成功能
	window.AutoBuilderWrapper.on("OnPackageInfoLoaded", function(evt, data){
		window.AutoBuilder.Packages = {};
		var pkgInfos = data.packages[0];
		var all_append = [];
		$.each(pkgInfos, function(i){
			var pkgInfo = pkgInfos[i].match(/(([^\.]+)\.)?(.+)/im);
			if (pkgInfo && pkgInfo.length >= 4) {
				if (pkgInfo[2]) {
					window.AutoBuilder.Packages[pkgInfo[2]] = window.AutoBuilder.Packages[pkgInfo[2]] || [];
					window.AutoBuilder.Packages[pkgInfo[2]].push(pkgInfo[3]);
				} else {
					all_append.push(pkgInfo[3]);
				}
			}
		});

		$.each($("option", "#publish_versions"), function(i, v){
			var branch_name = $(v).val();
			window.AutoBuilder.Packages[branch_name] = window.AutoBuilder.Packages[branch_name] || [];
			var pkg_set = window.AutoBuilder.Packages[branch_name];
			for (var i = 0; i < all_append.length; ++ i) {
				pkg_set.push(all_append[i]);
			}
		});
		
		// 注册自动完成
		$("#publish_versions").unbind('change');
		$("#publish_versions").change(function(){
			var key = $(this).val();
			if (window.AutoBuilder.Packages[key]) {
				$("#btn_publish_verno").autocomplete( "option", "source", window.AutoBuilder.Packages[key].sort(function(l,r) {
					if (l.length != r.length) {
						return r.length > l.length? 1: ((r.length < l.length)? -1: 0);
					}
					return r > l? 1: ((r < l)? -1: 0);
				}));
			}
		});
		$("#publish_versions").change();
	});
	
});
// -->
</script>