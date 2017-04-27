<form id="setting" method="post" class="form" action="">
  <fieldset>
    <legend><?php echo $auto_builder->getProjectName(); ?>-发布工具设置</legend>
    <p><div class="form-group" style="width: 100%;">
      <label for="build_shell"><span class="required">*</span>编译脚本文件(绝对路径):</label>
      <input id="build_shell" class="form-control" type="text" name="build_shell"  title="编译脚本文件" placeholder="编译脚本文件(绝对路径)" value="<?php if($auto_builder->conf !== null ) echo $auto_builder->conf->build_shell; ?>" />
    </div></p>
    <p><div class="form-group" style="width: 100%;">
      <label for="pub_shell"><span class="required">*</span>内部安装脚本文件(绝对路径):</label>
      <input id="pub_shell" class="form-control" type="text" name="pub_shell" title="内部安装脚本文件" placeholder="内部安装脚本文件(绝对路径)" value="<?php if($auto_builder->conf !== null ) echo $auto_builder->conf->pub_shell; ?>" />
    </div></p>
    <p><div class="form-group" style="width: 100%;">
      <label for="download_pkg"><span class="required">*</span>安装包下载地址(URL):</label>
      <input id="download_pkg" class="form-control" type="text" name="download_pkg" title="安装包下载地址" placeholder="安装包下载地址(URL)" value="<?php if($auto_builder->conf !== null ) echo $auto_builder->conf->download_pkg; ?>" />
    </div></p>
    <p><div class="form-group" style="width: 100%;">
      <label for="Message">权限管理(英文名，分号分隔):</label>
      <textarea id="Message" class="form-control" title="权限管理" name="permission"  placeholder="权限管理(英文名，分号分隔)" rows="3" cols="20"><?php if($auto_builder->conf !== null ) echo $auto_builder->conf->permission; ?></textarea>
    </div></p>
    <input type="hidden" name="project" value="<?php echo $auto_builder->getProjectName(); ?>" />
    <hr /><?php $auto_builder->loadProjectExtModule('setting.page'); ?>
    </fieldset>
  <a href="javascript: void();" id="save">保存</a>&nbsp;&nbsp;&nbsp;&nbsp;
  <a href="javascript:void(0);" id="force_unlock_cmds">强制解除指令锁</a>
</form>
<script type="text/javascript">
//<!--
$(document).ready(function(){
	$("#setting").tooltip();
	
	$("#save").button().click(function(){
		$.ajax({
			url: 'build.php?action=cmd&project=<?php echo $auto_builder->getProjectName(); ?>&cmd=savecfg',
			data: $("#setting").serialize(),
			type: 'POST',
			cache: false,
			dataType: 'json',
			success: function(data){
		        if (data && data.retCode == 0) 
		            $('<p>新的设置已经保存成功</p>').dialog({title: '保存成功！'});
		        else
		        	$('<p>' + data.msg + '</p>').dialog({title: '保存失败！'});
			}
		});
	});

    $("#force_unlock_cmds").button().click(function() {
    	$( "<div>您真的要强制解除指令锁吗?<br />如果发布系统未发生异常，请不要执行此命令</div>" ).dialog({
            title: '警告！ 您确定要强制解除指令锁吗?',
  	        resizable: false,
  	        modal: true,
						"width": 640,
						"height": 480,
  	        buttons: {
  	      	    '确认强制解除': function() {
  	        	    $( this ).dialog( "close" );

	  	      		$.ajax({
		  	  			url: 'build.php?action=cmd&project=<?php echo $auto_builder->getProjectName(); ?>&cmd=releaseactionlock',
		  	  			type: 'POST',
		  	  			cache: false,
		  	  			dataType: 'json',
		  	  			success: function(data){
		  	  		        if (data && data.retCode == 0) 
		  	  		            $('<p>解锁成功</p>').dialog({title: '执行成功！'});
		  	  		        else
		  	  		        	$('<p>' + data.msg + '</p>').dialog({title: '解锁失败！'});
		  	  			}
		  	  		});
  	  	  		
  	        	},
  	        	'我不确定, 取消': function() {
  	          		$( this ).dialog( "close" );
  	        	}
  	      	}
  	    });
    });
});

//-->
</script>