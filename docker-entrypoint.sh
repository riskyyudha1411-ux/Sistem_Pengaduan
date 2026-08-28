#!/bin/bash
set -e

# Replace Apache's default port (80) with the PORT env var set by Render
sed -i "s/Listen 80/Listen ${PORT:-10000}/g" /etc/apache2/ports.conf
sed -i "s/:80/:${PORT:-10000}/g" /etc/apache2/sites-available/*.conf

# Start Apache in the foreground
exec apache2-foreground
