# docker build must be ran from the root of the openiot project

# download all node dependencies
FROM node AS npmdeps

COPY platform/*.json /modules/

WORKDIR /modules

RUN npm install

# download all php dependencies
FROM composer AS phpdeps

COPY api/config/composer* /vendor/

WORKDIR /vendor

RUN composer install

# final image
FROM richarvey/nginx-php-fpm

ENV WEBROOT /var/www/html/platform/public

COPY . /var/www/html

COPY --from=npmdeps /modules /var/www/html/platform/

COPY --from=phpdeps /vendor /var/www/html/api/config/

EXPOSE 8888

