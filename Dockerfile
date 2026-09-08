FROM php:8.4-fpm

# Instala dependências do sistema, Nginx e extensões necessárias
RUN apt-get update && apt-get install -y \
    nginx \
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

# Copia a configuração do Nginx e ajusta o proxy do FPM para rodar na mesma máquina (127.0.0.1)
COPY nginx/default.conf /etc/nginx/sites-available/default
RUN sed -i 's/laravel:9000/127.0.0.1:9000/g' /etc/nginx/sites-available/default

# Instala as dependências do Composer otimizadas para produção (sem pacotes de dev)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Garante a criação das pastas necessárias de cache e log
RUN mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache

# Define as permissões corretas para o servidor web (www-data)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# O Render injeta a variável PORT.
ENV PORT=8000
EXPOSE $PORT

# Altera a porta do Nginx para a $PORT do Render, inicia o PHP-FPM em background e o Nginx em foreground
CMD sed -i "s/listen 80;/listen $PORT;/g" /etc/nginx/sites-available/default && \
    php-fpm -D && \
    nginx -g "daemon off;"
