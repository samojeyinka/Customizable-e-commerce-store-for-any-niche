<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$client = new Google\Client;

$client->setClientId("763211292189-o7vk7n690hbj637d4rb63hguebfd96pd.apps.googleusercontent.com");
$client->setClientSecret("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");
$client->setRedirectUri(DOMAIN . "/redirect.php");

$client->addScope("email");
$client->addScope("profile");

$url = $client->createAuthUrl();

?>


