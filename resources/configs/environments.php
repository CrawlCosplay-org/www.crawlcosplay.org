<?php

namespace app\environments;

use alkemann\h2l\{ Environment, Connections, data\PDO, Log, Request, Response};
use alkemann\h2l\util\{ Chain, Http };

$base = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR ;

Environment::add([
    'debug' => true,
    'logs_path' => $base . 'resources' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR,
    'parts_path' => $base . 'content' . DIRECTORY_SEPARATOR . 'parts' . DIRECTORY_SEPARATOR,
    'content_path' => $base . 'content' . DIRECTORY_SEPARATOR . 'pages' . DIRECTORY_SEPARATOR,
    'layout_path'  => $base . 'content' . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR,
], Environment::ALL);

// Set admin phrase to value from environment or random bytes if not set
$admin_phrase = getenv('ADMIN');
$admin_file = '../resources/configs/admin_phrase';
if (file_exists($admin_file))
{
        $admin_phrase = trim(file_get_contents($admin_file));
}
if ($admin_phrase === false) {
    $admin_phrase = random_bytes(16);
}
Environment::put('admin_phrase', $admin_phrase, Environment::ALL);

$env = getenv('ENV');
Environment::setEnvironment($env ? $env : Environment::LOCAL); // Check for valid value?

Log::handler('standard', [Log::class, 'std']);

$mysql_url = getenv('CLEARDB_DATABASE_URL');
$mysql_url_file = '../resources/configs/mysql_database_url';
if (file_exists($mysql_url_file))
{
        $mysql_url = file_get_contents($mysql_url_file);
}
if ($mysql_url) {
    $options = parse_url($mysql_url);
    $options['db'] = ltrim($options['path'], '/');
    Connections::add('default', function() use ($options) { return new PDO($options); });
}

// Middleware to add a log response for request and what response handler is chosen
$log_request_middleware = function(Request $request, Chain $chain): ?Response {
    Log::info("== REQUEST: {$request->method()} '{$request->url()}' ==");
    return $chain->next($request);
};
Environment::addMiddle($log_request_middleware, Environment::ALL);

$admin_middleware = function(Request $request, Chain $chain): ?Response {
    if (strpos(trim($request->url(), '/'), 'admin') === 0) {
        Log::info("Admin request");
        if (!$request->session('admin')) {
            $request->redirect('/');
            return null;
        }
    }
    $response = $chain->next($request);
    return $response;
};
Environment::addMiddle($admin_middleware, Environment::ALL);

