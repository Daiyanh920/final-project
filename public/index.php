<?php

require_once __DIR__ . '/../app/core/setup.php';
require_once __DIR__ . '/../app/core/Router.php';

$url = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$method = $_SERVER['REQUEST_METHOD'];

file_put_contents('debug.txt', "URL: $url | METHOD: $method | RAW: " . $_SERVER['REQUEST_URI']);

route($url, $method);

?>
