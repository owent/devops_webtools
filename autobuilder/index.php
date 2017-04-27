<?php 
if ($_REQUEST['action'] == 'cmd') {
	require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . 'cmd.php');
	
} else {
	require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . 'project.page.conf.php');
	
	if (file_exists($auto_builder->getProjectPath() . DIRECTORY_SEPARATOR . 'pages' . DIRECTORY_SEPARATOR . $_GET['action'] . '.page.php'))
		require_once ($auto_builder->getProjectPath() . DIRECTORY_SEPARATOR . 'pages' . DIRECTORY_SEPARATOR . $_GET['action'] . '.page.php');
	else
		require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . $_GET['action'] . '.page.php');
}

