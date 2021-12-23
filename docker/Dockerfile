FROM php:7.4-fpm
LABEL maintainer="Denis Vagner <help@abantecart.com>"

RUN apt-get update && apt-get install -y \
    curl \
    wget \
    git \
    libwebp-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libmcrypt-dev \
    libpng-dev \
    libxpm-dev \
    libonig-dev \
    libzip-dev \
    nginx \
    mariadb-server \
    mc \
    net-tools  \
    libxml2-dev && \
    docker-php-ext-install -j$(nproc) iconv mbstring mysqli pdo_mysql zip soap && \
    docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ && \
    docker-php-ext-install -j$(nproc) gd && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer 

ADD default /etc/nginx/sites-available/

RUN service mariadb start; \
    mysql -u root -e "CREATE USER 'admin'@'%' IDENTIFIED BY 'pass';"; \
    mysql -u root -e "GRANT ALL PRIVILEGES ON *.* TO 'admin'@'%' WITH GRANT OPTION;"; \
    mysql -u root -e "create database abantecart;";

RUN sed -i 's/bind-address/#bind-address/' /etc/mysql/my.cnf

ADD php.ini /usr/local/etc/php/conf.d/40-custom.ini

RUN git clone https://github.com/abantecart/abantecart-src.git /var/www/abantecart
RUN chown -R www-data:www-data /var/www/abantecart
WORKDIR /var/www/abantecart/public_html/

RUN  service mariadb start && /usr/local/bin/php ./install/cli_install.php install --db_host=127.0.0.1 --db_user=admin --db_password=pass --db_name=abantecart  --db_driver=amysqli  --db_prefix=abc_ --admin_path=admin  --username=admin  --password=admin  --email=admin@admin.com  --http_server=http://localhost --with-sample-data

RUN apt-get clean

EXPOSE 80 3306

ENTRYPOINT service mariadb start && service nginx start && php-fpm
