<?php

namespace app\Controllers;
use PDO;

require_once __DIR__ . '/../core/database.php';



class AuthController
{
    public function register($method)
    {
        header('Content-Type: application/json');

        if ($method !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);

        $username = trim($data['username'] ?? '');
        $password = trim($data['password'] ?? '');

        if (strlen($username) < 3 || strlen($password) < 6) {
            http_response_code(400);
            echo json_encode(['error' => 'Username must be at least 3 characters, password at least 6.']);
            return;
        }

        $db = getDB();

        
        $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);

        if ($stmt->fetch()) {
            http_response_code(409);
            echo json_encode(['error' => 'Username already exists']);
            return;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $db->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $success = $stmt->execute([$username, $hashedPassword]);

        echo json_encode([
            'message' => $success ? 'Registration successful' : 'Registration failed'
        ]);
    }

    public function login($method)
    {
        header('Content-Type: application/json');
        session_start();

        if ($method !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);

        $username = trim($data['username'] ?? '');
        $password = trim($data['password'] ?? '');

        if (!$username || !$password) {
            http_response_code(400);
            echo json_encode(['error' => 'Username and password are required']);
            return;
        }

        $db = getDB();

        $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['password'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid credentials']);
            return;
        }

        $_SESSION['user_id'] = $user['id'];
        echo json_encode(['message' => 'Login successful']);
    }
}

?>