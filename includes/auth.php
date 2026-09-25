<?php
session_start();
require_once __DIR__ . '/db.php';

function get_uploads_dir(): string
{
    return __DIR__ . '/../uploads/verification';
}

function require_database(): void
{
    if (!database_is_available()) {
        http_response_code(503);
        echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Database unavailable</title></head><body style="font-family: Arial, sans-serif; max-width: 700px; margin: 80px auto; padding: 24px; color: #1f2937;">';
        echo '<h1>Service unavailable</h1>';
        echo '<p>The application could not connect to the MySQL database. Please check the database configuration and try again.</p>';
        echo '</body></html>';
        exit;
    }
}

function db_rows_to_array(PDOStatement $statement): array
{
    $rows = $statement->fetchAll();
    return is_array($rows) ? $rows : [];
}

function load_users(): array
{
    $pdo = get_db_connection();
    if (!$pdo) {
        return [];
    }

    try {
        $statement = $pdo->query('SELECT * FROM users ORDER BY id ASC');
        return db_rows_to_array($statement);
    } catch (Throwable $exception) {
        return [];
    }
}

function save_users(array $users): bool
{
    $pdo = get_db_connection();
    if (!$pdo) {
        return false;
    }

    try {
        $pdo->exec('DELETE FROM users');

        $insert = $pdo->prepare(
            'INSERT INTO users (id, name, email, password, role, verified, verification_photo, created_at) VALUES (:id, :name, :email, :password, :role, :verified, :verification_photo, :created_at)'
        );

        foreach ($users as $user) {
            $insert->execute([
                ':id' => (int) ($user['id'] ?? 0),
                ':name' => (string) ($user['name'] ?? ''),
                ':email' => strtolower((string) ($user['email'] ?? '')),
                ':password' => (string) ($user['password'] ?? ''),
                ':role' => (string) (($user['role'] ?? 'user')),
                ':verified' => ((bool) ($user['verified'] ?? false)) ? 1 : 0,
                ':verification_photo' => $user['verification_photo'] ?? null,
                ':created_at' => (string) ($user['created_at'] ?? date('Y-m-d H:i:s')),
            ]);
        }

        return true;
    } catch (Throwable $exception) {
        return false;
    }
}

function load_products(): array
{
    $pdo = get_db_connection();
    if (!$pdo) {
        return [];
    }

    try {
        $statement = $pdo->query('SELECT * FROM products ORDER BY id ASC');
        return db_rows_to_array($statement);
    } catch (Throwable $exception) {
        return [];
    }
}

function save_products(array $products): bool
{
    $pdo = get_db_connection();
    if (!$pdo) {
        return false;
    }

    try {
        $pdo->exec('DELETE FROM products');
        $insert = $pdo->prepare('INSERT INTO products (id, name, price, icon) VALUES (:id, :name, :price, :icon)');

        foreach ($products as $product) {
            $insert->execute([
                ':id' => (int) ($product['id'] ?? 0),
                ':name' => (string) ($product['name'] ?? ''),
                ':price' => (float) ($product['price'] ?? 0),
                ':icon' => (string) (($product['icon'] ?? '🧰')),
            ]);
        }

        return true;
    } catch (Throwable $exception) {
        return false;
    }
}

function load_motorcycles(): array
{
    $pdo = get_db_connection();
    if (!$pdo) {
        return [];
    }

    try {
        $statement = $pdo->query('SELECT * FROM motorcycles ORDER BY id ASC');
        return db_rows_to_array($statement);
    } catch (Throwable $exception) {
        return [];
    }
}

function save_motorcycles(array $motorcycles): bool
{
    $pdo = get_db_connection();
    if (!$pdo) {
        return false;
    }

    try {
        $pdo->exec('DELETE FROM motorcycles');
        $insert = $pdo->prepare('INSERT INTO motorcycles (id, name, brand, daily_rate, status, icon) VALUES (:id, :name, :brand, :daily_rate, :status, :icon)');

        foreach ($motorcycles as $motorcycle) {
            $insert->execute([
                ':id' => (int) ($motorcycle['id'] ?? 0),
                ':name' => (string) ($motorcycle['name'] ?? ''),
                ':brand' => (string) ($motorcycle['brand'] ?? ''),
                ':daily_rate' => (float) ($motorcycle['daily_rate'] ?? 0),
                ':status' => (string) (($motorcycle['status'] ?? 'available')),
                ':icon' => (string) (($motorcycle['icon'] ?? '🏍️')),
            ]);
        }

        return true;
    } catch (Throwable $exception) {
        return false;
    }
}

function load_reservations(): array
{
    $pdo = get_db_connection();
    if (!$pdo) {
        return [];
    }

    try {
        $statement = $pdo->query('SELECT * FROM reservations ORDER BY id ASC');
        return db_rows_to_array($statement);
    } catch (Throwable $exception) {
        return [];
    }
}

function save_reservations(array $reservations): bool
{
    $pdo = get_db_connection();
    if (!$pdo) {
        return false;
    }

    try {
        $pdo->exec('DELETE FROM reservations');
        $insert = $pdo->prepare(
            'INSERT INTO reservations (id, user_email, motorcycle, start_date, end_date, renter_name, renter_phone, renter_address, status, created_at) VALUES (:id, :user_email, :motorcycle, :start_date, :end_date, :renter_name, :renter_phone, :renter_address, :status, :created_at)'
        );

        foreach ($reservations as $reservation) {
            $insert->execute([
                ':id' => (int) ($reservation['id'] ?? 0),
                ':user_email' => (string) ($reservation['user_email'] ?? ''),
                ':motorcycle' => (string) ($reservation['motorcycle'] ?? ''),
                ':start_date' => (string) ($reservation['start_date'] ?? ''),
                ':end_date' => (string) ($reservation['end_date'] ?? ''),
                ':renter_name' => (string) ($reservation['renter_name'] ?? ''),
                ':renter_phone' => (string) ($reservation['renter_phone'] ?? ''),
                ':renter_address' => (string) ($reservation['renter_address'] ?? ''),
                ':status' => (string) (($reservation['status'] ?? 'Pending')),
                ':created_at' => (string) ($reservation['created_at'] ?? date('Y-m-d H:i:s')),
            ]);
        }

        return true;
    } catch (Throwable $exception) {
        return false;
    }
}

