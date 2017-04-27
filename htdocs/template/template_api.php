<?php
// 注册全局框架服务
global $service;

if(false == $service->loadProjectFile($service->getWebTemplateConf('project_api'))){
	include ($service->getWebTemplatePath($service->getWebTemplateConf('default_api')));
}
