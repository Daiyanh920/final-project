<?php

namespace app\Controllers;
use app\models\Model;


class Controller 
{
    public function returnView($path) 
    {
        require_once __DIR__ . '/../../public/' . $path;
        exit;
    }

    public function handleRequest($method) 
    {
        header('Content-Type: application/json');

        if ($method === 'GET') {
            $transactions = Model::getAll();
            echo json_encode($transactions);
        }

        elseif ($method === 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);

            if (!isset($data['description']) || !isset($data['amount']) || !isset($data['type'])) {
                http_response_code(400);
                echo json_encode(['message' => 'Missing required fields']);
                return;
            }

            $result = Model::create($data);
            echo json_encode(['message' => $result ? 'Transaction added' : 'Failed to add transaction']);
        }
        elseif ($method === 'DELETE') {
            $data = json_decode(file_get_contents("php://input"), true);
    
             if (isset($data['all']) && $data['all'] === true) {
                $success = Model::clearAll();
                echo json_encode(['message' => $success ? 'All transactions cleared' : 'Failed to clear']);
            } else {
                $success = Model::deleteLast();
                echo json_encode(['message' => $success ? 'Transaction deleted' : 'Failed to delete']);
            }
        }
        
        else {
            http_response_code(405);
            echo json_encode(['message' => 'Method not allowed']);
        }
    }

}
