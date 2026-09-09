<?php
/**
 * Glorefy Catalog Seeder
 * ----------------------
 * Seeds categories, brands ("tags") and products into the database.
 *
 * Idempotent: safe to run as many times as you like. Lookups use natural
 * keys (category_slug, brand_title, product_slug); products found this way
 * have their row updated and their variants + images replaced, so the seed
 * always reflects this file. Existing rows are never deleted.
 *
 * Usage:
 *   CLI:      php database/seeds/seed_catalog.php
 *   Browser:  /glorefy/database/seeds/seed_catalog.php
 *
 * After a DB wipe: re-import database/schema.sql, then run this script.
 */

require_once __DIR__ . '/../../config/config.php';

$IS_CLI = (PHP_SAPI === 'cli');
if (!$IS_CLI) {
    echo '<pre>';
}

function seed_line(string $msg): void {
    echo $msg . "\n";
}

try {
    $conn = db();
} catch (Throwable $e) {
    seed_line('Database error: ' . $e->getMessage());
    exit(1);
}

$check = $conn->query("SHOW TABLES LIKE 'products'");
if (!$check || $check->num_rows === 0) {
    seed_line("Products table not found in database '{$conn->real_escape_string(DB_NAME)}'.");
    seed_line('Import database/schema.sql first, e.g.: mysql -u' . DB_USER . ' -p ' . DB_NAME . ' < database/schema.sql');
    exit(1);
}

// ---------------------------------------------------------------------------
// Data (single source of truth)
// ---------------------------------------------------------------------------

$categories = [
    ['title' => 'Cleansers',             'slug' => 'cleansers',      'desc' => 'Gentle cleansers, washes and toning cleansers for every skin type.'],
    ['title' => 'Moisturizers',          'slug' => 'moisturizers',   'desc' => 'Daily hydration - creams, gels and lotions that lock in moisture.'],
    ['title' => 'Serums & Treatments',   'slug' => 'serums',         'desc' => 'Concentrated treatment serums and facial oils targeting specific concerns.'],
    ['title' => 'Sunscreen',             'slug' => 'sunscreen',      'desc' => 'Broad spectrum protection for daily wear, indoors and outdoors.'],
    ['title' => 'Body Care',             'slug' => 'body-care',      'desc' => 'Butters, washes and scrubs that nourish skin from head to toe.'],
    ['title' => 'Face Masks',            'slug' => 'face-masks',     'desc' => 'Masks and overnight treatments for an instant glow reset.'],
];

$brands = [
    ['title' => 'Glow Essentials'],
    ['title' => 'DermaPure'],
    ['title' => 'Rose Petal'],
    ['title' => 'Natural Botanics'],
    ['title' => 'SkinScience'],
    ['title' => 'Velvet Glow'],
    ['title' => 'Purely Fresh'],
    ['title' => 'Rituals & Roots'],
    ['title' => 'Nubian Glow'],
    ['title' => 'Botanic Alchemy'],
];

