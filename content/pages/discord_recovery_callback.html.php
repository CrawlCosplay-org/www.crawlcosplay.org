<?php

use app\models\CrawlSuccessionUser;

session_start();

if (!empty($_GET['error'])) {
    unset(
        $_SESSION['discord_recovery_state'],
        $_SESSION['discord_recovery_started']
    );

    die('Discord verification was cancelled. Please try again.');
}

$state = $_GET['state'] ?? '';
$expected_state = $_SESSION['discord_recovery_state'] ?? '';
$started = $_SESSION['discord_recovery_started'] ?? 0;

unset(
    $_SESSION['discord_recovery_state'],
    $_SESSION['discord_recovery_started']
);

if (
    !is_string($state)
    || !is_string($expected_state)
    || $expected_state === ''
    || !hash_equals($expected_state, $state)
    || !is_int($started)
    || time() - $started > 600
) {
    http_response_code(400);
    die('Recovery request expired or was invalid. Please try again.');
}

$code = $_GET['code'] ?? '';

if (!is_string($code) || $code === '') {
    http_response_code(400);
    die('Discord did not return an authorisation code.');
}

$config = require dirname(__DIR__, 3)
    . '/Private/discord_config.php';

// Exchange the code for an access token.
$ch = curl_init('https://discord.com/api/v10/oauth2/token');

curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query([
        'client_id' => $config['client_id'],
        'client_secret' => $config['client_secret'],
        'grant_type' => 'authorization_code',
        'code' => $code,
        'redirect_uri' => $config['recovery_redirect_uri'],
    ]),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/x-www-form-urlencoded'
    ],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_TIMEOUT => 20,
]);

$token_response = curl_exec($ch);
$token_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$token_error = curl_error($ch);

curl_close($ch);

if ($token_response === false || $token_status !== 200) {
    error_log('Discord recovery token request failed: ' . $token_error);
    http_response_code(502);
    die('Could not verify Discord. Please try again later.');
}

$token_data = json_decode($token_response, true);
$access_token = $token_data['access_token'] ?? null;

if (!is_string($access_token) || $access_token === '') {
    http_response_code(502);
    die('Discord returned an invalid token response.');
}

// Retrieve the verified Discord identity.
$ch = curl_init('https://discord.com/api/v10/users/@me');

curl_setopt_array($ch, [
    CURLOPT_HTTPGET => true,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $access_token
    ],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_TIMEOUT => 20,
]);

$discord_response = curl_exec($ch);
$discord_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$discord_error = curl_error($ch);

curl_close($ch);

if ($discord_response === false || $discord_status !== 200) {
    error_log('Discord recovery identity request failed: ' . $discord_error);
    http_response_code(502);
    die('Could not verify Discord. Please try again later.');
}

$discord_user = json_decode($discord_response, true);
$discord_id = $discord_user['id'] ?? null;

if (!is_string($discord_id) || $discord_id === '') {
    http_response_code(502);
    die('Discord returned an invalid user identity.');
}

// Find an existing account; never link or create one here.
$users = CrawlSuccessionUser::find([
    'discord_id' => $discord_id
]);

$user = null;

foreach ($users as $found_user) {
    $user = $found_user;
    break;
}

if (!$user) {
    die(
        'No CrawlSuccession account is linked to this Discord account. ' .
        'If you believe this is an error, contact the site administrator.'
    );
}

// Establish a short-lived, session-bound reset permission.
session_regenerate_id(true);

// Do not leave a different account logged in during recovery.
unset($_SESSION['user_id']);

$_SESSION['password_reset_user_id'] = $user->id;
$_SESSION['password_reset_expires'] = time() + 600;
$_SESSION['password_reset_csrf'] = bin2hex(random_bytes(32));

return $this->request->redirect('/crawl_succession_reset_password');
