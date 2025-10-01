<?php
session_start();

require_once __DIR__ . '/vendor/autoload.php';

use Controllers\UserController;
use Controllers\BookController;
use Controllers\UserBookController;

use Utils\Router;
use Utils\ApiHelper;

// URI richiesto
$sUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$sUri = trim($sUri, '/');

// Endpoint pubblici
$aPublicEndpoints = [
    'login', 
    'login.html', 
    'js/api.js'
];

//  Pagine frontend che richiedono login
$aProtectedPages = [
    'user.html',
    'books.html',
    'book.html',
    'my-books.html'
];

// Percorso assoluto al file richiesto
$sFilepath = __DIR__ . '/frontend/' . $sUri;

$sUserBookpath = __DIR__ . '/backend/data/UserBooks.json';

// Middleware di protezione richieste (terra terra)
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

// Routing e mappe

$hRouter = new Router();

$hBookController = new BookController();
$hUserController = new UserController();
$hUserBookController = new UserBookController();

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Login 
$hRouter->add('POST', '/login', function () {
    $aData = json_decode(file_get_contents('php://input'), true);
    $aUsername = $aData['username'] ?? '';
    $aPassword = $aData['password'] ?? '';

    $aUsers = json_decode(file_get_contents(__DIR__ . '/backend/data/users.json'), true);

    $aUserLogged = null;
    foreach ($aUsers as $aUser) {
        if ($aUser['username'] === $aUsername && $aUser['password'] === $aPassword) {
            $aUserLogged = $aUser;
            break;
        }
    }

    if ($aUserLogged) {
        unset($aUserLogged['password']);
        $_SESSION['user'] = $aUserLogged; //  login avvenuto
        echo json_encode($aUserLogged);
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Credenziali non valide']);
    }
});

// Logout
$hRouter->add('GET', '/logout', function () {
    session_destroy();
    $hApiHelper = new ApiHelper();
    $hApiHelper->jsonResponse(['message' => 'Logout effettuato']);
});

// Utente loggato
$hRouter->add('GET', '/me', function () {
    $hApiHelper = new ApiHelper();
    $hApiHelper->requireAuth();
    $hApiHelper->jsonResponse($_SESSION['user']);
});

// Elenco libri
$hRouter->add('GET', '/books', function () {
    $hApiHelper = new ApiHelper();
    $hBookController = new BookController();

    $hApiHelper->requireAuth();
    $hApiHelper->jsonResponse($hBookController->index());
});

// Dettaglio libro con parametro dinamico
$hRouter->add('GET', '/books/{bookID}', function ($aParams){
    $hApiHelper = new ApiHelper();
    $hBookController = new BookController();

    $hApiHelper->requireAuth();
    $aBook = $hBookController->detail((int)$aParams['bookID']);
    $aBook ? $hApiHelper->jsonResponse($aBook) : $hApiHelper->jsonResponse(['error' => 'Libro non trovato'], 404);
});

// Libri utente loggato
$hRouter->add('GET', '/userbooks', function (){
    $hApiHelper = new ApiHelper();
    $hUserBookController = new UserBookController();

    $aUser = $hApiHelper->requireAuth();
    $hApiHelper->jsonResponse($hUserBookController->listUserBooks($_SESSION["user"]["id"]));
});

// Aggiungi libro all’utente
$hRouter->add('POST', '/userbooks', function (){
    $hApiHelper = new ApiHelper();
    $hUserBookController = new UserBookController();

    $aUser = $hApiHelper->requireAuth();
    $aInputData = $hApiHelper->getJsonInput();

    $aUserBooks = $hUserBookController->listUserBooks();
    $sUserId = $aUser['id'];
    $sBookId = $aInputData['bookId'];
    if(!$sBookId){
        $hApiHelper->jsonResponse(['error' => 'Libro non trovato'], 400);
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
        $bResult = file_put_contents($sUserBookpath, json_encode($aUserBooks, JSON_PRETTY_PRINT));
    }

    $bResult ? $hApiHelper->jsonResponse(['message' => 'Libro associato'])
            : $hApiHelper->jsonResponse(['error' => 'Libro già associato o non trovato'], 400);
});

// Rimuovi libro dall’utente
$hRouter->add('DELETE', '/userbooks', function (){
    $hApiHelper = new ApiHelper();
    $hUserBookController = new UserBookController();

    $aUser = $hApiHelper->requireAuth();
    $aInputData = $hApiHelper->getJsonInput();

    $aUserBooks = $hUserBookController->listUserBooks();
    $sUserId = $aUser['id'];
    $sBookId = $aInputData['bookId'];
    if(!$sBookId){
        $hApiHelper->jsonResponse(['error' => 'Libro non trovato'], 400);
    }

    $aUserBooks = array_filter($aUserBooks, function($aUserBook) use ($sUserId, $sBookId) {
        if (array_key_exists('userID', $aUserBook) && array_key_exists('bookID', $aUserBook))
        return !($aUserBook['userID'] == $sUserId && $aUserBook['bookID'] == $sBookId);
    });

    $bResult = file_put_contents($sUserBookpath, json_encode(array_values($aUserBooks), JSON_PRETTY_PRINT));

    $bResult ? $hApiHelper->jsonResponse(['message' => 'Associazione rimossa'])
            : $hApiHelper->jsonResponse(['error' => 'Associazione non trovata'], 404);
});

// Aggiornamento utente
$hRouter->add('PUT', '/me', function ()  {
    $hApiHelper = new ApiHelper();
    $hUserController = new UserController();

    $aUser = $hApiHelper->requireAuth();
    $aInputData = $hApiHelper->getJsonInput();
    $updatedUser = $hUserController->updateUser($aInputData);
    if ($updatedUser) {
        $_SESSION['user'] = $updatedUser; // aggiorna sessione
        $hApiHelper->jsonResponse(['message' => 'Utente aggiornato', 'user' => $updatedUser]);
    }
    $hApiHelper->jsonResponse(['error' => 'Aggiornamento fallito'], 400);
});

// Infine, dispatch della richiesta
$hRouter->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);