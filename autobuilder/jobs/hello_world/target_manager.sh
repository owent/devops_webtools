#!/bin/sh

WORKING_DIR=$PWD

source $WORKING_DIR/shell_common.sh

if [ -z "$AUTOBUILDER_ZONE_CUSTOM_PUBLISHURL" ]; then

    # ==================== 直接执行====================
    sh $WORKING_DIR/target_remote_manager.sh
    
else
    AUTOBUILDER_ENV_STR="-F \"$(set | grep AUTOBUILDER_ | sed ':label;N;s/\n/\" -F \"/;b label')\"";
    # ==================== 远程执行====================
    
    echo "curl $AUTOBUILDER_ENV_STR \"$AUTOBUILDER_ZONE_CUSTOM_PUBLISHURL\""
    echo "$AUTOBUILDER_ENV_STR" | xargs curl -S "$AUTOBUILDER_ZONE_CUSTOM_PUBLISHURL"
fi
