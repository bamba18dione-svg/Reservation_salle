# 1. Utiliser l'image officielle PHP 8.2 avec FPM/CLI sur Debian Alpine/Debian
FROM php:8.2-cli

# 2. Installer les dépendances système et les extensions PHP requises (PDO MySQL, zip)
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# 3. Installer Composer globalement depuis l'image officielle Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. Copier les fichiers de l'application dans le conteneur
COPY . /var/www/html/

# 5. Installer les dépendances Composer (sans dev pour la production)
RUN composer install --no-dev --no-interaction --optimize-autoloader

# 6. Définir les permissions d'écriture sur les dossiers nécessaires
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

# 7. Définir le répertoire de travail dans le conteneur
WORKDIR /var/www/html

# 8. Exposer le port du serveur PHP intégré
EXPOSE 8000

# 9. Commande par défaut pour démarrer le serveur intégré PHP
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
