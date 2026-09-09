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

// Detect if a stored image path is an external URL
function is_external_image_url($path) {
    return is_string($path) && preg_match('#^https?://#i', trim($path)) === 1;
}

// Build a renderable URL for a product image path (external URL or local file)
function product_image_url($path, $base = null) {
    if ($base === null) {
        $base = DOMAIN . '/assets/products/';
    }
    $path = is_string($path) ? trim($path) : '';
    if ($path === '') {
        return '';
    }
    if (is_external_image_url($path)) {
        return $path;
    }
    return $base . $path;
}

// Insert a list of external image URLs (comma/new-line separated) as gallery images
function product_image_urls_to_rows($con, $product_id, $urls_string) {
    $urls = preg_split('/[\r\n,]+/', (string) $urls_string);
    $order_query = "SELECT MAX(display_order) as max_order FROM product_images WHERE product_id = ?";
    $stmt_order = mysqli_prepare($con, $order_query);
    mysqli_stmt_bind_param($stmt_order, "i", $product_id);
    mysqli_stmt_execute($stmt_order);
    $order_result = mysqli_stmt_get_result($stmt_order);
    $order_row = mysqli_fetch_assoc($order_result);
    $last_order = ($order_row['max_order'] ? (int) $order_row['max_order'] + 1 : 2);
    foreach ($urls as $url) {
        $url = trim($url);
        if ($url === '' || !is_external_image_url($url)) {
            continue;
        }
        $insert_image = "INSERT INTO product_images (product_id, image_path, is_main, display_order) VALUES (?, ?, 0, ?)";
        $stmt_image = mysqli_prepare($con, $insert_image);
        mysqli_stmt_bind_param($stmt_image, "isi", $product_id, $url, $last_order);
        mysqli_stmt_execute($stmt_image);
        $last_order++;
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
