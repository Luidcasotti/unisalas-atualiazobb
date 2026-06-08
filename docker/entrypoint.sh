#!/usr/bin/env sh
set -e

cd /var/www/html

mkdir -p storage/app/public storage/framework/cache storage/framework/sessions storage/framework/testing storage/framework/views storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

if [ -z "${APP_KEY:-}" ]; then
    export APP_KEY="$(php artisan key:generate --show --no-interaction)"
fi

php artisan config:clear --no-interaction >/dev/null 2>&1 || true
php artisan view:clear --no-interaction >/dev/null 2>&1 || true

echo "Aguardando banco de dados em ${DB_HOST}:${DB_PORT}..."
php -r '
$host = getenv("DB_HOST") ?: "db";
$port = getenv("DB_PORT") ?: "3306";
$db = getenv("DB_DATABASE") ?: "reserva_salas";
$user = getenv("DB_USERNAME") ?: "unisalas";
$pass = getenv("DB_PASSWORD") ?: "";
$deadline = time() + 90;
do {
    try {
        new PDO("mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 5,
        ]);
        exit(0);
    } catch (Throwable $e) {
        if (time() >= $deadline) {
            fwrite(STDERR, "Banco indisponivel: ".$e->getMessage().PHP_EOL);
            exit(1);
        }
        sleep(3);
    }
} while (true);
'

if php -r '
$host = getenv("DB_HOST") ?: "db";
$port = getenv("DB_PORT") ?: "3306";
$db = getenv("DB_DATABASE") ?: "reserva_salas";
$user = getenv("DB_USERNAME") ?: "unisalas";
$pass = getenv("DB_PASSWORD") ?: "";
$pdo = new PDO("mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4", $user, $pass);
$stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = ? AND table_name = ?");
$stmt->execute([$db, "users"]);
exit((int) $stmt->fetchColumn() > 0 ? 0 : 1);
'; then
    php artisan migrate --force --no-interaction
    php artisan db:seed --class=UserAdminSeeder --force --no-interaction
else
    php artisan migrate --force --no-interaction
    php artisan db:seed --force --no-interaction
fi

php artisan config:cache --no-interaction
php artisan view:cache --no-interaction

exec "$@"
