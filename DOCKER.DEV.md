# Docker development

Run these commands from the repository root with Docker running and Docker Compose v2 installed. The stack provides Nginx, PHP-FPM, MariaDB, Redis and Adminer. Source files are mounted into `/var/www`; the web document root is `/var/www/public_html`.

## Contents

- [Configure `.env`](#configure-env)
- [Start and open the application](#start-and-open-the-application)
- [Optional demo installation](#optional-demo-installation)
- [Shells, Composer and logs](#shells-composer-and-logs)
- [Apply configuration and code changes](#apply-configuration-and-code-changes)
- [Stop and clean up](#stop-and-clean-up)
- [Known issues](#known-issues)

## Configure `.env`

Copy the example before starting:

```bash
cp .env.example .env
```

Edit `.env` for your local setup:

| Variable | Purpose |
| --- | --- |
| `NGINX_HOST` | Store hostname; default `abantecart.localhost`. |
| `NGINX_ADMINER_HOST` | Adminer hostname; default `adminer.abantecart.localhost`. |
| `INSTALL_DEMO_DATA` | Set `true` to install a fresh development store with sample data at PHP-FPM startup; default `false`. |
| `XDEBUG_MODE` | Xdebug mode; default `off`. Set `develop,debug` when debugging. |
| `MARIADB_VERSION` | MariaDB image tag; default `11.4`. |
| `MYSQL_DATABASE` | Database created on first initialization. |
| `MYSQL_USER`, `MYSQL_PASSWORD` | Application database credentials. |
| `MYSQL_ROOT_PASSWORD` | MariaDB root password. |
| `UID`, `GID` | Host user and group IDs used by PHP-FPM. Set them with `id -u` and `id -g` to preserve ownership of files created in the bind-mounted project directory. |

Use `.localhost` hostnames for local browser access. Other names require local DNS or an `/etc/hosts` entry pointing to `127.0.0.1`. 

MariaDB initialization variables apply only to an empty database volume. Changing passwords or database names in `.env` does not update an existing database; update it with SQL or deliberately [reset the volume](#stop-and-clean-up).

## Start and open the application

```bash
docker compose -f compose.dev.yml up -d --build
docker compose -f compose.dev.yml ps
```

With the example hostnames, open:

- Store / installer: <http://abantecart.localhost:8080>
- Adminer: <http://adminer.abantecart.localhost:8080>

In the installer or Adminer, use database host `mariadb`, port `3306`, and the database name and credentials from `.env`. Redis is available to containers at `redis:6379`.

## Optional demo installation

Set `INSTALL_DEMO_DATA=true` in `.env` before the first start to run the CLI installer automatically. Compose waits for MariaDB’s health check before starting PHP-FPM. The installer uses the `.env` database credentials, the `apdomysql` driver, table prefix `abc_`, and store URL `http://${NGINX_HOST}:8080`.

The development admin credentials are **admin / admin**, with email `admin@admin.com` and admin path `admin`.

## Shells, Composer and logs

Open a shell in a running service, and use `exit` to leave:

```bash
docker compose -f compose.dev.yml exec php-fpm sh
$ php -v
PHP 8.4.26 (cli) (built: Sep 24 2026 19:12:02) (NTS)
Copyright (c) The PHP Group
Built by https://github.com/docker-library/php
Zend Engine v4.4.26, Copyright (c) Zend Technologies
    with Zend OPcache v8.4.26, Copyright (c), by Zend Technologies
    with Xdebug v3.5.3, Copyright (c) 2002-2026, by Derick Rethans
$ exit
```

Run Composer in the directory containing the application's `composer.json`:

```bash
docker compose -f compose.dev.yml exec -w /var/www/public_html php-fpm composer install
```

Follow database or PHP logs (Ctrl+C to stop):

```bash
docker compose -f compose.dev.yml logs -f --tail=100 mariadb
docker compose -f compose.dev.yml logs -f --tail=100 php-fpm
docker compose -f compose.dev.yml logs -f --tail=100 web
```

Nginx access/error logs are written into the mounted `public_html/system/logs/` directory.

PHP-FPM error log is written into the mounted `public_html/system/logs/php-error.log`.

## Apply configuration and code changes

Source edits are visible immediately through the bind mount. Configuration changes depend on how the file reaches the container:

| Change                                                                                    | Command |
|-------------------------------------------------------------------------------------------| --- |
| PHP settings in `docker-dev/php-fpm/conf.d/99-custom.ini`                                 | `docker compose -f compose.dev.yml restart php-fpm` |
| Xdebug settings in `docker-dev/php-fpm/conf.d/98-xdebug.ini`                              | `docker compose -f compose.dev.yml restart php-fpm` |
| Nginx template in `docker-dev/nginx/nginx.conf.template`                                  | `docker compose -f compose.dev.yml restart web` |
| PHP Dockerfile, extensions, entrypoint or copied files in `docker-dev/php-fpm/php-fpm.d/` | `docker compose -f compose.dev.yml up -d --build --no-deps php-fpm` |
| Docker Compose configuration or `.env`                                                | `docker compose -f compose.dev.yml up -d` |

Check the generated Nginx configuration or loaded PHP extensions:

```bash
docker compose -f compose.dev.yml exec web nginx -t
docker compose -f compose.dev.yml exec php-fpm php -m
```

`--no-deps` leaves dependencies untouched; use it when the rest of the stack is already running. Xdebug connects from PHP to your IDE at `host.docker.internal:9003`; enable the IDE listener and configure path mappings.

## Stop and clean up

For a quick development reset, use the reset script from the repository root. **This deletes the development database volume**, deletes `public_html/system/config.php` plus the Nginx access/error and PHP error log files. Use it when you want to completely reset the development installation. Grant execute permission once before running it:

```bash
chmod +x docker-dev/reset.sh
./docker-dev/reset.sh
```

For a full development environment shutdown, use the cleanup script from the repository root. It will shut down and remove all docker-related resources, and deletes `public_html/system/config.php` plus the Nginx access/error and PHP error log files. Use it when you want to completely remove the development environment. Grant execute permission once before running it:

```bash
chmod +x docker-dev/cleanup.sh
./docker-dev/cleanup.sh
```

You can also use Docker Compose directly to start and stop the stack or its services.

Stop containers while keeping them and the database:

```bash
docker compose -f compose.dev.yml stop
```

Remove the stack's containers and network while preserving the database volume:

```bash
docker compose -f compose.dev.yml down
```

To reset the development database, remove the named volume too. **This deletes its database data.** Mounted project files remain on disk.

```bash
docker compose -f compose.dev.yml down -v
docker compose -f compose.dev.yml up -d
```

To also remove the locally built PHP image, use `docker compose -f compose.dev.yml down --rmi local`. Rebuild with `up -d --build` on the next start.

## Known issues

Xdebug is disabled by default. With PHP 8.4, enabling it during the web installer can abort a PHP-FPM worker with `zend_mm_heap corrupted`, and results in 502 Bad Gateway error. Most likely it is connected to Xdebug/SimpleXML interaction: [Xdebug issue 2383](https://bugs.xdebug.org/view.php?id=2383) reports the same PHP 8.4 heap-corruption symptom, with allocation in `SimpleXMLElement::getNamespaces()`.

To enable Xdebug after installation, change `.env` and recreate PHP-FPM so it receives the new environment variable:

```dotenv
XDEBUG_MODE=develop,debug
```

```bash
docker compose -f compose.dev.yml up -d --no-deps php-fpm
docker compose -f compose.dev.yml exec php-fpm php -r 'var_export(xdebug_info("mode"));'
```
