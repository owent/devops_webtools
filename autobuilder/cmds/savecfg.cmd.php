<?php 
if ($_POST) {
	$auto_builder->conf = $_POST;

	$auto_builder->log('setting', 'True');
}

if (isset($_FILES['publish_cfg'])) {
	move_uploaded_file($_FILES["file"]["tmp_name"], $auto_builder->getPublishCfgFilePath());
}

echo json_encode(array(
		'retCode' => '0',
		'msg' => 'success!'
));

