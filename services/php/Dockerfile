ARG PHP_VERSION
FROM php:${PHP_VERSION}-fpm-alpine
# 注意！FROM之前声明的ARG，是在构建阶段之外的，所以不能在FROM之后的任何指令中使用它。要使用在第一个FROM之前声明的ARG的默认值，需要在构建阶段内使用一个没有值的ARG指令
ARG CONTAINER_PACKAGE_URL
ARG PHPREDIS_VERSION
ARG SWOOLE_VERSION
ARG YASD_VERSION
ARG AMQP_VERSION

COPY php.ini /usr/local/etc/php

RUN sed -i "s/dl-cdn.alpinelinux.org/${CONTAINER_PACKAGE_URL}/g" /etc/apk/repositories

RUN cd ~ \
    && apk add wget \
    curl \
    --no-cache --virtual .build-deps autoconf g++ gcc libtool make linux-headers \
    boost boost-dev \
    libstdc++ \
    bzip2-dev \
    gettext-dev \
    libzip-dev \
    imagemagick-dev \
    libmcrypt-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    file-dev \
    libxml2-dev \
    zlib-dev \
    libpq-dev \
    libmemcached-dev \
    rabbitmq-c-dev \
    && wget https://github.com/phpredis/phpredis/archive/${PHPREDIS_VERSION}.tar.gz -O phpredis-${PHPREDIS_VERSION}.tar.gz \
    && tar -zxvf phpredis-${PHPREDIS_VERSION}.tar.gz \
    && mkdir -p /usr/src/php/ext \
    && mv phpredis-${PHPREDIS_VERSION} /usr/src/php/ext/redis \
    && docker-php-ext-install redis \
    && wget https://pecl.php.net/get/amqp-${AMQP_VERSION}.tgz \
    && tar -zxvf amqp-${AMQP_VERSION}.tgz \
    && mv amqp-${AMQP_VERSION} /usr/src/php/ext/amqp \
    && docker-php-ext-install amqp \
    && wget https://github.com/swoole/swoole-src/archive/v${SWOOLE_VERSION}.tar.gz -O swoole-src-${SWOOLE_VERSION}.tar.gz \
    && tar -zxvf swoole-src-${SWOOLE_VERSION}.tar.gz \
    && mkdir -p /usr/src/php/ext \
    && mv swoole-src-${SWOOLE_VERSION} /usr/src/php/ext/swoole \
    && docker-php-ext-install swoole \
    && wget https://github.com/swoole/yasd/archive/v${YASD_VERSION}.tar.gz -O yasd-${YASD_VERSION}.tar.gz \
    && tar -zxvf yasd-${YASD_VERSION}.tar.gz \
    && mkdir -p /usr/src/php/ext \
    && mv yasd-${YASD_VERSION} /usr/src/php/ext/yasd \
    && docker-php-ext-install yasd \
    && rm -f /usr/local/etc/php/conf.d/docker-php-ext-yasd.ini \
    && docker-php-ext-configure gd \
    --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd mysqli pdo_mysql pcntl sockets bz2 zip soap pgsql pdo_pgsql bcmath gettext sysvmsg sysvsem sysvshm xmlrpc opcache \
    && curl -o /usr/bin/composer https://mirrors.aliyun.com/composer/composer.phar \
    && chmod +x /usr/bin/composer \
    && composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/

ENV COMPOSER_HOME=/tmp/composer

WORKDIR /var/www

EXPOSE 9000
EXPOSE 9501

# 安装ssh，用于远程调试，不需要的可以注释掉
RUN cd ~ \
    && apk add --no-cache openssh-server openssh \
    && sed -i "s/#PermitRootLogin.*/PermitRootLogin yes/g" /etc/ssh/sshd_config \
    && sed -i "s/#Port.*/Port 22/g" /etc/ssh/sshd_config \
    && ssh-keygen -t dsa -P "" -f /etc/ssh/ssh_host_dsa_key \
    && ssh-keygen -t rsa -P "" -f /etc/ssh/ssh_host_rsa_key \
    && ssh-keygen -t ecdsa -P "" -f /etc/ssh/ssh_host_ecdsa_key \
    && ssh-keygen -t ed25519 -P "" -f /etc/ssh/ssh_host_ed25519_key \
    && echo "root:root" | chpasswd # 设置ssh账号:密码

# 开放22端口 用于ssh
EXPOSE 22

# 容器启动时执行ssh启动命令
CMD ["/usr/sbin/sshd", "-D"]