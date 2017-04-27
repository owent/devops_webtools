<div id="page-header">
	<h1>运营分析系统<span id="oss-subtitle"></span></h1>
</div>
<div id="article">
	<div id="oss-ctl-panel"></div>
	<div id="oss-chart" style="min-height: 600px; min-width: 800px;">尚未加载数据。</div>
</div>

<?php 

function oss_home_init() {
	global $service;
	if (!empty($service->oss['users'])) {
		// check access
		$access_users = $service->oss['users'];
		$user_info = $service->getUserInfo();

		if(!$user_info['is_logined'] || empty($access_users[$user_info['user_data']['channel']]) || 
			! in_array($user_info['user_data']['login_name'], $access_users[$user_info['user_data']['channel']])
			) { ?>
			
<script type="text/javascript">
(function(){
		seed_alert("需要登入或此账户没有查看权限", "Access Deny");
})();
</script>
		<?php }
	}
}

oss_home_init();
?>