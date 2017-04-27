#!/bin/sh

WORKING_DIR=$PWD

source $WORKING_DIR/shell_common.sh

function init_repo_dir()
{
    if [ ! -d "$COMPILE_ENV_PATH/$SERVER_DIR" ] || [ ! -d "$COMPILE_ENV_PATH/$SERVER_DIR/.git" ]; then
        cd "$COMPILE_ENV_PATH";
		echo "Run: git clone \"$GIT_PATH/$SERVER_DIR.git\" \"$COMPILE_ENV_PATH/$SERVER_DIR\" @ $PWD";
	    git clone "$GIT_PATH/$SERVER_DIR.git" "$COMPILE_ENV_PATH/$SERVER_DIR";
	    cd "$COMPILE_ENV_PATH/$SERVER_DIR";
		echo "Run: git submodule update -f -r --init @ $PWD";
	    git submodule update -f -r --init ;

        cd "$COMPILE_ENV_PATH";
		echo "git clone \"$GIT_PATH/$RESOURCE_DIR.git\" \"$COMPILE_ENV_PATH/$RESOURCE_DIR\"; @ $PWD";
	    git clone "$GIT_PATH/$RESOURCE_DIR.git" "$COMPILE_ENV_PATH/$RESOURCE_DIR";
	else
	    cd "$COMPILE_ENV_PATH/$RESOURCE_DIR";
	    git reset --hard;
	    git clean -dfx;
	    git pull;

	    cd "$COMPILE_ENV_PATH/$SERVER_DIR";
		git submodule foreach "git reset --hard && git clean -df";
        git reset --hard;
        git clean -d -f;
        git pull;
		git submodule update -f -r;
	fi
}

init_repo_dir $target
