<?php
session_start();

function get_users_file_path(): string
{
    return __DIR__ . '/../data/users.json';
}

function get_uploads_dir(): string
{
    return __DIR__ . '/../uploads/verification';
}

function get_products_file_path(): string
{
    return __DIR__ . '/../data/products.json';
}

function get_motorcycles_file_path(): string
{
    return __DIR__ . '/../data/motorcycles.json';
}

function get_reservations_file_path(): string
{
    return __DIR__ . '/../data/reservations.json';
}

function get_orders_file_path(): string
{
    return __DIR__ . '/../data/orders.json';
}

function load_json(string $filePath, array $default = []): array
{
    if (!file_exists($filePath)) {
        return $default;
    }

    $content = file_get_contents($filePath);
    $data = json_decode($content, true);

    return is_array($data) ? $data : $default;
}

function save_json(string $filePath, array $data): bool
{
    $dir = dirname($filePath);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    return (bool) file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

function load_users(): array
{
    $file = get_users_file_path();

    if (!file_exists($file)) {
        return [];
    }

    $content = file_get_contents($file);
    $users = json_decode($content, true);

    return is_array($users) ? $users : [];
}

function save_users(array $users): bool
{
    $dir = dirname(get_users_file_path());
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    return (bool) file_put_contents(get_users_file_path(), json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

function load_products(): array
{
    return load_json(get_products_file_path(), []);
}

function save_products(array $products): bool
{
    return save_json(get_products_file_path(), $products);
}

function load_motorcycles(): array
{
    return load_json(get_motorcycles_file_path(), []);
}

function save_motorcycles(array $motorcycles): bool
{
    return save_json(get_motorcycles_file_path(), $motorcycles);
}

function load_reservations(): array
{
    return load_json(get_reservations_file_path(), []);
}

function save_reservations(array $reservations): bool
{
    return save_json(get_reservations_file_path(), $reservations);
}

function load_orders(): array
{
    return load_json(get_orders_file_path(), []);
}

function save_orders(array $orders): bool
{
    return save_json(get_orders_file_path(), $orders);
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

ensure_demo_accounts();
ensure_demo_products();
ensure_demo_motorcycles();
