ARG PHP_VERSION
FROM php:$PHP_VERSION
WORKDIR /code
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN apt-get update && \
	apt-get install -y zlib1g-dev libzip-dev && \
	docker-php-ext-configure zip && \
	docker-php-ext-install -j$(nproc) zip
RUN pecl install ast && \
	docker-php-ext-enable ast

ADD composer.json composer.lock ./
RUN composer install --prefer-dist
ADD src ./src
ADD .phan ./.phan
ADD tests ./tests
ADD phpunit.xml ./
RUN vendor/bin/phpunit
RUN vendor/bin/phan
