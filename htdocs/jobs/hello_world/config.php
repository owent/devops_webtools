<?php 
global $service;
$service->setProjectTitle('酋长万岁');

$service->oss = array(
    'default' => 'mongodb://172.18.11.1:27017',
    'first_test' => 'mongodb://[用户名]:[密码]@123.59.42.181:27017/stats',
    'users' => array(
        'gitlab' => array('owent'),
        'github' => array('owt5008137')
    )
);
