<?php

require_once __DIR__ . '/../core/setup.php';
require_once __DIR__ . '/../models/Model.php';
require_once __DIR__ . '/../controllers/Controller.php';
require_once __DIR__ . '/../controllers/MainController.php';
require_once __DIR__ . '/../controllers/AuthController.php'; 
require_once __DIR__ . '/../controllers/StockController.php';

use app\controllers\Controller;
use app\controllers\MainController;
use app\controllers\AuthController;
use app\controllers\StockController;

function route($url, $method) {

    if ($url === 'transactions') {
        $controller = new Controller();
        $controller->handleRequest($method);
    }
    elseif ($url === 'register') {
        $auth = new AuthController();
        $auth->register($method);
    }
    elseif ($url === 'login') {
        $auth = new AuthController();
        $auth->login($method);
    }
    elseif ($url === 'logout') {
        session_start();
        session_destroy();
        echo json_encode(['message' => 'Logged out']);
    }
    elseif ($url === 'homepage') {
        $main = new MainController();
        $main->homepage();
    }
    elseif ($url === 'stock-price') {
        require_once __DIR__ . '/../controllers/StockController.php';
        $controller = new \app\Controllers\StockController();
        $controller->getStockPrice();
    }
    elseif ($url === 'buy-stock') {
        require_once __DIR__ . '/../controllers/StockController.php';
        $controller = new \app\Controllers\StockController();
        $controller->buy();
    }
    elseif ($url === 'sell-stock') {
        require_once __DIR__ . '/../controllers/StockController.php';
        $controller = new \app\Controllers\StockController();
        $controller->sell();
    }

     elseif ($url === 'portfolio') {
        $controller = new \app\Controllers\StockController();
        $controller->getPortfolio();
    }
     elseif ($url === 'login-page') {
        require_once __DIR__ . '/../../public/assets/views/auth/login.html';
        exit;
    }
    elseif ($url === 'register-page') {
        require_once __DIR__ . '/../../public/assets/views/auth/register.html';
        exit;
    }
     elseif ($url === 'robinhood') {
        require_once __DIR__ . '/../../public/assets/views/main/robinhood.html';
        exit;
    }
    else {
        http_response_code(404);
        echo json_encode(['error' => 'Resource not found']);
    }
}

?>