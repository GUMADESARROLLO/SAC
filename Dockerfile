# FROM kooldev/php:7.4-nginx-sqlsrv-prod 
# WORKDIR /app

FROM gumadesarrollo/php:7.4-apache-sqlsrv-prod

WORKDIR /var/www/html

RUN mkdir SAC

WORKDIR /var/www/html/SAC

COPY . .

RUN composer install --ignore-platform-reqs

RUN chmod -R 777 storage

EXPOSE 80