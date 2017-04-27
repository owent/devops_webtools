<?php

require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config.php');

if ($auto_builder->getProjectName() === null) {
	echo "<script type='text/javascript'>seed_alert('<h1>必须指定项目名称</h1>', '必须指定项目名称')</script>";
	exit;	
}

if (!$auto_builder->isInited()){
	if (!is_dir($auto_builder->getProjectPath()))
		$mkdir_res = mkdir($auto_builder->getProjectPath());
	include (dirname(__FILE__) . DIRECTORY_SEPARATOR . 'setting.page.php');
	exit;
}

if($auto_builder->user === null) {
	echo "<script type='text/javascript'>alert('Please login first!'); webtools_login();</script>";
    exit;
} else if(!$auto_builder->checkPermission()) {
	echo "<script type='text/javascript'>alert('Permission denied!');history.back()</script>";
	exit;
}
