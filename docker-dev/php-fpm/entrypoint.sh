#!/bin/sh
set -e

# Install demo data only when starting PHP-FPM on an uninstalled project.
if [ "${1:-}" = "php-fpm" ]; then
    case "${INSTALL_DEMO_DATA:-false}" in
        1|true|yes|on)
            cd /var/www/public_html
            if /usr/local/bin/php -r 'if (is_file("system/config.php")) { require "system/config.php"; } exit(defined("DB_HOSTNAME") && DB_HOSTNAME ? 0 : 1);'; then
                echo "AbanteCart is already installed; skipping demo installation."
            else
                : "${MYSQL_DATABASE:?MYSQL_DATABASE is required}"
                : "${MYSQL_USER:?MYSQL_USER is required}"
                : "${MYSQL_PASSWORD:?MYSQL_PASSWORD is required}"
                : "${NGINX_HOST:?NGINX_HOST is required}"
                echo "Installing AbanteCart with demo data..."
                XDEBUG_MODE=off /usr/local/bin/php ./install/cli_install.php install \
                    --db_host=mariadb \
                    --db_user="$MYSQL_USER" \
                    --db_password="$MYSQL_PASSWORD" \
                    --db_name="$MYSQL_DATABASE" \
                    --db_driver=apdomysql \
                    --db_prefix=abc_ \
                    --admin_path=admin \
                    --username=admin \
                    --password=admin \
                    --email=admin@admin.com \
                    --http_server="http://${NGINX_HOST}:8080" \
                    --with-sample-data=abantecart_sample_data.sql
            fi
            ;;
    esac
fi

exec "$@"
