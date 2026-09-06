<?php
// products/filters.php - Pure filter/sort logic + facet data. No markup.
// MUST be included BEFORE config/products.php so its functions are available
// while the main product query is being built.
if (defined('GLOREFY_PRODUCT_FILTERS_LOADED')) {
    return;
}
define('GLOREFY_PRODUCT_FILTERS_LOADED', true);

if (!isset($con)) {
    require_once __DIR__ . '/../config/config.php';
    $con = db();
}

// ---- Current selections (single source of truth for the products page) ----
$base_filter_url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$category_id = isset($_GET['category']) && $_GET['category'] !== '' ? (int)$_GET['category'] : null;
if ($category_id !== null && $category_id <= 0) {
    $category_id = null;
}

$brand_id = isset($_GET['brand']) && $_GET['brand'] !== '' ? (int)$_GET['brand'] : null;
if ($brand_id !== null && $brand_id <= 0) {
    $brand_id = null;
}

$search = isset($_GET['search']) ? trim((string)$_GET['search']) : '';

$raw_sort = isset($_GET['sort']) ? (string)$_GET['sort'] : '';
$sort_by = in_array($raw_sort, ['popular', 'price_high_low', 'price_low_high', 'latest'], true) ? $raw_sort : 'latest';

$color_filter = isset($_GET['color']) ? strtolower(str_replace(' ', '', trim((string)$_GET['color']))) : '';
$price_range = isset($_GET['price']) ? trim((string)$_GET['price']) : '';
$size_filter = isset($_GET['size']) ? trim((string)$_GET['size']) : '';
$texture_filter = isset($_GET['texture']) ? trim((string)$_GET['texture']) : '';

// ---- Price ranges ----
$price_ranges = [
    '0-50000' => '₦0 - ₦50,000',
    '50000-150000' => '₦50,000 - ₦150,000',
    '150000-250000' => '₦150,000 - ₦250,000',
    '250000-500000' => '₦250,000 - ₦500,000',
    '500000-99999999' => '₦500,000 and above'
];

// ---- Helpers ----
function buildFilterUrl($param_name, $param_value)
{
    $params = $_GET;

    if ($param_value === '' || $param_value === null || $param_value === 0) {
        unset($params[$param_name]);
    } else {
        $params[$param_name] = (string)$param_value;
    }

    $params['page'] = 1;

    foreach ($params as $k => $v) {
        if ($v === '' || $v === null) {
            unset($params[$k]);
        }
    }

    return '?' . http_build_query($params);
}

function isSelected($type, $value)
{
    global $sort_by, $color_filter, $price_range, $size_filter, $texture_filter, $category_id, $brand_id;

    $current = [
        'sort' => in_array($sort_by, ['', 'latest'], true) ? 'latest' : $sort_by,
        'color' => (string)$color_filter,
        'price' => (string)$price_range,
        'size' => (string)$size_filter,
        'texture' => (string)$texture_filter,
        'category' => $category_id === null ? '' : (string)$category_id,
        'brand' => $brand_id === null ? '' : (string)$brand_id,
    ];

    $ref = isset($current[$type]) ? $current[$type] : '';

    return $ref === (string)$value;
}

function sort_label()
{
    global $sort_by;
    $map = [
        'popular' => 'Popularity',
        'price_high_low' => 'Price: High to Low',
        'price_low_high' => 'Price: Low to High',
    ];
    return isset($map[$sort_by]) ? $map[$sort_by] : 'Latest';
}

function productCountWhere($extra_where)
{
    global $con;
    $sql = "SELECT COUNT(DISTINCT p.product_id) AS c FROM products p WHERE 1=1 " . $extra_where;
    $r = mysqli_query($con, $sql);
    if (!$r) {
        return 0;
    }
    return (int)mysqli_fetch_assoc($r)['c'];
}

