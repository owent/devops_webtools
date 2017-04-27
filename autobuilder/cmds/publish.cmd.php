<?php 

set_time_limit(600);

$publish_version = 'dev';
$publish_versionno = '1';
$publish_targetid = '1';
$publish_opr = 'publish';

if (isset($_REQUEST['opr_cmd']))
	$publish_opr = $_REQUEST['opr_cmd'];

if (isset($_REQUEST['ver']) && isset($_REQUEST['verno']) && isset($_REQUEST['targetid'])) {
	$publish_version = $_REQUEST['ver'];
	$publish_versionno = $_REQUEST['verno'];
	$publish_targetid = $_REQUEST['targetid'];
} else {
	echo json_encode(array(
		'retCode' => 421,
		'msg' => 'param of ver,verno and targetid is required.'
	));
	$auto_builder->log("publish unknown ver", 'False');
	exit;
}

$publish_option = '';
if (isset($_REQUEST['pubopt'])) {
	$publish_option = $_REQUEST['pubopt'];
}


// URL参数
putenv("AUTOBUILDER_PARAM_CMD=$publish_opr");
putenv("AUTOBUILDER_PARAM_VER=$publish_version");
putenv("AUTOBUILDER_PARAM_VERNO=$publish_versionno");
putenv("AUTOBUILDER_PARAM_TARGETID=$publish_targetid");
putenv("AUTOBUILDER_PARAM_CMD_OPTION=$publish_option");

// 逻辑跳转
if ($auto_builder->getLock()) {
	$need_segs = array('url', 'port', 'user', 'passwd', 'identity', 'install_path', 'use_ip', 'zone_id', 'custom');
	
	$conf_params = $auto_builder->getPublishCfgParamEnv(
		"/publish_addrs/zone[@id=$publish_targetid]", 
		$need_segs
	);
	
	if (count_chars($conf_params) == 0) {
		echo json_encode(array(
			'retCode' => 422,
			'cmdRetCode' => 0,
			'msg' => "zone $publish_targetid not found or configure error."
		));
		$auto_builder->log("publish $publish_version.$publish_versionno to $publish_targetid", 'False');
		exit;
	}
	
	passthru("sh {$auto_builder->conf->pub_shell} > {$auto_builder->getOperationInfoFilePath()} 2>&1", $ret);
	$auto_builder->releaseLock();

	echo json_encode(array(
			'retCode' => 0,
			'cmdRetCode' => $ret,
			'msg' => 'success'
	));
	$auto_builder->log("publish $publish_version.$publish_versionno to $publish_targetid", 'Run Shell: $ret');
	exit;
}

echo json_encode(array(
		'retCode' => 411,
		'msg' => 'get lock failed.'
));
$auto_builder->log("publish $publish_version.$publish_versionno to $publish_targetid", 'False');
