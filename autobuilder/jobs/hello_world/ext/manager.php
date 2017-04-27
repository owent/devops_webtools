<br />
服务器配置&nbsp;&nbsp;<a title="点击显示或隐藏服务器配置" href="javascript:void(0);" id="manager_env_option_viewer_enable">显示</a>
<div id="manager_env_option_viewer" style="display: none; overflow: scroll;">
<table class="table">
<tr><th style="width: 25%"><h3 style="text-align: center;">配置项</h3></th><th><h3 style="text-align: center;">配置值</h3></th></tr>
<tr><td colspan="2" style="text-align: center;"><h4>服务器基本配置</h4></td></tr>
<tr><td>服务器地址: </td><td><input type="text" data-source="url" class="form-control" value="" /></td></tr>
<tr><td>大区ID: </td><td><input type="text" data-source="zone_id" class="form-control" value="" /></td></tr>
<tr><td>分组ID: </td><td><input type="text" data-source="group_id" class="form-control" value="" /></td></tr>
<tr><td>对外域名: </td><td><input type="text" data-source="outer_domain" class="form-control" value="" /></td></tr>
<tr><td>对外IPv4: </td><td><input type="text" data-source="outer_ipv4" class="form-control" value="" /></td></tr>
<tr><td>对外IPv6: </td><td><input type="text" data-source="outer_ipv6" class="form-control" value="" /></td></tr>
<tr><td>对内IPv4: </td><td><input type="text" data-source="inner_ipv4" class="form-control" value="" /></td></tr>
<tr><td>对内IPv6: </td><td><input type="text" data-source="inner_ipv6" class="form-control" value="" /></td></tr>
<tr><td colspan="2" style="text-align: center;"><h4>框架配置</h4></td></tr>
<tr><td>是否开启标准输出: </td><td><input type="text" data-source="atframe_stdout" class="form-control" value="" /></td></tr>
<tr><td>共享内存池Key基址: </td><td><input type="text" data-source="atframe_shm_key" class="form-control" value="" /></td></tr>
<tr><td>共享内存池通道大小: </td><td><input type="text" data-source="atframe_shm_size" class="form-control" value="" /></td></tr>
<tr><td>TCP端口基址: </td><td><input type="text" data-source="atframe_port" class="form-control" value="" /></td></tr>

<tr><td colspan="2" style="text-align: center;"><h4>集群及相关服务配置</h4></td></tr>
<tr><td colspan="2" style="text-align: center;"><h5>集群服务配置</h5></td></tr>
<tr><td>Etcd服务地址: </td><td><input type="text" data-source="etcd_host" class="form-control" value="http://127.0.0.1:2379" /></td></tr>
<tr><td colspan="2" style="text-align: center;"><h5>数据库配置</h5></td></tr>
<tr><td>数据库地址 </td><td><input type="text" data-source="db_cluster" class="form-control" value="127.0.0.1:7001-7006" /></td></tr>
<tr><td colspan="2" style="text-align: center;"><h5>服务器配置</h5></td></tr>
<tr><td>地图服务器Etcd地址 </td><td><input type="text" data-source="map_server_etcd_host" class="form-control" value="http://127.0.0.1:2379" /></td></tr>
</table>
</div>
<script type="text/javascript">
jQuery(document).ready(function(){
	var server_conf_map = ({});
	window.AutoBuilderWrapper.on("OnServerInfoLoaded", function(evt, data){
		var zone_list = $("zone", data);
		server_conf_map = ({});
		$.each(zone_list, function(i){
			var zone_data = $(zone_list[i]);
			if (!zone_data.attr("id"))
				return;

			server_conf_map[zone_data.attr("id")] = zone_data;
		});
	});

	$("#manager_env_option_viewer input").attr("disabled", "disabled");

	var check_and_set_val = (function(jdom, tree){
		var val_txt = $(jdom.attr('data-source'), tree).text();
		if (val_txt)
			jdom.val(val_txt);
		else
			jdom.val("空或默认值");
	});
	
	var set_svr_info = (function(){
		var selected_svr = $("#manager_target_server").val();
		if (!server_conf_map[selected_svr]){
			$("#manager_env_option_viewer_ip").val("系统错误: 没有匹配的配置数据");
			return;
		}

		var svr_opt = server_conf_map[selected_svr];
		$.each($("input", "#manager_env_option_viewer"), function(k, v){
			check_and_set_val($(v), svr_opt);	
		});
	});
	
	$("#manager_server_list_container").on("change", "select", function(){
		set_svr_info();
	});

	$("#manager_env_option_viewer_enable").click(function(){
		var selected_opt = $("#manager_target_server").prop("selectedOptions");
		var dlg_title = "服务器配置";
		if (selected_opt)
			dlg_title += " [" + $(selected_opt).text() + "]";
		$("#manager_env_option_viewer").dialog({
        	dialogClass: "seed_alert",
			"minWidth": 800,
			"minHeight": 600,
			"maxHeight": Math.max(600, $(window).height() - 80),
            "title": dlg_title,
            "resizable": true,
			"buttons": {
				"Close": function() {
					$( this ).dialog( "close" );
				}
			}
		});
	

		set_svr_info();
	}).button({label: "显示"}).tooltip();
});
</script>
<?php
