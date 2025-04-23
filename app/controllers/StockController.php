<?php

namespace app\Controllers;
use PDO;

class StockController
{
    public function getStockPrice()
    {
        $symbol = $_GET['symbol'] ?? null;

        if (!$symbol) {
            http_response_code(400);
            echo json_encode(['error' => 'Symbol is required']);
            return;
        }

        $apiKey = $_ENV['FINNHUB_KEY'];
        $url = "https://finnhub.io/api/v1/quote?symbol=$symbol&token=$apiKey";

        $response = file_get_contents($url);
        if ($response === false) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch data']);
            return;
        }

        header('Content-Type: application/json');
        echo $response;
    }

    public function buy() {
        session_start();
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Not logged in']);
            return;
        }
    
        $data = json_decode(file_get_contents("php://input"), true);

        $symbol = $data['symbol'] ?? '';
        $quantity = (int)($data['quantity'] ?? 0);
        $price = (float)($data['price'] ?? 0);
    
        if (!$symbol || $quantity <= 0 || $price <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid data']);
            return;
        }
    
        $userId = $_SESSION['user_id'];
        $db = getDB();
    
        $stmt = $db->prepare("INSERT INTO portfolio (user_id, symbol, quantity, price) VALUES (?, ?, ?, ?)");
        $success = $stmt->execute([$userId, $symbol, $quantity, $price]);
    
        if (!$success) {
            $errorInfo = $stmt->errorInfo();
            file_put_contents(die(json_encode(['error' => $stmt->errorInfo()])));
        }

        echo json_encode(['message' => $success ? 'Stock purchased!' : 'Error buying stock']);
    }
    
    public function getPortfolio() 
    {
        session_start();

        try {
            
            $db = getDB();
            $userId = $_SESSION['user_id'];

            $stmt = $db->prepare("
                SELECT 
                    symbol, 
                    SUM(quantity) AS total_quantity,
                    SUM(quantity * price) AS total_invested,
                    SUM(quantity * price) / SUM(quantity) AS avg_price
                FROM portfolio 
                WHERE user_id = ? 
                GROUP BY symbol
                HAVING SUM(quantity) > 0
            ");

            if (!$stmt->execute([$userId])) {
                $error = $stmt->errorInfo();
                http_response_code(500);
                echo json_encode(['error' => 'Query failed']);
                return;
            }

            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            header('Content-Type: application/json');
            echo json_encode($data);

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Server error']);
        }
    }

    public function sell() {
        session_start();
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Not logged in']);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);
        $symbol = $data['symbol'] ?? '';
        $quantity = (int)($data['quantity'] ?? 0);
        $userId = $_SESSION['user_id'];

        if (!$symbol || $quantity <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid data']);
            return;
        }

        $db = getDB();

        // Calculate how many shares the user owns
        $stmt = $db->prepare("SELECT SUM(quantity) AS total FROM portfolio WHERE user_id = ? AND symbol = ?");
        $stmt->execute([$userId, $symbol]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $owned = (int)($result['total'] ?? 0);

        if ($quantity > $owned) {
            http_response_code(400);
            echo json_encode(['error' => 'Not enough shares to sell']);
            return;
        }

        // Insert negative quantity to indicate sell
        $stmt = $db->prepare("INSERT INTO portfolio (user_id, symbol, quantity, price) VALUES (?, ?, ?, ?)");
        $success = $stmt->execute([$userId, $symbol, -$quantity, 0]);

        echo json_encode(['message' => $success ? 'Stock sold!' : 'Failed to sell']);
    }


}

?>