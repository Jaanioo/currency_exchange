FROM php:8.2-fpm

# Instalacja zależności PHP
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install zip mysqli

# Ustawienie katalogu roboczego
WORKDIR /var/www/project

# Skopiowanie plików źródłowych aplikacji do kontenera
COPY . /var/www/project

# Uprawnienia plików
RUN chown -R www-data:www-data /var/www/project
RUN chmod -R 755 /var/www/project

# Expose port 9000
EXPOSE 9000

# Uruchomienie serwera PHP-FPM
CMD ["php-fpm"]
