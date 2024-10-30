FROM php:8.2-apache

ENV PHP_EXT_APCU_VERSION "5.1.18"
ENV PHP_EXT_MEMCACHED_VERSION "3.1.5"
ENV PHP_EXT_XDEBUG_VERSION "3.3.0"

RUN build_packages="libonig-dev libpng-dev libfreetype6-dev libjpeg62-turbo-dev libxml2-dev libxslt1-dev libmemcached-dev sendmail-bin sendmail libicu-dev libmcrypt-dev zlib1g-dev libssl-dev" \
    && apt-get update && apt-get install -y $build_packages \
    && yes "" | pecl install apcu-$PHP_EXT_APCU_VERSION && docker-php-ext-enable apcu \
    && docker-php-ext-install gd \
    && docker-php-ext-install calendar && docker-php-ext-configure calendar \
    && docker-php-ext-install mbstring \
    && docker-php-ext-install calendar \
    && echo "no" | pecl install memcached-$PHP_EXT_MEMCACHED_VERSION && docker-php-ext-enable memcached \
    && docker-php-ext-install pcntl \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install soap \
    && docker-php-ext-install bcmath \
    && yes | pecl install xdebug-$PHP_EXT_XDEBUG_VERSION && docker-php-ext-enable xdebug \
    && docker-php-ext-install xsl
RUN apt-get update && apt-get install -y \
    zlib1g-dev \
    libzip-dev
RUN docker-php-ext-install zip
RUN apt-get update && docker-php-ext-install intl \
    && apt-get install --no-install-recommends --assume-yes --quiet ca-certificates curl git \
    && rm -rf /var/lib/apt/lists/* \
    && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

COPY php.ini /usr/local/etc/php/conf.d/zz-magento.ini
COPY 000-default.conf /etc/apache2/sites-enabled/000-default.conf

RUN a2enmod rewrite

RUN curl -Lsf 'https://github.com/axllent/mailpit/releases/download/v1.9.10/mailpit-linux-386.tar.gz' | tar -C '/usr/local/bin' -xvzf -
ENV PATH /usr/local/bin/mailpit:$PATH

ARG currentUser
ARG uid
ARG gid

RUN useradd -ms /bin/bash $currentUser
ENV APACHE_RUN_USER $currentUser
ENV APACHE_RUN_GROUP $currentUser
CMD ["apache2-foreground"]
