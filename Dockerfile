FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl

# INSTALA O COMPOSER DENTRO DO CONTAINER
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# COPIA TODOS OS ARQUIVOS DO PROJETO
COPY . /var/www/html/

WORKDIR /var/www/html

# AQUI ESTÁ O QUE FALTAVA
RUN composer install --no-dev --optimize-autoloader

# PERMISSÕES
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80