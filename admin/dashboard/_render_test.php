<?php
$_SESSION['admin_id'] = 1;
$_SESSION['admin_fullname'] = 'Test Admin';
$_SESSION['admin_email'] = 'a@b.c';
$_SESSION['admin_role'] = 'admin';
ob_start();
include __DIR__ . "/products.php";
$out = ob_get_clean();
$pos = strpos($out, 'Add New Product');
echo substr($out, $pos - 700, 1100) . "\n---END---\n";