$products = [
    [
        'name' => 'Gentle Foaming Cleanser', 'slug' => 'gentle-foaming-cleanser', 'sku' => 'CLN-0001',
        'category' => 'cleansers', 'brand' => 'Glow Essentials', 'colors' => 'clear',
        'details' => 'A sulfate-free foaming cleanser that removes oil, makeup and impurities without stripping the skin barrier.',
        'featured' => 0, 'status' => 'active',
        'variants' => [
            ['size' => '150ml', 'texture' => 'foam',   'qty' => 40, 'price' => 6500,  'sale' => null, 'status' => 'available'],
            ['size' => '300ml', 'texture' => 'cream',  'qty' => 35, 'price' => 9000,  'sale' => null, 'status' => 'available'],
        ],
    ],
    [
        'name' => 'Charcoal Deep Clean Wash', 'slug' => 'charcoal-deep-clean-wash', 'sku' => 'CLN-0002',
        'category' => 'cleansers', 'brand' => 'DermaPure', 'colors' => 'black',
        'details' => 'Activated charcoal draws out impurities and excess oil while salicylic acid keeps pores clear and smooth.',
        'featured' => 0, 'status' => 'active',
        'variants' => [
            ['size' => '200ml', 'texture' => 'gel', 'qty' => 25, 'price' => 7500, 'sale' => null, 'status' => 'available'],
            ['size' => '400ml', 'texture' => 'gel', 'qty' => 30, 'price' => 9800, 'sale' => 8900,  'status' => 'available'],
        ],
    ],
    [
        'name' => 'Creamy Milk Cleanser', 'slug' => 'creamy-milk-cleanser', 'sku' => 'CLN-0003',
        'category' => 'cleansers', 'brand' => 'Rose Petal', 'colors' => 'white',
        'details' => 'A pH-balanced milky cleanser enriched with rose water for soft, calm and hydrated skin.',
        'featured' => 0, 'status' => 'active',
        'variants' => [
            ['size' => '200ml', 'texture' => 'milk', 'qty' => 30, 'price' => 6200, 'sale' => null, 'status' => 'available'],
        ],
    ],
    [
        'name' => 'Hydra-Burst Gel Moisturizer', 'slug' => 'hydra-burst-gel-moisturizer', 'sku' => 'MST-0004',
        'category' => 'moisturizers', 'brand' => 'Glow Essentials', 'colors' => 'clear, blue',
        'details' => 'A lightweight, oil-free gel with hyaluronic acid that delivers an instant water-burst feel and 24-hour hydration.',
        'featured' => 1, 'status' => 'active',
        'variants' => [
            ['size' => '50ml',  'texture' => 'gel', 'qty' => 40, 'price' => 8800,  'sale' => null, 'status' => 'available'],
            ['size' => '100ml', 'texture' => 'gel', 'qty' => 12, 'price' => 11500, 'sale' => null, 'status' => 'available'],
        ],
    ],
    [
        'name' => 'Shea Butter Rich Cream', 'slug' => 'shea-butter-rich-cream', 'sku' => 'MST-0005',
        'category' => 'moisturizers', 'brand' => 'Natural Botanics', 'colors' => 'natural',
        'details' => 'Unrefined shea butter whipped with botanical oils for deep nourishment of dry and mature skin.',
        'featured' => 0, 'status' => 'active',
        'variants' => [
            ['size' => '250ml', 'texture' => 'cream', 'qty' => 30, 'price' => 9500, 'sale' => null, 'status' => 'available'],
        ],
    ],
    [
        'name' => 'Vitamin C Brightening Lotion', 'slug' => 'vitamin-c-brightening-lotion', 'sku' => 'MST-0006',
        'category' => 'moisturizers', 'brand' => 'SkinScience', 'colors' => 'orange, yellow',
        'details' => 'Antioxidant-rich lotion with stabilized Vitamin C to fade dark spots and even out skin tone.',
        'featured' => 1, 'status' => 'active',
        'variants' => [
            ['size' => '100ml', 'texture' => 'lotion', 'qty' => 20, 'price' => 11500, 'sale' => null, 'status' => 'available'],
        ],
    ],
    [
        'name' => 'Oatmeal Soothing Moisturizer', 'slug' => 'oatmeal-soothing-moisturizer', 'sku' => 'MST-0007',
        'category' => 'moisturizers', 'brand' => 'Purely Fresh', 'colors' => 'white',
        'details' => 'Colloidal oatmeal and ceramides repair a weakened moisture barrier and calm red, irritated skin.',
        'featured' => 0, 'status' => 'active',
        'variants' => [
            ['size' => '200ml', 'texture' => 'cream', 'qty' => 25, 'price' => 7800, 'sale' => null, 'status' => 'available'],
        ],
    ],
    [
        'name' => 'Hyaluronic Acid Serum', 'slug' => 'hyaluronic-acid-serum', 'sku' => 'SRM-0008',
        'category' => 'serums', 'brand' => 'SkinScience', 'colors' => 'clear',
        'details' => 'Triple-weight hyaluronic acid plumps fine lines and draws moisture deep into the skin.',
        'featured' => 0, 'status' => 'active',
        'variants' => [
            ['size' => '30ml', 'texture' => 'serum', 'qty' => 18, 'price' => 14500, 'sale' => null, 'status' => 'available'],
        ],
    ],
    [
        'name' => 'Vitamin C 15% Serum', 'slug' => 'vitamin-c-15-serum', 'sku' => 'SRM-0009',
        'category' => 'serums', 'brand' => 'SkinScience', 'colors' => 'orange',
        'details' => 'A potent 15% L-ascorbic acid serum that brightens, firms and protects against environmental damage.',
        'featured' => 0, 'status' => 'active',
        'variants' => [
            ['size' => '30ml', 'texture' => 'serum', 'qty' => 22, 'price' => 16000, 'sale' => 14900, 'status' => 'available'],
        ],
    ],
    [
        'name' => 'Niacinamide 10% Serum', 'slug' => 'niacinamide-10-serum', 'sku' => 'SRM-0010',
        'category' => 'serums', 'brand' => 'Glow Essentials', 'colors' => 'clear',
        'details' => '10% niacinamide with zinc reduces the look of pores, controls oil and strengthens the skin barrier.',
        'featured' => 1, 'status' => 'active',
        'variants' => [
            ['size' => '30ml', 'texture' => 'serum', 'qty' => 26, 'price' => 12000, 'sale' => null, 'status' => 'available'],
            ['size' => '60ml', 'texture' => 'serum', 'qty' => 9,  'price' => 17000, 'sale' => null, 'status' => 'available'],
        ],
    ],
    [
        'name' => 'Rosehip Facial Oil', 'slug' => 'rosehip-facial-oil', 'sku' => 'SRM-0011',
        'category' => 'serums', 'brand' => 'Botanic Alchemy', 'colors' => 'amber',
        'details' => 'Cold-pressed rosehip oil rich in essential fatty acids and Vitamin A to brighten and restore texture.',
        'featured' => 0, 'status' => 'active',
        'variants' => [
            ['size' => '30ml', 'texture' => 'oil', 'qty' => 14, 'price' => 13800, 'sale' => null, 'status' => 'available'],
        ],
    ],
    [
        'name' => 'Retinol Night Treatment', 'slug' => 'retinol-night-treatment', 'sku' => 'SRM-0012',
        'category' => 'serums', 'brand' => 'DermaPure', 'colors' => 'cream',
        'details' => 'Encapsulated retinol resurfaces skin overnight, smoothing fine lines and breakouts while you sleep.',
        'featured' => 0, 'status' => 'active',
        'variants' => [
            ['size' => '30ml', 'texture' => 'cream', 'qty' => 11, 'price' => 15500, 'sale' => null, 'status' => 'available'],
        ],
    ],
    [
        'name' => 'Glycolic Acid Toner', 'slug' => 'glycolic-acid-toner', 'sku' => 'SRM-0013',
        'category' => 'serums', 'brand' => 'DermaPure', 'colors' => 'clear',
        'details' => 'A 7% glycolic acid toner that exfoliates away dullness and visibly refines skin texture.',
        'featured' => 0, 'status' => 'active',
        'variants' => [
            ['size' => '200ml', 'texture' => 'liquid', 'qty' => 30, 'price' => 9200, 'sale' => null, 'status' => 'available'],
        ],
    ],
    [
        'name' => 'SPF 50 Invisible Sunscreen', 'slug' => 'spf-50-invisible-sunscreen', 'sku' => 'SUN-0014',
        'category' => 'sunscreen', 'brand' => 'SkinScience', 'colors' => 'white',
        'details' => 'A weightless, non-greasy SPF 50 that dries clear and leaves zero white cast on deeper skin tones.',
        'featured' => 1, 'status' => 'active',
        'variants' => [
            ['size' => '50ml', 'texture' => 'gel', 'qty' => 32, 'price' => 9500, 'sale' => null, 'status' => 'available'],
        ],
    ],
    [
        'name' => 'Tinted Sunscreen SPF 30', 'slug' => 'tinted-sunscreen-spf-30', 'sku' => 'SUN-0015',
        'category' => 'sunscreen', 'brand' => 'Velvet Glow', 'colors' => 'beige',
        'details' => 'Mineral tinted SPF 30 that evens tone and doubles as a lightweight everyday tint.',
        'featured' => 0, 'status' => 'active',
        'variants' => [
            ['size' => '50ml', 'texture' => 'tint', 'qty' => 0, 'price' => 10200, 'sale' => null, 'status' => 'outOfStock'],
        ],
    ],
    [
        'name' => 'Sport Sunscreen Spray SPF 50', 'slug' => 'sport-sunscreen-spray-spf-50', 'sku' => 'SUN-0016',
        'category' => 'sunscreen', 'brand' => 'Purely Fresh', 'colors' => 'clear',
        'details' => 'Water-resistant SPF 50 spray that reapplies in seconds over makeup, for sweat-proof all-day protection.',
        'featured' => 0, 'status' => 'active',
        'variants' => [
            ['size' => '150ml', 'texture' => 'spray', 'qty' => 18, 'price' => 8500, 'sale' => null, 'status' => 'available'],
        ],
    ],
    [
        'name' => 'Cocoa Butter Whipped Body Butter', 'slug' => 'cocoa-butter-whipped-body-butter', 'sku' => 'BOD-0017',
        'category' => 'body-care', 'brand' => 'Natural Botanics', 'colors' => 'brown',
        'details' => 'An ultra-rich whipped cocoa butter that melts into skin, sealing in moisture for up to 48 hours.',
        'featured' => 0, 'status' => 'active',
        'variants' => [
            ['size' => '250ml', 'texture' => 'butter', 'qty' => 28, 'price' => 7500, 'sale' => null, 'status' => 'available'],
        ],
    ],
    [
        'name' => 'Oatmeal & Honey Body Wash', 'slug' => 'oatmeal-honey-body-wash', 'sku' => 'BOD-0018',
        'category' => 'body-care', 'brand' => 'Purely Fresh', 'colors' => 'honey',
        'details' => 'A creamy soap-free body wash with oatmeal and honey that cleanses without leaving skin tight.',
        'featured' => 0, 'status' => 'active',
        'variants' => [
            ['size' => '300ml', 'texture' => 'wash', 'qty' => 5, 'price' => 6800, 'sale' => null, 'status' => 'available'],
        ],
    ],
    [
        'name' => 'Exfoliating Coffee Body Scrub', 'slug' => 'exfoliating-coffee-body-scrub', 'sku' => 'BOD-0019',
        'category' => 'body-care', 'brand' => 'Rituals & Roots', 'colors' => 'brown',
        'details' => 'A gritty coffee and sugar scrub that polishes away rough patches and firms up the look of skin.',
        'featured' => 0, 'status' => 'active',
        'variants' => [
            ['size' => '300ml', 'texture' => 'scrub', 'qty' => 20, 'price' => 8200, 'sale' => 7400, 'status' => 'available'],
        ],
    ],
    [
        'name' => 'Clay Detox Face Mask', 'slug' => 'clay-detox-face-mask', 'sku' => 'MSK-0020',
        'category' => 'face-masks', 'brand' => 'DermaPure', 'colors' => 'gray',
        'details' => 'Kaolin and bentonite clay pull out toxins and decongest pores in ten minutes flat.',
        'featured' => 0, 'status' => 'active',
        'variants' => [
            ['size' => '100ml', 'texture' => 'clay', 'qty' => 24, 'price' => 6900, 'sale' => null, 'status' => 'available'],
        ],
    ],
    [
        'name' => 'Hydrating Jelly Sleep Mask', 'slug' => 'hydrating-jelly-sleep-mask', 'sku' => 'MSK-0021',
        'category' => 'face-masks', 'brand' => 'Glow Essentials', 'colors' => 'clear, blue',
        'details' => 'A cooling jelly overnight mask that floods skin with moisture while you sleep.',
        'featured' => 1, 'status' => 'active',
        'variants' => [
            ['size' => '50ml', 'texture' => 'jelly', 'qty' => 30, 'price' => 7200, 'sale' => null, 'status' => 'available'],
        ],
    ],
    [
        'name' => 'Brightening Turmeric Mask', 'slug' => 'brightening-turmeric-mask', 'sku' => 'MSK-0022',
        'category' => 'face-masks', 'brand' => 'Nubian Glow', 'colors' => 'gold',
        'details' => 'Wild turmeric root and lactic acid visibly brighten dull skin and fade marks in minutes.',
        'featured' => 0, 'status' => 'active',
        'variants' => [
            ['size' => '100ml', 'texture' => 'paste', 'qty' => 16, 'price' => 7800, 'sale' => null, 'status' => 'available'],
        ],
    ],
    [
        'name' => 'Charcoal Peel-Off Mask', 'slug' => 'charcoal-peel-off-mask', 'sku' => 'MSK-0023',
        'category' => 'face-masks', 'brand' => 'DermaPure', 'colors' => 'black',
        'details' => 'A peel-off charcoal mask that lifts away blackheads and leaves a visibly clearer nose in one pull.',
        'featured' => 0, 'status' => 'active',
        'variants' => [
            ['size' => '60ml', 'texture' => 'gel', 'qty' => 0, 'price' => 6400, 'sale' => null, 'status' => 'outOfStock'],
        ],
    ],
    [
        'name' => 'Vitamin E Collagen Sleeping Mask', 'slug' => 'vitamin-e-collagen-sleeping-mask', 'sku' => 'MSK-0024',
        'category' => 'face-masks', 'brand' => 'Velvet Glow', 'colors' => 'cream',
        'details' => 'An overnight collagen mask that plumps, firms and renews skin by morning.',
        'featured' => 1, 'status' => 'active',
        'variants' => [
            ['size' => '50ml', 'texture' => 'cream', 'qty' => 15, 'price' => 8600, 'sale' => null, 'status' => 'available'],
        ],
    ],
];

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

