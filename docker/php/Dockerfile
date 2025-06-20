FROM php:8.4-fpm

ARG user
ARG uid

RUN apt-get update && apt-get install -y \
  git \
  zip \
  unzip \
  curl \
  libzip-dev

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo pdo_mysql zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

WORKDIR /usr/share/nginx/html

USER $user
