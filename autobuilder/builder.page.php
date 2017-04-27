<?php 
	$script_dir = dirname(__FILE__);
	$publish_addrs = json_decode(file_get_contents($auto_builder->getPublishCfgFilePath()));
?>
<div class="card" id="build_form">
	<div class="card-header"><h3>操作选项</h3></div>
	<div class="card-block">
		<div id="tabs-operator">
			<ul style="width: 100%; display: inline-table;"><?php 
				$ui_tabs_headers = scandir($script_dir . '/ui/tabs/header');
				foreach($ui_tabs_headers as $key => $val) {
					if(strlen($val) < 4 || substr($val, strlen($val) - 4) != '.php' )
						continue;
					include "$script_dir/ui/tabs/header/$val";
				}
			?></ul><?php 
				$ui_tabs_bodys = scandir($script_dir . '/ui/tabs/body');
				foreach($ui_tabs_bodys as $key => $val) {
					if(strlen($val) < 4 || substr($val, strlen($val) - 4) != '.php' )
						continue;
					include "$script_dir/ui/tabs/body/$val";
				}
			?>
		</div>
	</div>
	<div class="card-footer">
		<div id="btns-operator">
			<a href="javascript: void(0);" id="btn_update_versions" >更新可编译版本列表</a>&nbsp;&nbsp;&nbsp;&nbsp;
			<a href="javascript: void(0);" id="btn_update_install_target" >更新发布目标列表</a>&nbsp;&nbsp;&nbsp;&nbsp;
			<a href="javascript: void(0);" id="btn_get_operation_log" >查看操作记录</a>&nbsp;&nbsp;&nbsp;&nbsp;
		</div>
	</div>
</div>
<br /><br />
<div class="card rounded">
  <div class="card-header">
    <h3 class="card-title">操作详情</h3>
  </div>
  <div class="card-block">
  <pre id="msg_box" class="card-text rounded" style="max-height: 480px; overflow: auto;"></pre>
  </div>
  <div class="card-footer" style="color: Red;" id="msg_box_notice"></div>
</div>
				
<script type="text/javascript">
(function($, window){
	window.AutoBuilder = ({
        fns : {
            login: webtools_login || function(){}
        }
    });
	window.AutoBuilderWrapper = $(AutoBuilder);
	
	window.AutoBuilder.execBuilder = (function(param, opt){
		opt = $.extend(true, {
			type: 'GET',
			cache: false,
			dataType: 'json',
			onSuccess: function(obj){
				if (!obj || obj.retCode != 0) {
					if (411 == obj.retCode) {
						seed_alert('获取指令锁失败<br />可能有其他人正在操作，请稍后重试', '上一个指令尚未完成');
					} else {
						seed_alert(obj.msg, '执行指令失败');
					}
				}

				// 鉴权问题，重新启动登入流程
				if (obj && obj.retCode && 301 == parseInt(obj.retCode)) {
                    window.AutoBuilder.fns.login();
				}
			}
		}, opt);

		param = $.extend(true, {
			action: 'cmd',
			project: '<?php echo $auto_builder->getProjectName(); ?>'
		}, param);
		
		$.ajax({
			url: 'build.php',
			data: param,
			type: opt.type,
			cache: opt.cache,
			dataType: opt.dataType,
			success: function() {
				opt.onSuccess.apply(this, arguments);
			}
		});
	});
	
	window.AutoBuilder.loadVersionInfo = (function(opt){
		opt = $.extend({cache: true, success: function(){} }, opt);
		window.AutoBuilder.execBuilder({cmd: 'getversions'}, {
			cache: opt.cache,
			onSuccess: function(){
				window.AutoBuilderWrapper.trigger("OnVersionInfoLoaded", arguments);
				$("<div style='padding: 5px 3px 3px 15px;'><h5 style='color: #333333;'>读取版本列表完成.</h5></div>").pushMessage({standTime: 2000});
				opt.success.apply(this, arguments);

				// 特殊操作
				window.AutoBuilder.loadPackageInfo({ cache: opt.cache });
			}
		});
	});

	window.AutoBuilder.loadServerInfo = (function(opt){
		opt = $.extend({cache: true, success: function(){} }, opt);
		window.AutoBuilder.execBuilder({cmd: 'getaddrs'}, {
			cache: opt.cache,
			dataType: 'xml',
			onSuccess: function(){
				window.AutoBuilderWrapper.trigger("OnServerInfoLoaded", arguments);
				$("<div style='padding: 5px 3px 3px 15px;'><h5 style='color: #333333;'>读取发布目标列表完成.</h5></div>").pushMessage({standTime: 2000});
				opt.success.apply(this, arguments);
			}
		});
		
	});

	window.AutoBuilder.loadPackageInfo = (function(opt){
		opt = $.extend({cache: true, success: function(){} }, opt);
		window.AutoBuilder.execBuilder({cmd: 'getpackages'}, {
			cache: opt.cache,
			dataType: 'json',
			onSuccess: function(){
				window.AutoBuilderWrapper.trigger("OnPackageInfoLoaded", arguments);
				$("<div style='padding: 5px 3px 3px 15px;'><h5 style='color: #333333;'>读取已编译列表完成.</h5></div>").pushMessage({standTime: 2000});
				opt.success.apply(this, arguments);
			}
		});
		
	});

	window.AutoBuilder.loadOperatorLog = (function(opt){
		opt = $.extend({cache: false, success: function(){} }, opt);
		window.AutoBuilder.execBuilder({cmd: 'getoperationlog'}, {
			cache: opt.cache,
			dataType: 'text',
			onSuccess: function(){
				window.AutoBuilderWrapper.trigger("OnOperatorLogLoaded", arguments);
				opt.success.apply(this, arguments);
			}
		});
		
	});
	
	window.AutoBuilder.init = (function(opt){
		window.AutoBuilderWrapper.trigger("OnInit", arguments);
		opt = $.extend({}, opt);
		window.AutoBuilder.loadVersionInfo(opt);
		window.AutoBuilder.loadServerInfo(opt);
	});
	
})(jQuery, window);


