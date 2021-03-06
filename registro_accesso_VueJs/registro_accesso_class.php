<?php

class registroAccessoClass
{
    public $id_utente;
    public $ip_server;
    public $remote_port;
    public $user_agent;
    public $datatime;

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

    public function regristrare_accesso()
    {
        // insert user access log
        try {
            $query = $this->database->prepare("INSERT INTO `accede_logs` (`id_utente`, `datatime`, `ip_server`, `remote_port`, `user_agent`) VALUES (:id_utente, :datatime, :ip_server, :remote_port, :user_agent)");
            $query->bindValue(":id_utente", $this->id_utente);
            $query->bindValue(":datatime", $this->datatime);
            $query->bindValue(":ip_server", $this->ip_server);
            $query->bindValue(":remote_port", $this->remote_port);
            $query->bindValue(":user_agent", $this->user_agent);
            $query->execute();
            $result = $query->rowCount();
        } catch (PDOException $e) {
            $result['catchError'] = 'code => ' . $e->getCode() . ' | message => ' . $e->getMessage() ;
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }

        return $result;
    }

    public function cerca_registri_accessi()
    {
        try {
            $query = $this->database->prepare("SELECT id_log, nome, datatime, ip_server, remote_port, user_agent  FROM `accede_logs` LEFT JOIN utenti ON accede_logs.id_utente = utenti.id_utente");
            $query->execute();
            $result = $query->fetchAll();
        } catch (PDOException $e) {
            $result['catchError'] = 'code => ' . $e->getCode() . ' | message => ' . $e->getMessage() ;
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }

        return $result;
    }
}
