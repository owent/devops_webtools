<header class="navbar-inverse">
<div id="page-header">
    <div class="header-top text-right">
        <p>Hello 
            <?php 
            $user_info = $service->getUserInfo();
            if($user_info['is_logined']){?>
            <strong><?php
                if(empty($user_info['user_data']['nick_name'])) {
                    echo $user_info['user_data']['login_name'];
                } else {
                    echo $user_info['user_data']['nick_name'] . ' (' . $user_info['user_data']['login_name'] . ')';
                } ?></strong><?php
                if(!empty($user_info['user_data']['channel'])) {
                    if (!empty($user_info['user_data']['channel_url'])) {
                        echo (' from <a href="' . sprintf($user_info['user_data']['channel_url'], $user_info['user_data']['login_name']) . '">' . $user_info['user_data']['channel'] . '</a>');
                    } else {
                        echo (' from ' . $user_info['user_data']['channel']);
                    }
                }
                ?> | <a href="logout.php">Logout</a>
            <?php } else { ?>
            welcome <strong><?php echo $user_info['user_data']['login_name']; ?></strong><span id="login"></span>
            <?php } ?>
        </p>
    </div>
    <div class="header-middle">
        <!-- Start Nav -->
        <div class="navbar-brand float-left">
            <!-- Start Logo -->
            <img id="logo" src="img/logo.png" alt="常用工具集" style="width: 64px;" />
            <!-- End Logo -->
        </div>
		<nav class="navbar navbar-toggleable-md bg-faded navbar-inverse float-right rounded" role="navigation">
            <div class="collapse navbar-collapse">
                <ul id="nav" class="navbar-nav text-center">
                    <li class="nav-item <?php if('hello_world' == $service->project['name']) echo ' active' ?>">
                        <a class="nav-link" href="hello_world.php">项目名称 (开发中)</a>
                    </li>
                </ul>
            </div>
        </nav>
        <!-- End Nav -->
		<div class="clearfix"></div>
    </div>
    <div class="header-bottom">
		<!-- Start Breadcrumbs -->
		<ol class="breadcrumb"><?php 
					foreach($service->getNav() as $k => $v){
			?><li class="breadcrumb-item"><a href="<?php echo $v['uri']?>"><?php echo $v['name']?></a></li><?php 
			}
			?></ol>
        <!-- End Breadcrumbs -->
    </div>
</div>
</header>
