<?php 

if (empty($_GET['readonly']) && !empty($_POST)) {
	$custom_cmds = $auto_builder->custom_cmd;
	if (isset($_GET['add'])) {
		$no = 0;
		foreach($custom_cmds as $key => $val) {
			$no = max($no, intval($val['id']));
		}
		++ $no;
		
		$new_cmd = $_POST;
		$new_cmd['id'] = $no;
		$custom_cmds["Cmd NO.$no"] = $new_cmd;
		$auto_builder->log('add custom cmd', 'True');
	}
	
	if (isset($_GET['del']) && isset($_POST['cmd_id'])) {
		$no = $_POST['cmd_id'];
		
		if (empty($custom_cmds["Cmd NO.$no"])) {
			$auto_builder->log('del custom cmd', 'False');
			echo json_encode(array(
				'retCode' => -1,
				'msg' => 'cmd not found!',
				'cmds' => json_encode($auto_builder->custom_cmd)
			));
			exit;
		}
	
		$auto_builder->log('del custom cmd', 'True');
		unset($custom_cmds["Cmd NO.$no"]);
	}
	
	$auto_builder->custom_cmd = $custom_cmds;
}

echo json_encode(array(
	'retCode' => '0',
	'msg' => 'success!',
	'cmds' => $auto_builder->custom_cmd
));

