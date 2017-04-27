<?php

global $service;

// https://tools.muyo.co/moyo_no1.php?oss=api&oss_action=mongodb&oss_env=default&oss_db=stat&oss_table=login&oss_x=timestamp&oss_gt=1448526695&oss_lt=1448882150
try {
    $req_action = $_REQUEST['oss_action'];

    if('config' == $req_action) {
        $file_path = $service->getProjectPath() . DIRECTORY_SEPARATOR . 'oss_config.xml';
        header('Content-Type: text/xml');
        if(file_exists($file_path)) {
            echo file_get_contents($file_path);
        } else {
            echo '<?xml version="1.0" encoding="utf-8"?>\n<file_not_found>oss_config.xml</file_not_found>';
        }
        exit(0);
    } else {
        if (!empty($service->oss['users'])) {
            // check access
            $access_users = $service->oss['users'];
            $user_info = $service->getUserInfo();
            if(!$user_info['is_logined'] || empty($access_users[$user_info['user_data']['channel']]) || 
                ! in_array($user_info['user_data']['login_name'], $access_users[$user_info['user_data']['channel']])
                ) {
                throw new Exception('Required login or access deny');
            }
        }
        if ('mongodb' == $req_action) {
            header('Content-Type: application/json');

            $oss_env = 'default';
            if (!empty($_REQUEST['oss_env'])) {
                $oss_env = $_REQUEST['oss_env'];
            }

            MongoPool::setSize(2);
            $db_cli = new MongoClient($service->oss[$oss_env]);
            if (!$db_cli->connected) {
                $db_cli->connect();
            }
            $db_name = $_REQUEST['oss_db'];
            $tb_name = $_REQUEST['oss_table'];
            $type_x = $_REQUEST['oss_x'];

            if (isset($_REQUEST['oss_count'])) {
                $count_limit = intval($_REQUEST['oss_count']);
            } else {
                $count_limit = 9000;
            }

            if (!empty($type_x)) {
                $find_rule = array(
                    "$type_x" => array(
                        '$gt' => 0
                    )
                );

                if (isset($_REQUEST['oss_gt'])) {
                    $find_rule[$type_x]['$gt'] = intval($_REQUEST['oss_gt']);
                }

                if (isset($_REQUEST['oss_lt'])) {
                    $find_rule[$type_x]['$lt'] = intval($_REQUEST['oss_lt']);
                }
            } else {
                $find_rule = array();
            }

            $output = array(
                'ret' => 0,
                'msg' => 'ok',
                'data' => array()
            );

            $res_cur = $db_cli->selectCollection($db_name, $tb_name)->find($find_rule);
            foreach ($res_cur->limit($count_limit) as $record) {
                array_push($output['data'], $record);
            }

            echo json_encode($output);
        } else {
            echo json_encode(array(
                'ret' => -1,
                'msg' => "oss_action=$req_action invalid"
            ));
        }
    }
} catch (Exception $e) {
    header('Content-Type: application/json');

    echo json_encode(array(
        'ret' => -1,
        'msg' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ));
}
