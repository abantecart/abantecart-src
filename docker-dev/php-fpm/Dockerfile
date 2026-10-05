# syntax=docker/dockerfile:1
FROM php:8.4-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    libxml2-dev \
    libicu-dev \
    libzip-dev \
    libwebp-dev \
    libavif-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    && docker-php-ext-configure gd  \
      --with-freetype --with-jpeg --with-webp --with-avif \
    && docker-php-ext-install -j$(nproc) \
    gd \
    pdo_mysql \
    intl \
    zip \
    bcmath \
    soap \
    && pecl install xdebug && docker-php-ext-enable xdebug \
    && pecl install redis && docker-php-ext-enable redis \
    && apt-get autoremove -y && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# add health check path
COPY ./docker-dev/php-fpm/php-fpm.d/*.conf /usr/local/etc/php-fpm.d/

# get actual composer directly from image
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Copy the entrypoint script
COPY ./docker-dev/php-fpm/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Set the working directory inside the container
WORKDIR /var/www

# Change the default command to run the entrypoint script
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
