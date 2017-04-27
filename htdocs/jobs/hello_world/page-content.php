<?php 
global $service;
if(isset($_REQUEST['action'])) { 
	require_once( $service->getWebRoot() . '/../autobuilder/index.php');
} else {
?><div id="page-header">
	<h1>常用链接:</h1>
</div>
<div id="article">
	<ul>
    <!-- <li><a href="?oss=view"><span style="font-style: italic; color: Red;">new</span> 运营分析系统</a></li>-->
    <li><a href="javascript: seed_alert('尚未接入')" title="运营分析系统">运营分析系统</a></li>
    <!-- <li><a href="http://GM工具地址/latest.nightly/gmtools?passwd=GM工具密码"> Client GM工具(每日构建)</a></li>-->
    <li><a href="javascript: seed_alert('尚未接入')" title="Client GM工具(每日构建)">Client GM工具(每日构建)</a></li>
    <li><a href="javascript: open_dialog_environment_list();" title="开发环境地址列表">开发环境地址列表</a></li>
	<li><a href="//jenkins.io/" title="Jenkins自动定时构建系统" target="_blank">Jenkins自动定时构建系统</a></li>
    <li><a href="javascript: seed_alert('尚未接入')" title="Server包下载" target="_blank">Server包下载(每日构建)</a></li>
	<li><a href="javascript: seed_alert('尚未接入')" title="Client包下载" target="_blank">Client包下载(每日构建)</a></li>
	<li><a href="javascript: seed_alert('尚未接入')" title="Client自动化文档">Client自动化文档</a></li>
	<li><a href="javascript: seed_alert('尚未接入')" title="Server自动化文档" target="_blank">Server自动化文档</a></li>
    <li><a href="?project=<?php echo $service->getProjectName(); ?>&action=builder" title="自动编译&发布系统">自动编译&amp;发布系统</a></li>
	</ul>
    <br />
    <table class="table table-hover table-bordered">
    <tbody><tr>
    <th>客户端构建状态</th>
    <th>Android</th>
    <th>IOS</th>
    <th>Windows</th>
    </tr>

    <tr>
    <td>Nightly Build</td>
    <td>尚未配置</td>
    <!--<td><a href="https://travis-ci.org/atframework/libatbus"><img src="https://travis-ci.org/atframework/libatbus.svg?branch=master" alt="Build Status"></a></td>-->
    <td><a href="https://travis-ci.org/atframework/libatbus"><img src="https://travis-ci.org/atframework/libatbus.svg?branch=master" alt="Build Status"></a></td>
    <td>尚未配置</td>
    <!--<td><a href="https://travis-ci.org/atframework/libatbus"><img src="https://travis-ci.org/atframework/libatbus.svg?branch=master" alt="Build Status"></a></td>-->
    </tr>
    </tbody>
    </table>
    <br />
    <table class="table table-hover table-bordered">
    <tbody><tr>
    <th>服务器构建状态</th>
    <th>CentOS 7 + GCC 4.8</th>
    </tr>
    <tr>
    <td>Nightly Build</td>
    <td><a href="https://travis-ci.org/atframework/libatbus"><img src="https://travis-ci.org/atframework/libatbus.svg?branch=master" alt="Build Status"></a></td>
    </tr>
    </tbody></table>
    <br />
    <hr />
    <div>
        附加可以安装插件的Chrome分支地址。<br />
        <h4>Chromium 下载地址</h4>
        ------<br />
        <a href="https://download-chromium.appspot.com/">https://download-chromium.appspot.com/</a><br />

        <h4>Chrome 下载地址</h4>
        ------<br />
        Chrome dev分支在线安装: <a href="https://www.google.com/chrome/browser/index.html?hl=zh-CN&extra=devchannel">https://www.google.com/chrome/browser/index.html?hl=zh-CN&extra=devchannel</a><br />
        Chrome dev分支(x86_64)在线安装: <a href="https://www.google.com/chrome/browser/?platform=win64&extra=devchannel">https://www.google.com/chrome/browser/?platform=win64&extra=devchannel</a><br />
        <br />
        Chrome canary分支在线安装: <a href="https://www.google.com/intl/en/chrome/browser/canary.html?hl=zh-CN#eula">https://www.google.com/intl/en/chrome/browser/canary.html?hl=zh-CN#eula</a><br />
        Chrome canary分支(x86_64)在线安装: <a href="https://www.google.com/intl/en/chrome/browser/canary.html?hl=zh-CN&platform=win64#eula">https://www.google.com/intl/en/chrome/browser/canary.html?hl=zh-CN&platform=win64#eula</a><br />
        <br />

    </div>
</div>
</div>
<div id="dialog_environment_list" title="开发环境地址列表"
	style="display: none;">
	<ul>
		<li>公共Dev环境-A1
			<a style="color: Blue;" href="javascript: void(0);" title="公共Dev环境-A1" target="_blank">然而并没有</a>
		</li>
	</ul>
</div>

<script type="text/javascript">
$(document).ready(function(){
	$("#article").tooltip();
	$("#dialog_environment_list").tooltip();
});

function open_dialog_environment_list(){
	$("#dialog_environment_list").dialog({
		minHeight: 480,
		minWidth: 600
	});
}

</script>
<?php } ?>
