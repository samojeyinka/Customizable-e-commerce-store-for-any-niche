<?php
/**
 * GLOREFY storefront configuration loader.
 *
 * Reads site settings from the `site_settings` key/value table and falls back
 * to the original design (current content, images, colors and logo) whenever a
 * setting has not been customized yet. This makes the storefront fully
 * editable from the admin "Storefront" page without touching the layout.
 *
 * Relying helpers:
 *   store('hero')            -> array  (grouped settings auto-JSON-decoded)
 *   store('store_name')      -> string (scalar settings returned as-is)
 *   store_color('color_primary') -> string hex color
 *   store_reviews()          -> array  (active testimonials)
 *   store_theme_style()      -> <style> CSS-variable block for component colors
 */
if (function_exists('store')) {
    return;
}

/* ------------------------------------------------------------------ */
/* Defaults = the original storefront design.                          */
/* ------------------------------------------------------------------ */
function store_defaults() {
    $dom = DOMAIN;

    $unsplash = function ($handle, $w = 1600, $q = 80) {
        return 'https://images.unsplash.com/' . $handle . '?auto=format&fit=crop&w=' . $w . '&q=' . $q;
    };

    return [
        'store_name' => 'GLOREFY',
        'logo_url' => $dom . '/assets/global/logo.png',
        'favicon_url' => $dom . '/assets/global/logo.png',
        'search_placeholder' => 'Search skincare, makeup, beauty...',

        // Brand colors (whole storefront)
        'color_primary' => '#C2185B',
        'color_primary_dark' => '#A01548',
        'color_heading' => '#3D1A2A',
        'color_tint' => '#F5EEF2',
        'color_tint2' => '#F8F0F4',
        'color_bg' => '#FEFEFE',

        // Hero carousel
        'hero' => [
            'title' => 'Glow Up Your Beauty Routine',
            'subtitle' => 'Discover premium skincare, makeup, and beauty essentials that bring out your natural radiance.',
            'overlay_from' => '#3D1A2A',
            'overlay_to' => '#C2185B',
            'btn1_text' => 'Shop Now',
            'btn1_url' => $dom . '/products/index.php',
            'btn2_text' => 'Our Story',
            'btn2_url' => $dom . '/details/about-us.php',
            'slides' => [
                $unsplash('photo-1556228720-195a672e8a03'),
                $unsplash('photo-1487412947147-5cebf100ffc2'),
                $unsplash('photo-1556228578-8c89e6adf883'),
                $unsplash('photo-1596462502278-27bfdc403348'),
            ],
        ],

        // "What You Can Get Here" (shop by search keywords, no category links)
        'shop_section' => [
            'eyebrow' => 'Browse',
            'title' => 'What You Can Get Here',
            'subtitle' => 'Explore our curated ranges and find the essentials your beauty routine is missing.',
            'view_all_text' => 'View All Products',
            'view_all_url' => $dom . '/products/index.php',
            'items' => [
                [
                    'image' => $unsplash('photo-1556228720-195a672e8a03', 1200),
                    'title' => 'Skincare Essentials',
                    'subtitle' => 'Cleansers, serums, moisturizers, and treatments for every skin type.',
                    'keyword' => 'skincare',
                ],
                [
                    'image' => $unsplash('photo-1598440947619-2c35fc9aa908', 800),
                    'title' => 'Face Makeup',
                    'subtitle' => 'Foundations, concealers, powders, and blush',
                    'keyword' => 'makeup',
                ],
                [
                    'image' => $unsplash('photo-1586495777744-4413f21062fa', 800),
                    'title' => 'Lips & Eyes',
                    'subtitle' => 'Lipsticks, glosses, eyeshadows, and mascaras',
                    'keyword' => 'lips',
                ],
                [
                    'image' => $unsplash('photo-1522337660859-02fbefca4702', 800),
                    'title' => 'Hair Care',
                    'subtitle' => 'Shampoos, conditioners, and treatments',
                    'keyword' => 'hair',
                ],
                [
                    'image' => $unsplash('photo-1596462502278-27bfdc403348', 800),
                    'title' => 'Beauty Tools',
                    'subtitle' => 'Brushes, sponges, and accessories',
                    'keyword' => 'tools',
                ],
            ],
        ],

        // Why-choose section
        'why' => [
            'label' => 'Why us',
            'title' => 'Why Choose Glorefy',
            'image' => $unsplash('photo-1571781926291-c477ebfd024b', 900),
            'badge_title' => 'Dermatologist-approved formulas',
            'badge_text' => 'Gentle, effective, and suited to every skin type.',
            'features' => [
                ['title' => 'Authentic Products', 'text' => 'Every product is sourced from trusted brands and verified for quality and authenticity.'],
                ['title' => 'Expert Guidance', 'text' => 'Our beauty specialists help you find the perfect products for your unique skin type and beauty goals.'],
                ['title' => 'Fast & Free Delivery', 'text' => 'Free express delivery on orders over ₦50,000. Your beauty essentials delivered to your doorstep.'],
                ['title' => 'Easy Returns', 'text' => 'Not satisfied? Enjoy hassle-free returns within 14 days of purchase, no questions asked.'],
            ],
        ],

        // Intro / stat banner
        'intro' => [
            'label' => 'The Glorefy Promise',
            'title' => 'Discover the glow within you with our curated collection of premium skincare, luxurious makeup, and beauty essentials designed to make you feel beautiful every day.',
            'image' => $unsplash('photo-1541643600914-78b084683601', 1600),
            'grad_from' => '#3D1A2A',
            'grad_to' => '#C2185B',
            'stats' => [
                ['value' => '100%', 'label' => 'Authentic Products'],
                ['value' => '500+', 'label' => 'Beauty Essentials'],
                ['value' => '24hrs', 'label' => 'Expert Support'],
            ],
        ],

        // FAQ
        'faq' => [
            'title' => 'Frequently Asked Questions',
            'items' => [
                ['q' => 'What payment methods do you accept?', 'a' => 'We accept all major credit/debit cards, bank transfers, and Paystack payments. All transactions are secure and encrypted.'],
                ['q' => 'How long does delivery take?', 'a' => 'Express delivery takes 1-3 business days within Lagos and 3-7 business days nationwide. Pick-up orders are ready within 24 hours.'],
                ['q' => 'Do you have a returns policy?', 'a' => 'Yes! We offer hassle-free returns within 14 days of purchase for unopened products. If you have any issues with your order, our support team is here to help.'],
                ['q' => 'Are your products authentic?', 'a' => 'Absolutely. Every product on Glorefy is sourced directly from authorized distributors and verified brands. We guarantee 100% authenticity.'],
                ['q' => 'Do you deliver nationwide?', 'a' => 'Yes, we deliver to all 36 states in Nigeria. Express delivery is available for most locations, and we also offer pick-up from our Lagos store.'],
                ['q' => 'How do I track my order?', 'a' => 'You can track your order from your account dashboard. Once your order is shipped, you\'ll receive a notification with real-time tracking details.'],
                ['q' => 'How can I contact customer support?', 'a' => 'You can reach us via WhatsApp, email at support@glorefy.com, or through the contact form on our website. We respond within 24 hours.'],
                ['q' => 'Can I get beauty advice?', 'a' => 'Yes! Our team of beauty experts can help you build a personalized skincare routine. Contact us or visit our store for a free consultation.'],
            ],
        ],

        // "See more products" CTA under the product grid
        'featured_cta' => 'See More Products',

        // About page
        'about' => [
            'hero_image' => $unsplash('photo-1556228720-195a672e8a03', 1600),
            'hero_heading' => 'About Us',
            'content' => [
                'At Glorefy, we believe beauty is a celebration of your true self where confidence meets radiance. Specializing in premium skincare, makeup, and beauty essentials, we thoughtfully curate products that help you glow with warmth, style, and self-care.',
                'From dermatologically tested serums and nourishing moisturizers to long-wear makeup and luxurious body care, our collection is crafted from the finest, cleanest ingredients to deliver visible results and timeless appeal. Whether you\'re perfecting your daily glow-up or pampering yourself with a full beauty ritual, our products blend skin-loving science with everyday elegance.',
                'With a commitment to quality, sustainability, and innovation, we offer cruelty-free, dermatologist-approved, and meticulously selected beauty essentials. Our mission is simple: to help you discover and embrace the glow within, because when you feel beautiful, you shine.',
            ],
            'image' => $unsplash('photo-1571781926291-c477ebfd024b', 900),
            'why' => [
                'title' => 'Why Choose Glorefy',
                'image' => $unsplash('photo-1608248543803-ba4f8c70ae0b', 900),
                'features' => [
                    ['icon' => 'fa-badge-check', 'title' => 'Authentic Beauty Products', 'text' => 'Every brand we stock is 100% authentic, sourced directly, and dermatologist-approved for real results.'],
                    ['icon' => 'fa-headset', 'title' => 'Beauty That Cares', 'text' => 'Cruelty-free, clean formulas crafted from skin-loving, sustainable ingredients you can trust.'],
                    ['icon' => 'fa-truck-fast', 'title' => 'Fast & Reliable Delivery', 'text' => 'We ensure quick and reliable delivery, so your order arrives on time and in perfect condition.'],
                    ['icon' => 'fa-shield-heart', 'title' => 'Beauty For Every Skin Type', 'text' => 'From sensitive to oily skin, find products tailored to your unique beauty needs and goals.'],
                ],
            ],
            'collection' => [
                'title' => 'Our Beauty Collection',
                'cards' => [
                    ['image' => $unsplash('photo-1556228720-195a672e8a03', 1200), 'title' => 'Skincare Essentials', 'subtitle' => 'Serums, moisturizers, and cleansers formulated for your daily glow'],
                    ['image' => $unsplash('photo-1598440947619-2c35fc9aa908', 800), 'title' => 'Face Makeup', 'subtitle' => 'Foundations, concealers, powders, and blush for a flawless finish.'],
                    ['image' => $unsplash('photo-1522337660859-02fbefca4702', 800), 'title' => 'Hair Care', 'subtitle' => 'Nourishing shampoos, conditioners, and treatments for healthy, shiny hair.'],
                    ['image' => $unsplash('photo-1541643600914-78b084683601', 800), 'title' => 'Fragrances', 'subtitle' => 'Long-lasting perfumes and body mists for a signature scent.'],
                    ['image' => $unsplash('photo-1596462502278-27bfdc403348', 800), 'title' => 'Beauty Tools', 'subtitle' => 'Professional brushes, sponges, and tools to perfect every application.'],
                ],
            ],
        ],

        // Contact page
        'contact' => [
            'hero_image' => $unsplash('photo-1598440947619-2c35fc9aa908', 1600),
            'hero_heading' => 'Contact Us',
            'phone' => '+1 (212) 555-0147',
            'email' => 'support@glorefy.com',
            'address' => '245 Fifth Avenue, Suite 1203, New York, NY 10016',
            'map_url' => 'https://maps.google.com/maps?q=245%20Fifth%20Avenue%2C%20New%20York%2C%20NY%2010016&t=&z=15&ie=UTF8&iwloc=&output=embed',
            'form_subject' => 'Message from GLOREFY',
        ],

        // Footer + contact chips
        'foot_blurb' => 'Premium skincare, makeup and beauty essentials that help you glow with confidence.',
        'news_title' => 'Get on the List',
        'news_text' => 'Beauty tips, new arrivals and exclusive offers, straight to your inbox.',
        'copyright_name' => 'GLOREFY',
        'social' => [
            'facebook' => 'https://www.facebook.com/glorefy',
            'instagram' => 'https://www.instagram.com/glorefy',
            'whatsapp' => 'https://wa.me/08004567339',
            'pinterest' => '#',
            'youtube' => '#',
            'x' => 'https://www.x.com/glorefy',
        ],
        'whatsapp_number' => '08004567339',
    ];
}

