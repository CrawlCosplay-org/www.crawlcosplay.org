<?php

session_start();

$config = require dirname(__DIR__, 3)
    . '/Private/discord_config.php';

$state = bin2hex(random_bytes(32));
$_SESSION['discord_recovery_state'] = $state;
$_SESSION['discord_recovery_started'] = time();

$authorize_url = 'https://discord.com/oauth2/authorize?' .
    http_build_query([
        'client_id' => $config['client_id'],
        'redirect_uri' => $config['recovery_redirect_uri'],
        'response_type' => 'code',
        'scope' => 'identify',
        'state' => $state,
    ]);

?>

<h2>Recover Your CrawlSuccession Account</h2>

<p>
    Verify the Discord account linked to your CrawlSuccession account
    to reset your password.
</p>

<p>
    <a href="<?= $e($authorize_url) ?>">Continue with Discord</a>
</p>

<p>
    <a href="/crawl_succession_login">Back to login</a>
</p>
