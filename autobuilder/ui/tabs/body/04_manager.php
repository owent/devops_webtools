<div id="tabs-operator-manager">
	<span id="manager_server_list_container" class="input-group"></span><br />
	<br />
	<div id="manager_cmd_list">
		<template v-for="cmd_node in cmds">
			<label v-bind:cmd-name="cmd_node.name" v-bind:for="'manager_target_cmd_option_' + cmd_node.id">{{cmd_node.name}}</label>
			<input type="radio" name="manager_target_cmd_option" v-bind:cmd-name="cmd_node.name" v-bind:noservice="cmd_node.jnode.attr('noserver')" v-bind:value="cmd_node.jnode.text()" v-bind:id="'manager_target_cmd_option_' + cmd_node.id" v-bind:checked="cmd_node.jnode.attr('check')" />
		</template>
	</div><br />
	<div id="manager_services_list">
		<template v-for="server_node in services">
			<label v-bind:server-name="server_node.name" v-bind:for="'manager_target_service_option_' + server_node.id">{{server_node.name}}</label>
			<input type="checkbox" name="manager_target_service_option" v-bind:server-name="server_node.name" v-bind:value="server_node.jnode.attr('param')" v-bind:id="'manager_target_service_option_' + server_node.id" v-bind:checked="server_node.jnode.attr('check')" />
		</template>
	</div><br />
	<a href="javascript: void(0);" id="btn_manager_exec" >执行</a><br />
	<?php $auto_builder->loadProjectExtModule('manager', '<div id="tabs-operator-manager-project">', '</div>'); ?>
</div>