#!/bin/bash
set -e

# Set permissions for /config directory
chmod -R 0777 /config
chown -R www-data:www-data /config

# Set permissions for tmp directory
mkdir -p /var/www/html/tmp
chmod -R 0777 /var/www/html/tmp
chown -R www-data:www-data /var/www/html/tmp

# Wait for the database to be ready
echo "Waiting for PostgreSQL to be ready..."
max_retries=30
counter=0
until pg_isready -h db -U tirreno_user -d tirreno; do
  >&2 echo "PostgreSQL is unavailable - sleeping"
  counter=$((counter+1))
  if [ $counter -gt $max_retries ]; then
    >&2 echo "PostgreSQL is still unavailable after $max_retries retries. Giving up."
    exit 1
  fi
  sleep 1
done
echo "PostgreSQL is up - executing command"

# Execute CMD
exec "$@"