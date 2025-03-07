<?php
// Automatically detect environment
if ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == '127.0.0.1') {
    $domain = 'http://localhost/victosah';
} else {
    $domain = 'https://victosah.com';
}

// Make the variable available globally
define('DOMAIN', $domain);

// You can add other configuration variables here as needed
?>