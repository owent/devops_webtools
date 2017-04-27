<script type="text/javascript">
// <!-- ======================= 管理模块 ======================= 
window.AutoBuilderWrapper.on("OnInit", function(){
	var manager_on_exec_func = (function(){
		var select_list = $("input", "#manager_cmd_list");
		var selected_cmd = null;
		$.each(select_list, function(i){
			if ($(select_list[i]).prop('checked')) {
				selected_cmd = $(select_list[i]).val();
				return false;
			}
		});

		select_list = $("input", "#manager_services_list");
		var selected_cmd_params = '';
		$.each(select_list, function(i){
			if ($(select_list[i]).prop('checked') && !$(select_list[i]).prop('disabled')) {
				selected_cmd_params += $(select_list[i]).val();
			}
		});

		if (!selected_cmd) {
			seed_alert('<h1>必须选择一个有效的命令</h1>', '警告');
			return;
		}
		// 处理参数
		var param = ({ 
			cmd: 'manager',
			targetid: $("#manager_target_server").val(),
			shell_cmd: selected_cmd + ' ' + selected_cmd_params
		});
		var opts = ({ 'type': 'POST' });

		// 触发OnManagerCmdSend事件
		window.AutoBuilderWrapper.trigger("OnManagerCmdSend", [param, opts]);

		// 执行事件
		window.AutoBuilder.execBuilder(param, opts);
		setTimeout(function(){ auto_builder_loadprocess(); }, 500);
	});
	
	$("#btn_manager_exec").button().hide().click(function(){
		manager_on_exec_func();
	});
	
	var manager_cmd_list_vuejs = new Vue({
		el: "#manager_cmd_list",
		data: {
			cmds: []
		},
		updated: function() {
			var mcl = $("#manager_cmd_list");
			// 绑定事件
			$.each($("input", mcl), function(key, val){
				var cmd_option = jQuery(val);
				if ("true" == cmd_option.attr('noservice')) {
					cmd_option.off('click');
					cmd_option.on('click', function(){
						var manager_script_content = $("<p>命令内容:<br /></p>");
						manager_script_content.append("<pre class='card-text rounded'><code>" + hljs.highlightAuto(cmd_option.val()).value + "</code></pre>");
						$("<div></div>").html("执行<strong style=\"color: Red;\">" + cmd_option.attr('cmd-name') + "</strong>指令?<br />")
						.append(manager_script_content).dialog({
							"modal": true,
							"width": 640,
							"height": 480,
				            "title": cmd_option.attr('cmd-name') + " 执行确认",
							"buttons": {
								"执行": function() {
									manager_on_exec_func();
									$( this ).dialog( "close" );
								}, 
								"取消": function() {
									$( this ).dialog( "close" );
								}
							}
						});
					});
				}

				cmd_option.checkboxradio({ 
					label: cmd_option.attr('cmd-name'),
					icon: false
				});
			});

			mcl.controlgroup();
		}
	});


	var manager_service_list_vuejs = new Vue({
		el: "#manager_services_list",
		data: {
			services: []
		},
		updated: function() {
			var msl = $("#manager_services_list");
			// 绑定事件
			$.each($("input", msl), function(key, val){
				var service_option = jQuery(val);
				service_option.checkboxradio({ 
					service_option: service_option.attr('server-name'),
					icon: false
				});
			});

			msl.controlgroup();
		}
	});

	window.AutoBuilderWrapper.on("OnServerInfoLoaded", function(evt, data){
		$("#manager_target_server").change(function(){
			var zone_data = $("zone[id=" + $(this).val() + "]", data);
			var mcl_list = [];
			$.each($('cmd', zone_data), function(i, val) {
				var jnode = jQuery(val);
				mcl_list.push({
					id: i + 1,
					name: jnode.attr('name'),
					jnode: jnode
				});
			});
			manager_cmd_list_vuejs.cmds = mcl_list;

			var msl_list = [];
			$.each($('server', zone_data), function(i, val){
				var jnode = jQuery(val);
				msl_list.push({
					id: i + 1,
					name: jnode.attr('name'),
					jnode: jnode
				});
			});
			manager_service_list_vuejs.services = msl_list;
		});		

		// 执行管理命令
		$("#btn_manager_exec").show();
		$("#manager_target_server").change();
	});
	
	$("#manager_cmd_list").on('click', 'input', function(){
		if ($(this).attr('noservice') == 'true') {
			$("#manager_services_list").controlgroup("disable");
		} else {
			$("#manager_services_list").controlgroup("enable");
		}
	});
	
});
// -->
</script>