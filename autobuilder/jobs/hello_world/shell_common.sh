#!/bin/sh

# 公共变量
WORKING_DIR=$PWD

# 获取当前脚本目录
function get_script_dir()
{
	echo "$( cd "$( dirname "$0" )" && pwd )";
}

# 检测当前shell编码
CURRENT_ENCODING="GB18030";
CURRENT_ENCODING_CHECK1="$(set | grep UTF-8 -i)";
CURRENT_ENCODING_CHECK2="$(set | grep UTF8 -i)";
if [ ! -z "$CURRENT_ENCODING_CHECK1" ] || [ ! -z "$CURRENT_ENCODING_CHECK2" ]; then
	CURRENT_ENCODING="UTF-8";
fi

# 设置本地语言
function set_local_lang()
{
	TARGET_LANG="zh_CN.UTF-8";
	if [ $# -gt 0 ]; then
		TARGET_LANG="$1";
	fi
	
	export LANG="$TARGET_LANG"
	export LC_CTYPE="$TARGET_LANG"
	export LC_NUMERIC="$TARGET_LANG"
	export LC_TIME="$TARGET_LANG"
	export LC_COLLATE="$TARGET_LANG"
	export LC_MONETARY="$TARGET_LANG"
	export LC_MESSAGES="$TARGET_LANG"
	export LC_PAPER="$TARGET_LANG"
	export LC_NAME="$TARGET_LANG"
	export LC_ADDRESS="$TARGET_LANG"
	export LC_TELEPHONE="$TARGET_LANG"
	export LC_MEASUREMENT="$TARGET_LANG"
	export LC_IDENTIFICATION="$TARGET_LANG"
	export LC_ALL="$TARGET_LANG"
	export RC_LANG="$TARGET_LANG"
	export RC_LC_CTYPE="$TARGET_LANG"
	export AUTO_DETECT_UTF8="yes"
	
	CURRENT_ENCODING="$(echo $TARGET_LANG | cut -d . -f2)"
}

# 保留指定个数的文件
function remove_more_than()
{
    filter="$1";
    number_left=$2
    
    FILE_LIST=( $(ls -dt $filter) );
    for (( i=$number_left; i<${#FILE_LIST[@]}; i++)); do
    	rm -rf "${FILE_LIST[$i]}";
    done
}

# 远程指令
function auto_scp()
{
    src="$1";
    dst="$2";
    pass="$3";
    port=""
    if [ $# -gt 3 ]; then
        port="-P $4";
    fi

	if [ -z "$AUTO_SCP_TIMEOUT" ]; then
		AUTO_SCP_TIMEOUT="-1";
	fi

    expect -c "set timeout $AUTO_SCP_TIMEOUT;
            spawn scp -p -o StrictHostKeyChecking=no -r $port $src $dst;
            expect \"*assword:*\" { send \"$pass\r\n\"; };
            expect eof {exit;};
            ";
}

function auto_scp_ident()
{
    src="$1";
    dst="$2";
    host_identity_file="$3";
    port=""
    if [ $# -gt 3 ]; then
        port="-P $4";
    fi

	if [ ! -z "$AUTO_SCP_TIMEOUT" ]; then
		AUTO_SCP_TIMEOUT="-o ConnectTimeout=$AUTO_SCP_TIMEOUT";
	fi

	scp -p -o StrictHostKeyChecking=no $AUTO_SCP_TIMEOUT -r -i "$host_identity_file" $port "$src" "$dst";
}

function auto_ssh_exec()
{
    host_ip="$1";
    host_port="$2";
    host_user="$3";
    host_pwd="$4";
	NOW_TIME=$(date +%s);
	cmd="if [ -e ~/.bash_profile ]; then source ~/.bash_profile; fi; $5";
    cmd="${cmd//\\/\\\\}";
	cmd="${cmd//-/\\-}";
	cmd="${cmd//[/\\[}";
	cmd="${cmd//]/\\]}";
    cmd="${cmd//\"/\\\"}";
    cmd="${cmd//\$/\\\$}";
	cmd="${cmd//\`/\\\`}";

	if [ -z "$AUTO_SSH_EXEC_TIMEOUT" ]; then
		AUTO_SSH_EXEC_TIMEOUT="-1";
	fi
    expect -c "set timeout $AUTO_SSH_EXEC_TIMEOUT;
            spawn ssh -o StrictHostKeyChecking=no ${host_user}@${host_ip} -p ${host_port} \"$cmd\";
            expect \"*assword:*\" { send \"$host_pwd\r\n\"; };
            expect eof {exit;};
            ";
}

function auto_ssh_exec_ident()
{
    host_ip="$1";
    host_port="$2";
    host_user="$3";
    host_identity_file="$4";

	if [ ! -z "$AUTO_SSH_EXEC_TIMEOUT" ]; then
		AUTO_SSH_EXEC_TIMEOUT="-o ConnectTimeout=$AUTO_SSH_EXEC_TIMEOUT";
	fi
	ssh -o StrictHostKeyChecking=no $AUTO_SSH_EXEC_TIMEOUT ${host_user}@${host_ip} -p $host_port -i "$host_identity_file" "if [ -e ~/.bash_profile\ ]; then source ~/.bash_profile; fi; $5";
}

# 清空Linux缓存
function free_useless_memory()
{
	CURRENT_USER_NAME=$(whoami)
	if [ "$CURRENT_USER_NAME" != "root" ]; then
		echo "Must run as root";
		exit -1;
	fi

	sync
	echo 3 > /proc/sys/vm/drop_caches
}

# 清空未被引用的用户共享内存
function remove_user_empty_ipc()
{
	CURRENT_USER_NAME=$(whoami)
	for i in $(ipcs | grep $CURRENT_USER_NAME | awk '{ if( $6 == 0 ) print $2}'); do
		ipcrm -m $i
		ipcrm -s $i
	done
}

# 清空用户共享内存
function remove_user_ipc()
{
	CURRENT_USER_NAME=$(whoami)
	for i in $(ipcs | grep $CURRENT_USER_NAME | awk '{print $2}'); do
		ipcrm -m $i
		ipcrm -s $i
	done
}

# 获取系统IPv4地址
function get_ipv4_address()
{
	ALL_IP_ADDRESS=($(ifconfig | grep 'inet addr' | awk '{print $2}' | cut -d: -f2));
	if [ $# -gt 0 ]; then
		if [ "$1" == "count" ] || [ "$1" == "number" ]; then
			echo ${#ALL_IP_ADDRESS[@]};
		else
			echo ${ALL_IP_ADDRESS[$1]};
		fi
		return;
	fi
	echo ${ALL_IP_ADDRESS[@]};
}

# 获取系统IPv6地址
function get_ipv6_address()
{
	ALL_IP_ADDRESS=($(ifconfig | grep 'inet6 addr' | awk '{print $3}' | cut -d/ -f1));
	if [ $# -gt 0 ]; then
		if [ "$1" == "count" ] || [ "$1" == "number" ]; then
			echo ${#ALL_IP_ADDRESS[@]};
		else
			echo ${ALL_IP_ADDRESS[$1]};
		fi
		return;
	fi
	echo ${ALL_IP_ADDRESS[@]};
}

function Message() {
	COLOR_CODE="$1";
	shift;
	
	# if [ -z "$WINDIR" ] || [ "$TERM" != "cygwin" ]; then
		echo -e "\\033[${COLOR_CODE}m$*\\033[39;49;0m";
	# else # Windows 下 cmake 直接调用cmd的，所以 Mingw 不支持着色
	# 	echo "$*";
	# fi
}

function AlertMsg() {
	Message "32;1" "-- Alert: $*";
}

function NoticeMsg() {
	Message "33;1" "-- Notice: $*";
}

function ErrorMsg() {
	Message "31;1" "-- Error: $*";
}

function WarningMsg() {
	Message "35;1" "-- Warning: $*";
}

function StatusMsg() {
	Message "36;1" "-- Status: $*";
}

function WaitProcessStarted() {
	if [ $# -lt 1 ]; then
		return;
	fi
	
	WAIT_TIME=20000;
	PROC_NAME="$1"
	
	if [ $# -gt 1 ]; then
		WAIT_TIME=$2;
	fi
	
	while [ -z "$(ps aux | grep "$PROC_NAME" | grep -v grep)" ]; do
		if [ $WAIT_TIME -gt 0 ]; then
			sleep 1;
			let WAIT_TIME=$WAIT_TIME-1000;
		else
			break;
		fi
	done
}

function WaitProcessStoped() {
	if [ $# -lt 1 ]; then
		return;
	fi
	
	WAIT_TIME=20000;
	PROC_NAME="$1"
	
	if [ $# -gt 1 ]; then
		WAIT_TIME=$2;
	fi
	
	while [ ! -z "$(ps aux | grep "$PROC_NAME" | grep -v grep)" ]; do
		if [ $WAIT_TIME -gt 0 ]; then
			sleep 1;
			let WAIT_TIME=$WAIT_TIME-1000;
		else
			break;
		fi
	done
}

function version_num2str() {
    NUM_VER=$1;
    STR_VER_D=$(($NUM_VER&0xFFFF));
    NUM_VER=$(($NUM_VER>>16));
    STR_VER_C=$(($NUM_VER&0xFF));
    NUM_VER=$(($NUM_VER>>8));
    STR_VER_B=$(($NUM_VER&0x0F));
    NUM_VER=$(($NUM_VER>>4));
    STR_VER_A=$(($NUM_VER&0x0F));
    
    echo "$STR_VER_A.$STR_VER_B.$STR_VER_C.$STR_VER_D";
}

# 公用SVN信息
GIT_PATH=[Git仓库地址]
GIT_USER=[编译机Git用户名]
SERVER_DIR=Server
CLIENT_DIR=Client
RESOURCE_DIR=Resource
PACKAGE_DIR=/home/website/htdocs/publish/hola_my_lord

# 公用编译机信息
COMPILE_ENV_IP=10.1.100.41
COMPILE_ENV_PORT=22
COMPILE_ENV_USER=[编译机用户名]
COMPILE_ENV_PWD=[编译机密码]
COMPILE_ENV_PROJECT_NAME=hola_my_lord
COMPILE_ENV_PATH=/home/autobuilder/hola_my_lord

# 公用发布机信息
PUBLISH_ENV_SERVER_DIR=hello_project
PUBLISH_ENV_SERVER_PKG=server
PUBLISH_ENV_SERVER_NUMBER=12

set_local_lang "zh_CN.UTF-8"

