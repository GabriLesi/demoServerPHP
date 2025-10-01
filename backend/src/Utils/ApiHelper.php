<?php
namespace Utils;

class ApiHelper {
    // Restituisce una risposta JSON con header e codice HTTP alle chiamate
    function jsonResponse($aData, int $nStatusCode = 200): void {
        header("Content-Type: application/json");
        http_response_code($nStatusCode);
        echo json_encode($aData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Controlla se l'utente è autenticato: se sì ritorna i dati, altrimenti termina la richiesta
    function requireAuth(): array {
        if (!isset($_SESSION["user"])) {
            jsonResponse(["error" => "Non autorizzato"], 401);
        }
        return $_SESSION["user"];
    }

    // Restituisce il payload JSON della richiesta
    function getJsonInput(): array {
        $sRawFile = file_get_contents("php://input");
        $aDecodedFile = json_decode($sRawFile, true);
        return is_array($aDecodedFile) ? $aDecodedFile : [];
    }
}