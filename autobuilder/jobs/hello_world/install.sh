#!/bin/sh

SCRIPT_DIR="$(dirname $0)";
if [ "$PWD" != "$SCRIPT_DIR" ]; then
	cd "$SCRIPT_DIR";
fi

source "$SCRIPT_DIR/shell_common.sh";

CMD="$AUTOBUILDER_PARAM_CMD";
AUTOBUILDER_ENV_STR="$(set | grep AUTOBUILDER_ | tr '\n' ' ')";

if [ "$CMD" == "publish" ]; then
	shift;
	auto_ssh_exec $COMPILE_ENV_IP $COMPILE_ENV_PORT $COMPILE_ENV_USER $COMPILE_ENV_PWD "cd $COMPILE_ENV_PATH && env $AUTOBUILDER_ENV_STR sh target_install.sh"
	exit;
fi

if [ "$CMD" == "manager" ]; then
	shift;
	auto_ssh_exec $COMPILE_ENV_IP $COMPILE_ENV_PORT $COMPILE_ENV_USER $COMPILE_ENV_PWD "cd $COMPILE_ENV_PATH && env $AUTOBUILDER_ENV_STR sh target_manager.sh"
	exit;
fi
