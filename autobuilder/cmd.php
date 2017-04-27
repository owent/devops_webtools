<?php 

require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . 'project.cmd.conf.php');

if ($auto_builder->cmd === null ) {
	echo json_encode(array(
		'retCode' => '401',
		'msg' => 'cmd is required!'
	));
	exit;
}

$cmd_project_run_file = $auto_builder->getProjectPath() . DIRECTORY_SEPARATOR . 'cmds' . DIRECTORY_SEPARATOR . $auto_builder->cmd . '.cmd.php';
$cmd_run_file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'cmds' . DIRECTORY_SEPARATOR . $auto_builder->cmd . '.cmd.php';

// 优先使用工程指令文件
if (file_exists($cmd_project_run_file)) {
	require_once ($cmd_project_run_file);
} else if (file_exists($cmd_run_file)) {
	require_once ($cmd_run_file);
} else {
	echo json_encode(array(
			'retCode' => '402',
			'msg' => 'cmd invalid!'
	));
}