function load_orders(): array
{
    $pdo = get_db_connection();
    if (!$pdo) {
        return [];
    }

    try {
        $statement = $pdo->query('SELECT * FROM orders ORDER BY id ASC');
        $orders = db_rows_to_array($statement);

        foreach ($orders as &$order) {
            if (!empty($order['items']) && is_string($order['items'])) {
                $decoded = json_decode($order['items'], true);
                $order['items'] = is_array($decoded) ? $decoded : [];
            }
        }
        unset($order);

        return $orders;
    } catch (Throwable $exception) {
        return [];
    }
}

function save_orders(array $orders): bool
{
    $pdo = get_db_connection();
    if (!$pdo) {
        return false;
    }

    try {
        $pdo->exec('DELETE FROM orders');
        $insert = $pdo->prepare('INSERT INTO orders (id, user_email, total, items, created_at) VALUES (:id, :user_email, :total, :items, :created_at)');

        foreach ($orders as $order) {
            $insert->execute([
                ':id' => (int) ($order['id'] ?? 0),
                ':user_email' => (string) ($order['user_email'] ?? ''),
                ':total' => (float) ($order['total'] ?? 0),
                ':items' => json_encode($order['items'] ?? [], JSON_UNESCAPED_SLASHES),
                ':created_at' => (string) ($order['created_at'] ?? date('Y-m-d H:i:s')),
            ]);
        }

        return true;
    } catch (Throwable $exception) {
        return false;
    }
}

function ensure_demo_products(): void
{
    $products = load_products();

    if (!empty($products)) {
        return;
    }

    $products = [
        ['id' => 1, 'name' => 'Brake Pad Kit', 'price' => 1250, 'icon' => '🧰'],
        ['id' => 2, 'name' => 'Chain Set', 'price' => 2450, 'icon' => '🔧'],
        ['id' => 3, 'name' => 'Motorcycle Tire', 'price' => 3600, 'icon' => '🛞'],
        ['id' => 4, 'name' => 'Battery', 'price' => 2980, 'icon' => '🔋'],
        ['id' => 5, 'name' => 'LED Lights', 'price' => 1690, 'icon' => '💡'],
        ['id' => 6, 'name' => 'Safety Helmet', 'price' => 1990, 'icon' => '🧢']
    ];

    save_products($products);
}

function ensure_demo_motorcycles(): void
{
    $motorcycles = load_motorcycles();

    if (!empty($motorcycles)) {
        return;
    }

    $motorcycles = [
        ['id' => 1, 'name' => 'Honda Click 160', 'brand' => 'Honda', 'daily_rate' => 1250, 'status' => 'available', 'icon' => '🏍️'],
        ['id' => 2, 'name' => 'Yamaha Mio Sporty', 'brand' => 'Yamaha', 'daily_rate' => 1050, 'status' => 'available', 'icon' => '🏍️'],
        ['id' => 3, 'name' => 'Kawasaki Raider', 'brand' => 'Kawasaki', 'daily_rate' => 2400, 'status' => 'available', 'icon' => '🏍️']
    ];

    save_motorcycles($motorcycles);
}

function ensure_demo_accounts(): void
{
    $users = load_users();

    $hasAdmin = false;
    $hasUser = false;

    foreach ($users as $user) {
        if (($user['role'] ?? '') === 'admin') {
            $hasAdmin = true;
        }

        if (($user['email'] ?? '') === 'user@gianecycle.com') {
            $hasUser = true;
        }
    }

    if (!$hasAdmin) {
        $users[] = [
            'id' => 1,
            'name' => 'Shop Admin',
            'email' => 'admin@gianecycle.com',
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'role' => 'admin',
            'verified' => true,
            'verification_photo' => null,
            'created_at' => date('Y-m-d H:i:s')
        ];
    }

    if (!$hasUser) {
        $users[] = [
            'id' => 2,
            'name' => 'Demo User',
            'email' => 'user@gianecycle.com',
            'password' => password_hash('user123', PASSWORD_DEFAULT),
            'role' => 'user',
            'verified' => false,
            'verification_photo' => null,
            'created_at' => date('Y-m-d H:i:s')
        ];
    }

    save_users($users);
}

function require_login(): void
{
    if (!isset($_SESSION['user'])) {
        header('Location: login.php');
        exit;
    }
}

function require_role(string $role): void
{
    require_login();

    if (($_SESSION['user']['role'] ?? '') !== $role) {
        header('Location: index.php');
        exit;
    }
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function refresh_session_user(): void
{
    if (empty($_SESSION['user']['email'])) {
        return;
    }

    $email = strtolower(trim((string) $_SESSION['user']['email']));
    $users = load_users();

    foreach ($users as $user) {
        if (strtolower(trim((string) ($user['email'] ?? ''))) === $email) {
            $_SESSION['user'] = $user;
            return;
        }
    }
}

require_database();
ensure_database();
ensure_demo_accounts();
ensure_demo_products();
ensure_demo_motorcycles();
