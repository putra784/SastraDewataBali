<?php

session_start();
require __DIR__ . "/../src/php/function.php";
require __DIR__ . "/../vendor/autoload.php";

$client = new Google\Client;

$client->setClientId("995546464258-pourag7abmebi6np06rn8apefktg6iq8.apps.googleusercontent.com");
$client->setClientSecret("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");
$client->setRedirectUri("http://localhost/SastraDewata/SastraDewata/login_page/redirect.php");

if (!isset($_GET["code"])) {
    exit("Login Failed");
}

$token = $client->fetchAccessTokenWithAuthCode($_GET["code"]);
$client->setAccessToken($token["access_token"]);
$oauth = new Google\Service\Oauth2($client);
$userinfo = $oauth->userinfo->get();
$name  = $userinfo->name;
$email = $userinfo->email;

$userCheck = query("SELECT * FROM user WHERE username = '$email'");

if (!$userCheck) {
    $randomPassword = bin2hex(random_bytes(8));
    $hashedPassword = password_hash($randomPassword, PASSWORD_DEFAULT);

    query("
        INSERT INTO user (name, username, password)
        VALUES ('$name', '$email', '$hashedPassword')
    ");

    $userId = getLastInsertedId();
} else {
    $userId = $userCheck[0]['id'];
}

$_SESSION["user_id"] = $userId;

header("Location: ../author/index.php");
exit;