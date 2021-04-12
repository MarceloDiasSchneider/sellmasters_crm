<?php

class livelloClass
{

    public $id_livello;
    public $descrizione;
    public $permissione;

    public $database;

    function __construct()
    {
        include_once("../connessione/database_pdo_sing.php");
        $obj = DatabasePdoClass::getInstance();
        $this->database = $obj->creaConnessione();
    }

    public function get_livelli()
    {
        try {
            $query = $this->database->prepare("SELECT `id_livello`, `descrizione`, `permissione` FROM `livelli`");
            $query->execute();
            $result = $query->fetchAll();
        } catch (PDOException $e) {
            $result['catchError'] = 'code => ' . $e->getCode() . ' | message => ' . $e->getMessage() ;
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }
        
        return $result;
    }

    public function get_utente_livello()
    {
        // cerca il livello del'utente 
        try {
            $query = $this->database->prepare("SELECT permissione FROM `livelli` WHERE `id_livello` = :id_livello");
            $query->bindValue(":id_livello", $this->id_livello);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $result['catchError'] = 'code => ' . $e->getCode() . ' | message => ' . $e->getMessage() ;
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }

        return $result;
    }
}
