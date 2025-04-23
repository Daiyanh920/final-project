<?php

$env = file(__DIR__ . '/../../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach ($env as $line) {
    if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) continue;
    list($key, $value) = explode('=', $line, 2);
    $_ENV[trim($key)] = trim($value);
}


if (!defined('DBNAME')) define('DBNAME', $_ENV['DBNAME']);
if (!defined('DBHOST')) define('DBHOST', $_ENV['DBHOST']);
if (!defined('DBUSER')) define('DBUSER', $_ENV['DBUSER']);
if (!defined('DBPASS')) define('DBPASS', $_ENV['DBPASS']);

?>