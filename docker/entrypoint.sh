#!/usr/bin/env bash
command=$@

SYMFONY_ENV=${SYMFONY_ENV:-dev}
if [[ "$SYMFONY_ENV" != "prod" ]]; then
	wantedUIDUsername=`getent passwd $USER_ID | cut -d: -f1`
	if [[ -n "$USER_ID" ]] && [[ $wantedUIDUsername != "dp" ]] ; then
		sudo usermod -u $USER_ID dp

		exit 2;
	fi;

	if [[ -n "$XDEBUG_HOST" ]]; then
		HOST_IP=`/sbin/ip route|awk '/default/ { print $3 }'`
		XDEBUG_HOST=${XDEBUG_HOST:-$HOST_IP}
		sudo xdebug-install $XDEBUG_HOST
	fi;
fi;

if [ "$APPS" != "" ]; then
	CONFIGURED_APPS=`cd /var/www/html/config/app/ && ls -1`
	CONFIGURED_APPS=${CONFIGURED_APPS//\.yml/}
	APPS_TO_REMOVE=`echo ${APPS[@]} ${CONFIGURED_APPS[@]} | tr ' ' '\n' | sort | uniq -u`
	for id in $APPS_TO_REMOVE; do
		if [ "$id" != "" ]; then
			rm -rf /var/www/html/config/app/$id.yml
		fi;
	done;
fi;

if [[ "$SYMFONY_ENV" != "prod" ]]; then
	composer install -n --prefer-dist
else
    composer install -n --prefer-dist -o
fi;

sudo nginx

if [[ -n "$XDEBUG_HOST" ]]; then
sudo run-php-fpm-dev
fi;

sudo -E LD_PRELOAD=/usr/lib/preloadable_libiconv.so php-fpm
