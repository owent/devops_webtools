<?php 

set_time_limit(600);

// URL参数
putenv("AUTOBUILDER_PARAM_CMD=getpackages");

$pageinfos = shell_exec("sh {$auto_builder->conf->build_shell}");
$match_res1 = array();
$match_res2 = array();
preg_match_all('|VERSION\([\w\d-_\.\\\/\s]+\)|', $pageinfos, $match_res1);

$res1_index_1 = count($match_res1);
if ($res1_index_1 > 0 && count($match_res1[$res1_index_1 - 1])) {
    $res1_index_2 = count($match_res1[$res1_index_1 - 1]);
	$version_text = substr($match_res1[$res1_index_1 - 1][$res1_index_2 - 1], 8);
	preg_match_all('|[\w\d-_\.\\\/]+|', $version_text, $match_res2);
}

echo json_encode(array(
	'retCode' => 0,
	'packages' => $match_res2,
	'msg' => 'success'
));