function buildFacetWhere(array $skip = [])
{
    global $con, $category_id, $brand_id, $search, $color_filter, $size_filter, $texture_filter, $price_range;

    $w = '';

    if ($category_id && !in_array('category', $skip, true)) {
        $w .= " AND p.category_id = " . (int)$category_id;
    }

    if ($brand_id && !in_array('brand', $skip, true)) {
        $w .= " AND p.brand_id = " . (int)$brand_id;
    }

    if ($search !== '' && !in_array('search', $skip, true)) {
        $s = mysqli_real_escape_string($con, $search);
        $s = str_replace(['%', '_'], ['\%', '\_'], $s);
        $w .= " AND (
                 p.product_name LIKE '%$s%'
              OR p.details LIKE '%$s%'
              OR p.colors LIKE '%$s%'
              OR EXISTS (SELECT 1 FROM categories cxs WHERE cxs.category_id = p.category_id AND cxs.category_title LIKE '%$s%')
              OR EXISTS (SELECT 1 FROM brands bxs WHERE bxs.brand_id = p.brand_id AND bxs.brand_title LIKE '%$s%')
              OR EXISTS (SELECT 1 FROM product_variants pvxs WHERE pvxs.product_id = p.product_id AND (pvxs.size LIKE '%$s%' OR pvxs.texture LIKE '%$s%'))
             )";
    }

    if ($color_filter !== '' && !in_array('color', $skip, true)) {
        $c = mysqli_real_escape_string($con, $color_filter);
        $w .= " AND FIND_IN_SET('$c', LOWER(REPLACE(p.colors,' ','')) ) > 0";
    }

    if ($size_filter !== '' && !in_array('size', $skip, true)) {
        $sz = mysqli_real_escape_string($con, $size_filter);
        $w .= " AND EXISTS (SELECT 1 FROM product_variants pvx WHERE pvx.product_id = p.product_id AND pvx.size = '$sz')";
    }

    if ($texture_filter !== '' && !in_array('texture', $skip, true)) {
        $t = mysqli_real_escape_string($con, $texture_filter);
        $w .= " AND EXISTS (SELECT 1 FROM product_variants pvx WHERE pvx.product_id = p.product_id AND pvx.texture = '$t')";
    }

    if ($price_range !== '' && !in_array('price', $skip, true) && isset($GLOBALS['price_ranges'][$price_range])) {
        $parts = array_map('intval', explode('-', $price_range));
        $min = $parts[0];
        $max = $parts[1];
        $w .= " AND EXISTS (SELECT 1 FROM product_variants pvx WHERE pvx.product_id = p.product_id AND ((pvx.discount_price > 0 AND pvx.discount_price BETWEEN $min AND $max) OR (pvx.discount_price IS NULL AND pvx.original_price BETWEEN $min AND $max)))";
    }

    return $w;
}

function applyFiltersToQuery($query)
{
    global $con, $search, $color_filter, $size_filter, $texture_filter, $price_range;

    if ($search !== '') {
        $s = mysqli_real_escape_string($con, $search);
        $s = str_replace(['%', '_'], ['\%', '\_'], $s);
        $query .= " AND (
                 p.product_name LIKE '%$s%'
              OR p.details LIKE '%$s%'
              OR p.colors LIKE '%$s%'
              OR EXISTS (SELECT 1 FROM categories cxs WHERE cxs.category_id = p.category_id AND cxs.category_title LIKE '%$s%')
              OR EXISTS (SELECT 1 FROM brands bxs WHERE bxs.brand_id = p.brand_id AND bxs.brand_title LIKE '%$s%')
              OR EXISTS (SELECT 1 FROM product_variants pvxs WHERE pvxs.product_id = p.product_id AND (pvxs.size LIKE '%$s%' OR pvxs.texture LIKE '%$s%'))
             )";
    }

    if ($color_filter !== '') {
        $c = mysqli_real_escape_string($con, $color_filter);
        $query .= " AND FIND_IN_SET('$c', LOWER(REPLACE(p.colors,' ','')) ) > 0";
    }

    if ($size_filter !== '') {
        $sz = mysqli_real_escape_string($con, $size_filter);
        $query .= " AND EXISTS (SELECT 1 FROM product_variants pv WHERE pv.product_id = p.product_id AND pv.size = '$sz')";
    }

    if ($texture_filter !== '') {
        $t = mysqli_real_escape_string($con, $texture_filter);
        $query .= " AND EXISTS (SELECT 1 FROM product_variants pv WHERE pv.product_id = p.product_id AND pv.texture = '$t')";
    }

    if ($price_range !== '' && isset($GLOBALS['price_ranges'][$price_range])) {
        $parts = array_map('intval', explode('-', $price_range));
        $min = $parts[0];
        $max = $parts[1];
        $query .= " AND EXISTS (SELECT 1 FROM product_variants pv WHERE pv.product_id = p.product_id AND ((pv.discount_price > 0 AND pv.discount_price BETWEEN $min AND $max) OR (pv.discount_price IS NULL AND pv.original_price BETWEEN $min AND $max)))";
    }

    return $query;
}

