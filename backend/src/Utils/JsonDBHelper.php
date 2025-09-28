<?php
namespace Utils;

/**
 * Classe che simula un DB relazionale andando a leggere da dei file JSON, per rendere la demo fruibile in locale solo con PHP
 */
class JsonDBHelper {
    private string $sFilename;

    /**
     * Istanzio l'helper collegandolo a un file (se esiste)
     */
    public function __construct(string $sFilename) {
        $this->sFilename = $sFilename;
    }

    /**
     * Itero tutti i dati del "file-database"
     * Nel caso di chiamata a DB relazionale avrei considerato paginazione dei risultati, indicizzazione dei record...
     */
    public function all(): array {
        if (!file_exists($this->sFilename)) return [];
        $sFetchedJSON = file_get_contents($this->sFilename);
        return json_decode($sFetchedJSON, true) ?? [];
    }

    /**
     * Funzione che itera sui singoli parametri del "file-database"
     * Nel caso di chiamata a DB relazionale avrei considerato una query con parametri sanificati, indici per strutture dati...
     */
    public function findByParam(string $sParamName, mixed $mParamValue): ?array {
        $aAllItems = $this->all();
        foreach ($aAllItems as $aItem) {
            if ($aItem[$sParamName] === $mParamValue) return $aItem;
        }
        return null;
    }
}
