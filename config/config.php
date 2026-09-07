<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('DOMAIN', 'http://localhost/glorefy');

function env($key, $default = null) {
    $value = getenv($key);
    return $value === false ? $default : $value;
}

function load_env($path = null) {
    static $loaded = false;
    if ($loaded) {
        return;
    }
    $loaded = true;

    $file = $path ?: __DIR__ . '/../.env';
    if (!is_file($file)) {
        return;
    }

    foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0 || strpos($line, '=') === false) {
            continue;
        }
        if (strpos($line, 'export ') === 0) {
            $line = substr($line, 7);
        }
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        $len = strlen($value);
        if ($len >= 2 && (($value[0] === '"' && $value[$len - 1] === '"') || ($value[0] === "'" && $value[$len - 1] === "'"))) {
            $value = substr($value, 1, -1);
        }
        if ($key !== '') {
            putenv($key . '=' . $value);
        }
    }
}

load_env();

define('DB_NAME', env('DB_NAME', ''));
define('DB_HOST', env('DB_HOST', '127.0.0.1'));
define('DB_PORT', (int) env('DB_PORT', 3306));
define('DB_USER', env('DB_USER', ''));
define('DB_PASS', env('DB_PASS', ''));

function db() {
    static $conn = null;
    if ($conn === null) {
        // FIX 2: Pass DB_PORT as the 5th argument to mysqli
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
        if ($conn->connect_error) {
            die('Database connection failed: ' . $conn->connect_error);
        }
        $conn->set_charset('utf8mb4');
    }
    return $conn;
}

function pdo_db() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            // FIX 3: Append ;port= to the PDO connection string
            $pdo = new PDO(
                'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4',
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            die('PDO connection failed: ' . $e->getMessage());
        }
    }
    return $pdo;
}

function slugify($text) {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    $text = trim($text, '-');
    return $text === '' ? 'product' : $text;
}

// Generate a unique, URL-safe slug for a product name
function generate_product_slug($name, $exclude_id = 0) {
    $base = slugify($name);
    $slug = $base;
    $conn = db();
    $i = 1;
    while (true) {
        $stmt = $conn->prepare("SELECT COUNT(*) AS c FROM products WHERE product_slug = ? AND product_id <> ?");
        $stmt->bind_param("si", $slug, $exclude_id);
        $stmt->execute();
        $count = (int) $stmt->get_result()->fetch_assoc()['c'];
        if ($count === 0) {
            return $slug;
        }
        $i++;
        $slug = $base . '-' . $i;
    }
}

// Build a friendly slug-based URL for a product row
function product_url($product) {
    $slug = isset($product['product_slug']) ? trim($product['product_slug']) : '';
    if ($slug !== '') {
        return DOMAIN . '/products/show.php?slug=' . urlencode($slug);
    }
    return DOMAIN . '/products/show.php?id=' . (int) $product['product_id'];
}

// Storefront configuration helpers (site_settings / store_reviews)
require_once __DIR__ . '/../includes/store-config.php';
