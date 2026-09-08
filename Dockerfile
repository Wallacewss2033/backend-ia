FROM php:8.4-fpm

# Instala as extensões necessárias para o Laravel com MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Instala o Composer (gerenciador de dependências do PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define o diretório de trabalho
WORKDIR /var/www/html

# Garante que o usuário www-data tenha permissão sobre os arquivos
RUN chown -R www-data:www-data /var/www/html

# Cria as pastas do Laravel e dá permissão de escrita
RUN mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache
