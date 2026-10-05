#!/bin/sh
set -e

SCPATH="$(dirname -- "$(dirname -- "$(realpath -- "$0")")")"

# shut down docker containers
# remove images and volumes
docker compose -f "${SCPATH}"/compose.dev.yml down -v --rmi local

# remove config
rm -f "${SCPATH}"/public_html/system/config.php

# remove nginx / php logs
rm -f "${SCPATH}"/public_html/system/logs/access.log
rm -f "${SCPATH}"/public_html/system/logs/error.log
rm -f "${SCPATH}"/public_html/system/logs/php-error.log
