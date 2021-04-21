<?php

class ordersClass
{
    public $default;

    public $database;

    function __construct()
    {
        include_once("../connessione/database_pdo_sing.php");
        $obj = DatabasePdoClass::getInstance();
        $this->database = $obj->creaConnessione();

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public function default()
    {
        try {
           # code...
        } catch (PDOException $e) {
            $result['catchError'] = 'code => ' . $e->getCode() . ' | message => ' . $e->getMessage();
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }

        return $result;
    }
}
