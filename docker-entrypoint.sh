#!/bin/bash
set -e

# Set permissions for /config directory
chmod -R 0777 /config
chown -R www-data:www-data /config

# Execute CMD
exec "$@"