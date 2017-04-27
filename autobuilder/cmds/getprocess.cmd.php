<?php 

$is_finished = $auto_builder->getLock();

if ($is_finished) {
	$auto_builder->releaseLock();
}

$log_offset = 0;
$log_len = 0;

if (!empty($_REQUEST['offset']))
	$log_offset = intval($_REQUEST['offset']);
	
$file_content = file_get_contents(
		$auto_builder->getOperationInfoFilePath(),
		false,
		null,
		$log_offset
);

$log_len = strlen($file_content);

echo json_encode(array(
	'retCode' => 0,
	'msg' => $file_content,
	'offset' => $log_offset + $log_len,
	'start_at' => $log_offset,
	'finished' => $is_finished? 1: 0
));
