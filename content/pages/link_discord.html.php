<?php

use app\models\CrawlSuccessionUser;

session_start();

if (empty($_SESSION['user_id'])) {
    return $this->request->redirect('/crawl_succession_login');
}

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

$config_path = dirname(__DIR__, 3) . '/private/discord_config.php';

if (!file_exists($config_path)) {
    die('Discord configuration file not found at: ' . $config_path);
}

$config = require $config_path;

$config = require dirname(__DIR__, 3) . '/private/discord_config.php';

$state = bin2hex(random_bytes(32));
$_SESSION['discord_oauth_state'] = $state;

$params = [
    'client_id' => $config['client_id'],
    'redirect_uri' => $config['redirect_uri'],
    'response_type' => 'code',
    'scope' => 'identify',
    'state' => $state,
];

$url = 'https://discord.com/oauth2/authorize?' .
    http_build_query($params);

return $this->request->redirect($url);
