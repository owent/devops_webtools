<?php 
class ProjectWebToolsFrame {
	private $_oauth_config = array(
		'gitlab' => array(
			'name' => 'webtools',
			'client_secret' => 'client_secret参数',
			'authorize' => 'https://git.giuer.com/oauth/authorize',
			'authorize_params' => array(
				'client_id' => 'client_id参数',
				'redirect_uri' => 'https://webtools.giuer.com',
				'response_type' => 'code'
			),
			'token' => 'https://git.giuer.com/oauth/token',
			'api' => 'https://git.giuer.com/api/v3',
			'url' => 'https://git.giuer.com/u/%s'
		), /*
		'github' => array(
			'name' => 'webtools',
			'client_secret' => 'client_secret参数',
			'authorize' => 'https://github.com/login/oauth/authorize',
			'authorize_params' => array(
				'client_id' => 'client_id参数',
				'redirect_uri' => 'https://webtools.giuer.com',
				'scope' => 'user'
			),
			'token' => 'https://github.com/login/oauth/access_token',
			'api' => 'https://api.github.com',
			'url' => 'https://github.com/%s'
        ),*/
        '企业微信' => array(
			'name' => 'webtools',
			'client_secret' => 'client_secret参数',
			'authorize' => 'https://open.work.weixin.qq.com/wwopen/sso/qrConnect',
			'authorize_params' => array(
                'appid' => 'appid参数',
                'agentid' => 'agentid参数',
				'redirect_uri' => 'https://webtools.giuer.com'
                # https://open.work.weixin.qq.com/wwopen/sso/qrConnect?appid=appid参数&agentid=agentid参数&redirect_uri=https%3A%2F%2Fwebtools.giuer.com&state=web_login@owent
			),
			'token' => 'https://qyapi.weixin.qq.com/cgi-bin/gettoken',
			'api' => 'https://qyapi.weixin.qq.com/cgi-bin',
			'url' => ''
		)
	);

	private $_user = array(
		'is_logined' => false,
		'access_token' => '',
		'user_data' => null
	);
	
	private $_config = array(
		'project' => array(
			'title' => 'Web端工具门户',
			'name' => 'Unknown'
		),
		'template' => array(
            'default_aside' => 'default_aside',
            'default_footer' => 'default_footer',
            'default_home' => 'default_home',
            'default_api' => 'default_api',
            'default_head' => 'default_head',
            'project_aside' => 'aside.php',
            'project_home' => 'page-content.php',
            'project_footer' => 'footer.php',
            'project_head' => 'head.php',
            'project_api' => 'api.php'
        )
	);
	
	private $_nav_list = array(
		array('uri' => '/', 'name' => '首页')
	);
	
	private function _login_with_gitlab($channel_config) {
		if (empty($_REQUEST['code']) || empty($channel_config)) {
			return;
		}

		// 通过code拿到Access Token
		$access_data = null;
		{
			$post_data = array(
				'client_id' => $channel_config['authorize_params']['client_id'],
				'client_secret' => $channel_config['client_secret'],
				'code' => $_REQUEST['code'],
				'grant_type' => 'authorization_code',
				'redirect_uri' => $channel_config['authorize_params']['redirect_uri'],
			);
			$post_data = http_build_query($post_data);

			$context = stream_context_create(array(
				'http' => array (
					'method' => 'POST',
					'header'=> "Content-type: application/x-www-form-urlencoded\r\n" .
						'Content-Length: ' . strlen($post_data) . "\r\n",
					'content' => $post_data)
			));
			$access_data = json_decode(file_get_contents($channel_config['token'], false, $context));
		}

		if (empty($access_data) || empty($access_data->access_token)) {
			return;
		}

		// 通过Access Token拿到用户信息
		$user_info = file_get_contents($channel_config['api'] . '/user?access_token=' . $access_data->access_token);
		$user_info = json_decode($user_info);

		if (empty($user_info) || empty($user_info->username)) {
			return;
		}

		$this->_user['user_data'] = array(
			'login_name' => $user_info->username,
			'nick_name' => empty($user_info->name)?$user_info->username: $user_info->name,
			'access_token' => $access_data->access_token,
			'channel' => 'gitlab',
			'channel_url' => empty($channel_config['url'])? null: $channel_config['url']
		);


		$this->_user['is_logined'] = true;
	}

