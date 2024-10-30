FROM php:8.2-cli

# Install dependencies
RUN apt-get update \
  && apt-get install -y \
    libfreetype6-dev \
    libonig-dev \
    libicu-dev \
    libjpeg62-turbo-dev \
    libmcrypt-dev \
    libpng-dev \
    libxslt1-dev \
    sendmail-bin \
    sendmail \
    sudo \
    cron \
    rsyslog \
    default-mysql-client \
    git \
    libzip-dev

# Install required PHP extensions

RUN docker-php-ext-install \
  dom \
  intl \
  mbstring \
  pdo_mysql \
  xsl \
  zip \
  soap \
  bcmath

# Install Xdebug (but don't enable)
RUN pecl install -o -f xdebug

ENV PHP_MEMORY_LIMIT 2G

VOLUME /root/.composer/cache

ADD php-cli.ini /usr/local/etc/php/conf.d/zz-magento.ini

# Get composer installed to /usr/local/bin/composer
RUN curl -sS https://getcomposer.org/installer | php -- --version=2.8.1 --install-dir=/usr/local/bin --filename=composer

ARG currentUser
ARG uid
ARG gid

RUN useradd -ms /bin/bash $currentUser
RUN chown $uid:$gid /usr/local/bin/*
USER $uid:$gid

CMD ["bash"]

RUN eval "$(ssh-agent -s)"
