<div id="tabs-operator-publish">
	<div class="form form-inline">
		<span id="publish_versions_container" class="input-group col-3"></span>
		<span class="input-group col-3">
			<span class="input-group-addon" id="tabs-operator-publish-version-no">版本号：</span>
			<input type="text" id="btn_publish_verno" class="form-control" aria-describedby="tabs-operator-publish-version-no" style="height: auto;" />
		</span>
		<span class="input-group col-5">
			<span class="input-group-addon" id="tabs-operator-publish-target-server-text">发布目标服务器：</span>
			<select id="btn_publish_install_target" class="form-control" aria-describedby="tabs-operator-publish-target-server-text" style="height: auto;">
				<option selected="selected">--正在读取可用的发布目标列表--</option>
			</select>
		</span>
		<a href="javascript: void(0);" id="btn_publish" class="btn btn-secondary" >发布</a>
	</div><br /><br />
	<div style="color: gray; font-size: small;">
	注意：请确保下载目录中的server文件夹有选择的版本分支和版本号的压缩包文件<br />
	版本号填入时，<strong>双击</strong>或<strong>输入数字</strong>可打开自动完成列表<br />
	</div>
	<?php $auto_builder->loadProjectExtModule('publish', '<div id="tabs-operator-publish-project">', '</div>'); ?>
</div>