<?php 
set_time_limit(1800); // 30分钟

class AutoBuilder{
	private $_project_conf = array(
		'ext_modules' => array(),
		'anyone_cmds' => array('getaddrs') 	
	);
	private $_lock_file = null;
	private $_data = array();
	private $user_info = null;
	private $_conf = null;
	private $_custom_cmd = null;
	
	public function __construct(){
		$var_names = array('project', 'cmd', 'action');
		
		foreach ($var_names as $key) {
			if (isset($_REQUEST[$key]))
				$this->_data[$key] = $_REQUEST[$key];
			else 
				$this->_data[$key] = null;
		}
	}
	
	function __destruct(){
		if ($this->_lock_file !== null) {
			$this->releaseLock();
		}
	}
	
	public function __get($key){
		if (isset($this->_data[$key])) {
			return $this->_data[$key];
			
		} else if ($key == 'conf') {
			if ($this->_conf === null) {
				if (is_file($this->getConfFilePath()))
					$this->_conf = json_decode(file_get_contents($this->getConfFilePath()));
			}
			return $this->_conf;
			
		}  else if ($key == 'custom_cmd') {
			if ($this->_custom_cmd === null) {
				if (is_file($this->getCustomCmdFilePath()))
					$this->_custom_cmd = json_decode(file_get_contents($this->getCustomCmdFilePath()), true);
				else
					$this->_custom_cmd = json_decode('{}', true);
			}
			return $this->_custom_cmd;
			
		} else if ($key == 'user') {
			session_start();

			if ($this->user_info === null && !empty($_SESSION['user'])) {
				$this->user_info = $_SESSION['user'];
			}

			session_write_close();
			return $this->user_info;
		}
		
		return null;
	}
	
	public function __set($name , $value){
		if ($name == 'conf') {
			$this->_conf = $value;
			file_put_contents($this->getConfFilePath(), json_encode($value));
		} else if ($name == 'custom_cmd') {
			$this->_custom_cmd = $value;
			file_put_contents($this->getCustomCmdFilePath(), json_encode($value));
		} else if ($this->_data[$name])
			$this->_data[$name] = $value;
		else
			throw new Exception("$name is not valid member.");
	}
	
	/**
	 * 获取工程名称
	 * @return mixed
	 */
	public function getProjectName(){
		return $this->project;
	}
	
	/**
	 * 检查是否已初始化
	 * @return boolean
	 */
	public function isInited(){
		return is_dir($this->getProjectPath()) && is_file($this->getConfFilePath());
	}
	
	/**
	 * 获取工程路径
	 * @return string
	 */
	public function getProjectPath(){
		return dirname(__FILE__) . DIRECTORY_SEPARATOR . 'jobs' . DIRECTORY_SEPARATOR . $this->project;
	}
	
	/**
	 * 获取配置文件路径
	 * @return string
	 */
	public function getConfFilePath(){
		return $this->getProjectPath() . DIRECTORY_SEPARATOR . 'conf.json';
	}
	
	/**
	 * 获取锁文件路径
	 * @return string
	 */
	public function getLockFilePath(){
		return $this->getProjectPath() . DIRECTORY_SEPARATOR . '.LOCK';
	}
	
	/**
	 * 获取自定义指令文件路径
	 * @return string
	 */
	public function getCustomCmdFilePath(){
		return $this->getProjectPath() . DIRECTORY_SEPARATOR . 'cmd.custom.json';
	}
	
	/**
	 * 尝试获取文件锁
	 * @return boolean
	 */
	public function getLock() {
		$lock = $this->getLockFilePath();
		$this->_lock_file = fopen($lock, 'w');
		if (flock($this->_lock_file, LOCK_EX|LOCK_NB)) {
			return true;
		}
		
		fclose($this->_lock_file);
		$this->_lock_file = null;

		return false;
	}
	
	/**
	 * 释放文件锁
	 */
	public function releaseLock() {
		if ($this->_lock_file === null)
			return;
		
		flock($this->_lock_file, LOCK_UN);
		fclose($this->_lock_file);
		
		$this->_lock_file = null;
	}
	
	/**
	 * 检查权限
	 * @return boolean
	 */
	public function checkPermission(){
		return in_array($this->user['login_name'], explode(';', $this->conf->permission));
	}
	
	/**
	 * 获取操作日志文件路径
	 * @param string $suffix 日志文件名后缀
	 * @return string
	 */
	public function getOperationLogFilePath($suffix = null){
		$dir_path = $this->getProjectPath() . DIRECTORY_SEPARATOR . 'log';
		if (false == file_exists($dir_path)) {
			mkdir($dir_path, 0777, true);
		}
		
		if (empty($suffix))
			$file_name = 'operation.' . date('Y-m') . '.log';
		else
			$file_name = 'operation.' . $suffix . '.log';
		
		return $dir_path . DIRECTORY_SEPARATOR . $file_name;
	}
	
	/**
	 * 获取操作信息文件路径
	 * @return string
	 */
	public function getOperationInfoFilePath(){
		return $this->getProjectPath() . DIRECTORY_SEPARATOR . 'operation.info';
	}
	
