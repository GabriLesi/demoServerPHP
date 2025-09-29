<?php
namespace Models;

use Utils\JsonDBHelper;

class Book {
    private JsonDBHelper $hDB;

    public function __construct() {
        $this->hDB = new JsonDBHelper(__DIR__ . '/../../data/Books.json');
    }

    public function getAll(): array {
        return $this->hDB->all();
    }

    public function getById(int $id): ?array {
        $aBook = $this->hDB->findByParam("id", $id);
        return $aBook[0] ?: []; 
    }
}
