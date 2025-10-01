<?php
namespace Controllers;

use Models\User;

class UserController {
    private User $hUserModel;
    private string $sUserpath = __DIR__ . '../data/Users.json';

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
        $aAllUsersData = $this->index();
        $bUpdated = false;
        $aReturnedUser = [];

        if(!empty($aUserData)){
            //Per gestire CRUD nel file prendo e sovrascrivo, in un DB farei operazione atomica
            for ($i = 0; $i < count($aAllUsersData); $i++){
                if($aAllUsersData[$i]["id"] === $_SESSION['user']["id"]){
                    foreach (array_keys($aAllUsersData[$i]) as $sKey){
                        if (array_key_exists($sKey, $aUserData)){
                            $aAllUsersData[$i][$sKey] = $aUserData[$sKey];
                        }
                    }
                    $aReturnedUser = $aAllUsersData[$i];
                    file_put_contents($sUserpath, json_encode(array_values($aAllUsersData), JSON_PRETTY_PRINT));
                }
            }
        }

        return $aReturnedUser;
    }
}
