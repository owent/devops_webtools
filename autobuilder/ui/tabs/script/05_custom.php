<script type="text/javascript">
// <!-- ======================= 自定义命令模块 ======================= 
(function($, window){
	var alert_msg = (function(msg, title){
        title = title || "Notice!";
        $("<div></div>").html(msg).dialog({
			"modal": true,
			"width": 640,
			"height": 480,
            "title": title,
			"buttons": {
				"Close": function() {
					$( this ).dialog( "close" );
				}
			}
		});
    });
    
	window.AutoBuilder.customCmds = ({});
	window.AutoBuilder.customCmdVuejs = null;
	window.AutoBuilder.loadCustomCmds = (function(opt){
		opt = $.extend({cache: false, success: function(){}, data: $("#tabs-operator-custom-cmds").serialize(), 'readonly': false , 'add': false, 'del': false}, opt);
		
		var ajax_url = 'build.php?action=cmd&project=<?php echo $auto_builder->getProjectName(); ?>&cmd=savecustomcmd';
		if (opt['readonly'])
			ajax_url += '&readonly=1';
		if (opt['add'])
			ajax_url += '&add=1';
		if (opt['del'])
			ajax_url += '&del=1';
		
    	$.ajax({
  	  		url: ajax_url,
  	  		data: opt.data,
  	  		type: 'POST',
  	  		cache: opt.cache,
  	  		dataType: 'json',
  	  		success: function(data){
	  			window.AutoBuilderWrapper.trigger("OnCustomCmdsLoaded", arguments);
				opt.success.apply(this, arguments);
  	  		}
  	  	});
	});

	var get_shell_cmd = (function(cmd_id, param_dom_id){
		if (!window.AutoBuilder.customCmds.cmd_list[cmd_id]) {
			return null;
		}

		var shell_cmd = window.AutoBuilder.customCmds.cmd_list[cmd_id]["shell_cmd"];
		if (!!param_dom_id){
			shell_cmd += " " + $(param_dom_id).val();
		}

		return shell_cmd;
	});

	window.AutoBuilder.customCmds.showCmd = (function(cmd_id, param_dom_id){
		var shell_cmd = get_shell_cmd(cmd_id, param_dom_id);
		if (null === shell_cmd) {
			alert_msg("命令ID" + cmd_id + "失效或未找到", "自定义指令错误!");
			return;
		}

		alert_msg("<pre class='rounded'><code>" + hljs.highlightAuto(shell_cmd).value + "</code></pre>", "命令ID:" + cmd_id + " -- 命令预览");
	});
	
	window.AutoBuilder.customCmds.runCmd = (function(cmd_id, param_dom_id){
		var shell_cmd = get_shell_cmd(cmd_id, param_dom_id);
		if (null === shell_cmd) {
			alert_msg("命令ID" + cmd_id + "失效或未找到", "自定义指令错误!");
			return;
		}
		
		// 处理参数
		var param = ({ 
			cmd: 'manager',
			targetid: $("#custom_target_server").val(),
			shell_cmd: shell_cmd
		});
		var opts = ({ 'type': 'POST' });
		
		var run_cmd_func = (function(){
			// 触发OnCustomCmdSend事件
			window.AutoBuilderWrapper.trigger("OnCustomCmdSend", [param, opts, {
				cmd_id: cmd_id,
				param_dom_id: param_dom_id,
				shell_cmd: shell_cmd
			}]);
	
			// 执行事件
			window.AutoBuilder.execBuilder(param, opts);
			setTimeout(function(){ auto_builder_loadprocess(); }, 500);
		});

		if (window.AutoBuilder.customCmds.cmd_list[cmd_id]["warning"]) {
			$("<div><pre><code>" + hljs.highlightAuto(shell_cmd).value + 
				'</code></pre><br /><span style="color: Red;"><strong>警告:</strong> ' + 
				window.AutoBuilder.customCmds.cmd_list[cmd_id]["warning"] +
				"</span></div>").dialog({
					
				minWidth: 640,
				minHeight: 480,
				modal: true,
				title: '您确定要执行这个指令？',
		  	    buttons: {
		  	        '确定执行': function() {
		  	        	run_cmd_func();
		  	        	$( this ).dialog( "close" );
		  	        },
		  	        '取消': function() {
		  	            $( this ).dialog( "close" );
		  	        }
		  	    }
			});
			
		} else {
			run_cmd_func();
		}
	});
	
	window.AutoBuilder.customCmds.delCmd = (function(cmd_id){
		$("<div>确定要删除这个命令?</div>").dialog({
			minWidth: 240,
			minHeight: 160,
			modal: true,
			title: "指令删除确认",
	  	    buttons: {
	  	        '确定': function() {
		  	  		window.AutoBuilder.loadCustomCmds({
		  				del: true,
		  				data: {'cmd_id': cmd_id}
		  			});
	  	            $( this ).dialog( "close" );
	  	        },
	  	        '取消': function() {
	  	            $( this ).dialog( "close" );
	  	        }
	  	    }
		});
	
	});
	
})(jQuery, window);