function applySortingToQuery($query)
{
    global $sort_by;

    $query = preg_replace('/ORDER BY.*$/i', '', $query);

    switch ($sort_by) {
        case 'popular':
            $query .= " ORDER BY p.is_featured DESC, p.product_id DESC";
            break;
        case 'price_high_low':
            $query .= " ORDER BY MIN(CASE WHEN v.discount_price > 0 THEN v.discount_price ELSE v.original_price END) DESC, p.product_id DESC";
            break;
        case 'price_low_high':
            $query .= " ORDER BY MIN(CASE WHEN v.discount_price > 0 THEN v.discount_price ELSE v.original_price END) ASC, p.product_id DESC";
            break;
        case 'latest':
        default:
            $query .= " ORDER BY p.date_added DESC";
            break;
    }

    return $query;
}

// ---- Facet data with product counts ----
// Colors
$color_map = [];
$r = mysqli_query($con, "SELECT colors FROM products WHERE colors IS NOT NULL AND colors != ''");
if ($r) {
    while ($row = mysqli_fetch_assoc($r)) {
        foreach (explode(',', $row['colors']) as $c) {
            $c = trim($c);
            if ($c === '') {
                continue;
            }
            $key = strtolower(str_replace(' ', '', $c));
            $color_map[$key] = $c;
        }
    }
}

$available_colors = [];
foreach ($color_map as $key => $label) {
    $count = productCountWhere(buildFacetWhere(['color']) . " AND FIND_IN_SET('" . mysqli_real_escape_string($con, $key) . "', LOWER(REPLACE(p.colors,' ','')) ) > 0");
    $available_colors[] = ['value' => $key, 'label' => $label, 'count' => $count];
}

// Sizes
$available_sizes = [];
$r = mysqli_query($con, "SELECT DISTINCT size FROM product_variants WHERE size IS NOT NULL AND size != '' ORDER BY size");
if ($r) {
    while ($row = mysqli_fetch_assoc($r)) {
        $size = $row['size'];
        $esc = mysqli_real_escape_string($con, $size);
        $count = productCountWhere(buildFacetWhere(['size']) . " AND EXISTS (SELECT 1 FROM product_variants pvx WHERE pvx.product_id = p.product_id AND pvx.size = '$esc')");
        $available_sizes[] = ['value' => $size, 'label' => $size, 'count' => $count];
    }
}

// Textures
$available_textures = [];
$r = mysqli_query($con, "SELECT DISTINCT texture FROM product_variants WHERE texture IS NOT NULL AND texture != '' ORDER BY texture");
if ($r) {
    while ($row = mysqli_fetch_assoc($r)) {
        $texture = $row['texture'];
        $esc = mysqli_real_escape_string($con, $texture);
        $count = productCountWhere(buildFacetWhere(['texture']) . " AND EXISTS (SELECT 1 FROM product_variants pvx WHERE pvx.product_id = p.product_id AND pvx.texture = '$esc')");
        $available_textures[] = ['value' => $texture, 'label' => $texture, 'count' => $count];
    }
}

// Categories
$available_categories = [];
$r = mysqli_query($con, "SELECT category_id, category_title FROM categories ORDER BY category_title");
if ($r) {
    while ($row = mysqli_fetch_assoc($r)) {
        $cid = (int)$row['category_id'];
        $count = productCountWhere(buildFacetWhere(['category']) . " AND p.category_id = $cid");
        $available_categories[] = ['value' => $cid, 'label' => $row['category_title'], 'count' => $count];
    }
}

// Brands
$available_brands = [];
$r = mysqli_query($con, "SELECT brand_id, brand_title FROM brands ORDER BY brand_title");
if ($r) {
    while ($row = mysqli_fetch_assoc($r)) {
        $bid = (int)$row['brand_id'];
        $count = productCountWhere(buildFacetWhere(['brand']) . " AND p.brand_id = $bid");
        $available_brands[] = ['value' => $bid, 'label' => $row['brand_title'], 'count' => $count];
    }
}

