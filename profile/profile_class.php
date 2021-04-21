<?php

class profileClass
{

    public $id_profile;
    public $descrizione;

    public $database;

    function __construct()
    {
        include_once("../connessione/database_pdo_sing.php");
        $obj = DatabasePdoClass::getInstance();
        $this->database = $obj->creaConnessione();
    }

    public function get_profiles()
    {
        try {
            $query = $this->database->prepare("SELECT `id_profile`, `descrizione`, `attivo` FROM `profiles`");
            $query->execute();
            $query->setFetchMode(PDO::FETCH_ASSOC);
            $result = $query->fetchAll();
        } catch (PDOException $e) {
            $result['catchError'] = 'code => ' . $e->getCode() . ' | message => ' . $e->getMessage() ;
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }
        
        return $result;
    }

    public function get_utente_profile()
    {
        // cerca il profile del'utente 
        try {
            $query = $this->database->prepare("SELECT permissione FROM `profiles` WHERE `id_profile` = :id_profile");
            $query->bindValue(":id_profile", $this->id_profile);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $result['catchError'] = 'code => ' . $e->getCode() . ' | message => ' . $e->getMessage() ;
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }

        return $result;
    }
}
