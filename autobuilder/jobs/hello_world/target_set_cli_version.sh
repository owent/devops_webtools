#!/bin/sh

WORKING_DIR=$PWD

source $WORKING_DIR/shell_common.sh

CMD="$AUTOBUILDER_PARAM_CMD";
SERVER_VERSION="$AUTOBUILDER_PARAM_EXEC_VER";
CLIENT_PUB_VERSION="$AUTOBUILDER_PARAM_EXEC_CLI_PUB_VER";

# 拉取resource仓库
cd "$COMPILE_ENV_PATH/$RESOURCE_DIR";
git reset --hard;
git clean -d -f;
git checkout $SERVER_VERSION;
git pull;

# 拉取客户端仓库
cd "$COMPILE_ENV_PATH/$CLIENT_DIR";
git reset --hard;
git clean -fdx;
git checkout $SERVER_VERSION;
git pull;

# 更新版本号
python "$COMPILE_ENV_PATH/$RESOURCE_DIR/tools/version_ctl/verctl.py" -s $CLIENT_PUB_VERSION "$COMPILE_ENV_PATH/$CLIENT_DIR/Resources/manifests/project.update.manifest";

if [ 0 -ne $? ]; then
    echo "<strong>python "$COMPILE_ENV_PATH/$RESOURCE_DIR/tools/version_ctl/verctl.py" -s $CLIENT_PUB_VERSION "$COMPILE_ENV_PATH/$CLIENT_DIR/Resources/manifests/project.update.manifest"</strong><br />";
    echo '<span style="color: Red;">设置客户端发布版本号失败.</span>';
    exit 1;
fi

CLIENT_FULL_VERSION=$(python "$COMPILE_ENV_PATH/$RESOURCE_DIR/tools/version_ctl/verctl.py" -t -b "$COMPILE_ENV_PATH/$CLIENT_DIR/Resources/manifests/project.update.manifest");
python "$COMPILE_ENV_PATH/$CLIENT_DIR/proj.android/add_android_version_code.py" -s $CLIENT_PUB_VERSION;

git add Resources/manifests/project.update.manifest proj.android/AndroidManifest.xml;
git commit -m "[AUTO PUBLISH] set client publish version=$CLIENT_PUB_VERSION, now full version=$CLIENT_FULL_VERSION";
git push;

if [ 0 -ne $? ]; then
    echo '<span style="color: Red;">推送新客户端版本号失败，请重试.</span>';
    exit 1;
fi

echo "设置客户端发布版本号（$CLIENT_PUB_VERSION）成功.(full version=$CLIENT_FULL_VERSION)";
