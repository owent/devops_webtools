<?php 

set_time_limit(600);

// URL参数
putenv("AUTOBUILDER_PARAM_CMD=getversions");

$output = shell_exec("sh {$auto_builder->conf->build_shell}");

echo json_encode(array(
	'retCode' => 0,
	'msg' => $output
));

