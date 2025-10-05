FROM php:8.2-fpm

# Arguments with default values
ARG user=www-data
ARG uid=1000

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    gnupg \
    g++ \
    make \
    python3

# Install Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set up existing www-data user
RUN usermod -u $uid www-data && \
    groupmod -g $uid www-data && \
    mkdir -p /home/$user && \
    chown -R $user:$user /home/$user && \
    mkdir -p /var/www/storage && \
    mkdir -p /var/www/bootstrap/cache && \
    chown -R $user:$user /var/www/storage /var/www/bootstrap/cache

# Set working directory
WORKDIR /var/www

# Copy existing application directory
COPY --chown=$user:$user . /var/www

# Change current user to www
USER $user

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
