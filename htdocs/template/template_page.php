<?php
// 注册全局框架服务
global $service;

?><!DOCTYPE html>
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="x-ua-compatible" content="IE=edge" />
<meta charset="utf-8" />
<title><?php echo $service->project['title']; ?></title>
<meta name="description" content="自动发布系统" />
<meta name="author" content="owentou" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<style type="text/css">
    @import url( css/base.css );
	@import url( css/tether.min.css );
	@import url( css/tether-theme-basic.min.css );
    @import url( css/bootstrap.min.css );
	@import url( css/bootstrap-theme.min.css );
    @import url( js/css/jquery-ui.min.css );
	@import url( js/css/jquery-ui.structure.min.css );
	@import url( js/css/jquery-ui.theme.min.css );
    @import url( js/css/highlight/default.css );
    @import url( js/css/jquery-ui-timepicker-addon.min.css );
    
    .seed_alert{
    	z-index: 1024;
    }
</style>
<script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="js/tether.min.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>

<script type="text/javascript">
	window.webtools_conf = {
<?php 
	function webtools_script_init(){
		global $service;
		$prefix_data = '';
		foreach($service->getAuthTypes() as $channel_name) {
			echo $prefix_data;
			?> 
		'<?php echo $channel_name; ?>': {
				oauth_login: "<?php echo $service->getAuthUrl($channel_name); ?>"
		}
<?php 		$prefix_data = ',';
		}
	}
	webtools_script_init();
	 ?>
	};

/**
DEBUG
<?php print_r($service->getUserInfo()); ?>
**/
</script>

<script type="text/javascript" src="js/moment/moment-with-locales.min.js"></script>
<script type="text/javascript" src="js/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/main.js"></script>
<script type="text/javascript" src="js/highlight.pack.js"></script>
<script type="text/javascript" src="js/jquery-ui-timepicker-addon.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-timepicker-i18n/jquery-ui-timepicker-zh-CN.js"></script>
<script type="text/javascript" src="js/vuejs/vue.min.js"></script>
<script type="text/javascript">
    function seed_alert(msg, title){
        title = title || "Notice!";
        $("<div></div>").html(msg).dialog({
        	dialogClass: "seed_alert",
			"modal": true,
			"width": 640,
			"height": 480,
            "title": title,
			"buttons": {
				"Close": function() {
					$( this ).dialog( "close" );
				}
			}
		});
    }
	
	$(document).ready(function(){
		try{
			if(hljs.configure)
				hljs.configure({
				  tabReplace: '    ', // 4 spaces
				});
			else
				hljs.tabReplace = '    ';
			hljs.initHighlighting();
		} catch(e){
		}
	});
</script>

<!--[if (gte IE 6)&(lte IE 8)]>
<script type="text/javascript">
jQuery(document).ready(function(){
	alert("本页使用的Bootstrap 3.0不再支持IE6-8，请升级至更高版本的浏览器。");
	var dlg_recommand_browser = '<p>为了保证完整效果，建议使用以下浏览器访问本站</p>' +
		'<ul><li><a style="color: Blue;" href="http://windows.microsoft.com/zh-CN/internet-explorer/download/ie-9/worldwide" target="_blank">Microsoft Internet Explorer 10.0</a> 或以上' +
		'</li><li><a style="color: Blue;" href="http://www.apple.com.cn/safari/" target="_blank">Apple Safari 5.0</a> 或以上' + 
		'</li><li><a style="color: Blue;" href="http://www.google.com/chrome/eula.html" target="_blank">Google Chrome 30.0</a> 或以上' + 
		'</li><li><a style="color: Blue;" href="http://www.mozilla.com/" target="_blank">Mozilla Firefox 25.0</a> 或以上' + 
		'</li><li><a style="color: Blue;" href="http://www.opera.com/browser/" target="_blank">Opera 17.0</a> 或以上' + 
		'</li></ul>';
		
	seed_alert(dlg_recommand_browser, "推荐浏览器");
});
</script>
<![endif]-->

<?php

if(false == $service->loadProjectFile($service->getWebTemplateConf('project_head'))){
    include ($service->getWebTemplatePath($service->getWebTemplateConf('default_head')));
}

?>

</head>
<body><?php
	include ($service->getWebTemplatePath('template_header'));
?><div id="page" class="row">
<div id="aside" class="card col-2"><div class="card-block"><?php 

	if(false == $service->loadProjectFile($service->getWebTemplateConf('project_aside'))){
		include ($service->getWebTemplatePath($service->getWebTemplateConf('default_aside')));
	}
	
?></div></div><div id="page-content" class="card col-10"><div class="card-block"><?php 
    
    if(false == $service->loadProjectFile($service->getWebTemplateConf('project_home'))){
    	include ($service->getWebTemplatePath($service->getWebTemplateConf('default_home')));
    }
    
?></div></div>
<?php
	if(false == $service->loadProjectFile($service->getWebTemplateConf('project_footer'))){
		include ($service->getWebTemplatePath($service->getWebTemplateConf('default_footer')));
	}
?></div>
</body></html>
