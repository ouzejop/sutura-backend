#!/bin/bash
set -e

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ]; then
  export APP_KEY="base64:$(php -r 'echo base64_encode(random_bytes(32));')"
  echo "========================================"
  echo "APP_KEY generated: $APP_KEY"
  echo "Copy this value into Render env vars!"
  echo "========================================"
fi

# Map Render DATABASE_URL to individual DB vars (PostgreSQL)
if [ -n "$DATABASE_URL" ]; then
  # Parse postgres://user:password@host:port/dbname
  export DB_CONNECTION=pgsql
  export DB_HOST=$(echo "$DATABASE_URL" | sed -e 's/^.*@//' -e 's/:.*//' -e 's/\/.*//')
  export DB_PORT=$(echo "$DATABASE_URL" | sed -e 's/^.*@//' -e 's/^[^:]*://' -e 's/\/.*//')
  export DB_DATABASE=$(echo "$DATABASE_URL" | sed -e 's/.*\///')
  export DB_USERNAME=$(echo "$DATABASE_URL" | sed -e 's/^.*:\/\///' -e 's/:.*//')
  export DB_PASSWORD=$(echo "$DATABASE_URL" | sed -e 's/^[^:]*:\/\/[^:]*://' -e 's/@.*//')
fi

# Cache config for performance
php artisan config:cache
php artisan route:cache

# Run migrations (and seed on first deploy)
php artisan migrate --force

# Seed demo users only if the users table is empty
USER_COUNT=$(php artisan tinker --execute="echo App\Models\User::count();" 2>/dev/null | tail -1 || echo "1")
if [ "$USER_COUNT" = "0" ]; then
  php artisan db:seed --force
  echo "Database seeded with demo users."
fi

echo "Starting server on port ${PORT:-10000}..."
exec php artisan serve --host=0.0.0.0 --port="${PORT:-10000}"
