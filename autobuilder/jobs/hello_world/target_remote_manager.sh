#!/bin/sh

WORKING_DIR=$PWD

source $WORKING_DIR/shell_common.sh

manager_ip="$AUTOBUILDER_ZONE_URL";
manager_port="$AUTOBUILDER_ZONE_PORT";
manager_user="$AUTOBUILDER_ZONE_USER";
manager_password="$AUTOBUILDER_ZONE_PASSWD";
manager_path="$AUTOBUILDER_ZONE_INSTALL_PATH";
manager_cmd="$AUTOBUILDER_PARAM_CMD_SHELL";

AUTOBUILDER_ENV_STR="$(set | grep AUTOBUILDER_ | grep -v AUTOBUILDER_PARAM_CMD_SHELL | tr '\n' ' ')";

if [ "$manager_user" == "null" ]; then
	echo "<strong style='color: Red;'>This function required a valid user.</strong>";
	exit 0;
fi

if [ ! -z "$AUTOBUILDER_ZONE_CUSTOM_OUTER_DOMAIN" ]; then
	AUTOBUILDER_ENV_STR="$AUTOBUILDER_ENV_STR SYSTEM_MACRO_HOSTNAME=$AUTOBUILDER_ZONE_CUSTOM_OUTER_DOMAIN";
fi

if [ ! -z "$AUTOBUILDER_ZONE_CUSTOM_OUTER_IPV6" ]; then
	AUTOBUILDER_ENV_STR="$AUTOBUILDER_ENV_STR SYSTEM_MACRO_OUTER_IPV6=$AUTOBUILDER_ZONE_CUSTOM_OUTER_IPV6";
fi

if [ ! -z "$AUTOBUILDER_ZONE_CUSTOM_OUTER_IPV4" ]; then
	AUTOBUILDER_ENV_STR="$AUTOBUILDER_ENV_STR SYSTEM_MACRO_OUTER_IPV4=$AUTOBUILDER_ZONE_CUSTOM_OUTER_IPV4";
fi

if [ ! -z "$AUTOBUILDER_ZONE_CUSTOM_INNER_IPV6" ]; then
	AUTOBUILDER_ENV_STR="$AUTOBUILDER_ENV_STR SYSTEM_MACRO_INNER_IPV6=$AUTOBUILDER_ZONE_CUSTOM_INNER_IPV6";
fi

if [ ! -z "$AUTOBUILDER_ZONE_CUSTOM_INNER_IPV4" ]; then
	AUTOBUILDER_ENV_STR="$AUTOBUILDER_ENV_STR SYSTEM_MACRO_INNER_IPV4=$AUTOBUILDER_ZONE_CUSTOM_INNER_IPV4";
fi

export AUTO_SSH_EXEC_TIMEOUT=1800;
auto_ssh_exec $manager_ip $manager_port $manager_user $manager_password "export $AUTOBUILDER_ENV_STR; export CPRINTF_MODE=html; export CPRINTF_THEME=dark; if [ -e '$manager_path/$PUBLISH_ENV_SERVER_DIR/tools/script' ]; then cd '$manager_path/$PUBLISH_ENV_SERVER_DIR/tools/script'; fi; $manager_cmd"