window.AutoBuilderWrapper.on("OnInit", function(){
	// 提示信息
	$("#tabs-operator-custom-readme-btn").click(function(){
		$("#tabs-operator-custom-readme-dlg").dialog({
			minWidth: 960,
			minHeight: 600
		});
	});

	// 读取和更新自定义命令事件
	window.AutoBuilderWrapper.on('OnCustomCmdsLoaded', function(evt, data){
		if(0 != data.retCode){
			$('<p>' + data.msg + '</p>').dialog({title: '获取自定义命令列表失败！'});
			return;
		}

		$("#tabs-operator-custom>span").first().empty();

		window.AutoBuilder.customCmds.cmd_list = {};

		// 重建指令缓存
		$.each(data.cmds, function(key){
			var val = data.cmds[key];
			window.AutoBuilder.customCmds.cmd_list[val["id"]] = val;
			if (val["param"] && val["param"].toLowerCase() == "none") {
				val["param"] = undefined;
			}
		});

		if (null === window.AutoBuilder.customCmdVuejs) {
			window.AutoBuilder.customCmdVuejs = new Vue({
				el: "#tabs-operator-custom-cmds-content",
				data: {
					cmds: []
				},
				updated: function() {
					// 绑定事件
					$.each(data.cmds, function(key){
						if (data.cmds[key]["param"] && data.cmds[key]["param"].toLowerCase() != "none") {
							var type_name = data.cmds[key]["param"].toLowerCase();
							if ("date" == type_name) {
								$('#tabs-operator-custom-cmd-param-' + data.cmds[key]["id"]).datepicker({ "dateFormat": "yy-mm-dd"});
							} else if ("datetime" == type_name) {
								$('#tabs-operator-custom-cmd-param-' + data.cmds[key]["id"]).datetimepicker({ "dateFormat": "''yy-mm-dd", "timeFormat": "HH:mm:ss'"});
							} else if ("integer" == type_name) {
								$('#tabs-operator-custom-cmd-param-' + data.cmds[key]["id"]).spinner({ "step": 1});
							} else if ("decimal" == type_name) {
								$('#tabs-operator-custom-cmd-param-' + data.cmds[key]["id"]).spinner({ "step": 0.001});
							}
						}
					});
				}
			});
		}

		// 渲染模板
		window.AutoBuilder.customCmdVuejs.cmds = data.cmds;
	});

	// 添加自定义命令按钮
	$("#tabs-operator-custom-add-btn").button().click(function(){
		$("#tabs-operator-custom-add-dlg").dialog({
			minWidth: 640,
			minHeight: 460,
			modal: true,
	  	    buttons: {
	  	        '添加命令': function() {
	  	            $( this ).dialog( "close" );
	  	        	window.AutoBuilder.loadCustomCmds({
	  	  	          	add: true,
	  	  	          	data: $("#tabs-operator-custom-add-form").serialize()
	  	  	        });
	  	        },
	  	        '取消': function() {
	  	            $( this ).dialog( "close" );
	  	        }
	  	    }
		});
	});

	var warning_dom = $("#tabs-operator-custom-add-warning");
	warning_dom.val("");
	warning_dom.prop("disabled", true);
	
	$("#tabs-operator-custom-add-warning-enable")
		.prop("checked", false).button()
		.click(function(){
		if ($(this).prop("checked")) {
			warning_dom.prop("disabled", false);
		} else {
			warning_dom.val("");
			warning_dom.prop("disabled", true);
		}
	});
	
	// 初始化时读取列表
	window.AutoBuilder.loadCustomCmds({'readonly': true});
});
// -->
</script>