	private function _login_with_github($channel_config) {
		if (empty($_REQUEST['code']) || empty($channel_config)) {
			return;
		}

		// 通过code拿到Access Token
		$access_data = null;
		{
			$post_data = array(
				'client_id' => $channel_config['authorize_params']['client_id'],
				'client_secret' => $channel_config['client_secret'],
				'code' => $_REQUEST['code']
			);
			$post_data = http_build_query($post_data);

			$context = stream_context_create(array(
				'http' => array (
					'method' => 'POST',
					'header'=> "Content-type: application/x-www-form-urlencoded\r\n" .
						'Content-Length: ' . strlen($post_data) . "\r\n" . 
						'Accept: application/json' . "\r\n", 
					'content' => $post_data)
			));
			$access_data = json_decode(file_get_contents($channel_config['token'], false, $context));
		}

		if (empty($access_data) || empty($access_data->access_token)) {
			return;
		}

		// 通过Access Token拿到用户信息
		$user_info = file_get_contents($channel_config['api'] . '/user?access_token=' . $access_data->access_token);
		$user_info = json_decode($user_info);

		if (empty($user_info) || empty($user_info->login)) {
			return;
		}

		$this->_user['user_data'] = array(
			'login_name' => $user_info->login,
			'nick_name' => empty($user_info->name)?$user_info->login: $user_info->name,
			'access_token' => $access_data->access_token,
			'channel' => 'github',
			'channel_url' => empty($channel_config['url'])? null: $channel_config['url']
		);


		$this->_user['is_logined'] = true;
	}

    private function _login_with_work_weixin($channel_config) {
		if (empty($_REQUEST['code']) || empty($channel_config)) {
			return;
		}

		// 通过code拿到Access Token
		$access_data = null;
		{
			$post_data = array(
				'corpid' => $channel_config['authorize_params']['appid'],
				'corpsecret' => $channel_config['client_secret']
			);
            $post_data = http_build_query($post_data);
            $access_data = json_decode(file_get_contents($channel_config['token'] . '?' . $post_data));
		}

		if (empty($access_data) || empty($access_data->access_token)) {
			return;
		}

		// 通过Access Token拿到用户信息
        $user_info = file_get_contents($channel_config['api'] . '/user/getuserinfo?access_token=' . $access_data->access_token . '&code=' . $_REQUEST['code']);
		$user_info = json_decode($user_info);

		if (empty($user_info) || empty($user_info->UserId)) {
			return;
		}

        $user_id = $user_info->UserId;
        $user_info = file_get_contents($channel_config['api'] . '/user/get?access_token=' . $access_data->access_token . '&userid=' . $user_id);
        $user_info = json_decode($user_info);

		$this->_user['user_data'] = array(
			'login_name' => $user_info->userid,
			'nick_name' => empty($user_info->name)?$user_info->userid: $user_info->name,
            'access_token' => $access_data->access_token,
            'user_id' => $user_id,
			'channel' => '企业微信',
			'channel_url' => empty($channel_config['url'])? null: $channel_config['url']
		);


		$this->_user['is_logined'] = true;
	}

	private function _check_user() {
		session_start();

		if (!empty($_SESSION['user'])) {
            $this->_user['user_data'] = $_SESSION['user'];
			$this->_user['is_logined'] = $this->_user['user_data']['login_name'] != 'guest';
		} else {
            $this->_user['user_data'] = array(
                'login_name' => 'guest'
            );
		}

        // 尝试使用OAuth登入gitlab
        while (!$this->_user['is_logined'] && !empty($_COOKIE['oauth_type'])) {
			$channel_name = $_COOKIE['oauth_type'];
			$channel_config = null;
			if (!empty($this->_oauth_config[$channel_name])) {
				$channel_config = $this->_oauth_config[$channel_name];
			}

			if ('gitlab' == $channel_name) {
				$this->_login_with_gitlab($channel_config);
			} elseif ('github' == $channel_name) {
				$this->_login_with_github($channel_config);
			} elseif ('企业微信' == $channel_name) {
				$this->_login_with_work_weixin($channel_config);
			}

			if ($this->_user['is_logined']) {
            	$_SESSION['user'] = $this->_user['user_data'];
				if (!empty($_COOKIE['oauth_type'])) {
					setcookie('oauth_type', null, time() - 31536000);
				}
			}

            if (!empty($_COOKIE['oauth_redirect'])) {
                $redit_to = $_COOKIE['oauth_redirect'];
                setcookie('oauth_redirect', null, time() - 31536000);

                header("Location: $redit_to");
                exit(0);
            }

			break;
        }

		session_write_close();
	}
	
