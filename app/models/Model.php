<?php

namespace app\models;
use PDO;

require_once __DIR__ . '/../core/setup.php';


class Model {
    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM transactions ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO transactions (description, category, amount, type)
                              VALUES (:description, :category, :amount, :type)");

        return $stmt->execute([
            ':description' => htmlspecialchars($data['description']),
            ':category' => $data['category'] ?? null,
            ':amount' => floatval($data['amount']),
            ':type' => $data['type']
        ]);
    }

    public static function deleteLast() {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM transactions ORDER BY id DESC LIMIT 1");
        return $stmt->execute();
    }

    public static function clearAll() {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM transactions");
        return $stmt->execute();
    }

}

?>