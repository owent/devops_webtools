<?php 

set_time_limit(600);

// URL参数
putenv("AUTOBUILDER_PARAM_CMD=update_script");

if ($auto_builder->getLock()) {
	passthru("sh {$auto_builder->conf->build_shell} > {$auto_builder->getOperationInfoFilePath()} 2>&1", $ret);
	$auto_builder->releaseLock();
	
	echo json_encode(array(
		'retCode' => 0,
		'cmdRetCode' => $ret,
		'msg' => 'success'
	));
	$auto_builder->log('update script', 'True');
	exit;
}

echo json_encode(array(
		'retCode' => 411,
		'msg' => 'get lock failed.'
));
$auto_builder->log('update script', 'False');
