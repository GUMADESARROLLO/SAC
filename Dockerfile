FROM composer:1.6.5 as build
WORKDIR /app
COPY . /app
RUN composer install --ignore-platform-reqs

# FROM kooldev/php:7.4-nginx-sqlsrv-prod 
# WORKDIR /app
FROM gumadesarrollo/php:7.4-apache-sqlsrv-prod

WORKDIR /var/www/html

RUN mkdir -p SAC/

COPY --from=build /app /SAC

RUN chmod -R 777 /SAC/storage

EXPOSE 80