<?php 

set_time_limit(600);

$manager_targetid = '1';
$manager_opr = 'manager';
$manager_shell_cmd = '';

if (isset($_REQUEST['opr_cmd']))
	$manager_opr = $_REQUEST['opr_cmd'];

if (isset($_REQUEST['targetid']) && isset($_REQUEST['shell_cmd'])) {
	$manager_targetid = $_REQUEST['targetid'];
	$manager_shell_cmd = $_REQUEST['shell_cmd'];
} else {
	echo json_encode(array(
		'retCode' => 431,
		'msg' => 'param of targetid and shell_cmd is required.'
	));
	$auto_builder->log("manage unknown targetid", 'False');
	exit;
}

// URL参数
putenv("AUTOBUILDER_PARAM_CMD=$manager_opr");
putenv("AUTOBUILDER_PARAM_TARGETID=$manager_targetid");
putenv("AUTOBUILDER_PARAM_CMD_SHELL=$manager_shell_cmd");

// 逻辑跳转
if ($auto_builder->getLock()) {
	$need_segs = array('url', 'port', 'user', 'passwd', 'identity', 'install_path', 'use_ip', 'zone_id', 'custom');
	
	$conf_params = $auto_builder->getPublishCfgParamEnv(
		"/publish_addrs/zone[@id=$manager_targetid]", 
		$need_segs
	);
	
	if (count_chars($conf_params) == 0) {
		echo json_encode(array(
			'retCode' => 432,
			'cmdRetCode' => 0,
			'msg' => "zone $manager_targetid not found or configure error."
		));
		$auto_builder->log("manage $manager_targetid", 'False');
		exit;
	}
	
	
	passthru("sh {$auto_builder->conf->pub_shell} > {$auto_builder->getOperationInfoFilePath()} 2>&1", $ret);
	$auto_builder->releaseLock();

	echo json_encode(array(
			'retCode' => 0,
			'cmdRetCode' => $ret,
			'msg' => 'success'
	));
	$auto_builder->log("manage $manager_targetid", "Run Shell: $ret");
	exit;
}

echo json_encode(array(
		'retCode' => 411,
		'msg' => 'get lock failed.'
));
$auto_builder->log("manage $manager_targetid", 'False');
