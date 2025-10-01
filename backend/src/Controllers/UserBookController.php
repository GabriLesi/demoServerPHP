<?php
namespace Controllers;

use Models\UserBook;
use Models\User;
use Models\Book;

class UserBookController {
    private UserBook $hUserBookModel;
    private Book $hBookModel;
    private User $hUserModel;

    public function __construct() {
        $this->hUserBookModel = new UserBook();
        $this->hUserModel = new User();
        $this->hBookModel = new Book();
    }

    public function index() {
        header('Content-Type: application/json');
        return $this->hUserBookModel->getAll();
    }

    public function detail($nUserBookID) {
        header('Content-Type: application/json');
        $aUserBook = $this->hUserBookModel->getById((int)$nUserBookID);
        if (!empty($aUserBook)) {
            return $aUserBook;
        } else {
            http_response_code(404);
            return ['error' => 'Pairing user book not found'];
        }
    }

    public function listUserBooks($nUserID) {
        header('Content-Type: application/json');
        $aUserBooks = $this->hUserBookModel->getByUserID((int)$nUserID);
        if (!empty($aUserBooks)) {
            for ($i = 0; $i < count($aUserBooks); $i++){
                $aBookDetails = $this->hBookModel->getById((int)$aUserBooks[$i]["bookID"]);
                $aUserBooks[$i]["book"] = $aBookDetails;
            }
            return $aUserBooks;
        } else {
            http_response_code(404);
            return ['error' => 'Pairing user book not found'];
        }
    }
}
