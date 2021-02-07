FROM php:8.0.0-cli-alpine

LABEL maintainer="ben@fyona.com"

ENV COMPOSER_HOME "/tmp/.composer"
ENV WORKDIR "/srv"

## Update System / Install minimum requirements
RUN set -xe
RUN apk --update --no-cache upgrade
RUN apk add --no-cache --update --virtual .phpize-deps $PHPIZE_DEPS

RUN apk --update --no-cache add \
        bzip2-dev \
        coreutils \
        curl \
        git \
        gmp-dev \
        openssh-client \
        su-exec \
        zlib-dev \
        libzip-dev

# install xdebug
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug
ADD ./xdebug.ini /usr/local/etc/php/conf.d/98-xdebug-conf.ini

## install prepared extensions
RUN docker-php-ext-install -j$(nproc) bcmath
RUN docker-php-ext-install -j$(nproc) gmp

RUN pecl install zip
RUN docker-php-ext-enable zip

## install composer
RUN curl -s https://getcomposer.org/installer > "/tmp/composer-setup.php" \
    && echo $(curl -s "https://composer.github.io/installer.sig")"  /tmp/composer-setup.php" | sha384sum -c - \
    && php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && rm /tmp/composer-setup.php

## cleanup
RUN rm -rf /var/cache/* /tmp/*

ADD entrypoint.sh /bin/entrypoint.sh

## fix file permissions
RUN set -xe \
    && chmod 777 "${WORKDIR}"

## prepare composer home
RUN set -xe \
    && mkdir -p "${COMPOSER_HOME}" \
    && chmod 0777 "${COMPOSER_HOME}"

VOLUME ["${COMPOSER_HOME}"]

ENTRYPOINT ["/bin/entrypoint.sh"]

WORKDIR "${WORKDIR}"

CMD ["php", "-a"]