function seed_category_id(mysqli $conn, array $c): int {
    $title = $c['title'];
    $slug  = $c['slug'];
    $desc  = $c['desc'] ?? '';

    $stmt = $conn->prepare("SELECT category_id FROM categories WHERE category_slug = ?");
    $stmt->bind_param('s', $slug);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    if ($row) {
        $cid = (int) $row['category_id'];
        $up = $conn->prepare("UPDATE categories SET category_title = ?, category_description = ? WHERE category_id = ?");
        $up->bind_param('ssi', $title, $desc, $cid);
        $up->execute();
        seed_line("  category '{$title}' already exists (id {$cid}) - updated");
        return $cid;
    }

    $ins = $conn->prepare("INSERT INTO categories (category_title, category_slug, category_description) VALUES (?, ?, ?)");
    $ins->bind_param('sss', $title, $slug, $desc);
    $ins->execute();
    seed_line("  + category '{$title}' (id {$conn->insert_id})");
    return (int) $conn->insert_id;
}

function seed_brand_id(mysqli $conn, array $b): int {
    $title = $b['title'];

    $stmt = $conn->prepare("SELECT brand_id FROM brands WHERE brand_title = ?");
    $stmt->bind_param('s', $title);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    if ($row) {
        seed_line("  brand '{$title}' already exists (id {$row['brand_id']})");
        return (int) $row['brand_id'];
    }

    $ins = $conn->prepare("INSERT INTO brands (brand_title) VALUES (?)");
    $ins->bind_param('s', $title);
    $ins->execute();
    seed_line("  + brand '{$title}' (id {$conn->insert_id})");
    return (int) $conn->insert_id;
}

