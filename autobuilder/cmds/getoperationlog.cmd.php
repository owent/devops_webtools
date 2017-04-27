<?php 

$log_path = '';
if(false == empty($_REQUEST['log_date']))
	$log_path = $auto_builder->getOperationLogFilePath();
else
	$log_path = $auto_builder->getOperationLogFilePath($_REQUEST['log_date']);

		
if (file_exists($log_path)) {
	echo file_get_contents($log_path);
} else {
	echo "";
}
