<?php
namespace Controllers;

use Models\UserBook;

class UserBookController {
    private UserBook $hUserBookModel;

    public function __construct() {
        $this->hUserBookModel = new UserBook();
    }

    public function index() {
        header('Content-Type: application/json');
        echo json_encode($this->hUserBookModel->getAll());
    }

    public function detail($nUserBookID) {
        header('Content-Type: application/json');
        $hUserBook = $this->hUserBookModel->getById((int)$nUserBookID);
        if ($hUserBook) {
            echo json_encode($hUserBook);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Pairing user book not found']);
        }
    }
}
