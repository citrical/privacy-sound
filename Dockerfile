FROM php:8.5.6-apache-trixie

# Activa mod_rewrite
RUN a2enmod rewrite

# Instala las dependencias del sistema
RUN apt-get update && apt-get install -y \
    ffmpeg \
    unzip \
    git \
    curl \
    libxml2-dev \
    libzip-dev \
    libcurl4-openssl-dev \
    libsqlite3-dev \
    libonig-dev \
    nodejs \
    npm \
    && rm -rf /var/lib/apt/lists/*

# Instala las extensiones de PHP
RUN docker-php-ext-install \
    pdo_sqlite \
    mbstring \
    xml \
    fileinfo \
    curl \
    zip

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copia vhost.conf al contenedor
COPY docker/apache/vhost.conf /etc/apache2/sites-available/privacy-sound.conf

# Activa nuestro sitio y desactiva el sitio por defecto
RUN a2ensite privacy-sound.conf && a2dissite 000-default.conf