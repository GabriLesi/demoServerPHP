<?php
namespace Models;

use Utils\JsonDBHelper;

class User {
    private JsonDBHelper $hDB;

    public function __construct() {
        $this->hDB = new JsonDBHelper(__DIR__ . '/../../data/Users.json');
    }

    public function getAll(): array {
        return $this->hDB->all();
    }

    public function getById(int $id): ?array {
        $aUser = $this->hDB->findByParam("id", $id);
        return $aUser[0] ?: [];
    }

    public function updateUser($aUserData): ?array {
        $aAllUsersData = $this->getAll();
        $bUpdated = false;
        $aReturnedUser = [];
        $sUserpath = __DIR__ . '../data/Users.json';

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
