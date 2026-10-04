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

# Add ARGs for syncing permissions
ARG UID=1000
ARG GID=1000

# Create a new user with the specified UID and GID, reusing an existing group if GID exists
RUN if getent group ${GID}; then \
      useradd -m -u ${UID} -g ${GID} -s /bin/bash www; \
    else \
      groupadd -g ${GID} www && \
      useradd -m -u ${UID} -g www -s /bin/bash www; \
    fi

# Dynamically update php-fpm to use the new user and group
RUN group_name=$(getent group ${GID} | cut -d: -f1) \
    && sed -i "s/user = www-data/user = www/g" /usr/local/etc/php-fpm.d/www.conf \
    && sed -i "s/group = www-data/group = $group_name/g" /usr/local/etc/php-fpm.d/www.conf

# Copy the entrypoint script
COPY ./docker-dev/php-fpm/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Set the working directory inside the container
WORKDIR /var/www

# Switch to the non-privileged user to run the application
USER www-data

# Change the default command to run the entrypoint script
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