function auto_builder_loadprocess(call_back){
	if (auto_builder_loadprocess.is_running) {
		if (call_back)
			auto_builder_loadprocess.callback_list.push(call_back);
		
		return false;
	}
	
	auto_builder_loadprocess.is_running = true;
	auto_builder_loadprocess.callback_list = [];
	if (call_back)
		auto_builder_loadprocess.callback_list.push(call_back);
	
	$("#msg_box_notice").empty();
	
	var jmsg_box = $("#msg_box");
	jmsg_box.empty();
	var log_msg_offset = 0;
	
	var load_func = (function(){
		if (!auto_builder_loadprocess.is_running)
			return;
	
		window.AutoBuilder.execBuilder({
				cmd: 'getprocess',
				offset: log_msg_offset
			}, {
				onSuccess: function(data){
					log_msg_offset = (data && data.offset)? parseInt(data.offset): 0;
					if (data && data.msg)
						jmsg_box.append(data.msg);
					
					jmsg_box.scrollTop(jmsg_box.prop('scrollHeight'));
					if (data && data.finished == 1) {
						auto_builder_loadprocess.is_running = false;
						$.each(auto_builder_loadprocess.callback_list, function(i){
							auto_builder_loadprocess.callback_list[i](data);
						});
						auto_builder_loadprocess.callback_list = [];
						$("#msg_box_notice").html('完成.');

						// 触发事件
						window.AutoBuilderWrapper.trigger("OnProcessFinished", arguments);
					} else if(!data || !data.retCode || 301 != parseInt(data.retCode)) {
						setTimeout(function(){ load_func(); }, 500);
					}
			}
		});
	});

	load_func();

	return true;
}

