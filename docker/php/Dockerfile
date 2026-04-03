FROM php:8.2-apache

# Installer l'extension PDO MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Activer mod_rewrite Apache
RUN a2enmod rewrite