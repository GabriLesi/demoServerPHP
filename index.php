<?php

require_once __DIR__ . '/vendor/autoload.php';

use Controllers\UserController;

$sUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$sUri = trim($sUri, '/');

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$aParts = explode('/', $sUri);

switch ($aParts[0]) {
    case 'users':
        $hController = new UserController();
        if (isset($aParts[1])) $hController->detail($aParts[1]);
        else $hController->index();
        break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint not found']);
        break;
}
