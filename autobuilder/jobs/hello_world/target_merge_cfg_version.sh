#!/bin/sh

WORKING_DIR=$PWD

source $WORKING_DIR/shell_common.sh

CMD="$AUTOBUILDER_PARAM_CMD";
SERVER_VERSION="$AUTOBUILDER_PARAM_EXEC_VER";
CLIENT_CHANNEL="$AUTOBUILDER_PARAM_EXEC_CLI_CHANNEL";
CLIENT_SRC_VERSION="$AUTOBUILDER_PARAM_EXEC_CLI_SRC_VER";
CLIENT_DST_VERSION="$AUTOBUILDER_PARAM_EXEC_CLI_DST_VER";
CLIENT_UPDATETYPE="$AUTOBUILDER_PARAM_EXEC_CLI_UPDATETYPE";

# 拉取resource仓库
cd "$COMPILE_ENV_PATH/$RESOURCE_DIR";
git reset --hard;
git clean -d -f;
git checkout $SERVER_VERSION;
git pull;

# 拉取客户端仓库
cd "$COMPILE_ENV_PATH/$CLIENT_DIR";
git reset --hard;
git clean -d -f;
git checkout $SERVER_VERSION;
git pull;

# 调用客户端
cd Tools;

PACKAGE_DIR=$(cd "$PACKAGE_DIR" && cd ../zjtx && pwd );

if [ ! -e "$PACKAGE_DIR/$CLIENT_DIR/$CLIENT_DST_VERSION/update" ]; then
    mkdir "$PACKAGE_DIR/$CLIENT_DIR/$CLIENT_DST_VERSION/update";
    if [ 0 -ne $? ]; then
        echo '<span style="color: Red;">创建更新资源目录失败，请确保版本信息正确.</span>';
        exit 1;
    fi
fi

python auto_patch_ex.py -s "$PACKAGE_DIR/$CLIENT_DIR/$CLIENT_SRC_VERSION" -d "$PACKAGE_DIR/$CLIENT_DIR/$CLIENT_DST_VERSION" -o "$PACKAGE_DIR/$CLIENT_DIR/$CLIENT_DST_VERSION/update" ;
chmod 777 -R "$PACKAGE_DIR/$CLIENT_DIR/$CLIENT_DST_VERSION/update";

if [ 0 -ne $? ]; then
    echo "<strong>python auto_patch.py -s "$PACKAGE_DIR/$CLIENT_DIR/$CLIENT_SRC_VERSION" -d "$PACKAGE_DIR/$CLIENT_DIR/$CLIENT_DST_VERSION" -v "$PACKAGE_DIR/$CLIENT_DIR/$CLIENT_DST_VERSION/update"</strong><br />";
    echo '<span style="color: Red;">生成客户端差分包失败.</span>';
    exit 1;
fi

# 设置更新路径
for manifest_file in $(cd "$PACKAGE_DIR/$CLIENT_DIR/$CLIENT_DST_VERSION/update" && ls */*.update.manifest); do
    cur_dir_name=$(dirname "$manifest_file");
    
    # 设置更新目录
    python "$COMPILE_ENV_PATH/$RESOURCE_DIR/tools/version_ctl/verctl.py" -u "publish/moyo-no.1/client/$CLIENT_DST_VERSION/update/$cur_dir_name" "$PACKAGE_DIR/$CLIENT_DIR/$CLIENT_DST_VERSION/update/$manifest_file";
    
    # 获取客户端版本信息
    CLIENT_MANIFEST_NUMVER=$(python "$COMPILE_ENV_PATH/$RESOURCE_DIR/tools/version_ctl/verctl.py" -n "$PACKAGE_DIR/$CLIENT_DIR/$CLIENT_DST_VERSION/update/$manifest_file");
done

# 拉取服务器仓库
cd "$COMPILE_ENV_PATH/$SERVER_DIR";
git reset --hard;
git clean -d -f;
git checkout $SERVER_VERSION;
git pull;

# 调用服务器版本信息生成

APK_FILES=($(cd "$PACKAGE_DIR/$CLIENT_DIR/$CLIENT_DST_VERSION" && ls *.apk));
IPA_FILES=($(cd "$PACKAGE_DIR/$CLIENT_DIR/$CLIENT_DST_VERSION" && ls *.ipa));

if [ -z "${APK_FILES[0]}" ]; then
    echo '<span style="color: Red;">apk包找不到，生成客户端版本信息失败.</span>';
    exit 2;
fi
APK_FILES="${APK_FILES[0]}";

if [ -z "${IPA_FILES[0]}" ]; then
    #echo '<span style="color: Red;">ipa包找不到，生成客户端版本信息失败.</span>';
    #exit 2;
    echo '<span style="color: Yellow;">ipa包找不到，跳过ipa版本差分包生成.</span>';
    IPA_FILES="";
fi
IPA_FILES="${IPA_FILES[0]}";

echo "python \"$COMPILE_ENV_PATH/$SERVER_DIR/installer/loginsvr/cfg/merge_cfg_version.py\" -c $CLIENT_CHANNEL -f \"$COMPILE_ENV_PATH/$SERVER_DIR/installer/loginsvr/cfg/cfg_version.xml\" -n $CLIENT_MANIFEST_NUMVER -u $CLIENT_UPDATETYPE -d \"$CLIENT_DST_VERSION/update\" -p \"$CLIENT_DST_VERSION/$IPA_FILES\" -p \"$CLIENT_DST_VERSION/$APK_FILES\" -m \"project.update.manifest\"";

python "$COMPILE_ENV_PATH/$SERVER_DIR/installer/loginsvr/cfg/merge_cfg_version.py" -c $CLIENT_CHANNEL -f "$COMPILE_ENV_PATH/$SERVER_DIR/installer/loginsvr/cfg/cfg_version.xml" -n $CLIENT_MANIFEST_NUMVER -u $CLIENT_UPDATETYPE -d "$CLIENT_DST_VERSION/update" -p "$CLIENT_DST_VERSION/$IPA_FILES" -p "$CLIENT_DST_VERSION/$APK_FILES" -m "project.update.manifest";

if [ 0 -ne $? ]; then
    echo '<span style="color: Red;">生成更新信息失败。</span>';
    exit 1;
fi

git add installer/loginsvr/cfg/cfg_version.xml;
git commit -m "[AUTO PUBLISH] add client update info to loginsvr channel=$CLIENT_CHANNEL, version=$CLIENT_MANIFEST_NUMVER, full version=$CLIENT_DST_VERSION, update type=$CLIENT_UPDATETYPE";
git push;

if [ 0 -ne $? ]; then
    echo '<span style="color: Red;">推送新客户端版本信息到服务器失败，请重试.</span>';
    exit 1;
fi

echo "生成客户端版本信息成功.(from $CLIENT_SRC_VERSION to $CLIENT_DST_VERSION)";
