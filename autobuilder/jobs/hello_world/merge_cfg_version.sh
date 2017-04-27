#!/bin/sh

SCRIPT_DIR="$(dirname $0)";
if [ "$PWD" != "$SCRIPT_DIR" ]; then
	cd "$SCRIPT_DIR";
fi

source "$SCRIPT_DIR/shell_common.sh";

AUTOBUILDER_ENV_STR="$(set | grep AUTOBUILDER_ | tr '\n' ' ')";

auto_ssh_exec $COMPILE_ENV_IP $COMPILE_ENV_PORT $COMPILE_ENV_USER $COMPILE_ENV_PWD "cd $COMPILE_ENV_PATH && env $AUTOBUILDER_ENV_STR sh target_merge_cfg_version.sh"
