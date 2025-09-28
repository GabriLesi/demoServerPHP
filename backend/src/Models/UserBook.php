<?php
namespace Models;

use Utils\JsonDBHelper;

class User {
    private JsonDBHelper $hDB;

    public function __construct() {
        $this->hDB = new JsonDBHelper(__DIR__ . '/../../data/UserBooks.json');
    }

    public function getAll(): array {
        return $this->hDB->all();
    }

    public function getById(int $id): ?array {
        return $this->hDB->findByParam("id", $id);
    }
}