// Price ranges
$available_price_ranges = [];
foreach ($price_ranges as $key => $label) {
    $parts = array_map('intval', explode('-', $key));
    $min = $parts[0];
    $max = $parts[1];
    $count = productCountWhere(buildFacetWhere(['price']) . " AND EXISTS (SELECT 1 FROM product_variants pvx WHERE pvx.product_id = p.product_id AND ((pvx.discount_price > 0 AND pvx.discount_price BETWEEN $min AND $max) OR (pvx.discount_price IS NULL AND pvx.original_price BETWEEN $min AND $max)))");
    $available_price_ranges[] = ['value' => $key, 'label' => $label, 'count' => $count];
}

// ---- Labels for current selections ----
$category_label = '';
foreach ($available_categories as $c) {
    if ($c['value'] == $category_id) {
        $category_label = $c['label'];
        break;
    }
}

$brand_label = '';
foreach ($available_brands as $b) {
    if ($b['value'] == $brand_id) {
        $brand_label = $b['label'];
        break;
    }
}

// ---- Validation notices when a selected filter value doesn't exist ----
$filter_notices = [];

if ($category_id !== null && $category_label === '') {
    $filter_notices[] = ['message' => 'The selected category no longer exists.', 'url' => buildFilterUrl('category', '')];
}

if ($brand_id !== null && $brand_label === '') {
    $filter_notices[] = ['message' => 'The selected brand no longer exists.', 'url' => buildFilterUrl('brand', '')];
}

if ($color_filter !== '' && !isset($color_map[$color_filter])) {
    $filter_notices[] = ['message' => 'The selected color is not available.', 'url' => buildFilterUrl('color', '')];
}

if ($price_range !== '' && !isset($price_ranges[$price_range])) {
    $filter_notices[] = ['message' => 'The selected price range is not valid.', 'url' => buildFilterUrl('price', '')];
}

if ($size_filter !== '') {
    $size_exists = false;
    foreach ($available_sizes as $s) {
        if ($s['value'] === $size_filter) {
            $size_exists = true;
            break;
        }
    }
    if (!$size_exists) {
        $filter_notices[] = ['message' => 'The selected size is not available.', 'url' => buildFilterUrl('size', '')];
    }
}

if ($texture_filter !== '') {
    $texture_exists = false;
    foreach ($available_textures as $t) {
        if ($t['value'] === $texture_filter) {
            $texture_exists = true;
            break;
        }
    }
    if (!$texture_exists) {
        $filter_notices[] = ['message' => 'The selected texture is not available.', 'url' => buildFilterUrl('texture', '')];
    }
}

// ---- Active filter chips ----
function active_filter_chips()
{
    global $category_label, $brand_label, $search, $color_filter, $price_range, $size_filter, $texture_filter, $price_ranges;

    $chips = [];

    if ($category_label !== '') {
        $chips[] = ['label' => 'Category: ' . $category_label, 'url' => buildFilterUrl('category', '')];
    }

    if ($brand_label !== '') {
        $chips[] = ['label' => 'Brand: ' . $brand_label, 'url' => buildFilterUrl('brand', '')];
    }

    if ($search !== '') {
        $chips[] = ['label' => 'Search: "' . $search . '"', 'url' => buildFilterUrl('search', '')];
    }

    if ($color_filter !== '') {
        $chips[] = ['label' => 'Color: ' . $color_filter, 'url' => buildFilterUrl('color', '')];
    }

    if ($price_range !== '' && isset($price_ranges[$price_range])) {
        $chips[] = ['label' => 'Price: ' . $price_ranges[$price_range], 'url' => buildFilterUrl('price', '')];
    }

    if ($size_filter !== '') {
        $chips[] = ['label' => 'Size: ' . $size_filter, 'url' => buildFilterUrl('size', '')];
    }

    if ($texture_filter !== '') {
        $chips[] = ['label' => 'Texture: ' . $texture_filter, 'url' => buildFilterUrl('texture', '')];
    }

    return $chips;
}

// ---- Sort options for markup ----
$sort_options = [
    ['value' => '', 'label' => 'All'],
    ['value' => 'popular', 'label' => 'Popularity'],
    ['value' => 'latest', 'label' => 'Latest'],
    ['value' => 'price_low_high', 'label' => 'Amount: Low to High'],
    ['value' => 'price_high_low', 'label' => 'Amount: High to Low'],
];