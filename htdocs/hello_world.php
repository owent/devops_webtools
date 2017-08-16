<?php 
require_once('config.php');

$service->loadProject('hello_world');
$service->setNav(array(array('uri' => 'hello_world.php', 'name' => $service->getProjectTitle())));

if(isset($_REQUEST['action'])) {
	$service->setNav(array(array('uri' => '?project=' . $service->getProjectName() . '&amp;action=builder', 'name' => '自动编译&amp;发布系统')));
} elseif(isset($_REQUEST['oss'])) {
    $service->setNav(array(array('uri' => '?project=' . $service->getProjectName() . '&amp;oss=view', 'name' => 'OSS运营分析系统')));
    $service->setWebTemplateConf('default_aside', 'oss_aside');
    $service->setWebTemplateConf('default_home', 'oss_home');
    $service->setWebTemplateConf('default_api', 'oss_api');
    $service->setWebTemplateConf('default_head', 'oss_head');

    $service->setWebTemplateConf('project_aside', 'oss_aside.php');
    $service->setWebTemplateConf('project_home', 'oss_home.php');
    $service->setWebTemplateConf('project_api', 'oss_api.php');
    $service->setWebTemplateConf('project_head', 'oss_head.php');

    if ('view' != $_REQUEST['oss']) {
        require_once($service->getWebTemplatePath('template_api'));
        exit(0);
    }
}

require_once($service->getWebTemplatePath('template_page'));


