#!/bin/sh
set -e

# Clear configurations
echo "Clearing configurations..."

# Run the default command (e.g., php-fpm or bash)
exec "$@"