$(document).ready(function(){
	$("#tabs-operator").tabs();
	$("a", "#btns-operator").button();
});
</script><?php
$ui_tabs_scripts = scandir($script_dir . '/ui/tabs/script');
foreach($ui_tabs_scripts as $key => $val) {
	if(strlen($val) < 4 || substr($val, strlen($val) - 4) != '.php' )
		continue;
	include "$script_dir/ui/tabs/script/$val";
}
?>
<script type="text/javascript">
$(document).ready(function(){
	// ======================= 公共功能模块 =======================
	$("#btn_update_versions").click(function(){
		$("<div style='padding: 5px 3px 3px 15px;'><h2 style='color: #333333;'>正在读取版本列表...</h2></div>").pushMessage({standTime: 2000});
		window.AutoBuilder.loadVersionInfo({cache: false});
	}).button();
	
	$("#btn_update_install_target").click(function(){
		$("<div style='padding: 5px 3px 3px 15px;'><h2 style='color: #333333;'>正在读取发布目标列表...</h2></div>").pushMessage({standTime: 2000});
		window.AutoBuilder.loadServerInfo({cache: false});
	}).button();

	$("#btn_get_operation_log").click(function(){
		var dlg_log = $("<div></div>");
		var dlg_msg_log = $("<div></div>");
		var dlg_tbl_list = $("<table></table>");
		dlg_msg_log.html("正在读取日志...");
		dlg_tbl_list.addClass("table");
		
		dlg_log.append(dlg_msg_log).append(dlg_tbl_list)
			.dialog({
			"width": 936,
			"height": 700,
            "title": "操作日志",
			"buttons": {
				"关闭": function() {
					$( this ).dialog( "close" );
				}
			}
		});
		$(dlg_log.parent("div[role=dialog]").get(0)).css({"zIndex": 1023});
		
		window.AutoBuilder.loadOperatorLog({
			cache: false, 
			success: function(text){
				dlg_msg_log.empty();
				var log_list = text.split("\n");
				dlg_tbl_list.empty();
				
				$.each(log_list, function(i){
					var log_item = log_list[i];
					if (! $.trim(log_item)) {
						return;
					}

					var log_info = log_item.split("|");
					var log_user = log_info.length > 0? log_info[0]: "未知用户";
					var log_time = log_info.length > 1? (new Date(parseInt(log_info[1]) * 1000)).toLocaleString(): "未知时间";
					var log_opr = log_info.length > 2? log_info[2]: "未知操作";
					var log_res = log_info.length > 3? log_info[3]: "未知";

					var dlg_tbl_item_class = "";
					
					// 操作结果枚举
					do
					{
						var reg_match = /setting/i;
						var reg_res = [];
						if (reg_res = log_opr.match(reg_match)) {
							log_opr = "更新配置";
							break;
						}

						reg_match = /update\s+script/i;
						if (reg_res = log_opr.match(reg_match)) {
							log_opr = "更新编译机脚本";
							break;
						}

						reg_match = /compile\s+(.*)/i;
						if (reg_res = log_opr.match(reg_match)) {
							log_opr = "编译" + reg_res[1] + "分支";
							break;
						}

						reg_match = /publish\s+([^\.]+)\.([\w\d\.\-\@]*)\s+to\s+(\d+)/i;
						if (reg_res = log_opr.match(reg_match)) {
							log_opr = "发布分支" + reg_res[1] + "版本" + reg_res[2] + "到目标服务器" + reg_res[3];
							break;
						}

						reg_match = /manage\s+(\d+)/i;
						if (reg_res = log_opr.match(reg_match)) {
							log_opr = "在目标服务器" + reg_res[1] + "上执行命令";
							break;
						}

						reg_match = /^\s*exec\s+(.*)/i;
						if (reg_res = log_opr.match(reg_match)) {
							log_opr = "执行工具 " + reg_res[1];
							break;
						}

						reg_match = /add\s+custom\s+cmd/i;
						if (reg_res = log_opr.match(reg_match)) {
							log_opr = "添加自定义命令";
							break;
						}

						reg_match = /del\s+custom\s+cmd/i;
						if (reg_res = log_opr.match(reg_match)) {
							log_opr = "删除自定义命令";
							break;
						}

						reg_match = /release\s+action\s+lock/i;
						if (reg_res = log_opr.match(reg_match)) {
							log_opr = "<span style=\"color: Red;\">强制解除指令锁</span>";
							dlg_tbl_item_class = "warning";
							break;
						}
						
					} while(false);

					do
					{
						if (log_res.toLowerCase() == "true") {
							log_res = "<span style=\"color: Green;\">成功</span>";
							if (! dlg_tbl_item_class){
								dlg_tbl_item_class = "success";
							}
						}

						if (log_res.toLowerCase() == "false") {
							log_res = "<span style=\"color: Red;\">失败</span>";
							dlg_tbl_item_class = "danger";
						}

						var log_res_parten = log_res.match(/run shell\s*\:\s*(\d*)/i);
						if (log_res_parten) {
							log_res = "<span style=\"color: Gray;\">执行脚本(" + log_res_parten[1] + ")</span>";
						}
						
						if (log_res.toLowerCase() == "run shell") {
							log_res = "<span style=\"color: Gray;\">执行脚本</span>";
						}
						
					}while(false);
					dlg_tbl_list.prepend("<tr class=\"" + dlg_tbl_item_class + "\"><td>" + log_user + "</td><td>" + log_time + "</td><td>" + log_opr + "</td><td>" + log_res + "</td></tr>");
				});

				dlg_tbl_list.prepend("<tr><th style=\"text-align: center;\">操作用户</th><th style=\"text-align: center;\">时间</th><th style=\"text-align: center;\">操作内容</th><th style=\"text-align: center;\">执行结果</th></tr>");
			}
		});
	}).button();
	
	// ======================= 启动数据初始化  ======================= 
	window.AutoBuilder.init();
});
</script>
