# Docker development

Run these commands from the repository root with Docker running and Docker Compose v2 installed. The stack provides Nginx, PHP-FPM, MariaDB, Redis and Adminer. Source files are mounted into `/var/www`; the web document root is `/var/www/public_html`.

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
| `MARIADB_VERSION` | MariaDB image tag. |
| `MYSQL_DATABASE` | Database created on first initialization. |
| `MYSQL_USER`, `MYSQL_PASSWORD` | Application database credentials. |
| `MYSQL_ROOT_PASSWORD` | MariaDB root password. |
| `UID`, `GID` | Currently not wired into Compose build arguments or its runtime user; changing these does not change the container's user IDs. |

Use `.localhost` hostnames for local browser access. Other names require local DNS or an `/etc/hosts` entry pointing to `127.0.0.1`. Nginx hostnames select the site; Compose binds host port `8080` to loopback for local access only.

MariaDB initialization variables apply only to an empty database volume. Changing passwords or database names in `.env` does not update an existing database; update it with SQL or deliberately reset the volume.

## Start and open the application

```bash
docker compose -f compose.dev.yml up -d --build
docker compose -f compose.dev.yml ps
```

With the example hostnames, open:

- Store / installer: <http://abantecart.localhost:8080>
- Adminer: <http://adminer.abantecart.localhost:8080>

In the installer or Adminer, use database host `mariadb`, port `3306`, and the database name and credentials from `.env`. Redis is available to containers at `redis:6379`. Neither database service needs a host port mapping.

For subsequent starts, use `docker compose -f compose.dev.yml up -d`. After changing `.env`, the same command recreates services whose configuration changed.

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

Follow database or PHP logs (Ctrl+C stops following, not the container):

```bash
docker compose -f compose.dev.yml logs -f --tail=100 mariadb
docker compose -f compose.dev.yml logs -f --tail=100 php-fpm
docker compose -f compose.dev.yml logs -f --tail=100 web
```

The store's Nginx access/error logs are written into the mounted `public_html/system/logs/` directory. Locations with `access_log off` are excluded. Container logs still show Nginx startup messages and other output.

Open the database client; enter `MYSQL_ROOT_PASSWORD` when prompted:

```bash
docker compose -f compose.dev.yml exec mariadb mariadb -u root -p
```

## Apply configuration and code changes

Source edits are visible immediately through the bind mount. Configuration changes depend on how the file reaches the container:

| Change | Command |
| --- | --- |
| PHP settings in `docker-dev/php-fpm/conf.d/99-custom.ini` | `docker compose -f compose.dev.yml restart php-fpm` |
| Xdebug settings in `docker-dev/php-fpm/conf.d/98-xdebug.ini` | `docker compose -f compose.dev.yml restart php-fpm` |
| Nginx template in `docker-dev/nginx/nginx.conf.template` | `docker compose -f compose.dev.yml restart web` |
| PHP Dockerfile, extensions, entrypoint or copied files in `docker-dev/php-fpm/php-fpm.d/` | `docker compose -f compose.dev.yml up -d --build --no-deps php-fpm` |
| Compose configuration or `.env` | `docker compose -f compose.dev.yml up -d` |

Nginx generates `/etc/nginx/nginx.conf` from its template at container startup. A reload alone does not regenerate the template, and `up -d` alone does not restart a container for a template-only edit.

Check the generated Nginx configuration or loaded PHP extensions:

```bash
docker compose -f compose.dev.yml exec web nginx -t
docker compose -f compose.dev.yml exec php-fpm php -m
docker compose -f compose.dev.yml exec php-fpm php --ri gd
```

`--no-deps` leaves dependencies untouched; use it when the rest of the stack is already running. Xdebug connects from PHP to your IDE at `host.docker.internal:9003`; enable the IDE listener and configure path mappings. For debugging on demand, set `xdebug.start_with_request=trigger` in the mounted Xdebug settings and restart PHP-FPM.

## Stop and clean up

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
