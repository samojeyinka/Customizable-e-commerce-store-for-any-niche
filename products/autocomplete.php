<?php
// products/autocomplete.php - Live search suggestions (JSON only).
// Searches products + categories + brands from the database.
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/connect.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$q = trim((string)($_GET['q'] ?? ''));
$q = mb_substr($q, 0, 60);

$empty = ['ok' => false, 'term' => $q, 'message' => 'Type at least 2 characters.', 'products' => [], 'categories' => [], 'brands' => [], 'total' => 0, 'domain' => DOMAIN];

if (mb_strlen($q) < 2) {
    echo json_encode($empty, JSON_UNESCAPED_UNICODE);
    exit;
}

// Escape LIKE wildcards so user input is matched literally
$esc = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $q);
$like = '%' . $esc . '%';
$prefix = $esc . '%';

$out = ['ok' => true, 'term' => $q, 'domain' => DOMAIN, 'products' => [], 'categories' => [], 'brands' => [], 'total' => 0];

// 1) Matching products (ranked: name-prefix > name-contains > details/colors > category/brand > variant)
$sql = "SELECT p.product_id, p.product_name, p.product_slug, p.is_featured, p.colors,
               c.category_title, b.brand_title,
               i.image_path AS main_image,
               MIN(v.original_price) AS min_price,
               MIN(v.discount_price) AS min_discount
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.category_id
        LEFT JOIN brands b ON p.brand_id = b.brand_id
        LEFT JOIN product_images i ON p.product_id = i.product_id AND i.is_main = 1
        LEFT JOIN product_variants v ON p.product_id = v.product_id
        WHERE (p.product_status IS NULL OR p.product_status = 'active')
          AND (
               p.product_name LIKE ?
            OR p.details LIKE ?
            OR p.colors LIKE ?
            OR c.category_title LIKE ?
            OR b.brand_title LIKE ?
            OR EXISTS (SELECT 1 FROM product_variants pv WHERE pv.product_id = p.product_id AND (pv.size LIKE ? OR pv.texture LIKE ?))
          )
        GROUP BY p.product_id, p.product_name, p.product_slug, p.is_featured, p.colors,
                 c.category_title, b.brand_title, i.image_path
        ORDER BY
          (CASE
             WHEN p.product_name LIKE ? THEN 0
             WHEN p.product_name LIKE ? THEN 1
             WHEN (p.details LIKE ? OR p.colors LIKE ?) THEN 2
             WHEN (c.category_title LIKE ? OR b.brand_title LIKE ?) THEN 3
             ELSE 4
           END) ASC,
          p.is_featured DESC,
          p.date_added DESC
        LIMIT 6";

$stmt = mysqli_prepare($con, $sql);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, 'sssssssssssss', $like, $like, $like, $like, $like, $like, $like, $prefix, $like, $like, $like, $like, $like);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($res)) {
        $out['products'][] = [
            'product_id' => (int)$row['product_id'],
            'product_name' => $row['product_name'],
            'slug' => $row['product_slug'],
            'category_title' => $row['category_title'],
            'brand_title' => $row['brand_title'],
            'image' => $row['main_image'],
            'price' => $row['min_price'] !== null ? (float)$row['min_price'] : 0,
            'discount_price' => $row['min_discount'] !== null ? (float)$row['min_discount'] : null,
            'colors' => $row['colors'],
        ];
    }
    $out['total'] += count($out['products']);
}

// 2) Matching categories (only ones that still have active products)
$stmt = mysqli_prepare($con, "SELECT c.category_id, c.category_title
                              FROM categories c
                              WHERE c.category_title LIKE ?
                              ORDER BY c.category_title LIKE ? DESC, c.category_title ASC
                              LIMIT 3");
if ($stmt) {
    mysqli_stmt_bind_param($stmt, 'ss', $prefix, $prefix);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($res)) {
        $out['categories'][] = ['category_id' => (int)$row['category_id'], 'category_label' => $row['category_title']];
    }
}

// 3) Matching brands (only ones that still have active products)
$stmt = mysqli_prepare($con, "SELECT b.brand_id, b.brand_title
                              FROM brands b
                              WHERE b.brand_title LIKE ?
                              ORDER BY b.brand_title LIKE ? DESC, b.brand_title ASC
                              LIMIT 3");
if ($stmt) {
    mysqli_stmt_bind_param($stmt, 'ss', $prefix, $prefix);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($res)) {
        $out['brands'][] = ['brand_id' => (int)$row['brand_id'], 'brand_label' => $row['brand_title']];
    }
}

echo json_encode($out, JSON_UNESCAPED_UNICODE);