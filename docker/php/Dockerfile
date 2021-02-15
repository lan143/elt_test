FROM php:8.0-fpm

# Install modules
RUN apt-get update && apt-get install -y zlib1g-dev libicu-dev \
        libmcrypt-dev \
        libxslt1-dev \
        zip unzip \
        autoconf \
        pkg-config \
        libssl-dev \
        libzip-dev \
        build-essential \
        git-core \
        wget \
        ssh-client \
        --no-install-recommends

RUN pecl channel-update pecl.php.net \
    && docker-php-ext-install bcmath pdo_mysql zip \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN usermod -u 1000 www-data && chown -R www-data:www-data ~www-data

RUN rm -rf /usr/local/etc/php-fpm.d
COPY ./pools /usr/local/etc/php-fpm.d
COPY ./php.ini /usr/local/etc/php/
COPY ./php-fpm.conf /usr/local/etc/php-fpm.conf
COPY ./docker-php-entrypoint /usr/local/bin/

RUN chmod 777 /usr/local/bin/docker-php-entrypoint \
    && ln -s /usr/local/bin/docker-php-entrypoint /

ENTRYPOINT ["docker-php-entrypoint"]
CMD ["php-fpm"]
