# Utiliser PHP 8.2 avec FPM
FROM php:8.2-fpm

# Installer les extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    git unzip libpq-dev libpng-dev libonig-dev libxml2-dev zip \
    && docker-php-ext-install pdo pdo_mysql mbstring gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www

# Copier uniquement composer.json et composer.lock pour optimiser le cache Docker
COPY composer.json composer.lock /var/www/

# Installer les dépendances Symfony
RUN composer install --no-scripts --no-autoloader --prefer-dist

# Copier le reste du projet Symfony avec les bonnes permissions
COPY --chown=www-data:www-data . /var/www

# Ajuster les permissions des dossiers nécessaires
RUN chmod -R 775 /var/www/var /var/www/public

# Définir l'entrée du conteneur
ENTRYPOINT ["php-fpm"]