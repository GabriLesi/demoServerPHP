<?php
namespace Controllers;

use Models\Book;

class BookController {
    private Book $hBookModel;

    public function __construct() {
        $this->hBookModel = new Book();
    }

    public function index() {
        header('Content-Type: application/json');
        return $this->hBookModel->getAll();
    }

    public function detail($nBookID) {
        header('Content-Type: application/json');
        $aBook = $this->hBookModel->getById((int)$nBookID);
        if (!empty($aBook)) {
            return $aBook;
        } else {
            http_response_code(404);
            return ['error' => 'Book not found'];
        }
    }
}
