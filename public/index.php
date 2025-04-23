<?php

require_once __DIR__ . '/../app/core/Router.php';

$url = $_GET['url'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

route($url, $method);

?>
