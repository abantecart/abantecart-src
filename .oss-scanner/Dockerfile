# Dockerfile for Anthropic's OSS Scanner (https://github.com/anthropics/oss-scanner)
#
# The scanner builds this image with the abantecart-src repository checkout as the
# build context ("COPY . ..." below copies the exact revision being audited).
# Network access is available ONLY during this build; the security audit that
# follows runs offline, so everything the agent needs (PHP extensions, database,
# an installed cart with sample data, and the PHPUnit runner) is set up here.
#
# This file is intentionally separate from docker/Dockerfile, which is built with
# docker/ as its context for the DockerHub image and clones master from GitHub.
FROM php:8.5-fpm
LABEL maintainer="Pavel Rojkov <help@abantecart.com>"

RUN apt-get update && apt-get install -y \
    curl \
    wget \
    git \
    libwebp-dev \
    libavif-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libmcrypt-dev \
    libpng-dev \
    libxpm-dev \
    libonig-dev \
    libzip-dev \
    nginx \
    mariadb-server \
    net-tools \
    libxml2-dev && \
    docker-php-ext-install -j$(nproc) iconv mbstring mysqli pdo_mysql zip soap && \
    docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ --with-webp --with-avif --with-xpm && \
    docker-php-ext-install -j$(nproc) gd && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# PHPUnit for offline test runs during the audit (tests/phpunit/phpunit_mysql.xml targets PHPUnit 10.5)
RUN curl -sSL https://phar.phpunit.de/phpunit-10.5.phar -o /usr/local/bin/phpunit && \
    chmod +x /usr/local/bin/phpunit

ADD docker/default /etc/nginx/sites-available/
ADD docker/php.ini /usr/local/etc/php/conf.d/40-custom.ini

RUN service mariadb start; \
    mysql -u root -e "CREATE USER 'admin'@'%' IDENTIFIED BY 'pass';"; \
    mysql -u root -e "GRANT ALL PRIVILEGES ON *.* TO 'admin'@'%' WITH GRANT OPTION;"; \
    mysql -u root -e "create database abantecart;";

RUN sed -i 's/bind-address/#bind-address/' /etc/mysql/my.cnf

# Scanner contract: the checkout being audited lives inside the image.
# This copies the local build context (the revision under audit) — do NOT git clone here,
# or the audited sources and the running code would be different revisions.
COPY . /var/www/abantecart
WORKDIR /var/www/abantecart/public_html/

# Install AbanteCart with sample data so the agent can exercise a working store offline
RUN service mariadb start && /usr/local/bin/php ./install/cli_install.php install \
    --db_host=127.0.0.1 --db_user=admin --db_password=pass --db_name=abantecart \
    --db_driver=amysqli --db_prefix=abc_ --admin_path=admin \
    --username=admin --password=admin --email=admin@admin.com \
    --http_server=http://localhost --with-sample-data=install/abantecart_sample_data.sql

RUN chown -R www-data:www-data /var/www/abantecart

# Run the cheap tests so we know the image works, but a failing test must not abort the build
RUN service mariadb start && \
    (php /usr/local/bin/phpunit -c /var/www/abantecart/tests/phpunit/phpunit_mysql.xml || echo "WARNING: phpunit reported failures")

RUN apt-get clean

EXPOSE 80 3306

CMD ["bash", "-c", "service mariadb start && until mysqladmin ping -h 127.0.0.1 -u admin -ppass --silent; do sleep 1; done && service nginx start && php-fpm"]
