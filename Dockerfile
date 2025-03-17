FROM php:8.3.19-apache

# Update package lists and install PostgreSQL development libraries and curl
RUN apt-get update && apt-get install -y libpq-dev curl

# Configure and install the PostgreSQL extensions:
# - The configuration step tells PHP where to find PostgreSQL headers.
# - We install both the pgsql and pdo_pgsql extensions.
RUN docker-php-ext-configure pgsql --with-pgsql=/usr/local/pgsql && \
    docker-php-ext-install pgsql pdo_pgsql

# Enable Apache modules required by Tirreno
RUN a2enmod rewrite headers

# Create and set permissions for the configuration folder
RUN mkdir -p /config && chown -R www-data:www-data /config

# Copy Tirreno source code into the container
COPY . /var/www/html/

# Ensure correct file permissions
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]