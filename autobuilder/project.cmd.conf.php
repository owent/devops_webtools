<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config.php');

if ($auto_builder->getProjectName() === null) {
	echo json_encode(array(
		'retCode' => '101',
		'msg' => 'project name is required.'
	));
	exit;	
}

if (!($auto_builder->cmd == 'savecfg' && $auto_builder->action == 'cmd')) {
	if (!$auto_builder->isInited()){
		echo json_encode(array(
				'retCode' => '201',
				'msg' => 'project has not inited.'
		));
		exit;
	}
	
	// 检查无需登入命令
	if (!$auto_builder->isAccessFree($auto_builder->cmd, 'cmd'))
	{
		if($auto_builder->user === null) {
		    echo json_encode(array(
				'retCode' => '301',
				'msg' => 'Please login first.'
			));
		    exit;
		} else if(!$auto_builder->checkPermission()) {
			echo json_encode(array(
					'retCode' => '302',
					'msg' => 'Permission denied!'
			));
			exit;
		}
	}
}
