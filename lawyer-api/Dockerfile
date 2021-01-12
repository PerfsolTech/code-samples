FROM phpdockerio/php7-fpm

WORKDIR /var/www/ammurapi

ADD . /var/www/ammurapi

RUN apt-get update \
    && apt-get install -y php7.0-mbstring php7.0-mysql php7.0-gd mysql-client

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN bash ./docker/setup_deploy.sh