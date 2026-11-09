FROM serversideup/php:8.4-fpm-nginx

# Switch to root to configure PHP and install packages
USER root

# Copy custom PHP configuration (upload limits, memory, timezone)
COPY docker/php/local.ini /usr/local/etc/php/conf.d/local.ini

# Install MariaDB client for direct CLI database operations if needed
RUN apt-get update && apt-get install -y --no-install-recommends \
    mariadb-client \
    && rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /var/www/html

# Copy application source code with correct ownership
COPY --chown=www-data:www-data . /var/www/html

# Ensure writable directories have appropriate permissions
RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Switch back to non-root user
USER www-data

# Expose internal NGINX port
EXPOSE 8080
