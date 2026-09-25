FROM dunglas/frankenphp:php8.4

RUN install-php-extensions pdo_mysql

ENV SERVER_NAME=:8080

COPY . /app/public