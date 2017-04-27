<div id="tabs-operator-compile">
	<div class="form form-inline">
		<span class="input-group col-3">
			<span class="input-group-addon" id="tabs-operator-compile-version-text">版本:</span>
			<select id="compile_versions" class="form-control" aria-describedby="tabs-operator-compile-version-text" style="height: auto;">
				<option selected="selected">--正在读取版本列表--</option>
			</select>
		</span>
		&nbsp;&nbsp;&nbsp;&nbsp;
		<a href="javascript: void(0);" id="btn_compile" style="vertical-align:top;">开始编译</a>
		&nbsp;&nbsp;&nbsp;&nbsp;
		<a href="javascript: void(0);" id="btn_update_scripts" style="vertical-align:top;">更新编译机脚本</a>
	</div><br /><br />
	<?php $auto_builder->loadProjectExtModule('compile', '<div id="tabs-operator-compile-project">', '</div>'); ?>
</div>