	public function __construct(){
		
		// 检查登入状态
		$this->_check_user();
	}
	
	function __destruct(){
	}
	
	public function __get($key){
		if (isset($this->_config[$key])) {
			return $this->_config[$key];	
		}
	
		return null;
	}
	
	public function __set($name , $value){
		$this->_config[$name] = $value;
	}

	public function getAuthTypes() {
		$channel_types = array();
		foreach($this->_oauth_config as $key => $val) {
			array_push($channel_types, $key);
		}

		return $channel_types;
	}

    public function getAuthUrl($channel_name) {
		$channel_config = null;
		if (!empty($this->_oauth_config[$channel_name])) {
			$channel_config = $this->_oauth_config[$channel_name];
		}

        if (empty($channel_config) || empty($channel_config['authorize'])) {
            return '';
        }

        return $channel_config['authorize'] . '?' . http_build_query(
            $channel_config['authorize_params']
        );
    }

	public function getWebRoot(){
		return dirname(__FILE__);
	}
	
	/**
	 * 获取工程名称
	 * @param string $proj_name 工程标题
	 * @return ProjectWebToolsFrame 自身
	 */
	public function getProjectName(){
		return $this->project['name'];
	}
	
	/**
	 * 获取工程标题
	 * @return string
	 */
	public function getProjectTitle(){
		return $this->project['title'];
	}
	
	/**
	 * 设置工程名称
	 * @param string $proj_title 工程标题
	 * @return ProjectWebToolsFrame 自身
	 */
	public function setProjectTitle($proj_title){
		$this->_config['project']['title'] = $proj_title;
		return $this;
	}
	
	/**
	 * 获取工程路径
	 * @return string
	 */
	public function getProjectPath(){
		return $this->getWebRoot() . DIRECTORY_SEPARATOR . 'jobs' . DIRECTORY_SEPARATOR . $this->project['name'];
	}
	
	/**
	 * 获取工程配置路径
	 * @return string
	 */
	public function getProjectConfigPath(){
		return $this->getProjectPath() . DIRECTORY_SEPARATOR . 'config.php';
	}
	
	/**
	 * 获取工程模板文件夹路径
	 * @return string
	 */
	public function getWebTemplateDir(){
		return $this->getWebRoot() . DIRECTORY_SEPARATOR . 'template';
	}
	
	/**
	 * 获取工程模板路径
	 * @return string
	 */
	public function getWebTemplatePath($temp_name){
		return $this->getWebTemplateDir() . DIRECTORY_SEPARATOR . $temp_name . '.php';
	}

    /**
     * 获取工程模板配置
     * @return string
     */
    public function getWebTemplateConf($temp_name){
        return isset($this->_config['template'][$temp_name])? $this->_config['template'][$temp_name]: null;
    }

    /**
     * 设置工程模板配置
     * @return string
     */
    public function setWebTemplateConf($temp_name, $temp_val){
        return $this->_config['template'][$temp_name] = $temp_val;
    }
	
	/**
	 * 载入工程文件
	 * @return boolean
	 */
	public function loadProjectFile($temp_name){
		$file_path = $this->getProjectPath() . DIRECTORY_SEPARATOR . $temp_name;
		if (file_exists($file_path)) {
			include($file_path);
			return true;
		}
		
		return false;
	}
	
	/**
	 * 载入工程配置
	 */
	public function loadProject($proj_name){
		$this->_config['project']['name'] = $proj_name;
		require_once ($this->getProjectPath() . DIRECTORY_SEPARATOR . 'config.php');
	}
	
	/**
	 * 获取用户信息
	 * @return unknown
	 */
	public function getUserInfo() {
		return $this->_user;
	}
	
	public function setNav($nodes) {
		foreach($nodes as $node){
			array_push($this->_nav_list, $node);
		}
	}
	
	public function getNav() {
		return $this->_nav_list;
	}
}

$service = new ProjectWebToolsFrame();
