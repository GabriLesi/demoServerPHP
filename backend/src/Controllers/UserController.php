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
        return $this->hUserModel->getAll();
    }

    public function detail($nUserID) {
        header('Content-Type: application/json');
        $aUser = $this->hUserModel->getById((int)$nUserID);
        if (!empty($aUser)) {
            return $aUser;
        } else {
            http_response_code(404);
            return ['error' => 'User not found'];
        }
    }

    public function updateUser($aUserData){
        header('Content-Type: application/json');
        $aUser = $this->hUserModel->updateUser($aUserData);
        if (!empty($aUser)) {
            return $aUser;
        } else {
            http_response_code(404);
            return ['error' => 'User not found'];
        }
    }
}
