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
            $query = $this->database->prepare("SELECT * FROM `sellmasters`.`livelli`");
            $query->execute();
            $livelli = $query->fetchAll();
        } catch (PDOException $e) {
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }
        return $livelli;
    }

    public function get_utente_livello()
    {
        // cerca il livello del'utente 
        try {
            $permissione = $this->database->prepare("SELECT permissione FROM `livelli` WHERE `id_livello` = :id_livello");
            $permissione->bindValue(":id_livello", $this->id_livello);
            $permissione->execute();
            $resultPermissione = $permissione->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }

        // controlla se ha livello dell'utente 
        if (isset($resultPermissione['permissione'])) {
            $this->permissione = $resultPermissione['permissione'];

            // Messaggio di riuscito a trovare un livello

            $data['code'] = '200';
            $data['state'] = 'success';
            $data['message'] = 'Riuscito a trovare un livello';

            return $data;
        } else {
            // Messaggio di errore se l'utente non dispone del livello di autorizzazione
            $data['code'] = '401';
            $data['state'] = 'error';
            $data['message'] = 'Utente senza livello di permissione';

            return $data;
        }
    }
}
