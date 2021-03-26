<?php

class registroAccessoClass
{
    public $database;

    function __construct()
    {
        include_once("../connessione/database_pdo_sing.php");
        $obj = DatabasePdoClass::getInstance();
        $this->database = $obj->creaConnessione();
    }

    public function regristrare_accesso($id_utente)
    {
        // preparare la data 
        $timezone = new DateTimeZone('Europe/Rome');
        $now = new DateTime('now', $timezone);
        $datatime = $now->format('Y-m-d H:i:s');

        // ottenendo informazioni da utente 
        $info = $_SERVER;
        $ip_server = $info["REMOTE_ADDR"];
        $remote_port = $info["REMOTE_PORT"];
        $user_agent = $info["HTTP_USER_AGENT"];

        // registra le informazione ogni volta che l'utente accede
        try {
            $logs = $this->database->prepare("INSERT INTO `accede_logs` (`id_utente`, `datatime`, `ip_server`, `remote_port`, `user_agent`) VALUES (:id_utente, :datatime, :ip_server, :remote_port, :user_agent)");
            $logs->bindValue(":id_utente", $id_utente);
            $logs->bindValue(":datatime", $datatime);
            $logs->bindValue(":ip_server", $ip_server);
            $logs->bindValue(":remote_port", $remote_port);
            $logs->bindValue(":user_agent", $user_agent);
            $logs->execute();
        } catch (PDOException $e) {
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }
    }

    public function cerca_registri_accessi()
    {
        // cecrca i registri di accessi di tutti gli l'utenti
        try {
            $logs = $this->database->prepare("SELECT id_log, nome, datatime, ip_server, remote_port, user_agent  FROM `accede_logs` LEFT JOIN utenti ON accede_logs.id_utente = utenti.id_utente");
            $logs->execute();
            $rows = $logs->rowCount();
            $result = $logs->setFetchMode(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }
        $allLogs = array();
        if ($rows >= 1) {
            foreach ($logs->fetchAll() as $key => $value) {
                $allLogs[] = $value;
            }
            return $allLogs;
        }
    }
}
