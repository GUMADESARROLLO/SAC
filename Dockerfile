FROM kooldev/php:7.4-nginx-sqlsrv-prod 

# working directory
WORKDIR /app

# composer install dependencies
COPY . .

RUN chmod -R 777 storage
RUN chmod -R 777 public/images/promocion/

RUN composer install --ignore-platform-reqs

# expose
EXPOSE 80

# start supervisor
#CMD ["--nginx-env"]