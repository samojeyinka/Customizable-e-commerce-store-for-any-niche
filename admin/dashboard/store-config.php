<?php
session_start();
include('../../config/connect.php');
require_once "../../config/config.php";

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

// Only super_admin / admin roles may edit the storefront.
$guard_role = isset($_SESSION['admin_role']) ? $_SESSION['admin_role'] : 'admin';
if ($guard_role !== 'super_admin' && $guard_role !== 'admin') {
    die('You do not have permission to edit the storefront.');
}

$admin_id = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_fullname'] ?? 'Admin User';
$admin_email = $_SESSION['admin_email'] ?? 'admin@example.com';
$admin_role = $_SESSION['admin_role'] ?? 'admin';

// Fetch admin details for the profile chip
$conn = db();
$profile_photo = "../assets/home/user.svg";
$stmt = $conn->prepare("SELECT * FROM administrators WHERE admin_id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();
if (!empty($admin['profile_photo'])) {
    $photo_path = "../../uploads/profiles/" . $admin['profile_photo'];
    if (file_exists($photo_path)) {
        $profile_photo = $photo_path;
    }
}

// Notifications count
require_once __DIR__ . '/../../includes/notifications.php';
if (!isset($con)) {
    include('../../config/connect.php');
}
$unread_count = function_exists('get_unread_count') ? get_unread_count($con, true) : 0;
$latest_notifications = function_exists('get_notifications') ? get_notifications($con, true, null, 5, 0) : [];

$saved_msg = '';
$error_msg = '';

/* ================================================================ */
/* Save handlers                                                    */
/* ================================================================ */
function post_str($key, $default = '') { return isset($_POST[$key]) ? (string) $_POST[$key] : $default; }

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['save_tab'])) {
    $tab = $_POST['save_tab'];

    if ($tab === 'branding') {
        store_set('store_name', post_str('store_name'));
        store_set('logo_url', post_str('logo_url'));
        store_set('favicon_url', post_str('favicon_url'));
        store_set('search_placeholder', post_str('search_placeholder'));
        store_set('color_primary', post_str('color_primary', '#C2185B'));
        store_set('color_primary_dark', post_str('color_primary_dark', post_str('color_primary', '#A01548')));
        store_set('color_heading', post_str('color_heading', '#3D1A2A'));
        store_set('color_tint', post_str('color_tint', '#F5EEF2'));
        store_set('color_tint2', post_str('color_tint2', '#F8F0F4'));
        store_set('color_bg', post_str('color_bg', '#FEFEFE'));
        $saved_msg = 'Branding saved.';
    }

    if ($tab === 'homepage') {
        $hero = store('hero');
        $hero['title'] = post_str('hero_title');
        $hero['subtitle'] = post_str('hero_subtitle');
        $hero['overlay_from'] = post_str('hero_overlay_from');
        $hero['overlay_to'] = post_str('hero_overlay_to');
        $hero['btn1_text'] = post_str('hero_btn1_text');
        $hero['btn1_url'] = post_str('hero_btn1_url');
        $hero['btn2_text'] = post_str('hero_btn2_text');
        $hero['btn2_url'] = post_str('hero_btn2_url');
        $defaultSlides = isset($hero['slides']) ? array_values($hero['slides']) : [];
        for ($i = 0; $i < 4; $i++) {
            $hero['slides'][$i] = post_str('hero_slides_' . $i, $defaultSlides[$i] ?? '');
        }
        $hero['slides'] = array_values(array_filter($hero['slides'], fn($s) => trim($s) !== ''));
        store_set('hero', $hero);

        $shop = store('shop_section');
        $shop['eyebrow'] = post_str('shop_eyebrow');
        $shop['title'] = post_str('shop_title');
        $shop['subtitle'] = post_str('shop_subtitle');
        $shop['view_all_text'] = post_str('shop_view_all_text');
        $shop['view_all_url'] = post_str('shop_view_all_url');
        $shop['items'] = [];
        for ($i = 0; $i < 5; $i++) {
            $shop['items'][] = [
                'image' => post_str('shop_item_' . $i . '_image'),
                'title' => post_str('shop_item_' . $i . '_title'),
                'subtitle' => post_str('shop_item_' . $i . '_subtitle'),
                'keyword' => post_str('shop_item_' . $i . '_keyword'),
            ];
        }
        store_set('shop_section', $shop);

        $why = store('why');
        $why['label'] = post_str('why_label');
        $why['title'] = post_str('why_title');
        $why['image'] = post_str('why_image');
        $why['badge_title'] = post_str('why_badge_title');
        $why['badge_text'] = post_str('why_badge_text');
        $why['features'] = [];
        for ($i = 0; $i < 4; $i++) {
            $why['features'][] = ['title' => post_str('why_f' . $i . '_title'), 'text' => post_str('why_f' . $i . '_text')];
        }
        store_set('why', $why);

        $intro = store('intro');
        $intro['label'] = post_str('intro_label');
        $intro['title'] = post_str('intro_title');
        $intro['image'] = post_str('intro_image');
        $intro['grad_from'] = post_str('intro_grad_from');
        $intro['grad_to'] = post_str('intro_grad_to');
        $intro['stats'] = [];
        for ($i = 0; $i < 3; $i++) {
            $intro['stats'][] = ['value' => post_str('intro_stat_' . $i . '_value'), 'label' => post_str('intro_stat_' . $i . '_label')];
        }
        store_set('intro', $intro);

        store_set('faq_title', post_str('faq_title'));
        $faqQuestions = isset($_POST['faq_q']) ? $_POST['faq_q'] : [];
        $faqAnswers = isset($_POST['faq_a']) ? $_POST['faq_a'] : [];
        $faqItems = [];
        for ($i = 0; $i < count($faqQuestions); $i++) {
            $q = trim($faqQuestions[$i]);
            $a = trim(isset($faqAnswers[$i]) ? $faqAnswers[$i] : '');
            if ($q !== '' || $a !== '') {
                $faqItems[] = ['q' => $q, 'a' => $a];
            }
        }
        store_set('faq', ['title' => post_str('faq_title'), 'items' => $faqItems]);

        store_set('featured_cta', post_str('featured_cta'));
        store_set('reviews_title', post_str('reviews_title'));
        $saved_msg = 'Homepage saved.';
    }

    if ($tab === 'about') {
        $about = store('about');
        $about['hero_image'] = post_str('about_hero_image');
        $about['hero_heading'] = post_str('about_hero_heading');
        $about['image'] = post_str('about_image');
        $contentRaw = preg_split('/\r\n|\r|\n/', post_str('about_content'));
        $lines = array_map('trim', $contentRaw);
        $about['content'] = array_values(array_filter($lines, fn($l) => $l !== ''));
        $about['why']['title'] = post_str('about_why_title');
        $about['why']['image'] = post_str('about_why_image');
        $about['why']['features'] = [];
        for ($i = 0; $i < 4; $i++) {
            $about['why']['features'][] = [
                'icon' => post_str('about_wf' . $i . '_icon'),
                'title' => post_str('about_wf' . $i . '_title'),
                'text' => post_str('about_wf' . $i . '_text'),
            ];
        }
        $about['collection']['title'] = post_str('about_collection_title');
        $about['collection']['cards'] = [];
        for ($i = 0; $i < 5; $i++) {
            $about['collection']['cards'][] = [
                'image' => post_str('about_card_' . $i . '_image'),
                'title' => post_str('about_card_' . $i . '_title'),
                'subtitle' => post_str('about_card_' . $i . '_subtitle'),
            ];
        }
        store_set('about', $about);
        $saved_msg = 'About page saved.';
    }

    if ($tab === 'contact') {
        $contact = store('contact');
        $contact['hero_image'] = post_str('contact_hero_image');
        $contact['hero_heading'] = post_str('contact_hero_heading');
        $contact['phone'] = post_str('contact_phone');
        $contact['email'] = post_str('contact_email');
        $contact['address'] = post_str('contact_address');
        $contact['map_url'] = post_str('contact_map_url');
        $contact['form_subject'] = post_str('contact_form_subject');
        store_set('contact', $contact);

        store_set('foot_blurb', post_str('foot_blurb'));
        store_set('news_title', post_str('news_title'));
        store_set('news_text', post_str('news_text'));
        store_set('copyright_name', post_str('copyright_name'));
        store_set('whatsapp_number', post_str('whatsapp_number'));

        $social = store('social');
        $social['facebook'] = post_str('social_facebook');
        $social['instagram'] = post_str('social_instagram');
        $social['whatsapp'] = post_str('social_whatsapp');
        $social['pinterest'] = post_str('social_pinterest');
        $social['youtube'] = post_str('social_youtube');
        $social['x'] = post_str('social_x');
        store_set('social', $social);
        $saved_msg = 'Contact & footer saved.';
    }

    if ($tab === 'testimonials') {
        try {
            $pdo = pdo_db();
            if (!empty($_POST['delete_review']) && is_array($_POST['delete_review'])) {
                $del = $pdo->prepare("DELETE FROM store_reviews WHERE review_id = ?");
                foreach ($_POST['delete_review'] as $rid) {
                    if ((int) $rid) $del->execute([(int) $rid]);
                }
            }
            if (!empty($_POST['review_id']) && is_array($_POST['review_id'])) {
                $upd = $pdo->prepare("UPDATE store_reviews SET name = ?, rating = ?, text = ?, `row` = ?, active = ? WHERE review_id = ?");
                foreach ($_POST['review_id'] as $key => $rid) {
                    $rid = (int) $rid;
                    if (!$rid) continue;
                    $name = isset($_POST['review_name'][$key]) ? trim($_POST['review_name'][$key]) : '';
                    $rating = isset($_POST['review_rating'][$key]) ? max(1, min(5, (int) $_POST['review_rating'][$key])) : 5;
                    $text = isset($_POST['review_text'][$key]) ? trim($_POST['review_text'][$key]) : '';
                    $row = isset($_POST['review_row'][$key]) ? ((int) $_POST['review_row'][$key] === 1 ? 1 : 0) : 0;
                    $active = !empty($_POST['review_active'][$key]) ? 1 : 0;
                    if ($name === '') continue;
                    $upd->execute([$name, $rating, $text, $row, $active, $rid]);
                }
            }
            if (!empty($_POST['new_name']) && is_array($_POST['new_name'])) {
                $ins = $pdo->prepare("INSERT INTO store_reviews (name, rating, text, `row`, active) VALUES (?, ?, ?, ?, 1)");
                foreach ($_POST['new_name'] as $key => $name) {
                    $name = trim($name);
                    if ($name === '') continue;
                    $rating = isset($_POST['new_rating'][$key]) ? max(1, min(5, (int) $_POST['new_rating'][$key])) : 5;
                    $text = isset($_POST['new_text'][$key]) ? trim($_POST['new_text'][$key]) : '';
                    $row = isset($_POST['new_row'][$key]) ? ((int) $_POST['new_row'][$key] === 1 ? 1 : 0) : 0;
                    $ins->execute([$name, $rating, $text, $row]);
                }
            }
            $saved_msg = 'Testimonials saved.';
        } catch (Exception $e) {
            $error_msg = 'Could not save testimonials: ' . $e->getMessage();
        }
    }
}

