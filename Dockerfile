FROM php:8.3.19-apache

# Update package lists and install required dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev \
    curl \
    postgresql-client \
    geoipupdate \
    && rm -rf /var/lib/apt/lists/*

# Configure and install the PostgreSQL extensions
RUN docker-php-ext-configure pgsql --with-pgsql=/usr/local/pgsql && \
    docker-php-ext-install pgsql pdo_pgsql

# Enable Apache modules required by Tirreno
RUN a2enmod rewrite headers

# Create necessary directories with proper permissions
RUN mkdir -p /config /var/www/html/tmp /usr/share/GeoIP && \
    chmod -R 0777 /config && \
    chmod -R 0777 /var/www/html/tmp && \
    chmod -R 0755 /usr/share/GeoIP

# Set up GeoIP update configuration
COPY GeoIP.conf /etc/GeoIP.conf
RUN geoipupdate

# Copy Tirreno source code into the container
COPY . /var/www/html/

# Ensure correct file permissions
RUN chown -R www-data:www-data /var/www/html

# Create a startup script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Expose port 80
EXPOSE 80

# Start with our entrypoint script
ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]