<?php 
if ($_POST) {
	$auto_builder->conf = $_POST;

	$auto_builder->log('setting', 'True');
}

// 逻辑
if ($auto_builder->getLock()) {
	$auto_builder->releaseLock();
	
	echo json_encode(array(
			'retCode' => -1,
			'msg' => '未加锁'
	));
	$auto_builder->log("release action lock", 'False');
	exit;
}

$lock_file_path = $auto_builder->getLockFilePath();
if(false == file_exists($lock_file_path)) {
	echo json_encode(array(
		'retCode' => -2,
		'msg' => '锁文件未找到!'
	));
	$auto_builder->log("release action lock", 'False');
	exit;
}

if (false == unlink($lock_file_path)) {
	echo json_encode(array(
			'retCode' => -3,
			'msg' => '权限不足或其他错误，未能解锁!'
	));
	$auto_builder->log("release action lock", 'False');
	exit;
}


echo json_encode(array(
		'retCode' => 0,
		'msg' => 'success!'
));
$auto_builder->log("release action lock", 'True');
