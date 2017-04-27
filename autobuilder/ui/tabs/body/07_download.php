<?php if(!empty($auto_builder->conf->download_pkg)) { ?>
<div id="tabs-operator-download"><iframe style="width: 97%; height: 480px;" scrolling="auto" frameborder="0" src="<?php echo $auto_builder->conf->download_pkg; ?>"></iframe><?php
	$auto_builder->loadProjectExtModule('download', '<div id="tabs-operator-download-project">', '</div>');
} ?></div>