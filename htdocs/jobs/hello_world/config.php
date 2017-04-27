<?php 
global $service;
$service->setProjectTitle('项目名称');

$service->oss = array(
    'default' => 'mongodb://172.18.11.1:27017',
    'first_test' => 'mongodb://moyo_public:passwd2048@123.59.42.181:27017/stats',
    'users' => array(
        'gitlab' => array('owent'),
        'github' => array('owt5008137')
    )
);
