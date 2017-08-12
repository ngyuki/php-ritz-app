FROM php:apache

RUN apt-get update && apt-get install -y zlib1g-dev &&\
    apt-get clean && rm -r /var/lib/apt/lists/*

RUN docker-php-ext-install zip
RUN docker-php-ext-enable opcache

RUN curl -sSL https://getcomposer.org/download/1.5.1/composer.phar > /usr/local/bin/composer &&\
    chmod +x /usr/local/bin/composer

RUN rmdir /var/www/html && ln -sfn /app/public /var/www/html

ADD composer.json /app/
ADD composer.lock /app/

WORKDIR /app/
RUN composer install --no-dev --prefer-dist && rm -fr ~/.composer/

ADD ./ /app/
RUN composer dump-autoload -o
