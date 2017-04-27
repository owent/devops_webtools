<?php 

$auto_builder->loadProjectConf(array(
	'ext_modules' => array(
		'readme' => 'ext/readme.php',
		'compile' => 'ext/compile.php',
		'publish' => 'ext/publish.php',
		'manager' => 'ext/manager.php'
	)/*,
		
	'anyone_cmds' => array('calc_version')*/
));
