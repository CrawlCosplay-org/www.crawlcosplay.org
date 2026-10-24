<?php

use app\models\CrawlSuccessionUser;

session_start();

if (empty($_SESSION['user_id'])) {
    die('Please log in to CrawlSuccession before linking Discord.');
}

if (!empty($_GET['error'])) {
    unset($_SESSION['discord_oauth_state']);
    die('Discord account linking was cancelled or denied. Please try again.');
}

$state = $_GET['state'] ?? '';
$expected_state = $_SESSION['discord_oauth_state'] ?? '';

unset($_SESSION['discord_oauth_state']);

if (
    !is_string($state)
    || !is_string($expected_state)
    || $expected_state === ''
    || !hash_equals($expected_state, $state)
) {
    http_response_code(400);
    die('Invalid Discord authorisation state. Please try again.');
}

$code = $_GET['code'] ?? '';

if (!is_string($code) || $code === '') {
    http_response_code(400);
    die('Discord did not return an authorisation code.');
}

$config = require dirname(__DIR__, 3)
    . '/private/discord_config.php';

$users = CrawlSuccessionUser::find([
    'id' => $_SESSION['user_id']
]);

$user = null;

foreach ($users as $found_user) {
    $user = $found_user;
    break;
}

if (!$user) {
    die('CrawlSuccession account not found.');
}

// Exchange the authorisation code for an access token.
$ch = curl_init('https://discord.com/api/v10/oauth2/token');

curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query([
        'client_id' => $config['client_id'],
        'client_secret' => $config['client_secret'],
        'grant_type' => 'authorization_code',
        'code' => $code,
        'redirect_uri' => $config['redirect_uri'],
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

if (
    $token_response === false
    || $token_status !== 200
) {
    error_log('Discord OAuth token request failed: ' . $token_error);
    http_response_code(502);
    die('Could not verify your Discord account. Please try again later.');
}

$token_data = json_decode($token_response, true);
$access_token = $token_data['access_token'] ?? null;

if (!is_string($access_token) || $access_token === '') {
    http_response_code(502);
    die('Discord returned an invalid token response.');
}

// Retrieve the identity associated with that token.
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

if (
    $discord_response === false
    || $discord_status !== 200
) {
    error_log('Discord identity request failed: ' . $discord_error);
    http_response_code(502);
    die('Could not retrieve your Discord identity. Please try again.');
}

$discord_user = json_decode($discord_response, true);
$discord_id = $discord_user['id'] ?? null;

if (!is_string($discord_id) || $discord_id === '') {
    http_response_code(502);
    die('Discord returned an invalid user identity.');
}

// Don't allow one Discord account to link to multiple players.
$linked_users = CrawlSuccessionUser::find([
    'discord_id' => $discord_id
]);

foreach ($linked_users as $linked_user) {
    if ((string) $linked_user->id !== (string) $user->id) {
        http_response_code(409);
        die('This Discord account is already linked to another CrawlSuccession account.');
    }
}

// Don't silently replace a different existing Discord link.
if (
    !empty($user->discord_id)
    && $user->discord_id !== $discord_id
) {
    die('Your CrawlSuccession account is already linked to a different Discord account.');
}

$user->discord_id = $discord_id;
$user->save();

return $this->request->redirect('/crawl_succession');
