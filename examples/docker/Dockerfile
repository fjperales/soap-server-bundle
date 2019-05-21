#Service php
FROM php:7.2.10-apache

RUN a2enmod actions proxy rewrite ssl headers

#INSTALLING XDEBUG
RUN	pecl install xdebug-2.6.1 \
    && docker-php-ext-enable xdebug \
#INSTALLING OTHERS
	&& apt-get update && apt-get install -y zlib1g-dev libxslt1-dev libxml2-dev libbz2-dev libcurl3 libcurl4-gnutls-dev \
    &&  docker-php-ext-install mbstring zip soap xmlrpc bz2 xsl \
#PURGE
    && apt-get -y purge


ADD apache/conf/30-custom.ini /usr/local/etc/php/conf.d/30-custom.ini