#!/bin/sh

SCRIPT_DIR="$(dirname $0)";
if [ "$PWD" != "$SCRIPT_DIR" ]; then
	cd "$SCRIPT_DIR";
fi

source "$SCRIPT_DIR/shell_common.sh";

CMD="$AUTOBUILDER_PARAM_CMD";
AUTOBUILDER_ENV_STR="$(set | grep AUTOBUILDER_ | tr '\n' ' ')";

if [ "$CMD" == "update_script" ]; then
	shell_pkg_file=target_shell.tar.gz
	
	auto_ssh_exec $COMPILE_ENV_IP $COMPILE_ENV_PORT $COMPILE_ENV_USER $COMPILE_ENV_PWD "mkdir -p $COMPILE_ENV_PATH && cd $COMPILE_ENV_PATH ; rm -f $shell_pkg_file;"

	rm -f $shell_pkg_file;

	tar -zcvf $shell_pkg_file shell_common.sh target_*
	
	auto_scp $shell_pkg_file "$COMPILE_ENV_USER@$COMPILE_ENV_IP:$COMPILE_ENV_PATH" $COMPILE_ENV_PWD $COMPILE_ENV_PORT

	auto_ssh_exec $COMPILE_ENV_IP $COMPILE_ENV_PORT $COMPILE_ENV_USER $COMPILE_ENV_PWD "cd $COMPILE_ENV_PATH && rm -f *.sh && tar -zxvf $shell_pkg_file && sh target_init.sh"
	
	exit;
fi

if [ "$CMD" == "getpackages" ]; then
	auto_ssh_exec $COMPILE_ENV_IP $COMPILE_ENV_PORT $COMPILE_ENV_USER $COMPILE_ENV_PWD "cd $PACKAGE_DIR/$SERVER_DIR; echo 'VERSION('; ls; echo ')';"
	exit;
fi

auto_ssh_exec $COMPILE_ENV_IP $COMPILE_ENV_PORT $COMPILE_ENV_USER $COMPILE_ENV_PWD "cd $COMPILE_ENV_PATH && env $AUTOBUILDER_ENV_STR sh target_compile.sh"