/* Default testimonials (first-run seed). */
function store_default_reviews() {
    return [
        ['name' => 'Ogundipe K.', 'rating' => 5, 'text' => 'I\'ve had a great shopping experience! Easy to navigate, fast checkout, and the quality of the products is unbeatable.', 'row' => 0],
        ['name' => 'Chiamaka O.', 'rating' => 5, 'text' => 'The niacinamide serum transformed my skin in just a few weeks - it\'s now my holy-grail product and delivery was super fast.', 'row' => 0],
        ['name' => 'Faith N.', 'rating' => 4, 'text' => 'My favourite lipstick shade is always in stock, ships quickly, and lasts all day. Glorefy is now my go-to beauty store.', 'row' => 0],
        ['name' => 'Nneka M.', 'rating' => 5, 'text' => 'The moisturizer leaves my skin so soft and dewy. Finally, a brand I can trust for authentic products.', 'row' => 0],
        ['name' => 'Jenneans D.', 'rating' => 4, 'text' => 'I ordered the vitamin C set and the glow-up is real. My dark spots are fading and my skin looks brighter.', 'row' => 1],
        ['name' => 'Akin L.', 'rating' => 5, 'text' => 'The makeup brushes are incredible quality, and the support team helped me find the perfect foundation shade.', 'row' => 1],
        ['name' => 'Samuel O.', 'rating' => 5, 'text' => 'Beautiful fragrance with amazing staying power. The packaging was elegant and delivery was right on time.', 'row' => 1],
        ['name' => 'Khadijat N.', 'rating' => 4, 'text' => 'The cleanser is so gentle and my breakouts have cleared up completely. I highly recommend Glorefy.', 'row' => 1],
    ];
}

