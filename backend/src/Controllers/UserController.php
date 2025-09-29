<?php
namespace Controllers;

use Models\User;

class UserController {
    private User $hUserModel;

    public function __construct() {
        $this->hUserModel = new User();
        
    }

    public function index() {
        header('Content-Type: application/json');
        echo json_encode($this->hUserModel->getAll());
    }

    public function detail($nUserID) {
        header('Content-Type: application/json');
        $aUser = $this->hUserModel->getById((int)$nUserID);
        if (!empty($aUser)) {
            echo json_encode($aUser);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'User not found']);
        }
    }
}
