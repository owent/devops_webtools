#!/bin/sh

WORKING_DIR=$PWD

source $WORKING_DIR/shell_common.sh

install_ver="$AUTOBUILDER_PARAM_VER";
install_verno="$AUTOBUILDER_PARAM_VERNO";
install_ip="$AUTOBUILDER_ZONE_URL";
install_port="$AUTOBUILDER_ZONE_PORT";
install_user="$AUTOBUILDER_ZONE_USER";
install_password="$AUTOBUILDER_ZONE_PASSWD";
install_path="$AUTOBUILDER_ZONE_INSTALL_PATH";
install_use_ip_index="$AUTOBUILDER_ZONE_USE_IP";
install_zone="$AUTOBUILDER_ZONE_ZONE_ID";
install_target="$AUTOBUILDER_PARAM_CMD_OPTION";

AUTOBUILDER_ENV_STR="$(set | grep AUTOBUILDER_ | tr '\n' ' ')";

# ==================== 发布服务器 ====================
function publish_server() {
	cd "$WORKING_DIR"

	# 如果用户名为null则跳过部署
	if [ "$install_user" == "null" ]; then
		echo "<strong style='color: Red;'>This function required a valid user.</strong>";
		return 0;
	fi

    file_src="$AUTOBUILDER_REMOTE_SERVER_SRC_FILE";
	file_name="$AUTOBUILDER_REMOTE_SERVER_FILE_NAME";

	unzip_opt="x -y";

	if [ ! -f "$file_src" ]; then
		echo "<span style='color: Red;'><strong>package [$file_src] not found.</strong></span>";
		exit;
	fi

    TMP_DIR="$(date '+%Y%m%d%H%M%S')";
	auto_ssh_exec $install_ip $install_port $install_user $install_password "mkdir -p $install_path/$TMP_DIR";
	auto_scp "$file_src"  "$install_user@$install_ip:$install_path/$TMP_DIR/$file_name" "$install_password";
	auto_ssh_exec $install_ip $install_port $install_user $install_password "if [ -e '$install_path/$PUBLISH_ENV_SERVER_DIR' ]; then cd $install_path/$PUBLISH_ENV_SERVER_DIR/tools/script; env $AUTOBUILDER_ENV_STR CPRINTF_MODE=html CPRINTF_THEME=dark sh stop_all.sh; cd $install_path; fi; cd $install_path/$TMP_DIR && 7z $unzip_opt $file_name; ln -sf $install_path/$TMP_DIR/$PUBLISH_ENV_SERVER_DIR $install_path/$PUBLISH_ENV_SERVER_DIR;";
    auto_ssh_exec $install_ip $install_port $install_user $install_password "cd $install_path; source $install_path/$PUBLISH_ENV_SERVER_DIR/tools/script/common/common.sh; remove_more_than * $PUBLISH_ENV_SERVER_NUMBER ;";
}

# ==================== 发布客户端 ====================

# ==================== 发布服务器资源  ====================
function publish_server_resource() {
    cd "$WORKING_DIR"

    # 如果用户名为null则跳过部署
    if [ "$install_user" == "null" ]; then
        echo "<strong style='color: Red;'>This function required a valid user.</strong>";
        return 0;
    fi

    file_src="$AUTOBUILDER_REMOTE_SERVER_SRC_FILE";
    file_name="$AUTOBUILDER_REMOTE_SERVER_FILE_NAME";

    unzip_opt="x -y";

    if [ ! -f "$file_src" ]; then
        echo "<span style='color: Red;'><strong>package [$file_src] not found.</strong></span>";
        exit;
    fi

    auto_ssh_exec $install_ip $install_port $install_user $install_password "mkdir -p $install_path && cd $install_path && rm -rf $AUTOBUILDER_REMOTE_SERVER_FILE_NAME;";
    auto_scp "$file_src"  "$install_user@$install_ip:$install_path/$file_name" "$install_password";
    auto_ssh_exec $install_ip $install_port $install_user $install_password "cd $install_path/$PUBLISH_ENV_SERVER_DIR/loginsvr/cfg && chmod +w * && 7z $unzip_opt $install_path/$file_name";
}

# 客户端版本
function publish_client_version() {
    cd "$WORKING_DIR"

    # 如果用户名为null则跳过部署
    if [ "$install_user" == "null" ]; then
        echo "<strong style='color: Red;'>This function required a valid user.</strong>";
        return 0;
    fi

    file_src="$AUTOBUILDER_REMOTE_SERVER_SRC_FILE";
    file_name="$AUTOBUILDER_REMOTE_SERVER_FILE_NAME";

    unzip_opt="x -y";

    if [ ! -f "$file_src" ]; then
        echo "<span style='color: Red;'><strong>package [$file_src] not found.</strong></span>";
        exit;
    fi

    auto_ssh_exec $install_ip $install_port $install_user $install_password "mkdir -p $install_path && cd $install_path && rm -rf $AUTOBUILDER_REMOTE_SERVER_FILE_NAME;";
    auto_scp "$file_src"  "$install_user@$install_ip:$install_path/$file_name" "$install_password";
    auto_ssh_exec $install_ip $install_port $install_user $install_password "cd $install_path/$PUBLISH_ENV_SERVER_DIR/loginsvr/cfg && chmod +w * && 7z $unzip_opt $install_path/$file_name";
}

for target in $install_target; do
    if [ "$target" == "server" ]; then
		publish_server;
	elif [ "$target" == "server_resource" ]; then
	    publish_server_resource;
	elif [ "$target" == "client_version" ]; then
        publish_client_version;
    fi
done;
