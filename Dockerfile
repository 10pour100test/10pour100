# Utiliser PHP 8.2 avec FPM
FROM php:8.2-fpm

# Installer les extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    git unzip libpq-dev libpng-dev libonig-dev libxml2-dev zip \
    && docker-php-ext-install pdo pdo_mysql mbstring gd

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www

# Copier uniquement composer.json et composer.lock pour optimiser le cache Docker
COPY composer.json composer.lock /var/www/

# Installer Symfony 6.4
RUN composer config extra.symfony.require "6.4.*" \
    && composer install --no-scripts --no-autoloader --prefer-dist

# Copier le reste du projet Symfony
COPY . /var/www

# Ajuster les permissions pour www-data (utilisé par Nginx et PHP-FPM)
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/var /var/www/public /var/www/config

# Commande de démarrage (important pour les volumes Docker)
CMD ["php-fpm"]
