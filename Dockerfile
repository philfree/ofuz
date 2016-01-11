FROM php:5.5.30-apache

RUN cd /etc/apache2/mods-enabled && ln -s ../mods-available/rewrite.load
RUN apt-get update && apt-get install -y \
	vim \
	git \
	libpng-dev \
	zlib1g-dev
#RUN cd /var/www && git clone https://github.com/philfree/radria.git && cd radria && git checkout 4520fa0698261c137de8b06477eef56182a70ecb
RUN docker-php-ext-install gettext
RUN docker-php-ext-install mysql
#RUN docker-php-ext-install curl
#RUN docker-php-ext-install gd
RUN docker-php-ext-install zip
RUN sed -i "s/short_open_tag = Off/short_open_tag = On/" /usr/src/php/php.ini-production
RUN sed -i "s/short_open_tag = Off/short_open_tag = On/" /usr/src/php/php.ini-development
RUN sed -i "s/;date.timezone =.*/date.timezone = UTC/" /usr/src/php/php.ini-development
RUN sed -i "s/;date.timezone =.*/date.timezone = UTC/" /usr/src/php/php.ini-production

#RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ADD ./php-apache/ofuz.conf /etc/apache2/sites-available/ofuz.conf/
RUN ln -s /etc/apache2/sites-available/ofuz.conf /etc/apache2/sites-enabled/ofuz.conf

RUN ln -s ../ofuzlib/Zend /var/www/Zend
RUN ln -s ../ofuzlib/radria/RadriaCore /var/www/RadriaCore

WORKDIR /var/www/ofuz/
COPY . ./
RUN chmod 777 radria_run.log
RUN chmod 777 radria_errors.log

#COPY composer.json composer.lock ./
#RUN composer update --prefer-source --no-interaction