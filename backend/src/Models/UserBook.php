<?php
namespace Models;

use Utils\JsonDBHelper;

class UserBook {
    private JsonDBHelper $hDB;

    public function __construct() {
        $this->hDB = new JsonDBHelper(__DIR__ . '/../../data/UserBooks.json');
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
}
