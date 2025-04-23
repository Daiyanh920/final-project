<?php

namespace app\Controllers;
//use app\controllers\Controller;

require_once __DIR__ . '/Controller.php';

class MainController extends Controller {

    public function homepage() {
    session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php?url=login-page");
        exit;
    }

    $this->returnView('assets/views/main/homepage.html');
}


    public function notFound() {
    }

}

?>
