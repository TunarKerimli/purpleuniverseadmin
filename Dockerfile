FROM php:8.1-apache

# Lazımi PHP genişləndirmələrini və SQLite dəstəyini quraşdıraq
RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
     && docker-php-ext-install pdo pdo_sqlite

# Apache-nin sənəd kök qovluğunu (DocumentRoot) tənzimləyirik
# Əgər əsas index.php birbaşa kök qovluqdadırsa, bu sətri saxlaya bilərsiniz
RUN sed -i -e 's!/var/www/html!/var/www/html!g' /etc/apache2/sites-available/000-default.conf

# Bütün faylları container-ə köçürürük
COPY . /var/www/html/

# İcazələri tənzimləyirik (xüsusilə SQLite bazası üçün)
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
