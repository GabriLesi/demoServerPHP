<?php
namespace Utils;

class Router {
    private array $aRoutes = [];

    public function add(string $sMethod, string $sPath, callable $hHandler): void {
        $this->aRoutes[] = [
            "method" => strtoupper($sMethod),
            "path"   =>  trim($sPath, "/"),
            "handler"=> $hHandler
        ];
    }

    public function dispatch(string $sMethod, string $sUri): void {
        $sMethod = strtoupper($sMethod);
        $sUri = trim(strtok($sUri, "?"), "/"); // elimino query string (per sicurezza)
        $aUriSegments = $sUri === "" ? [] : explode("/", $sUri);

        foreach ($this->aRoutes as $aRoute) {
            if ($sMethod !== $aRoute["method"]) {
                continue;
            }

            $aRouteSegments = $aRoute["path"] === "" ? [] : explode("/", $aRoute["path"]);

            if (count($aRouteSegments) !== count($aUriSegments)) {
                continue;
            }

            $aParams = [];
            $bMatch = true;

            foreach ($aRouteSegments as $nIndex => $sSegment) {
                if (str_starts_with($sSegment, "{") && str_ends_with($sSegment, "}")) {
                    $sKey = trim($sSegment, "{}");
                    $aParams[$sKey] = $aUriSegments[$nIndex];
                } elseif ($sSegment !== $aUriSegments[$nIndex]) {
                    $bMatch = false;
                    break;
                }
            }

            if ($bMatch) {
                call_user_func($aRoute["handler"], $aParams);
                return;
            }
        }

        // Se nessuna route trovata
        http_response_code(404);
    }
}