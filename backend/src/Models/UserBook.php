<?php
namespace Models;

use Utils\JsonDBHelper;

class UserBook {
    private JsonDBHelper $hDB;
    private string $sUserBookpath = __DIR__ . '/../../data/UserBooks.json';

    public function __construct() {
        $this->hDB = new JsonDBHelper($this->sUserBookpath);
    }

    public function getAll(): array {
        return $this->hDB->all();
    }

    public function getById(int $id): ?array {
        $aUserBook = $this->hDB->findByParam("id", $id);
        return $aUserBook[0] ?: [];
    }

    public function getByUserID(int $id): ?array {
        $aUserBooksByUser = $this->hDB->findByParam("userID", $id);
        return $aUserBooksByUser ?: [];
    }

    public function addUserBook(int $nUserID, int $nBookID): bool {
        $aUserBooks = $this->getAll();
        if(!$nBookID){
            return false;
        }

        // Controlla se esiste già
        $bExists = false;
        foreach ($aUserBooks as $aUserBook) {
            if ($aUserBook['userID'] == $nUserID && $aUserBook['bookID'] == $nBookID) {
                $bExists = true;
                break;
            }
        }

        if (!$bExists) {
            $aUserBooks[] = ['userID' => $nUserID, 'bookID' => $nBookID];
            $bResult = file_put_contents($this->sUserBookpath, json_encode($aUserBooks, JSON_PRETTY_PRINT));
        } else {
            // Se esiste già è come se l'avessi creata, non deve tirare un errore
            $bResult = true;
        }

        return ($bResult) ? true : false;
    }
    
    public function deleteUserBook(int $nUserID, int $nBookID): bool {
        $aUserBooks = $this->getAll();

        if(!$nBookID){
            return false;
        }

        $aUserBooks = array_filter($aUserBooks, function($aUserBook) use ($nUserID, $nBookID) {
            if (array_key_exists('userID', $aUserBook) && array_key_exists('bookID', $aUserBook))
            return !($aUserBook['userID'] == $nUserID && $aUserBook['bookID'] == $nBookID);
        });

        $bResult = file_put_contents($this->sUserBookpath, json_encode(array_values($aUserBooks), JSON_PRETTY_PRINT));

        return ($bResult) ? true : false;
    }
}
