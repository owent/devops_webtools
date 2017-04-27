<?php 

set_time_limit(600);

$compile_version = 'dev';

if (isset($_REQUEST['ver']))
	$compile_version = $_REQUEST['ver'];

$cmd_option = '';
if (isset($_REQUEST['cmdopt'])) {
	$cmd_option = $_REQUEST['cmdopt'];
}

// URL参数
putenv("AUTOBUILDER_PARAM_CMD=compile");
putenv("AUTOBUILDER_PARAM_VER=$compile_version");
putenv("AUTOBUILDER_PARAM_CMD_OPTION=$cmd_option");

// 逻辑
if ($auto_builder->getLock()) {
	passthru("sh {$auto_builder->conf->build_shell} > {$auto_builder->getOperationInfoFilePath()} 2>&1", $ret);
	$auto_builder->releaseLock();
	
	echo json_encode(array(
			'retCode' => 0,
			'cmdRetCode' => $ret,
			'msg' => 'success'
	));
	$auto_builder->log("compile $compile_version", "Run Shell: $ret");
	exit;
}

$auto_builder->log("compile $compile_version", 'False');
echo json_encode(array(
		'retCode' => 411,
		'msg' => 'get lock failed.'
));
