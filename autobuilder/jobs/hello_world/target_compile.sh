#!/bin/sh

WORKING_DIR=$PWD

source $WORKING_DIR/shell_common.sh

CMD="$AUTOBUILDER_PARAM_CMD";
SERVER_VERSION="$AUTOBUILDER_PARAM_VER"

if [ $CMD == "getversions" ]; then
	cd "$COMPILE_ENV_PATH/$SERVER_DIR";
	echo "repo dir list begin";
	for FULL_BRANCH_NAME in $(git branch -r | grep -v HEAD); do echo "${FULL_BRANCH_NAME#*/}"; done
	echo "repo dir list end";

	cd "$COMPILE_ENV_PATH/$RESOURCE_DIR";
    echo "repo dir list begin";
    for FULL_BRANCH_NAME in $(git branch -r | grep -v HEAD); do echo "${FULL_BRANCH_NAME#*/}"; done
    echo "repo dir list end";
    
	exit;
fi

if [ $CMD == "compile" ]; then
    BUILD_DIR=build;
    export CPRINTF_MODE=html;
    export CPRINTF_THEME=dark;

    # 更新资源和源码
    cd "$COMPILE_ENV_PATH/$RESOURCE_DIR";
    git reset --hard;
    git clean -fdx;
    git checkout $SERVER_VERSION;
    git pull;

    cd "$COMPILE_ENV_PATH/$SERVER_DIR";
    git reset --hard;
    git clean -fd;
    git checkout $SERVER_VERSION;
    git pull;
    # 更新子模块
    git submodule update --init --recursive --merge;
    
	# 生成版本号
	VERSION_NO=$(date "+%y%m%d%H%M");
    LASTBUILD_NAME="latest.nightly";
	PUBLISH_DIR="$PACKAGE_DIR/$SERVER_DIR/$SERVER_VERSION.$VERSION_NO";
	mkdir -p "$PUBLISH_DIR";

    # 文件名
	SERVER_ZIP_FILE_NAME=$PUBLISH_ENV_SERVER_PKG.7z;
	
	# 如果要编译的包存在则跳过
	if [ ! -f "$PUBLISH_DIR/SERVER_ZIP_FILE_NAME" ]; then
		# 启动编译服务器
		mkdir -p "$COMPILE_ENV_PATH/$SERVER_DIR/$BUILD_DIR";
		cd "$COMPILE_ENV_PATH/$SERVER_DIR/$BUILD_DIR";
		rm -rf *;
		BUILD_TYPE="Debug";
		if [ ! -z "$AUTOBUILDER_PARAM_CMD_OPTION" ]; then
		    BUILD_TYPE="$AUTOBUILDER_PARAM_CMD_OPTION";
		fi
        CCACHE="$(which ccache)";
        CMAKE_CCACHE_LANCHER="";
        if [ ! -z "$CCACHE" ] && [ -e "$CCACHE" ]; then
            CMAKE_CCACHE_LANCHER="-DCMAKE_C_COMPILER_LAUNCHER=$CCACHE -DCMAKE_CXX_COMPILER_LAUNCHER=$CCACHE"
        else
            CMAKE_CCACHE_LANCHER="";
        fi
		cmake .. -DCMAKE_BUILD_TYPE=$BUILD_TYPE $CMAKE_CCACHE_LANCHER && make all excel -j4;
		
		if [ $? -ne 0 ]; then
		    exit;
		fi

        # 打包备份
        if [ -e "md5.txt" ]; then
            rm -f md5.txt;
        fi
        echo "$(find $PUBLISH_ENV_SERVER_DIR -xtype f -name '*')" | while read file; do
            echo "generate md5sum for $file";
            md5sum -b "$file" >> md5.txt;
        done
        7z a -r -y $SERVER_ZIP_FILE_NAME md5.txt $PUBLISH_ENV_SERVER_DIR;
        mv -f $SERVER_ZIP_FILE_NAME "$PUBLISH_DIR/$SERVER_ZIP_FILE_NAME";
        rm -f md5.txt;

        if [ -e "$PACKAGE_DIR/$SERVER_DIR/$LASTBUILD_NAME" ]; then
            rm "$PACKAGE_DIR/$SERVER_DIR/$LASTBUILD_NAME";
        fi

        ln -s "$PUBLISH_DIR" "$PACKAGE_DIR/$SERVER_DIR/$LASTBUILD_NAME";
        chmod 777 -R "$PACKAGE_DIR/$SERVER_DIR/$LASTBUILD_NAME" "$PUBLISH_DIR";
	fi
	
	# 仅保留200个编译结果包
	remove_more_than "$PACKAGE_DIR/$SERVER_DIR/*" 200;
	echo "server compile version no => $VERSION_NO";
	exit;
fi
