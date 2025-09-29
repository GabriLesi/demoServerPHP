<?php
session_start();

require_once __DIR__ . '/vendor/autoload.php';

use Controllers\UserController;
use Controllers\BookController;
use Controllers\UserBookController;

// URI richiesto
$sUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$sUri = trim($sUri, '/');

// Endpoint pubblici
$aPublicEndpoints = [
    'login', 
    'frontend/login.html', 
    'js/api.js'
];

// 🔒 pagine frontend che richiedono login
$aProtectedPages = [
    'frontend/user.html',
    'frontend/books.html',
    'frontend/book.html',
    'frontend/my-books.html'
];

// Percorso assoluto al file richiesto
$sFilepath = __DIR__ . '/frontend/' . $sUri;

//Le pagine statiche vengono servite direttamente
if ($sUri && file_exists($sFilepath) && !is_dir($sFilepath)) {
    // Se non è tra i pubblici e l’utente non è loggato, redirect al login
    // Se è una pagina protetta e l’utente non è loggato, redirect al login
    if ((!in_array($sUri, $aPublicEndpoints) || in_array($sUri, $aProtectedPages)) 
        && !isset($_SESSION['user'])) {
        header("Location: /frontend/login.html");
        exit;
    }

    // set content-type minimale
    $sExt = pathinfo($sFilepath, PATHINFO_EXTENSION);
    switch ($sExt) {
        case 'html': header('Content-Type: text/html'); break;
        case 'js':   header('Content-Type: application/javascript'); break;
        case 'css':  header('Content-Type: text/css'); break;
        case 'json': header('Content-Type: application/json'); break;
        default:     header('Content-Type: text/plain'); break;
    }

    readfile($sFilepath);
    exit;
}


//SWITCH per le API REST

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$aParts = explode('/', $sUri);

switch ($aParts[0]) {
    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $aUsername = $data['username'] ?? '';
            $aPassword = $data['password'] ?? '';

            $aUsers = json_decode(file_get_contents(__DIR__ . '/backend/data/users.json'), true);

            $aUser = null;
            foreach ($aUsers as $u) {
                if ($u['username'] === $aUsername && $u['password'] === $aPassword) {
                    $aUser = $u;
                    break;
                }
            }

            if ($aUser) {
                unset($aUser['password']);
                $_SESSION['user'] = $aUser; // 🔒 login avvenuto
                echo json_encode($aUser);
            } else {
                http_response_code(401);
                echo json_encode(['error' => 'Credenziali non valide']);
            }
        }
        break;

    case 'logout':
        session_destroy();
        echo json_encode(['message' => 'Logout effettuato']);
        break;

    case 'me':
        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Non autorizzato']);
        } else {
            echo json_encode($_SESSION['user']);
        }
        break;


    case 'users':
    case 'books':
    case 'userbooks':
        // Protezione API
        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Non autorizzato']);
            exit;
        }

        if ($aParts[0] === 'users') {
            $hController = new UserController();
            if (isset($aParts[1])) {
                $hController->detail($aParts[1]);
            }
        } elseif ($aParts[0] === 'books') {
            $hController = new BookController();
            if (isset($aParts[1])) {
                $hController->detail($aParts[1]);
            } else {
                $hController->index();
            }
        } else {
            $hController = new UserBookController();
            if (isset($aParts[2]) && $aParts[2] === "books") {
                $hController->listUserBooks($aParts[1]);
            } elseif (isset($aParts[1])) {
                $hController->detail($aParts[1]);
            } else {
                $hController->index();
            }
        }
        
        break;

    case '':
        // redirect alla login se non loggato
        if (!isset($_SESSION['user'])) {
            header("Location: /frontend/login.html");
        } else {
            header("Location: /frontend/my-books.html");
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint not found']);
}