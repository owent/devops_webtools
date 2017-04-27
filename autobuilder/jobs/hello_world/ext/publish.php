<input type="hidden" value="server" class="publish_options" id="publish_target_option_param" />
<div class="input-group">
	发布选项<strong>(注: 白色为选中，黑色为未选中)</strong>:&nbsp;&nbsp;&nbsp;&nbsp;
  <span id="publish_target_option">
	  <button type="checkbox" id="publish_target_option_server" value="server" checked="checked">部署后台服务器</label>
	  <button type="checkbox" id="publish_target_option_server_resource" disabled="disabled" value="server_resource">最新后台资源</label>
	  <button type="checkbox" id="publish_target_option_client_version" disabled="disabled" value="client_version">客户端版本信息</label>
  </span>
</div>
<div class="help-block">资源更新可不填写版本号</div>

<script type="text/javascript">
jQuery(document).ready(function(){
	var reset_publish_param_func = (function(){
		var doms = $("#publish_target_option>input");
		var final_str = "";
		$.each(doms, function(i){
			var jdom = $(doms[i]);
			if (jdom.prop("checked")) {
				final_str += " " + jdom.val();
			}
		});

		final_str = $.trim(final_str);

		$("#publish_target_option_param").val(final_str);
	});


	$("#publish_target_option>input").click(function(){
		reset_publish_param_func();
	});

	$("#publish_target_option").controlgroup();

	// ========== 为了防止资源覆盖 服务器发布或服务器资源发布和客户端版本信息发布只能启用一个 ==========
	$("#publish_target_option_server").click(function(){
		if ($(this).prop("checked")){
			$("#publish_target_option_server_resource").prop("checked", false);
			$("#publish_target_option_server_resource").button("refresh");

			$("#publish_target_option_client_version").prop("checked", false);
			$("#publish_target_option_client_version").button("refresh");

			reset_publish_param_func();
		}
	});
	$("#publish_target_option_server_resource").click(function(){
		if ($(this).prop("checked")){
			$("#publish_target_option_server").prop("checked", false);
			$("#publish_target_option_server").button("refresh");

			reset_publish_param_func();
		}
	});

    $("#publish_target_option_client_version").click(function(){
		if ($(this).prop("checked")){
			$("#publish_target_option_server").prop("checked", false);
			$("#publish_target_option_server").button("refresh");

			reset_publish_param_func();
		}
	});
	// ---------- 为了防止资源覆盖 服务器发布和服务器资源发布只能启用一个----------
});
</script>
<?php
