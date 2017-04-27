#!/bin/sh

WORKING_DIR=$PWD

source $WORKING_DIR/shell_common.sh

install_ver="$AUTOBUILDER_PARAM_VER";
install_verno="$AUTOBUILDER_PARAM_VERNO";
install_target="$AUTOBUILDER_PARAM_CMD_OPTION";

SERVER_INSTALLER_DIR="$COMPILE_ENV_PATH/$SERVER_DIR/$install_ver/installer";

if [ -f "$PACKAGE_DIR/$SERVER_DIR/$install_ver.$install_verno/$PUBLISH_ENV_SERVER_PKG.7z" ]; then
    file_name=$PUBLISH_ENV_SERVER_PKG.7z
elif [ -f "$PACKAGE_DIR/$SERVER_DIR/$install_ver.$install_verno/$PUBLISH_ENV_SERVER_PKG.tar.xz" ]; then
    file_name=$PUBLISH_ENV_SERVER_PKG.tar.xz
else
    file_name=$PUBLISH_ENV_SERVER_PKG.tar.gz
fi

function remote_publish() {
    # ==================== 转到发布逻辑====================
    if [ -z "$AUTOBUILDER_ZONE_CUSTOM_PUBLISHURL" ]; then

        # ==================== 直接发布====================
        sh $WORKING_DIR/target_remote_install.sh

    else
        AUTOBUILDER_ENV_STR="-F \"$(set | grep AUTOBUILDER_ | sed ':label;N;s/\n/\" -F \"/;b label')\"";
        # ==================== 远程发布====================

        echo "curl $AUTOBUILDER_ENV_STR -F \"shell=@target_shell.tar.gz\" -F \"package=@$AUTOBUILDER_REMOTE_SERVER_SRC_FILE\" \"$AUTOBUILDER_ZONE_CUSTOM_PUBLISHURL\""
        echo "$AUTOBUILDER_ENV_STR" | xargs curl -S -F "shell=@target_shell.tar.gz" -F "package=@$AUTOBUILDER_REMOTE_SERVER_SRC_FILE" "$AUTOBUILDER_ZONE_CUSTOM_PUBLISHURL"
    fi
}

# 服务器
function publish_server() {
    if [ -e "$PACKAGE_DIR/$SERVER_DIR/$install_ver.$install_verno/$file_name" ]; then
        export AUTOBUILDER_REMOTE_SERVER_SRC_FILE="$PACKAGE_DIR/$SERVER_DIR/$install_ver.$install_verno/$file_name";
    else
        export AUTOBUILDER_REMOTE_SERVER_SRC_FILE="$PACKAGE_DIR/$SERVER_DIR/$install_ver.$install_verno/$file_name";
    fi

    export AUTOBUILDER_REMOTE_SERVER_FILE_NAME="$file_name";
    remote_publish;
}

# 服务器资源
function publish_server_resource() {

    # 配置资源包路径
    export AUTOBUILDER_REMOTE_SERVER_SRC_FILE="$WORKING_DIR/server.$install_ver.resource.tar.bz2";
    export AUTOBUILDER_REMOTE_SERVER_FILE_NAME="server.$install_ver.resource.tar.bz2";

    # 资源打包
    if [ -f "$AUTOBUILDER_REMOTE_SERVER_SRC_FILE" ]; then
        rm -f "$AUTOBUILDER_REMOTE_SERVER_SRC_FILE";
    fi
    RESOURCE_BAS_DIR="$(p4 info | grep 'Client root:' | sed 's/Client\s*root:\s*//i')";
    RESOURCE_BIN_DIR="$RESOURCE_BAS_DIR/Release/$install_ver/server/resource";
    echo "$(cd "$RESOURCE_BIN_DIR" && tar -jcvf "$AUTOBUILDER_REMOTE_SERVER_SRC_FILE" *.bin)";

    remote_publish;
}

# 客户端版本
function publish_client_version() {
    # 配置资源包路径
    export AUTOBUILDER_REMOTE_SERVER_FILE_NAME="server.$install_ver.cli_ver.tar.gz";
    export AUTOBUILDER_REMOTE_SERVER_SRC_FILE="$WORKING_DIR/$AUTOBUILDER_REMOTE_SERVER_FILE_NAME";

    # 资源打包
    if [ -f "$AUTOBUILDER_REMOTE_SERVER_SRC_FILE" ]; then
        rm -f "$AUTOBUILDER_REMOTE_SERVER_SRC_FILE";
    fi

    echo "$(cd "$COMPILE_ENV_PATH/$SERVER_DIR/installer/loginsvr/cfg" && tar -zcvf "$AUTOBUILDER_REMOTE_SERVER_SRC_FILE" *.xml)";

    remote_publish;
}

# ==================== 枚举发布类型 ====================
for target in $install_target; do
    export AUTOBUILDER_PARAM_CMD_OPTION=$target;

    # ==================== 发布分支 ====================
    if [ "$target" == "server" ]; then
        publish_server;
    elif [ "$target" == "server_resource" ]; then
        publish_server_resource;
    elif [ "$target" == "client_version" ]; then
        publish_client_version;
    fi
done;