	/**
	 * 获取发布目标配置文件路径
	 * @return string
	 */
	public function getPublishCfgFilePath(){
		return $this->getProjectPath() . DIRECTORY_SEPARATOR . 'publish_addrs.xml';
	}
	

	/**
	 * 获取字段数据并拼接为字符串
	 * @param string $str_xpath 数据源节点xpath
	 * @param array $seg_array 数据字段集合
	 * @param integer $limits 选取节点个数
	 * @return string
	 */
	public function getPublishCfgParamString($str_xpath, $seg_array, $limits = 1){
		$doc = new DOMDocument();
		$doc->load($this->getPublishCfgFilePath());
		$xpath = new DOMXPath($doc);
		$eles = $xpath->query($str_xpath);
		
		$ret = '';
		for ($i = 0; $i < $eles->length; ++ $i) {
			if ($limits <= 0)
				break;
			
			$ele = $eles->item($i);
			$conf_params = '';
			
			foreach($seg_array as $seg_name) {
				$entries = $xpath->query($seg_name, $ele);
				if ($entries->length > 0) {
					$conf_params .= ' ' . $entries->item(0)->nodeValue;
				} else {
					$conf_params = '';
					break;
				}
			}
			
			$ret .= $conf_params;
			
			-- $limits;
		}
		
		return $ret;
	}
	
	/**
	 * 
	 * @param DOMNode $ele 当前节点
	 * @param string $prefix 当前前缀
	 */
	public function getPublishCfgXmlNodeEnv($ele, $prefix){
		if(XML_ELEMENT_NODE == $ele->nodeType) {
			$prefix .= '_' . $ele->nodeName;
			for($i = 0; $i < $ele->childNodes->length; ++ $i){
				$this->getPublishCfgXmlNodeEnv($ele->childNodes->item($i), $prefix);
			}
			return;
		}
		
		if (XML_TEXT_NODE == $ele->nodeType && "" != trim($ele->nodeValue)) {
			$prefix = strtoupper($prefix);
			putenv("$prefix=" . $ele->nodeValue);
		}
	}
	
	/**
	 * 获取字段数据并转换为环境变量(只会取第一个符合条件的节点)
	 * @param string $str_xpath 数据源节点xpath
	 * @param array $seg_array 数据字段集合
	 * @param integer $prefix 前缀
	 */
	public function getPublishCfgParamEnv($str_xpath, $seg_array, $prefix='AUTOBUILDER'){
		$doc = new DOMDocument();
		$doc->load($this->getPublishCfgFilePath());
		$xpath = new DOMXPath($doc);
		$eles = $xpath->query($str_xpath);
	
		if ($eles->length <= 0) {
			return;
		}
		
		$ele = $eles->item(0);
		
		for($i = 0; $i < $ele->childNodes->length; ++ $i){
			$ele_child = $ele->childNodes->item($i);
			if (XML_ELEMENT_NODE != $ele_child->nodeType || false == in_array($ele_child->nodeName, $seg_array)) {
				continue;
			}
			$this->getPublishCfgXmlNodeEnv($ele_child, $prefix . '_' . $ele->nodeName);
		}
	}
	
	/**
	 * 载入项目拓展页，拓展页必须位于 [项目目录]/ext/模块名.php
	 * @param string $module 拓展模块名
	 * @param string $pre 前缀
	 * @param string $suf 后缀
	 * @return boolean 如果拓展内容存在，返回true，否则返回false
	 */
	public function loadProjectExtModule($module, $pre = '', $suf = ''){
		if (isset($this->_project_conf['ext_modules']) && !empty($this->_project_conf['ext_modules'][$module])) {
			echo $pre;
			include $this->getProjectPath() . DIRECTORY_SEPARATOR . $this->_project_conf['ext_modules'][$module];
			echo $suf;
			return true;
		}
		
		return false;
	}
	
	/**
	 * 检查非登入权限
	 * @param string $name 指令/页面/模块名称
	 * @param string $mode 名称类型(cmd: 指令)
	 * @return boolean 允许匿名查看，返回true，否则返回false
	 */
	public function isAccessFree($name, $mode = 'cmd') {
		if ($mode == 'cmd') {
			return isset($this->_project_conf['anyone_cmds']) && in_array($name, $this->_project_conf['anyone_cmds']);
		}
		
		return false;
	}
	
	/**
	 * 覆盖项目配置
	 * @param array $arr
	 */
	public function loadProjectConf($arr){
		$this->_project_conf = array_merge_recursive($this->_project_conf, $arr);
	}
	
	/**
	 * 记录日志
	 * @param string $opr 操作名称
	 * @param string $res 操作结果
	 */
	public function log($opr, $res){
		$time = time();
		$log_path = $this->getOperationLogFilePath();
		file_put_contents($log_path, "{$this->user['login_name']}|{$time}|$opr|$res\n", FILE_APPEND);
	}
}

$auto_builder = new AutoBuilder();
if (file_exists($auto_builder->getProjectPath() . DIRECTORY_SEPARATOR . 'config.php')) {
	include $auto_builder->getProjectPath() . DIRECTORY_SEPARATOR . 'config.php';
}

