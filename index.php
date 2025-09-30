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
    'login.html', 
    'js/api.js'
];

//  pagine frontend che richiedono login
$aProtectedPages = [
    'user.html',
    'books.html',
    'book.html',
    'my-books.html'
];

// Percorso assoluto al file richiesto
$sFilepath = __DIR__ . '/frontend/' . $sUri;

//Le pagine statiche vengono servite direttamente
if ($sUri && file_exists($sFilepath) && !is_dir($sFilepath)) {
    // Se non è tra i pubblici e l’utente non è loggato, redirect al login
    // Se è una pagina protetta e l’utente non è loggato, redirect al login
    if ((!in_array($sUri, $aPublicEndpoints) || in_array($sUri, $aProtectedPages)) 
        && !isset($_SESSION['user'])) {
        header("Location: login.html");
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
            $aData = json_decode(file_get_contents('php://input'), true);
            $aUsername = $aData['username'] ?? '';
            $aPassword = $aData['password'] ?? '';

            $aUsers = json_decode(file_get_contents(__DIR__ . '/backend/data/users.json'), true);

            $aUser = null;
            foreach ($aUsers as $aUser) {
                if ($aUser['username'] === $aUsername && $aUser['password'] === $aPassword) {
                    $aUser = $aUser;
                    break;
                }
            }

            if ($aUser) {
                unset($aUser['password']);
                $_SESSION['user'] = $aUser; //  login avvenuto
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
        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Non autorizzato']);
            exit;
        }

        if (isset($aParts[1]) && $aParts[1] === 'me' && $_SERVER['REQUEST_METHOD'] === 'PUT') {
            $data = json_decode(file_get_contents('php://input'), true);
            $userId = $_SESSION['user']['id'];

            $usersFile = __DIR__ . '/backend/data/users.json';
            $users = json_decode(file_get_contents($usersFile), true);

            foreach ($users as &$u) {
                if ($u['id'] == $userId) {
                    // Aggiorna solo i campi sensati (no id, no username obbligatorio)
                    foreach (['first_name', 'last_name', 'email', 'address', 'city', 'phone', 'password'] as $field) {
                        if (isset($data[$field])) $u[$field] = $data[$field];
                    }
                    $_SESSION['user'] = $u; // aggiorna sessione
                    file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
                    echo json_encode(['message' => 'Profilo aggiornato', 'user' => $u]);
                    exit;
                }
            }
            http_response_code(404);
            echo json_encode(['error' => 'Utente non trovato']);
        }
        break;
    case 'books':
        // Protezione API
        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Non autorizzato']);
            exit;
        }

        $hController = new BookController();
        if (isset($aParts[1])) {
            $hController->detail($aParts[1]);
        } else {
            $hController->index();
        }
        
        break;

    case 'userbooks':
        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Non autorizzato']);
            exit;
        }

        $sUserId = $_SESSION['user']['id'];
        $sFilePath = __DIR__ . '/backend/data/UserBooks.json';
        $aUserBooks = json_decode(file_get_contents($sFilePath), true);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Aggiungi associazione
            $aData = json_decode(file_get_contents('php://input'), true);
            $sBookId = $aData['bookID'] ?? null;
            if (!$sBookId) {
                http_response_code(400);
                echo json_encode(['error' => 'bookID mancante']);
                exit;
            }

            // Controlla se esiste già
            $bExists = false;
            foreach ($aUserBooks as $aUserBook) {
                if ($aUserBook['userID'] == $sUserId && $aUserBook['bookID'] == $sBookId) {
                    $bExists = true;
                    break;
                }
            }

            if (!$bExists) {
                $aUserBooks[] = ['userID' => $sUserId, 'bookID' => $sBookId];
                file_put_contents($sFilePath, json_encode($aUserBooks, JSON_PRETTY_PRINT));
            }

            echo json_encode(['message' => 'Associazione aggiunta']);
        } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            // Rimuovi associazione
            $aData = json_decode(file_get_contents('php://input'), true);
            $sBookId = $aData['bookID'] ?? null;
            if (!$sBookId) {
                http_response_code(400);
                echo json_encode(['error' => 'bookID mancante']);
                exit;
            }

            $aUserBooks = array_filter($aUserBooks, function($aUserBook) use ($sUserId, $sBookId) {
                if (array_key_exists('userID', $aUserBook) && array_key_exists('bookID', $aUserBook))
                return !($aUserBook['userID'] == $sUserId && $aUserBook['bookID'] == $sBookId);
            });

            file_put_contents($sFilePath, json_encode(array_values($aUserBooks), JSON_PRETTY_PRINT));
            echo json_encode(['message' => 'Associazione rimossa']);
        } else {
            $hController = new UserBookController();
            $hController->listUserBooks($_SESSION["user"]["id"]);
        }
        break;


    case '':
        // redirect alla login se non loggato
        if (!isset($_SESSION['user'])) {
            header("Location: login.html");
        } else {
            header("Location: my-books.html");
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint not found']);
}