function seed_product_id(mysqli $conn, array $p, int $categoryId, int $brandId, string $care = '', string $warranty = ''): int {
    $name     = $p['name'];
    $slug     = $p['slug'];
    $colors   = $p['colors'];
    $details  = $p['details'];
    $sku      = $p['sku'] ?? '';
    $featured = (int) ($p['featured'] ?? 0);
    $status   = $p['status'] ?? 'active';

    $stmt = $conn->prepare("SELECT product_id FROM products WHERE product_slug = ?");
    $stmt->bind_param('s', $slug);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    if ($row) {
        $pid = (int) $row['product_id'];
        $up = $conn->prepare(
            "UPDATE products SET product_name = ?, sku = ?, category_id = ?, brand_id = ?, colors = ?, details = ?, warranty = ?, care = ?, is_featured = ?, product_status = ? WHERE product_id = ?"
        );
        $up->bind_param('ssisssssisi', $name, $sku, $categoryId, $brandId, $colors, $details, $warranty, $care, $featured, $status, $pid);
        $up->execute();
        seed_line("  product '{$name}' already exists (id {$pid}) - updated");
        return $pid;
    }

    $ins = $conn->prepare(
        "INSERT INTO products (product_name, sku, product_slug, category_id, brand_id, colors, details, warranty, care, is_featured, product_status)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    $ins->bind_param('sssisssssis', $name, $sku, $slug, $categoryId, $brandId, $colors, $details, $warranty, $care, $featured, $status);
    $ins->execute();
    seed_line("  + product '{$name}' (id {$conn->insert_id})");
    return (int) $conn->insert_id;
}

function seed_variants(mysqli $conn, int $productId, array $variants): void {
    $conn->query("DELETE FROM product_variants WHERE product_id = " . (int) $productId);

    foreach ($variants as $v) {
        $size    = $v['size'];
        $texture = $v['texture'] ?? '';
        $qty     = (int) $v['qty'];
        $price   = (float) $v['price'];
        $sale    = isset($v['sale']) && $v['sale'] !== null ? (float) $v['sale'] : null;
        $st      = $v['status'] ?? 'available';

        if ($sale !== null && $sale > 0) {
            $ins = $conn->prepare(
                "INSERT INTO product_variants (product_id, size, texture, quantity, original_price, discount_price, status)
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            $ins->bind_param('issiids', $productId, $size, $texture, $qty, $price, $sale, $st);
        } else {
            $ins = $conn->prepare(
                "INSERT INTO product_variants (product_id, size, texture, quantity, original_price, discount_price, status)
                 VALUES (?, ?, ?, ?, ?, NULL, ?)"
            );
            $ins->bind_param('issiis', $productId, $size, $texture, $qty, $price, $st);
        }
        $ins->execute();
    }
    seed_line("    variants: " . count($variants));
}

// ---------------------------------------------------------------------------
// Product images (external, category-appropriate links)
// ---------------------------------------------------------------------------

/**
 * One product shot per seeded product (same order as $products).
 * These are live.staticflickr.com URLs (CC-licensed, hotlink-friendly),
 * replacing the old generated SVG placeholders.
 */
$seedImages = [
    'https://live.staticflickr.com/65535/53411227377_23d1096bfe_b.jpg', // 01 Gentle Foaming Cleanser
    'https://live.staticflickr.com/2108/4508402635_5250ba26c5_b.jpg', // 02 Charcoal Deep Clean Wash
    'https://live.staticflickr.com/7367/26759665070_b425929da1_b.jpg', // 03 Creamy Milk Cleanser
    'https://live.staticflickr.com/4194/33804961643_66da87dc32_b.jpg', // 04 Hydra-Burst Gel Moisturizer
    'https://live.staticflickr.com/231/489358541_b787ae3334_b.jpg', // 05 Shea Butter Rich Cream
    'https://live.staticflickr.com/7857/32376537837_90848a42c1.jpg', // 06 Vitamin C Brightening Lotion
    'https://live.staticflickr.com/5004/5288157218_3b86302095_b.jpg', // 07 Oatmeal Soothing Moisturizer
    'https://live.staticflickr.com/7237/7170588668_523cf7cc1a.jpg', // 08 Hyaluronic Acid Serum
    'https://live.staticflickr.com/7436/16446972155_3dc7a34169.jpg', // 09 Vitamin C 15% Serum
    'https://live.staticflickr.com/7342/27056463026_d91a30a1ff_b.jpg', // 10 Niacinamide 10% Serum
    'https://live.staticflickr.com/8238/8498507108_8283bf99e7.jpg', // 11 Rosehip Facial Oil
    'https://live.staticflickr.com/7834/46595535894_f01bc44d85.jpg', // 12 Retinol Night Treatment
    'https://live.staticflickr.com/8068/8276058717_3b59768e0d.jpg', // 13 Glycolic Acid Toner
    'https://live.staticflickr.com/65535/50096701346_837a3369b3_b.jpg', // 14 SPF 50 Invisible Sunscreen
    'https://live.staticflickr.com/7463/26724312321_9eb818e00b.jpg', // 15 Tinted Sunscreen SPF 30
    'https://live.staticflickr.com/601/20656331680_662c87344e_b.jpg', // 16 Sport Sunscreen Spray SPF 50
    'https://live.staticflickr.com/5534/11003252485_432168850c_b.jpg', // 17 Cocoa Butter Whipped Body Butter
    'https://live.staticflickr.com/7851/32376445287_066d30990f_b.jpg', // 18 Oatmeal & Honey Body Wash
    'https://live.staticflickr.com/2056/2108522824_17e5358b32_b.jpg', // 19 Exfoliating Coffee Body Scrub
    'https://live.staticflickr.com/3134/3233712044_a7a51b5d25_b.jpg', // 20 Clay Detox Face Mask
    'https://live.staticflickr.com/3445/3781260629_ed9594c211.jpg', // 21 Hydrating Jelly Sleep Mask
    'https://live.staticflickr.com/4075/4766033156_30768e878c_b.jpg', // 22 Brightening Turmeric Mask
    'https://live.staticflickr.com/65535/50370337926_6c2a2620ae_b.jpg', // 23 Charcoal Peel-Off Mask
    'https://live.staticflickr.com/7305/9301305426_d960989a8e.jpg', // 24 Vitamin E Collagen Sleeping Mask
];

/**
 * One care/usage note per seeded product (same order as $products).
 */
$seedCare = [
    'Pump a small amount into damp palms, massage over face and rinse with warm water. Use morning and night.',
    'Massage onto damp skin, leave for up to 60 seconds, then rinse thoroughly. Avoid the eye area.',
    'Apply with fingertips or a cotton pad, massage gently, then rinse or wipe off with warm water.',
    'Apply to clean skin after cleansing, morning and night. Follow with SPF during the day.',
    'Warm a pea-size amount between fingers and press into skin. Reapply on very dry areas.',
    'Use in the morning on clean skin before sunscreen. Store away from direct sunlight.',
    'Apply generously to irritated or dry skin as often as needed. Gentle enough for sensitive skin.',
    'Press a few drops into damp skin after cleansing, then seal with a moisturizer.',
    'Apply sparingly in the morning onto dry skin. Always follow with SPF 50.',
    'Use morning or night daily. Begin every other day if your skin is new to actives.',
    'Warm 2-3 drops in palms and press into damp skin before your moisturizer.',
    'Apply a pea-size amount to clean, dry skin at night, avoiding eyes and lips. Start 2-3 nights a week.',
    'Swipe over clean skin with a cotton pad at night. Avoid the eye contour and rinse if stinging.',
    'Apply generously 15 minutes before sun exposure and reapply every two hours.',
    'Apply as the last step of your morning routine and blend evenly. Reapply midday for full protection.',
    'Shake well, spray generously over skin and rub in. Reapply after swimming, sweat or towelling.',
    'Massage onto clean, dry skin after a shower, focusing on elbows, knees and heels.',
    'Lather over wet skin and rinse. Suitable for daily use on sensitive skin.',
    'Massage in circular motions on damp skin, rinse and follow with moisturizer. Use 2-3 times a week.',
    'Apply an even layer to clean skin, leave for 10 minutes, then rinse with warm water.',
    'Apply a thin layer as the last step of your night routine. No rinsing needed.',
    'Apply to clean skin, leave for 10-15 minutes, then rinse off with lukewarm water.',
    'Apply a smooth, even layer to clean dry skin, let dry completely, then peel off gently from the edges.',
    'Apply before bed over your night cream. Rinse in the morning for a fresh, plump look.',
];

/**
 * Default warranty shown on every seeded product.
 */
$seedWarranty = 'Shop with confidence and try it for 30 days; return opened or unopened items for a full refund within 30 days of delivery.';

// ---------------------------------------------------------------------------
// Run
// ---------------------------------------------------------------------------

seed_line('== Glorefy catalog seed ==');
seed_line('Database: ' . DB_NAME);

$categoryIds = [];
seed_line("Categories:");
foreach ($categories as $c) {
    $categoryIds[$c['slug']] = seed_category_id($conn, $c);
}

$brandIds = [];
seed_line("Brands (tags):");
foreach ($brands as $b) {
    $brandIds[$b['title']] = seed_brand_id($conn, $b);
}

seed_line("Products:");
foreach ($products as $i => $p) {
    $catId  = $categoryIds[$p['category']];
    $brandId = $brandIds[$p['brand']];
    $care     = $seedCare[$i] ?? '';
    $warranty = $seedWarranty;
    $pid    = seed_product_id($conn, $p, $catId, $brandId, $care, $warranty);
    seed_variants($conn, $pid, $p['variants']);

    $imageUrl = $seedImages[$i] ?? '';
    $conn->query("DELETE FROM product_images WHERE product_id = " . (int) $pid);
    if (!empty($imageUrl)) {
        $img = $conn->prepare("INSERT INTO product_images (product_id, image_path, is_main, display_order) VALUES (?, ?, 1, 1)");
        $img->bind_param('ss', $pid, $imageUrl);
        $img->execute();
    }
}

$catCount  = (int) $conn->query("SELECT COUNT(*) c FROM categories")->fetch_assoc()['c'];
$brandCount = (int) $conn->query("SELECT COUNT(*) c FROM brands")->fetch_assoc()['c'];
$prodCount = (int) $conn->query("SELECT COUNT(*) c FROM products")->fetch_assoc()['c'];

seed_line('');
seed_line("Done. Totals: {$catCount} categories, {$brandCount} brands, {$prodCount} products.");
seed_line('Re-run anytime to sync - nothing will be duplicated.');

if (!$IS_CLI) {
    echo '</pre>';
}