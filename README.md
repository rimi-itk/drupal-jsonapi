## Installation

Create local settings file with database connection:

```sh
cat <<'EOF' > web/sites/default/settings.local.php
<?php
$databases['default']['default'] = [
 'database' => getenv('DATABASE_DATABASE') ?: 'db',
 'username' => getenv('DATABASE_USERNAME') ?: 'db',
 'password' => getenv('DATABASE_PASSWORD') ?: 'db',
 'host' => getenv('DATABASE_HOST') ?: 'mariadb',
 'port' => getenv('DATABASE_PORT') ?: '',
 'driver' => getenv('DATABASE_DRIVER') ?: 'mysql',
 'prefix' => '',
];
EOF
```

```sh
docker-compose up -d
docker-compose exec phpfpm composer install
docker-compose exec phpfpm vendor/bin/drush --yes site:install minimal --config-dir=../config/sync
# Get the site url
echo "http://$(docker-compose port nginx 80)"
# Get admin sign in url
docker-compose exec phpfpm vendor/bin/drush --yes --uri="http://$(docker-compose port nginx 80)" user:login
```

### Using `symfony` binary

```sh
docker-compose up -d
symfony composer install
symfony php vendor/bin/drush --yes site:install minimal --config-dir=../config/sync
# Start the server
symfony local:server:start --daemon
# Get admin sign in url
symfony php vendor/bin/drush --uri=http://127.0.0.1:8000 user:login
```
