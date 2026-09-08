FROM php:8.4-fpm

# Instala dependências do sistema e extensões necessárias
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instala as extensões do PHP necessárias para o Laravel
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Instala o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define o diretório de trabalho
WORKDIR /var/www/html

# Copia todo o código do projeto para o container
COPY . .

# Instala as dependências do Composer otimizadas para produção (sem pacotes de dev)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Garante a criação das pastas necessárias de cache e log
RUN mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache

# Define as permissões corretas para o servidor web (www-data)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Expõe a porta que o PHP-FPM usa
EXPOSE 9000

# Inicia o PHP-FPM
CMD ["php-fpm"]
