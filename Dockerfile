FROM php:8.2-apache

# Install CA certificates and MySQL PDO extension
RUN apt-get update && apt-get install -y \
    ca-certificates \
    && docker-php-ext-install pdo pdo_mysql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite for .htaccess support
RUN a2enmod rewrite

# Allow .htaccess overrides in /var/www/html
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Set working directory
WORKDIR /var/www/html

# Copy all application files
COPY . /var/www/html/

# Create logs directory and set ownership to www-data
RUN mkdir -p /var/www/html/logs && chown -R www-data:www-data /var/www/html

# Configure Apache to listen on $PORT (Render passes dynamic PORT variable, default 80)
ENV PORT=80
RUN sed -i 's/80/${PORT}/g' /etc/apache2/ports.conf /etc/apache2/sites-available/*.conf

EXPOSE 80

CMD ["apache2-foreground"]