/* ------------------------------------------------------------------ */
/* Table bootstrap + seeding                                           */
/* ------------------------------------------------------------------ */
function store_ensure_tables() {
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;

    try {
        $pdo = pdo_db();
        $pdo->exec("CREATE TABLE IF NOT EXISTS site_settings (
            setting_key VARCHAR(191) NOT NULL PRIMARY KEY,
            setting_value LONGTEXT DEFAULT NULL,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        $pdo->exec("CREATE TABLE IF NOT EXISTS store_reviews (
            review_id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            rating TINYINT NOT NULL DEFAULT 5,
            text TEXT DEFAULT NULL,
            `row` TINYINT DEFAULT 0,
            sort_order INT DEFAULT 0,
            active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_active (active, `row`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $has = (int) $pdo->query("SELECT COUNT(*) FROM store_reviews")->fetchColumn();
        if ($has === 0) {
            $insert = $pdo->prepare("INSERT INTO store_reviews (name, rating, text, `row`, sort_order) VALUES (?, ?, ?, ?, ?)");
            $i = 0;
            foreach (store_default_reviews() as $r) {
                $insert->execute([$r['name'], $r['rating'], $r['text'], $r['row'], $i]);
                $i++;
            }
        }
    } catch (Exception $e) {
        // DB unavailable - loader simply falls back to defaults.
    }
}

/* ------------------------------------------------------------------ */
/* Read / merge                                                        */
/* ------------------------------------------------------------------ */
function &store_cache_ref() {
    static $all = null;
    return $all;
}

function store_config_all() {
    $all =& store_cache_ref();
    if ($all !== null) {
        return $all;
    }

    store_ensure_tables();
    $defaults = store_defaults();
    $rows = [];

    try {
        $pdo = pdo_db();
        $rows = $pdo->query("SELECT setting_key, setting_value FROM site_settings")->fetchAll(PDO::FETCH_KEY_PAIR);
    } catch (Exception $e) {
        $rows = [];
    }

    $all = [];
    foreach ($defaults as $key => $value) {
        if (array_key_exists($key, $rows) && $rows[$key] !== null && $rows[$key] !== '') {
            if (is_array($value)) {
                $decoded = json_decode($rows[$key], true);
                $all[$key] = is_array($decoded) ? array_replace_recursive($value, $decoded) : $value;
            } else {
                $all[$key] = $rows[$key];
            }
        } else {
            $all[$key] = $value;
        }
    }

    foreach ($rows as $key => $value) {
        if (!array_key_exists($key, $all) && $value !== null && $value !== '') {
            $all[$key] = $value;
        }
    }

    return $all;
}

function store($key, $default = null) {
    $all = store_config_all();
    if (array_key_exists($key, $all)) {
        return $all[$key];
    }
    if ($default !== null) {
        return $default;
    }
    $defaults = store_defaults();
    return $defaults[$key] ?? null;
}

function store_color($key = 'color_primary', $fallback = '#C2185B') {
    $value = (string) store($key, $fallback);
    return $value !== '' ? $value : $fallback;
}

function store_escape($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/* ------------------------------------------------------------------ */
/* Write (used by the admin Storefront page)                           */
/* ------------------------------------------------------------------ */
function store_set($key, $value) {
    store_ensure_tables();
    $payload = is_array($value) || is_object($value) ? json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : (string) $value;
    $pdo = pdo_db();
    $stmt = $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?)
                           ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
    $stmt->execute([(string) $key, $payload]);
    $cache =& store_cache_ref();
    $cache = null;
}

/* ------------------------------------------------------------------ */
/* Testimonials                                                        */
/* ------------------------------------------------------------------ */
function store_reviews($row = null) {
    $defaults = store_default_reviews();
    $reviews = [];

    try {
        $pdo = pdo_db();
        $sql = "SELECT review_id, name, rating, text, `row`, sort_order FROM store_reviews WHERE active = 1";
        $params = [];
        if ($row === 0 || $row === 1) {
            $sql .= " AND `row` = ?";
            $params[] = $row;
        }
        $sql .= " ORDER BY `row` ASC, sort_order ASC, review_id ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $reviews = [];
    }

    if (empty($reviews)) {
        return array_map(function ($d, $i) {
            return ['review_id' => 0, 'name' => $d['name'], 'rating' => $d['rating'], 'text' => $d['text'], 'row' => $d['row'], 'sort_order' => $i];
        }, $defaults, array_keys($defaults));
    }

    return $reviews;
}

/* ------------------------------------------------------------------ */
/* Emit the CSS-variable block so component styles follow the brand.   */
/* ------------------------------------------------------------------ */
function store_theme_style() {
    $css = ':root{'
        . '--glor-primary:' . store_color('color_primary') . ';'
        . '--glor-primary-dark:' . store_color('color_primary_dark', store_color('color_primary')) . ';'
        . '--glor-heading:' . store_color('color_heading', '#3D1A2A') . ';'
        . '--glor-tint:' . store_color('color_tint', '#F5EEF2') . ';'
        . '--glor-tint2:' . store_color('color_tint2', '#F8F0F4') . ';'
        . '--glor-bg:' . store_color('color_bg', '#FEFEFE') . ';}'
        . '.glor-text{color:var(--glor-primary)}'
        . '.glor-bg{background-color:var(--glor-primary)}'
        . '.glor-border{border-color:var(--glor-primary)}'
        . '.glor-text-dark{color:var(--glor-primary-dark)}'
        . '.glor-bg-dark{background-color:var(--glor-primary-dark)}'
        . '.glor-heading{color:var(--glor-heading)}'
        . '.glor-tintbg{background-color:var(--glor-tint)}';
    return '<style>' . $css . '</style>';
}