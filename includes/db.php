<?php

function get_db_connection(): ?PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $driver = strtolower((string) (getenv('DB_DRIVER') ?: 'mysql'));

    try {
        if ($driver !== 'mysql') {
            throw new RuntimeException('Only MySQL is supported for this app.');
        }

        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $port = getenv('DB_PORT') ?: '3306';
        $database = getenv('DB_DATABASE') ?: 'gianecycle';
        $username = getenv('DB_USERNAME') ?: 'root';
        $password = getenv('DB_PASSWORD') ?: '';

        $pdo = new PDO(
            "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4",
            $username,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4',
            ]
        );

        return $pdo;
    } catch (Throwable $exception) {
        error_log('GianeCycle DB connection failed: ' . $exception->getMessage());
        return null;
    }
}

function database_is_available(): bool
{
    return get_db_connection() instanceof PDO;
}

function ensure_database(): void
{
    $pdo = get_db_connection();
    if (!$pdo) {
        return;
    }

    $schema = [
        "CREATE TABLE IF NOT EXISTS users (
            id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            role VARCHAR(50) NOT NULL DEFAULT 'user',
            verified TINYINT(1) NOT NULL DEFAULT 0,
            verification_photo VARCHAR(255) NULL,
            created_at DATETIME NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        "CREATE TABLE IF NOT EXISTS products (
            id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            price DECIMAL(10,2) NOT NULL,
            icon VARCHAR(50) NOT NULL DEFAULT '🧰'
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        "CREATE TABLE IF NOT EXISTS motorcycles (
            id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            brand VARCHAR(255) NOT NULL,
            daily_rate DECIMAL(10,2) NOT NULL,
            status VARCHAR(50) NOT NULL DEFAULT 'available',
            icon VARCHAR(50) NOT NULL DEFAULT '🏍️'
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        "CREATE TABLE IF NOT EXISTS reservations (
            id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            user_email VARCHAR(255) NOT NULL,
            motorcycle VARCHAR(255) NOT NULL,
            start_date DATE NOT NULL,
            end_date DATE NOT NULL,
            renter_name VARCHAR(255) NOT NULL,
            renter_phone VARCHAR(50) NOT NULL,
            renter_address TEXT NOT NULL,
            status VARCHAR(50) NOT NULL DEFAULT 'Pending',
            created_at DATETIME NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        "CREATE TABLE IF NOT EXISTS orders (
            id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            user_email VARCHAR(255) NOT NULL,
            total DECIMAL(10,2) NOT NULL,
            items JSON NOT NULL,
            created_at DATETIME NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    ];

    foreach ($schema as $statement) {
        try {
            $pdo->exec($statement);
        } catch (Throwable $exception) {
            // Ignore structure errors here; the app should still run with JSON fallback if DB creation fails.
        }
    }
}
