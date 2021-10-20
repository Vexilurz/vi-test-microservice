ARG DOCKER_PHP_VERSION

FROM php:${DOCKER_PHP_VERSION}-fpm-alpine

ARG DOCKER_PHP_ENABLE_XDEBUG='off'
ARG XDEBUG_TARGET_HOST='192.168.1.20'
ARG XDEBUG_TARGET_PORT=9000
ARG TZ='UTC'

# https://wiki.alpinelinux.org/wiki/Setting_the_timezone
RUN echo "${TZ}" && apk --update add tzdata && \
    cp /usr/share/zoneinfo/$TZ /etc/localtime && \
    echo $TZ > /etc/timezone && \
    apk del tzdata

RUN apk add --update --no-cache icu-libs \
        libintl \
        build-base \
        zlib-dev \
        cyrus-sasl-dev \
        libgsasl-dev \
        oniguruma-dev \
        procps \
        imagemagick \
        patch \
        bash \
        htop \
        acl \
        apk-cron \
        augeas-dev \
        autoconf \
        curl \
        ca-certificates \
        dialog \
        freetype-dev \
        gomplate \
        git \
        gcc \
        gettext-dev \
        icu-dev \
        libcurl \
        libffi-dev \
        libgcrypt-dev \
        libjpeg-turbo-dev \
        libpng-dev \
        libmcrypt-dev \
        libressl-dev \
        libxslt-dev \
        libzip-dev \
        linux-headers \
        libxml2-dev \
        ldb-dev \
        make \
        musl-dev \
        mysql-client \
        openssh-client \
        pcre-dev \
        ssmtp \
        sqlite-dev \
        supervisor \
        su-exec \
        wget \
        nodejs \
        npm \
        nano \
        mc

#  Install php extensions
RUN php -m && \
    docker-php-ext-configure bcmath --enable-bcmath && \
    docker-php-ext-configure gd \
      --with-freetype=/usr/include/ \
      --with-jpeg=/usr/include/ && \
    docker-php-ext-configure gettext && \
    docker-php-ext-configure intl --enable-intl && \
    docker-php-ext-configure opcache --enable-opcache && \
    docker-php-ext-configure pcntl --enable-pcntl && \
    docker-php-ext-configure soap && \
    docker-php-ext-configure zip --with-zip && \
    docker-php-ext-install exif \
        mysqli \
        opcache \
        xsl \
        bcmath \
        gd \
        gettext \
        intl \
        opcache \
        pcntl \
        soap \
        zip \
        calendar \
        pdo \
        pdo_mysql

# Enable Xdebug
RUN if [ "${DOCKER_PHP_ENABLE_XDEBUG}" == "on" ]; then \
      yes | pecl install xdebug-2.9.8 && \
      echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini && \
      chmod +x "$(find /usr/local/lib/php/extensions/ -name xdebug.so)" && \
      echo "xdebug.remote_enable=1" >> /usr/local/etc/php/conf.d/xdebug.ini && \
      echo "xdebug.remote_autostart=1" >> /usr/local/etc/php/conf.d/xdebug.ini && \
      echo "xdebug.remote_port=${XDEBUG_TARGET_PORT}" >> /usr/local/etc/php/conf.d/xdebug.ini && \
      echo "xdebug.remote_host=${XDEBUG_TARGET_HOST}" >> /usr/local/etc/php/conf.d/xdebug.ini && \
      echo "xdebug.remote_handler=dbgp" >> /usr/local/etc/php/conf.d/xdebug.ini && \
      echo "xdebug.remote_connect_back=0" >> /usr/local/etc/php/conf.d/xdebug.ini && \
      echo "xdebug.idekey=PHPSTORM" >> /usr/local/etc/php/conf.d/xdebug.ini && \
      php -m; \
    else \
      echo "Skip xdebug support"; \
    fi

# Install phpunit
RUN wget https://phar.phpunit.de/phpunit-9.phar && \
    chmod +x phpunit-9.phar && \
    mv phpunit-9.phar /usr/local/bin/phpunit

# Clean
RUN rm -rf /var/cache/apk/* && docker-php-source delete

USER root

RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer

RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony/bin/symfony /usr/local/bin/symfony

USER www-data:www-data

WORKDIR /var/www/

#USER root

CMD bash -c "composer update && php-fpm"