/* Load merged values for rendering */
$cfg = store_config_all();
$hero = $cfg['hero'];
$shop = $cfg['shop_section'];
$why = $cfg['why'];
$intro = $cfg['intro'];
$faq = $cfg['faq'];
$about = $cfg['about'];
$contact = $cfg['contact'];
$social = $cfg['social'];

/* Load ALL testimonials (incl. inactive) for editing */
$reviewsAdmin = [];
try {
    $reviewsAdmin = pdo_db()->query("SELECT * FROM store_reviews ORDER BY `row` ASC, sort_order ASC, review_id ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $reviewsAdmin = [];
}

/* ---------------------------------------------------------------- */
/* Per-tab "active" detection: a tab is Active when any of its       */
/* stored settings differ from the shipped defaults.                 */
/* ---------------------------------------------------------------- */
function glor_diff($a, $b) {
    if (is_array($a)) {
        return !is_array($b) || $a != $b;
    }
    if (is_array($b)) {
        return true;
    }
    return (string) $a !== (string) $b;
}

function glor_any_custom(array $keys) {
    $cfg = store_config_all();
    $defaults = store_defaults();
    $effective = [
        'reviews_title' => "Don't Just Hear From Us, Hear From Our Glorefy Family",
        'faq_title' => 'Frequently Asked Questions',
    ];
    foreach ($keys as $key) {
        $def = array_key_exists($key, $defaults) ? $defaults[$key] : ($effective[$key] ?? null);
        $val = $cfg[$key] ?? null;
        if (glor_diff($val, $def)) {
            return true;
        }
    }
    return false;
}

function glor_reviews_custom() {
    try {
        $rows = pdo_db()->query("SELECT name, rating, text, `row` FROM store_reviews ORDER BY review_id ASC")->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return false;
    }
    $defs = store_default_reviews();
    if (count($rows) !== count($defs)) {
        return true;
    }
    foreach ($rows as $i => $row) {
        $d = $defs[$i] ?? null;
        if (!$d || (string) $row['name'] !== $d['name'] || (int) $row['rating'] !== (int) $d['rating'] || (string) $row['text'] !== $d['text'] || (int) $row['row'] !== (int) $d['row']) {
            return true;
        }
    }
    return false;
}

function glor_flag($active) {
    return '<span class="flex items-center gap-1.5 rounded-full border px-2 py-0.5 text-[11px] font-semibold '
        . ($active ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-gray-50 border-gray-200 text-gray-500') . '">'
        . '<span class="w-1.5 h-1.5 rounded-full ' . ($active ? 'bg-emerald-500' : 'bg-gray-300') . '"></span>'
        . ($active ? 'Active' : 'Default') . '</span>';
}

$tabActive = [
    'branding'     => glor_any_custom(['store_name', 'logo_url', 'favicon_url', 'search_placeholder', 'color_primary', 'color_primary_dark', 'color_heading', 'color_tint', 'color_tint2', 'color_bg']),
    'homepage'     => glor_any_custom(['hero', 'shop_section', 'why', 'intro', 'faq', 'featured_cta']),
    'about'        => glor_any_custom(['about']),
    'contact'      => glor_any_custom(['contact', 'foot_blurb', 'news_title', 'news_text', 'copyright_name', 'social', 'whatsapp_number']),
    'testimonials' => glor_reviews_custom() || glor_any_custom(['reviews_title']),
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Storefront Settings - <?php echo htmlspecialchars($admin_name); ?></title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <?php include '../tailwind-components.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        summary.opt-collapse-head::-webkit-details-marker { display: none; }
        summary.opt-collapse-head::marker { content: ''; }
    </style>
</head>

<body class="relative">

<header class="w-full bg-white z-50 flex items-center justify-between px-4 md:px-6 lg:px-8 h-16 border-b border-gray-100 fixed top-0 left-0 shadow-[0_1px_3px_rgba(0,0,0,0.04)]">
    <nav class="w-full flex items-center justify-between">
        <div class="flex items-center gap-4 md:gap-6">
            <a href="./overview.php">
                <img src="<?php echo DOMAIN; ?>/assets/global/logo.png" alt="Store" class="w-[32px] md:w-[40px]" />
            </a>
            <i class="fa-solid fa-bars text-[22px] text-[#4B5563] cursor-pointer lg:hidden" onclick="toggleNav()"></i>
            <h1 class="text-[18px] md:text-[20px] font-Onest font-semibold text-[#111827]">Storefront Settings</h1>
        </div>
        <div class="flex items-center gap-3 md:gap-5">
            <a href="<?php echo DOMAIN; ?>/index.php" target="_blank" class="hidden sm:inline-flex items-center gap-2 text-[13px] font-['Open Sans'] font-medium text-[#C2185B] hover:underline">
                <i class="fa-solid fa-arrow-up-right-from-square text-[12px]"></i> View storefront
            </a>
            <div class="relative">
                <button onclick="openNotification()" class="relative w-[40px] h-[40px] rounded-full bg-gray-50 hover:bg-gray-100 flex items-center justify-center transition-colors cursor-pointer">
                    <i class="fa-regular fa-bell text-[18px] text-[#4B5563]"></i>
                    <?php if ($unread_count > 0): ?>
                    <span class="absolute -top-[2px] -right-[2px] min-w-[18px] h-[18px] px-1 rounded-full bg-[#C2185B] text-white text-[10px] font-bold flex items-center justify-center"><?php echo $unread_count > 99 ? '99+' : $unread_count; ?></span>
                    <?php endif; ?>
                </button>
            </div>
            <a href="./settings/profile.php" class="flex items-center gap-2 cursor-pointer">
                <div class="w-[40px] h-[40px] rounded-full overflow-hidden ring-2 ring-gray-100">
                    <img src="<?php echo htmlspecialchars($profile_photo); ?>" alt="Profile" class="w-full h-full object-cover" onerror="this.src='../assets/home/user.svg';" />
                </div>
                <div class="hidden xl:block">
                    <p class="text-[14px] font-Onest font-semibold text-[#111827] leading-tight"><?php echo htmlspecialchars($admin_name); ?></p>
                    <p class="text-[12px] font-Onest font-regular text-gray-500"><?php echo htmlspecialchars($admin_email); ?></p>
                </div>
            </a>
        </div>
    </nav>
</header>

<?php include("sidebar.php"); ?>

<div id="main" class="md:p-4 flex flex-col gap-4 bg-[#FAFAFA] p-4">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h1 class="admin-page-title">Storefront Settings</h1>
            <p class="text-[13px] md:text-[14px] font-['Open Sans'] text-gray-500 mt-1">Customize every detail of your store — name, logo, colors, homepage content, testimonials, footer and contact info. The current design stays until you change it.</p>
        </div>
        <a href="<?php echo DOMAIN; ?>/index.php" target="_blank" class="admin-btn-outline w-fit">
            <i class="fa-solid fa-eye"></i> Preview live site
        </a>
    </div>

    <?php if ($saved_msg): ?>
    <div class="flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-3 text-[14px] font-medium">
        <i class="fa-solid fa-circle-check"></i> <?php echo htmlspecialchars($saved_msg); ?>
    </div>
    <?php endif; ?>
    <?php if ($error_msg): ?>
    <div class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-[14px] font-medium">
        <i class="fa-solid fa-circle-exclamation"></i> <?php echo htmlspecialchars($error_msg); ?>
    </div>
    <?php endif; ?>

    <!-- Tabs -->
    <div class="flex items-center gap-1 overflow-x-auto border-b border-gray-200 bg-white rounded-t-xl px-2 pt-2" id="store-options-tabs">
        <button data-opt="branding" class="opt-tab active flex items-center gap-2 px-4 py-2 text-[14px] font-['Open Sans'] font-medium text-gray-500 hover:text-gray-800 whitespace-nowrap border-b-[3px] border-transparent cursor-pointer">Branding <?php echo glor_flag($tabActive['branding']); ?></button>
        <button data-opt="homepage" class="opt-tab flex items-center gap-2 px-4 py-2 text-[14px] font-['Open Sans'] font-medium text-gray-500 hover:text-gray-800 whitespace-nowrap border-b-[3px] border-transparent cursor-pointer">Homepage <?php echo glor_flag($tabActive['homepage']); ?></button>
        <button data-opt="about" class="opt-tab flex items-center gap-2 px-4 py-2 text-[14px] font-['Open Sans'] font-medium text-gray-500 hover:text-gray-800 whitespace-nowrap border-b-[3px] border-transparent cursor-pointer">About Page <?php echo glor_flag($tabActive['about']); ?></button>
        <button data-opt="contact" class="opt-tab flex items-center gap-2 px-4 py-2 text-[14px] font-['Open Sans'] font-medium text-gray-500 hover:text-gray-800 whitespace-nowrap border-b-[3px] border-transparent cursor-pointer">Contact &amp; Footer <?php echo glor_flag($tabActive['contact']); ?></button>
        <button data-opt="testimonials" class="opt-tab flex items-center gap-2 px-4 py-2 text-[14px] font-['Open Sans'] font-medium text-gray-500 hover:text-gray-800 whitespace-nowrap border-b-[3px] border-transparent cursor-pointer">Testimonials <?php echo glor_flag($tabActive['testimonials']); ?></button>
    </div>

    <!-- ============ BRANDING ============ -->
    <form method="POST" class="opt-panel admin-card rounded-xl p-5 flex flex-col gap-4" data-panel="branding">
        <input type="hidden" name="save_tab" value="branding" />
        <details open class="opt-collapse rounded-xl border border-gray-100 bg-white group">
            <summary style="list-style:none" class="opt-collapse-head w-full flex items-center justify-between gap-2 px-4 py-3 bg-gray-50/70 cursor-pointer select-none rounded-t-xl text-left">
                <span class="text-[14px] font-Onest font-semibold text-gray-800">Store details</span>
                <span class="opt-collapse-chevron flex items-center justify-center w-6 h-6 rounded-md bg-white border border-gray-200 text-gray-500"><i class="fa-solid fa-chevron-down text-[12px] transition-transform group-open:rotate-180"></i></span>
            </summary>
            <div class="px-4 py-4 flex flex-col gap-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex flex-col gap-1">
                <label class="text-[13px] font-medium text-gray-600">Store name</label>
                <input name="store_name" class="admin-input" value="<?php echo store_escape($cfg['store_name']); ?>" />
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-[13px] font-medium text-gray-600">Search placeholder text</label>
                <input name="search_placeholder" class="admin-input" value="<?php echo store_escape($cfg['search_placeholder']); ?>" />
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex flex-col gap-1">
                <label class="text-[13px] font-medium text-gray-600">Logo URL</label>
                <input name="logo_url" class="admin-input" value="<?php echo store_escape($cfg['logo_url']); ?>" />
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-[13px] font-medium text-gray-600">Favicon URL</label>
                <input name="favicon_url" class="admin-input" value="<?php echo store_escape($cfg['favicon_url']); ?>" />
            </div>
        </div>
            </div>
        </details>

        <details open class="opt-collapse rounded-xl border border-gray-100 bg-white group">
            <summary style="list-style:none" class="opt-collapse-head w-full flex items-center justify-between gap-2 px-4 py-3 bg-gray-50/70 cursor-pointer select-none rounded-t-xl text-left">
                <span class="text-[14px] font-Onest font-semibold text-gray-800">Brand colors <span class="font-normal text-gray-400 text-[12px]">(applied across the whole storefront)</span></span>
                <span class="opt-collapse-chevron flex items-center justify-center w-6 h-6 rounded-md bg-white border border-gray-200 text-gray-500"><i class="fa-solid fa-chevron-down text-[12px] transition-transform group-open:rotate-180"></i></span>
            </summary>
            <div class="px-4 py-4 flex flex-col gap-4">
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <?php
                $colorFields = [
                    ['color_primary', 'Primary color'],
                    ['color_primary_dark', 'Primary dark (hover)'],
                    ['color_heading', 'Heading / dark color'],
                    ['color_tint', 'Header & footer tint'],
                    ['color_tint2', 'Reviews section tint'],
                    ['color_bg', 'Page background'],
                ];
                foreach ($colorFields as $cf):
                    list($ck, $cl) = $cf;
                ?>
                <div class="flex flex-col gap-1">
                    <label class="text-[13px] font-medium text-gray-600"><?php echo $cl; ?></label>
                    <div class="flex items-center gap-2">
                        <input name="<?php echo $ck; ?>" type="color" value="<?php echo store_escape($cfg[$ck]); ?>" class="w-[42px] h-[38px] rounded-md border border-gray-200 bg-white cursor-pointer" />
                        <input name="<?php echo $ck; ?>_text" type="text" value="<?php echo store_escape($cfg[$ck]); ?>" class="admin-input" data-color-text="<?php echo $ck; ?>" />
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            </div>
        </details>

        <div class="flex items-center justify-end gap-3 pt-2">
            <button type="submit" class="admin-btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save Branding</button>
        </div>
    </form>

    <!-- ============ HOMEPAGE ============ -->
    <form method="POST" class="opt-panel admin-card rounded-xl p-5 flex flex-col gap-6 hidden" data-panel="homepage">
        <input type="hidden" name="save_tab" value="homepage" />

        <details open class="opt-collapse rounded-xl border border-gray-100 bg-white group">
            <summary style="list-style:none" class="opt-collapse-head w-full flex items-center justify-between gap-2 px-4 py-3 bg-gray-50/70 cursor-pointer select-none rounded-t-xl text-left">
                <span class="text-[14px] font-Onest font-semibold text-gray-800">Hero Carousel</span>
                <span class="opt-collapse-chevron flex items-center justify-center w-6 h-6 rounded-md bg-white border border-gray-200 text-gray-500"><i class="fa-solid fa-chevron-down text-[12px] transition-transform group-open:rotate-180"></i></span>
            </summary>
            <div class="px-4 py-4 flex flex-col gap-3">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex flex-col gap-1">
                    <label class="text-[13px] font-medium text-gray-600">Hero title</label>
                    <input name="hero_title" class="admin-input" value="<?php echo store_escape($hero['title']); ?>" />
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-[13px] font-medium text-gray-600">Hero subtitle</label>
                    <input name="hero_subtitle" class="admin-input" value="<?php echo store_escape($hero['subtitle']); ?>" />
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-[13px] font-medium text-gray-600">Overlay gradient - from</label>
                    <div class="flex items-center gap-2">
                        <input name="hero_overlay_from" type="color" value="<?php echo store_escape($hero['overlay_from']); ?>" class="w-[42px] h-[38px] rounded-md border border-gray-200 bg-white cursor-pointer" />
                        <input type="text" value="<?php echo store_escape($hero['overlay_from']); ?>" class="admin-input" data-color-bind="hero_overlay_from" />
                    </div>
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-[13px] font-medium text-gray-600">Overlay gradient - to</label>
                    <div class="flex items-center gap-2">
                        <input name="hero_overlay_to" type="color" value="<?php echo store_escape($hero['overlay_to']); ?>" class="w-[42px] h-[38px] rounded-md border border-gray-200 bg-white cursor-pointer" />
                        <input type="text" value="<?php echo store_escape($hero['overlay_to']); ?>" class="admin-input" data-color-bind="hero_overlay_to" />
                    </div>
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-[13px] font-medium text-gray-600">Button 1 text</label>
                    <input name="hero_btn1_text" class="admin-input" value="<?php echo store_escape($hero['btn1_text']); ?>" />
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-[13px] font-medium text-gray-600">Button 1 link</label>
                    <input name="hero_btn1_url" class="admin-input" value="<?php echo store_escape($hero['btn1_url']); ?>" />
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-[13px] font-medium text-gray-600">Button 2 text</label>
                    <input name="hero_btn2_text" class="admin-input" value="<?php echo store_escape($hero['btn2_text']); ?>" />
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-[13px] font-medium text-gray-600">Button 2 link</label>
                    <input name="hero_btn2_url" class="admin-input" value="<?php echo store_escape($hero['btn2_url']); ?>" />
                </div>
            </div>

            <div>
                <p class="text-[13px] font-semibold text-gray-700 mb-2">Hero slide images (URLs) — 1 big + 3 smaller</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <?php
                    $slides = array_values($hero['slides']);
                    for ($i = 0; $i < 4; $i++):
                    ?>
                    <div class="flex items-center gap-2">
                        <span class="shrink-0 w-5 h-5 rounded-full bg-gray-100 text-gray-500 text-[11px] font-bold flex items-center justify-center"><?php echo $i + 1; ?></span>
                        <input name="hero_slides_<?php echo $i; ?>" class="admin-input" value="<?php echo store_escape($slides[$i] ?? ''); ?>" placeholder="https://image-url.jpg" />
                    </div>
                    <?php endfor; ?>
                </div>
            </div>
            </div>
        </details>

        <details open class="opt-collapse rounded-xl border border-gray-100 bg-white group">
            <summary style="list-style:none" class="opt-collapse-head w-full flex items-center justify-between gap-2 px-4 py-3 bg-gray-50/70 cursor-pointer select-none rounded-t-xl text-left">
                <span class="text-[14px] font-Onest font-semibold text-gray-800">"What You Can Get Here" section <span class="font-normal text-gray-400 text-[12px]">(each card links to a product search by keyword)</span></span>
                <span class="opt-collapse-chevron flex items-center justify-center w-6 h-6 rounded-md bg-white border border-gray-200 text-gray-500"><i class="fa-solid fa-chevron-down text-[12px] transition-transform group-open:rotate-180"></i></span>
            </summary>
            <div class="px-4 py-4 flex flex-col gap-3">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex flex-col gap-1">
                    <label class="text-[13px] font-medium text-gray-600">Eyebrow label</label>
                    <input name="shop_eyebrow" class="admin-input" value="<?php echo store_escape($shop['eyebrow']); ?>" />
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-[13px] font-medium text-gray-600">Heading</label>
                    <input name="shop_title" class="admin-input" value="<?php echo store_escape($shop['title']); ?>" />
                </div>
                <div class="flex flex-col gap-1 md:col-span-2">
                    <label class="text-[13px] font-medium text-gray-600">Subtitle</label>
                    <input name="shop_subtitle" class="admin-input" value="<?php echo store_escape($shop['subtitle']); ?>" />
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-[13px] font-medium text-gray-600">"View all" button text</label>
                    <input name="shop_view_all_text" class="admin-input" value="<?php echo store_escape($shop['view_all_text']); ?>" />
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-[13px] font-medium text-gray-600">"View all" button link</label>
                    <input name="shop_view_all_url" class="admin-input" value="<?php echo store_escape($shop['view_all_url']); ?>" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                <?php
                $shopItems = array_values($shop['items']);
                for ($i = 0; $i < 5; $i++):
                    $item = $shopItems[$i] ?? ['image' => '', 'title' => '', 'subtitle' => '', 'keyword' => ''];
                ?>
                <div class="border border-gray-100 rounded-xl p-3 flex flex-col gap-2 bg-gray-50/50">
                    <p class="text-[13px] font-semibold text-gray-600">Card <?php echo $i + 1; ?><?php echo $i === 0 ? ' (large)' : ''; ?></p>
                    <div class="flex flex-col gap-1">
                        <label class="text-[12px] font-medium text-gray-500">Image URL</label>
                        <input name="shop_item_<?php echo $i; ?>_image" class="admin-input" value="<?php echo store_escape($item['image']); ?>" placeholder="https://image-url.jpg" />
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-[12px] font-medium text-gray-500">Title</label>
                        <input name="shop_item_<?php echo $i; ?>_title" class="admin-input" value="<?php echo store_escape($item['title']); ?>" />
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-[12px] font-medium text-gray-500">Subtitle</label>
                        <input name="shop_item_<?php echo $i; ?>_subtitle" class="admin-input" value="<?php echo store_escape($item['subtitle']); ?>" />
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-[12px] font-medium text-gray-500">Search keyword <span class="text-gray-400">(what the card searches)</span></label>
                        <input name="shop_item_<?php echo $i; ?>_keyword" class="admin-input" value="<?php echo store_escape($item['keyword']); ?>" />
                    </div>
                </div>
                <?php endfor; ?>
            </div>
            </div>
        </details>

        <details open class="opt-collapse rounded-xl border border-gray-100 bg-white group">
            <summary style="list-style:none" class="opt-collapse-head w-full flex items-center justify-between gap-2 px-4 py-3 bg-gray-50/70 cursor-pointer select-none rounded-t-xl text-left">
                <span class="text-[14px] font-Onest font-semibold text-gray-800">Why-choose section</span>
                <span class="opt-collapse-chevron flex items-center justify-center w-6 h-6 rounded-md bg-white border border-gray-200 text-gray-500"><i class="fa-solid fa-chevron-down text-[12px] transition-transform group-open:rotate-180"></i></span>
            </summary>
            <div class="px-4 py-4 flex flex-col gap-3">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex flex-col gap-1">
                    <label class="text-[13px] font-medium text-gray-600">Eyebrow label</label>
                    <input name="why_label" class="admin-input" value="<?php echo store_escape($why['label']); ?>" />
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-[13px] font-medium text-gray-600">Heading</label>
                    <input name="why_title" class="admin-input" value="<?php echo store_escape($why['title']); ?>" />
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-[13px] font-medium text-gray-600">Side image URL</label>
                    <input name="why_image" class="admin-input" value="<?php echo store_escape($why['image']); ?>" />
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-[13px] font-medium text-gray-600">Overlay card title</label>
                    <input name="why_badge_title" class="admin-input" value="<?php echo store_escape($why['badge_title']); ?>" />
                </div>
                <div class="flex flex-col gap-1 md:col-span-2">
                    <label class="text-[13px] font-medium text-gray-600">Overlay card text</label>
                    <input name="why_badge_text" class="admin-input" value="<?php echo store_escape($why['badge_text']); ?>" />
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                <?php
                $whyFeatures = array_values($why['features']);
                for ($i = 0; $i < 4; $i++):
                    $f = $whyFeatures[$i] ?? ['title' => '', 'text' => ''];
                ?>
                <div class="border border-gray-100 rounded-xl p-3 flex flex-col gap-2 bg-gray-50/50">
                    <p class="text-[13px] font-semibold text-gray-600">Feature <?php echo $i + 1; ?></p>
                    <div class="flex flex-col gap-1">
                        <label class="text-[12px] font-medium text-gray-500">Title</label>
                        <input name="why_f<?php echo $i; ?>_title" class="admin-input" value="<?php echo store_escape($f['title']); ?>" />
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-[12px] font-medium text-gray-500">Text</label>
                        <input name="why_f<?php echo $i; ?>_text" class="admin-input" value="<?php echo store_escape($f['text']); ?>" />
                    </div>
                </div>
                <?php endfor; ?>
            </div>
            </div>
        </details>

        <details open class="opt-collapse rounded-xl border border-gray-100 bg-white group">
            <summary style="list-style:none" class="opt-collapse-head w-full flex items-center justify-between gap-2 px-4 py-3 bg-gray-50/70 cursor-pointer select-none rounded-t-xl text-left">
                <span class="text-[14px] font-Onest font-semibold text-gray-800">Intro banner &amp; stats</span>
                <span class="opt-collapse-chevron flex items-center justify-center w-6 h-6 rounded-md bg-white border border-gray-200 text-gray-500"><i class="fa-solid fa-chevron-down text-[12px] transition-transform group-open:rotate-180"></i></span>
            </summary>
            <div class="px-4 py-4 flex flex-col gap-3">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex flex-col gap-1">
                    <label class="text-[13px] font-medium text-gray-600">Overline label</label>
                    <input name="intro_label" class="admin-input" value="<?php echo store_escape($intro['label']); ?>" />
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-[13px] font-medium text-gray-600">Background image URL</label>
                    <input name="intro_image" class="admin-input" value="<?php echo store_escape($intro['image']); ?>" />
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-[13px] font-medium text-gray-600">Gradient - from</label>
                    <div class="flex items-center gap-2">
                        <input name="intro_grad_from" type="color" value="<?php echo store_escape($intro['grad_from']); ?>" class="w-[42px] h-[38px] rounded-md border border-gray-200 bg-white cursor-pointer" />
                        <input type="text" value="<?php echo store_escape($intro['grad_from']); ?>" class="admin-input" data-color-bind="intro_grad_from" />
                    </div>
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-[13px] font-medium text-gray-600">Gradient - to</label>
                    <div class="flex items-center gap-2">
                        <input name="intro_grad_to" type="color" value="<?php echo store_escape($intro['grad_to']); ?>" class="w-[42px] h-[38px] rounded-md border border-gray-200 bg-white cursor-pointer" />
                        <input type="text" value="<?php echo store_escape($intro['grad_to']); ?>" class="admin-input" data-color-bind="intro_grad_to" />
                    </div>
                </div>
                <div class="flex flex-col gap-1 md:col-span-2">
                    <label class="text-[13px] font-medium text-gray-600">Heading text</label>
                    <textarea name="intro_title" rows="2" class="admin-input"><?php echo store_escape($intro['title']); ?></textarea>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                <?php
                $stats = array_values($intro['stats']);
                for ($i = 0; $i < 3; $i++):
                    $s = $stats[$i] ?? ['value' => '', 'label' => ''];
                ?>
                <div class="border border-gray-100 rounded-xl p-3 flex flex-col gap-2 bg-gray-50/50">
                    <p class="text-[13px] font-semibold text-gray-600">Stat <?php echo $i + 1; ?></p>
                    <div class="flex flex-col gap-1">
                        <label class="text-[12px] font-medium text-gray-500">Value</label>
                        <input name="intro_stat_<?php echo $i; ?>_value" class="admin-input" value="<?php echo store_escape($s['value']); ?>" />
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-[12px] font-medium text-gray-500">Label</label>
                        <input name="intro_stat_<?php echo $i; ?>_label" class="admin-input" value="<?php echo store_escape($s['label']); ?>" />
                    </div>
                </div>
                <?php endfor; ?>
            </div>
            </div>
        </details>

        <details open class="opt-collapse rounded-xl border border-gray-100 bg-white group">
            <summary style="list-style:none" class="opt-collapse-head w-full flex items-center justify-between gap-2 px-4 py-3 bg-gray-50/70 cursor-pointer select-none rounded-t-xl text-left">
                <span class="text-[14px] font-Onest font-semibold text-gray-800">FAQ <span class="font-normal text-gray-400 text-[12px]">+ "See More Products" button</span></span>
                <span class="opt-collapse-chevron flex items-center justify-center w-6 h-6 rounded-md bg-white border border-gray-200 text-gray-500"><i class="fa-solid fa-chevron-down text-[12px] transition-transform group-open:rotate-180"></i></span>
            </summary>
            <div class="px-4 py-4 flex flex-col gap-3">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex flex-col gap-1">
                    <label class="text-[13px] font-medium text-gray-600">FAQ heading</label>
                    <input name="faq_title" class="admin-input" value="<?php echo store_escape($faq['title']); ?>" />
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-[13px] font-medium text-gray-600">"See More Products" text</label>
                    <input name="featured_cta" class="admin-input" value="<?php echo store_escape($cfg['featured_cta']); ?>" />
                </div>
                <div class="flex flex-col gap-1 md:col-span-2">
                    <label class="text-[13px] font-medium text-gray-600">Testimonials heading</label>
                    <input name="reviews_title" class="admin-input" value="<?php echo store_escape($cfg['reviews_title'] ?? "Don't Just Hear From Us, Hear From Our Glorefy Family"); ?>" />
                </div>
            </div>
            <div class="flex flex-col gap-2" id="faq-list">
                <?php
                $faqItems = isset($faq['items']) && is_array($faq['items']) ? array_values($faq['items']) : [];
                foreach ($faqItems as $fi): ?>
                <div class="border border-gray-100 rounded-xl p-3 flex flex-col md:flex-row gap-3 bg-gray-50/50">
                    <input name="faq_q[]" class="admin-input flex-1" placeholder="Question" value="<?php echo store_escape($fi['q']); ?>" />
                    <textarea name="faq_a[]" class="admin-input flex-1" rows="2" placeholder="Answer"><?php echo store_escape($fi['a']); ?></textarea>
                    <button type="button" onclick="this.closest('div').remove()" class="self-start md:self-center text-red-500 text-[14px] cursor-pointer px-2 py-1 hover:bg-red-50 rounded-md"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" onclick="addFaqRow()" class="admin-btn-outline w-fit text-[13px]"><i class="fa-solid fa-plus"></i> Add FAQ item</button>
            </div>
        </details>

        <div class="flex items-center justify-end gap-3 pt-2">
            <button type="submit" class="admin-btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save Homepage</button>
        </div>
    </form>

    <!-- ============ ABOUT ============ -->
    <form method="POST" class="opt-panel admin-card rounded-xl p-5 flex flex-col gap-6 hidden" data-panel="about">
        <input type="hidden" name="save_tab" value="about" />

        <details open class="opt-collapse rounded-xl border border-gray-100 bg-white group">
            <summary style="list-style:none" class="opt-collapse-head w-full flex items-center justify-between gap-2 px-4 py-3 bg-gray-50/70 cursor-pointer select-none rounded-t-xl text-left">
                <span class="text-[14px] font-Onest font-semibold text-gray-800">Hero &amp; about content</span>
                <span class="opt-collapse-chevron flex items-center justify-center w-6 h-6 rounded-md bg-white border border-gray-200 text-gray-500"><i class="fa-solid fa-chevron-down text-[12px] transition-transform group-open:rotate-180"></i></span>
            </summary>
            <div class="px-4 py-4 flex flex-col gap-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex flex-col gap-1">
                    <label class="text-[13px] font-medium text-gray-600">Hero banner image URL</label>
                <input name="about_hero_image" class="admin-input" value="<?php echo store_escape($about['hero_image']); ?>" />
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-[13px] font-medium text-gray-600">Hero banner heading</label>
                <input name="about_hero_heading" class="admin-input" value="<?php echo store_escape($about['hero_heading']); ?>" />
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-[13px] font-medium text-gray-600">About image URL</label>
                <input name="about_image" class="admin-input" value="<?php echo store_escape($about['image']); ?>" />
            </div>
        </div>
        <div class="flex flex-col gap-1">
            <label class="text-[13px] font-medium text-gray-600">About content <span class="text-gray-400">(one paragraph per line)</span></label>
            <textarea name="about_content" rows="8" class="admin-input"><?php echo store_escape(implode("\n", array_values($about['content']))); ?></textarea>
        </div>
            </div>
        </details>

        <details open class="opt-collapse rounded-xl border border-gray-100 bg-white group">
            <summary style="list-style:none" class="opt-collapse-head w-full flex items-center justify-between gap-2 px-4 py-3 bg-gray-50/70 cursor-pointer select-none rounded-t-xl text-left">
                <span class="text-[14px] font-Onest font-semibold text-gray-800">Why-choose (about page)</span>
                <span class="opt-collapse-chevron flex items-center justify-center w-6 h-6 rounded-md bg-white border border-gray-200 text-gray-500"><i class="fa-solid fa-chevron-down text-[12px] transition-transform group-open:rotate-180"></i></span>
            </summary>
            <div class="px-4 py-4 flex flex-col gap-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex flex-col gap-1">
                    <label class="text-[13px] font-medium text-gray-600">Heading</label>
                    <input name="about_why_title" class="admin-input" value="<?php echo store_escape($about['why']['title']); ?>" />
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-[13px] font-medium text-gray-600">Image URL</label>
                    <input name="about_why_image" class="admin-input" value="<?php echo store_escape($about['why']['image']); ?>" />
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                <?php
                $aboutWhys = array_values($about['why']['features']);
                for ($i = 0; $i < 4; $i++):
                    $f = $aboutWhys[$i] ?? ['icon' => '', 'title' => '', 'text' => ''];
                ?>
                <div class="border border-gray-100 rounded-xl p-3 flex flex-col gap-2 bg-gray-50/50">
                    <p class="text-[13px] font-semibold text-gray-600">Feature <?php echo $i + 1; ?></p>
                    <div class="flex flex-col gap-1">
                        <label class="text-[12px] font-medium text-gray-500">Icon (Font Awesome class)</label>
                        <input name="about_wf<?php echo $i; ?>_icon" class="admin-input" value="<?php echo store_escape($f['icon']); ?>" placeholder="fa-badge-check" />
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-[12px] font-medium text-gray-500">Title</label>
                        <input name="about_wf<?php echo $i; ?>_title" class="admin-input" value="<?php echo store_escape($f['title']); ?>" />
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-[12px] font-medium text-gray-500">Text</label>
                        <input name="about_wf<?php echo $i; ?>_text" class="admin-input" value="<?php echo store_escape($f['text']); ?>" />
                    </div>
                </div>
                <?php endfor; ?>
            </div>
            </div>
        </details>

        <details open class="opt-collapse rounded-xl border border-gray-100 bg-white group">
            <summary style="list-style:none" class="opt-collapse-head w-full flex items-center justify-between gap-2 px-4 py-3 bg-gray-50/70 cursor-pointer select-none rounded-t-xl text-left">
                <span class="text-[14px] font-Onest font-semibold text-gray-800">Collection showcase</span>
                <span class="opt-collapse-chevron flex items-center justify-center w-6 h-6 rounded-md bg-white border border-gray-200 text-gray-500"><i class="fa-solid fa-chevron-down text-[12px] transition-transform group-open:rotate-180"></i></span>
            </summary>
            <div class="px-4 py-4 flex flex-col gap-4">
            <div class="flex flex-col gap-1 md:max-w-sm">
                <label class="text-[13px] font-medium text-gray-600">Heading</label>
                <input name="about_collection_title" class="admin-input" value="<?php echo store_escape($about['collection']['title']); ?>" />
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                <?php
                $aboutCards = array_values($about['collection']['cards']);
                for ($i = 0; $i < 5; $i++):
                    $c = $aboutCards[$i] ?? ['image' => '', 'title' => '', 'subtitle' => ''];
                ?>
                <div class="border border-gray-100 rounded-xl p-3 flex flex-col gap-2 bg-gray-50/50">
                    <p class="text-[13px] font-semibold text-gray-600">Card <?php echo $i + 1; ?><?php echo $i === 0 ? ' (large)' : ''; ?></p>
                    <div class="flex flex-col gap-1">
                        <label class="text-[12px] font-medium text-gray-500">Image URL</label>
                        <input name="about_card_<?php echo $i; ?>_image" class="admin-input" value="<?php echo store_escape($c['image']); ?>" />
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-[12px] font-medium text-gray-500">Title</label>
                        <input name="about_card_<?php echo $i; ?>_title" class="admin-input" value="<?php echo store_escape($c['title']); ?>" />
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-[12px] font-medium text-gray-500">Subtitle</label>
                        <input name="about_card_<?php echo $i; ?>_subtitle" class="admin-input" value="<?php echo store_escape($c['subtitle']); ?>" />
                    </div>
                </div>
                <?php endfor; ?>
            </div>
            </div>
        </details>

        <div class="flex items-center justify-end gap-3 pt-2">
            <button type="submit" class="admin-btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save About Page</button>
        </div>
    </form>

    <!-- ============ CONTACT & FOOTER ============ -->
    <form method="POST" class="opt-panel admin-card rounded-xl p-5 flex flex-col gap-6 hidden" data-panel="contact">
        <input type="hidden" name="save_tab" value="contact" />

        <details open class="opt-collapse rounded-xl border border-gray-100 bg-white group">
            <summary style="list-style:none" class="opt-collapse-head w-full flex items-center justify-between gap-2 px-4 py-3 bg-gray-50/70 cursor-pointer select-none rounded-t-xl text-left">
                <span class="text-[14px] font-Onest font-semibold text-gray-800">Contact details</span>
                <span class="opt-collapse-chevron flex items-center justify-center w-6 h-6 rounded-md bg-white border border-gray-200 text-gray-500"><i class="fa-solid fa-chevron-down text-[12px] transition-transform group-open:rotate-180"></i></span>
            </summary>
            <div class="px-4 py-4 flex flex-col gap-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex flex-col gap-1">
                    <label class="text-[13px] font-medium text-gray-600">Phone</label>
                    <input name="contact_phone" class="admin-input" value="<?php echo store_escape($contact['phone']); ?>" />
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-[13px] font-medium text-gray-600">Email</label>
                    <input name="contact_email" class="admin-input" value="<?php echo store_escape($contact['email']); ?>" />
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-[13px] font-medium text-gray-600">Address</label>
                    <input name="contact_address" class="admin-input" value="<?php echo store_escape($contact['address']); ?>" />
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-[13px] font-medium text-gray-600">Google Maps embed URL</label>
                    <input name="contact_map_url" class="admin-input" value="<?php echo store_escape($contact['map_url']); ?>" />
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-[13px] font-medium text-gray-600">Hero banner image URL</label>
                    <input name="contact_hero_image" class="admin-input" value="<?php echo store_escape($contact['hero_image']); ?>" />
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-[13px] font-medium text-gray-600">Hero banner heading</label>
                    <input name="contact_hero_heading" class="admin-input" value="<?php echo store_escape($contact['hero_heading']); ?>" />
                </div>
                <div class="flex flex-col gap-1 md:col-span-2">
                    <label class="text-[13px] font-medium text-gray-600">Contact form subject</label>
                    <input name="contact_form_subject" class="admin-input" value="<?php echo store_escape($contact['form_subject']); ?>" />
                </div>
            </div>
            </div>
        </details>

        <details open class="opt-collapse rounded-xl border border-gray-100 bg-white group">
            <summary style="list-style:none" class="opt-collapse-head w-full flex items-center justify-between gap-2 px-4 py-3 bg-gray-50/70 cursor-pointer select-none rounded-t-xl text-left">
                <span class="text-[14px] font-Onest font-semibold text-gray-800">Footer</span>
                <span class="opt-collapse-chevron flex items-center justify-center w-6 h-6 rounded-md bg-white border border-gray-200 text-gray-500"><i class="fa-solid fa-chevron-down text-[12px] transition-transform group-open:rotate-180"></i></span>
            </summary>
            <div class="px-4 py-4 flex flex-col gap-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex flex-col gap-1">
                    <label class="text-[13px] font-medium text-gray-600">Footer about text</label>
                    <textarea name="foot_blurb" rows="3" class="admin-input"><?php echo store_escape($cfg['foot_blurb']); ?></textarea>
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-[13px] font-medium text-gray-600">Newsletter heading</label>
                    <input name="news_title" class="admin-input" value="<?php echo store_escape($cfg['news_title']); ?>" />
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-[13px] font-medium text-gray-600">Newsletter text</label>
                    <input name="news_text" class="admin-input" value="<?php echo store_escape($cfg['news_text']); ?>" />
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-[13px] font-medium text-gray-600">Copyright name</label>
                    <input name="copyright_name" class="admin-input" value="<?php echo store_escape($cfg['copyright_name']); ?>" />
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-[13px] font-medium text-gray-600">WhatsApp number (no symbols)</label>
                    <input name="whatsapp_number" class="admin-input" value="<?php echo store_escape($cfg['whatsapp_number']); ?>" />
                </div>
            </div>
            <div>
                <p class="text-[13px] font-semibold text-gray-700 mb-2">Social links <span class="text-gray-400 font-normal">(leave empty to hide an icon)</span></p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <?php
                    $socialFields = [
                        ['facebook', 'Facebook', 'fa-facebook-f'],
                        ['instagram', 'Instagram', 'fa-instagram'],
                        ['whatsapp', 'WhatsApp', 'fa-whatsapp'],
                        ['pinterest', 'Pinterest', 'fa-pinterest-p'],
                        ['youtube', 'YouTube', 'fa-youtube'],
                        ['x', 'X (Twitter)', 'fa-x-twitter'],
                    ];
                    foreach ($socialFields as $sf):
                        list($sk, $sl, $si) = $sf;
                    ?>
                    <div class="flex items-center gap-2">
                        <i class="fa-brands <?php echo $si; ?> text-gray-400 w-4 text-center"></i>
                        <input name="social_<?php echo $sk; ?>" class="admin-input" value="<?php echo store_escape($social[$sk] ?? ''); ?>" placeholder="https://..." />
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            </div>
        </details>

        <div class="flex items-center justify-end gap-3 pt-2">
            <button type="submit" class="admin-btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save Contact &amp; Footer</button>
        </div>
    </form>

    <!-- ============ TESTIMONIALS ============ -->
    <form method="POST" class="opt-panel admin-card rounded-xl p-5 flex flex-col gap-4 hidden" data-panel="testimonials">
        <input type="hidden" name="save_tab" value="testimonials" />

        <details open class="opt-collapse rounded-xl border border-gray-100 bg-white group">
            <summary style="list-style:none" class="opt-collapse-head w-full flex items-center justify-between gap-2 px-4 py-3 bg-gray-50/70 cursor-pointer select-none rounded-t-xl text-left">
                <span class="text-[14px] font-Onest font-semibold text-gray-800">Reviews <span class="font-normal text-gray-400 text-[12px]">(shown in two auto-scrolling rows — Row 1 right, Row 2 left)</span></span>
                <span class="opt-collapse-chevron flex items-center justify-center w-6 h-6 rounded-md bg-white border border-gray-200 text-gray-500"><i class="fa-solid fa-chevron-down text-[12px] transition-transform group-open:rotate-180"></i></span>
            </summary>
            <div class="px-4 py-4 flex flex-col gap-4">
            <div class="flex flex-col gap-3">
            <?php foreach ($reviewsAdmin as $i => $review): ?>
            <div class="border border-gray-100 rounded-xl p-3 flex flex-col gap-2 bg-gray-50/50">
                <div class="flex items-center justify-between">
                    <p class="text-[13px] font-semibold text-gray-600">Review <?php echo $i + 1; ?></p>
                    <label class="flex items-center gap-2 text-[13px] text-gray-500 cursor-pointer">
                        <input type="checkbox" name="review_active[<?php echo $i; ?>]" value="1" <?php echo $review['active'] ? 'checked' : ''; ?> class="accent-pink-600" /> Show
                    </label>
                </div>
                <input type="hidden" name="review_id[<?php echo $i; ?>]" value="<?php echo (int) $review['review_id']; ?>" />
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div class="flex flex-col gap-1">
                        <label class="text-[12px] font-medium text-gray-500">Name</label>
                        <input name="review_name[<?php echo $i; ?>]" class="admin-input" value="<?php echo store_escape($review['name']); ?>" />
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-[12px] font-medium text-gray-500">Rating (1-5)</label>
                        <select name="review_rating[<?php echo $i; ?>]" class="admin-select">
                            <?php for ($r = 1; $r <= 5; $r++): ?>
                            <option value="<?php echo $r; ?>" <?php echo (int) $review['rating'] === $r ? 'selected' : ''; ?>><?php echo $r; ?> <?php echo $r === 1 ? 'star' : 'stars'; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-[12px] font-medium text-gray-500">Row</label>
                        <select name="review_row[<?php echo $i; ?>]" class="admin-select">
                            <option value="0" <?php echo (int) $review['row'] === 0 ? 'selected' : ''; ?>>Row 1 (top)</option>
                            <option value="1" <?php echo (int) $review['row'] === 1 ? 'selected' : ''; ?>>Row 2 (bottom)</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-1 md:col-span-2">
                        <label class="text-[12px] font-medium text-gray-500">Text</label>
                        <textarea name="review_text[<?php echo $i; ?>]" rows="2" class="admin-input"><?php echo store_escape($review['text']); ?></textarea>
                    </div>
                    <div class="flex items-end justify-end">
                        <button type="submit" name="delete_review[<?php echo $i; ?>]" value="<?php echo (int) $review['review_id']; ?>" onclick="return confirm('Delete this review?')" class="text-red-500 text-[14px] cursor-pointer px-3 py-2 hover:bg-red-50 rounded-md border border-red-100">
                            <i class="fa-solid fa-trash-can"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            </div>
            </div>
        </details>

        <details open class="opt-collapse rounded-xl border border-gray-100 bg-white group">
            <summary style="list-style:none" class="opt-collapse-head w-full flex items-center justify-between gap-2 px-4 py-3 bg-gray-50/70 cursor-pointer select-none rounded-t-xl text-left">
                <span class="text-[14px] font-Onest font-semibold text-gray-800">Add a new review</span>
                <span class="opt-collapse-chevron flex items-center justify-center w-6 h-6 rounded-md bg-white border border-gray-200 text-gray-500"><i class="fa-solid fa-chevron-down text-[12px] transition-transform group-open:rotate-180"></i></span>
            </summary>
            <div class="px-4 py-4 flex flex-col gap-4">
            <div class="flex flex-col gap-3">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div class="flex flex-col gap-1">
                        <label class="text-[12px] font-medium text-gray-500">Name</label>
                        <input name="new_name[0]" class="admin-input" />
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-[12px] font-medium text-gray-500">Rating</label>
                        <select name="new_rating[0]" class="admin-select">
                            <?php for ($r = 5; $r >= 1; $r--): ?><option value="<?php echo $r; ?>"><?php echo $r; ?> <?php echo $r === 1 ? 'star' : 'stars'; ?></option><?php endfor; ?>
                        </select>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-[12px] font-medium text-gray-500">Row</label>
                        <select name="new_row[0]" class="admin-select">
                            <option value="0">Row 1 (top)</option>
                            <option value="1">Row 2 (bottom)</option>
                        </select>
                    </div>
                </div>
<div class="flex flex-col gap-1">
                    <label class="text-[13px] font-medium text-gray-600">Text</label>
                    <textarea name="new_text[0]" rows="2" class="admin-input"></textarea>
                </div>
            </div>
            </div>
        </details>

        <div class="flex items-center justify-end gap-3 pt-2">
            <button type="submit" class="admin-btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save Testimonials</button>
        </div>
    </form>

</div>

<script>
    // Tab switching
    function addFaqRow() {
        const list = document.getElementById('faq-list');
        const div = document.createElement('div');
        div.className = 'border border-gray-100 rounded-xl p-3 flex flex-col md:flex-row gap-3 bg-gray-50/50';
        div.innerHTML = '<input name="faq_q[]" class="admin-input flex-1" placeholder="Question" />' +
            '<textarea name="faq_a[]" class="admin-input flex-1" rows="2" placeholder="Answer"></textarea>' +
            '<button type="button" onclick="this.closest(\'div\').remove()" class="self-start md:self-center text-red-500 text-[14px] cursor-pointer px-2 py-1 hover:bg-red-50 rounded-md"><i class="fa-solid fa-xmark"></i></button>';
        list.appendChild(div);
    }

    document.querySelectorAll('#store-options-tabs .opt-tab').forEach(function(tab) {
        tab.addEventListener('click', function() {
            document.querySelectorAll('#store-options-tabs .opt-tab').forEach(function(t) {
                t.classList.remove('active', 'text-[#C2185B]', 'border-[#C2185B]');
                t.classList.add('text-gray-500', 'border-transparent');
            });
            this.classList.add('active', 'text-[#C2185B]', 'border-[#C2185B]');
            const panel = this.getAttribute('data-opt');
            document.querySelectorAll('.opt-panel').forEach(function(p) {
                p.classList.toggle('hidden', p.getAttribute('data-panel') !== panel);
            });
        });
    });

    // Sync color pickers with hex text boxes
    document.querySelectorAll('input[type="color"]').forEach(function(picker) {
        const bindName = picker.getAttribute('data-color-bind');
        const textMirror = document.querySelector('[data-color-bind="' + bindName + '"]');
        const textByKey = document.querySelector('[data-color-text="' + picker.name + '"]');
        picker.addEventListener('input', function() {
            if (textMirror) textMirror.value = picker.value;
            if (textByKey) textByKey.value = picker.value;
        });
        if (textMirror) {
            textMirror.addEventListener('input', function() {
                if (/^#[0-9a-fA-F]{6}$/.test(textMirror.value)) picker.value = textMirror.value;
            });
        }
        if (textByKey) {
            textByKey.addEventListener('input', function() {
                if (/^#[0-9a-fA-F]{6}$/.test(textByKey.value)) picker.value = textByKey.value;
            });
        }
    });
</script>

<script type="text/javascript" src="../../functions/drop-select.js"></script>
<script type="text/javascript" src="../../functions/order.js"></script>
<script type="text/javascript" src="../../functions/dash.js"></script>
<script type="text/javascript" src="../../functions/tab.js"></script>
<script type="text/javascript" src="../../functions/overlay.js"></script>
<script type="text/javascript" src="../../functions/ordermenu.js"></script>
<script type="text/javascript" src="../../functions/nav.js"></script>

</body>

</html>