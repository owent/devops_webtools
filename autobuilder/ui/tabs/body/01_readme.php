<div id="tabs-operator-readme">
	使用说明：<br />
	<ul style="list-style: decimal;">
		<li>点击编译标签并选择要编译的版本，点击 <strong>编译</strong></li>
		<li>编译完成后，转到发布标签，选择编译的版本，输入刚才编译的版本号和发布目标机器，点击 <strong>发布</strong></li>
		<li>发布完成后，转到管理标签，选择刚才的发布目标 <strong>(注：发布会自动停止所有服务)</strong></li>
		<li>如果是新环境或需要清空数据库，选中<strong>初始化环境</strong>,并点 <strong>执行</strong></li>
		<li>选择 <strong>启动服务</strong>,并点击 <strong>执行</strong></li>
	</ul><?php $auto_builder->loadProjectExtModule('readme', '<div id="tabs-operator-readme-project">', '</div>'); ?>
</div>