<div id="tabs-operator-custom">
	<span>正在读取自定义命令...</span>
	<span id="custom_server_list_container" class="input-group"></span><br />
	<form id="tabs-operator-custom-cmds" method="post" class="form" action="">
	  	<fieldset>
	  		<legend><?php echo $auto_builder->getProjectName(); ?> - 自定义命令</legend>
 			<div id="tabs-operator-custom-cmds-content">
			 	<table class="table">
				<tr v-for="cmd in cmds">
					<td>{{ cmd.id }}</td>
					<td>
						<a v-if="cmd.param" style="color: #444; font-weight: bolder;" v-bind:href="'javascript: window.AutoBuilder.customCmds.showCmd(' + cmd.id + ', \'#tabs-operator-custom-cmd-param-' + cmd.id + '\');'">{{ cmd.name }}</a>
						<a v-else style="color: #444; font-weight: bolder;" v-bind:href="'javascript: window.AutoBuilder.customCmds.showCmd(' + cmd.id  + ');'">{{ cmd.name }}</a>
					</td>
					<td>
						<input v-if="cmd.param" class="form-control" type="text" v-bind:id="'tabs-operator-custom-cmd-param-' + cmd.id" />
					</td>
					<td>
						<a v-if="cmd.param" style="color: Blue; font-weight: bolder;" v-bind:href="'javascript: window.AutoBuilder.customCmds.runCmd(' + cmd.id + ', \'#tabs-operator-custom-cmd-param-' + cmd.id + '\');'">执行</a>
						<a v-else style="color: Blue; font-weight: bolder;" v-bind:href="'javascript: window.AutoBuilder.customCmds.runCmd(' + cmd.id + ');'">执行</a>
					</td>
					<td><a style="color: Blue; font-weight: bolder;" v-bind:href="'javascript: window.AutoBuilder.customCmds.delCmd(' + cmd.id + ');'">删除</a></td>
				</tr>
				</table>
            </div>
	    </fieldset>
	</form>
	<?php $auto_builder->loadProjectExtModule('custom', '<div id="tabs-operator-custom-project">', '</div>'); ?>
	<br /><br /><br />
	<a href="javascript: void(0);" id="tabs-operator-custom-add-btn" >添加指令</a>&nbsp;&nbsp;&nbsp;&nbsp;
	<a href="javascript: void(0);" id="tabs-operator-custom-readme-btn" style="color: Blue;" >自定义命令须知</a>

<div id="tabs-operator-custom-add-dlg" title="自定义命令选项" style="display: none;">
<form id="tabs-operator-custom-add-form" method="post" class="form-inline" action="">
	<span for="City">指令名称:</span>
    <input id="tabs-operator-custom-add-name" type="text" name="name" title="自定义脚本名称" value="设置服务器时间" class="form-control" /><br />
	<span for="City">指令脚本:</span>
    <input id="tabs-operator-custom-add-shell_cmd" type="text" name="shell_cmd" title="自定义脚本" value="date -s" class="form-control" /><br />
    <span for="City">自定义脚本参数类型:</span>
    <select id="tabs-operator-custom-add-param" name="param" class="btn btn-default">
    	<option value="none">无参数</option>
    	<option value="custom">自定义</option>
    	<option value="date">日期</option>
    	<option value="datetime" selected="selected">日期时间</option>
    	<option value="integer">整数</option>
    	<option value="decimal">小数(精确到0.001)</option>
    </select><br /><br />
    <input type="checkbox" id="tabs-operator-custom-add-warning-enable" /><label for="tabs-operator-custom-add-warning-enable">开启执行前警告?</label><br />
    警告信息(如果没有警告信息, 警告也不会生效) <textarea style="width: 100%;" class="form-control" id="tabs-operator-custom-add-warning" disabled="disabled" name="warning" title="执行警告" rows="3"></textarea><br />
</form>
</div>

<div id="tabs-operator-custom-readme-dlg" title="自定义命令须知" style="display: none;">
	<h4>自定义命令须知:</h4>
	<ul>
		<li>自定义命令的执行环境与xml中配置的管理命令相同(即会发布manager指令到安装脚本中)</li>
		<li>指令中可用的环境变量: <table class="table"><thead>
			<tr><th>环境变量名称</th><th>值描述</th></tr>
		</thead><tbody>
			<tr><td><strong>AUTOBUILDER_PARAM_CMD</strong></td><td>指令名称</td></tr>
			<tr><td><strong>AUTOBUILDER_PARAM_TARGETID</strong></td><td>目标服务器ID</td></tr>
			<tr><td><strong>AUTOBUILDER_PARAM_CMD_SHELL</strong></td><td>指令代码</td></tr>
			<tr><td><strong>AUTOBUILDER_ZONE_URL</strong></td><td>登入IP</td></tr>
			<tr><td><strong>AUTOBUILDER_ZONE_PORT</strong></td><td>登入ssh使用的端口</td></tr>
			<tr><td><strong>AUTOBUILDER_ZONE_INSTALL_PATH</strong></td><td>服务器安装目录</td></tr>
			<tr><td><strong>AUTOBUILDER_ZONE_USER</strong></td><td>执行指令的用户</td></tr>
			<tr><td><strong>AUTOBUILDER_ZONE_PASSWD</strong></td><td>执行指令的用户密码</td></tr>
			<tr><td><strong>AUTOBUILDER_ZONE_USE_IP</strong></td><td>服务器采用的IP编号（即第几个IP）</td></tr>
			<tr><td><strong>AUTOBUILDER_ZONE_ZONE_ID</strong></td><td>服务器大区ID</td></tr>
			<tr><td><strong>AUTOBUILDER_CUSTOM_...[名称全大写]</strong></td><td> (用户自定义参数, 配置在XML内的custom字段中)</td></tr>
		</tbody></table></li><li>
		<?php $auto_builder->loadProjectExtModule('custom-readme', '', ''); ?>
		</li>
	</ul>

</div>
</div>
