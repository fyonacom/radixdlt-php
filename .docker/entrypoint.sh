#!/usr/bin/env sh

## break on errors
set -e

## prepare vars
HOST_USER_UID=${HOST_USER_UID}
HOST_USER_GID=${HOST_USER_GID:-0}
HOST_USER_NAME=${HOST_USER_NAME:-"user"}

## if run as root and HOST_USER_ID is provided
if [[ "$(id -u)" = "0"  && "${HOST_USER_UID}" != "" ]]; then

    ## create group if not exists
    if ! getent group ${HOST_USER_GID} > /dev/null 2>&1; then
        addgroup -g ${HOST_USER_GID} ${HOST_USER_NAME}
    fi

    ## if not exists, create it
    if ! getent passwd ${HOST_USER_UID} > /dev/null 2>&1; then
        adduser -u ${HOST_USER_UID} \
                -G $(getent group ${HOST_USER_GID} | cut -d: -f1) \
                -D \
                -k "/root" \
                -g "Docker Host User" \
                "${HOST_USER_NAME}"
    fi

    chown -R ${HOST_USER_UID}:${HOST_USER_GID} $(getent passwd ${HOST_USER_UID} | cut -d: -f6)

    ## run command as user
    su-exec "${HOST_USER_UID}" "$@"
else
    ## run command normally
    exec "$@"
fi;

