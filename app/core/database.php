<?php

if (!function_exists('getDB')) {
    function getDB() {
        $host = $_ENV['DBHOST'];
        $user = $_ENV['DBUSER'];
        $pass = $_ENV['DBPASS'];
        $name = $_ENV['DBNAME'];

        try {
            $pdo = new PDO("mysql:host=$host;dbname=$name", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            die(json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]));
        }
    }
}

?>