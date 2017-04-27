<?php 

set_time_limit(600);

$exec_shell = 'exec.sh';
$exec_params = '';
$exec_lock = false;

if (isset($_REQUEST['exec']))
	$exec_shell = $_REQUEST['exec'];

if (isset($_REQUEST['lock']))
	$exec_lock = true;

if (isset($_REQUEST['argv']))
	$exec_params = $_REQUEST['argv'];

// URL参数
putenv("AUTOBUILDER_PARAM_CMD=exec");
foreach($_REQUEST as $key => $val) {
	$skey = strtoupper($key);
	if ('EXEC' == strtoupper(substr($skey, 0, 4))) {
		putenv("AUTOBUILDER_PARAM_$skey=$val");
	}
}

// 逻辑
if (false == $exec_lock || $auto_builder->getLock()) {
	$exec_run_script = "cd '{$auto_builder->getProjectPath()}' && chmod +x '$exec_shell' && ./'$exec_shell' $exec_params > {$auto_builder->getOperationInfoFilePath()} 2>&1";
	passthru($exec_run_script, $ret);
	
	if ($exec_lock)
		$auto_builder->releaseLock();
	
	echo json_encode(array(
			'retCode' => 0,
			'cmdRetCode' => $ret,
			'msg' => 'success',
			'shell' => $exec_run_script
	));
	$auto_builder->log("exec $exec_shell", "Run Shell: $ret");
	exit;
}

$auto_builder->log("exec $exec_shell", 'False');
echo json_encode(array(
		'retCode' => 411,
		'msg' => 'get lock failed.'
));
