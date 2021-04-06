<?php

class utenteClass
{
    public $id_utente;
    public $nome;
    public $cognome;
    public $email;
    public $codice_fiscale;
    public $telefono;
    public $data_nascita;
    public $password;
    public $livello;
    public $attivo;

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

    public function check_email_other_user()
    {
        // check if the email is already used from another user
        try {
            $query = $this->database->prepare("SELECT email FROM `utenti` WHERE email = :email AND id_utente != :id_utente");
            $query->bindValue(":email", $this->email);
            $query->bindValue(":id_utente", $this->id_utente);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }

        return $result;
    }

    public function update_user()
    {
        try {
            // check if password is setted
            if($this->password == null){
                $query = $this->database->prepare("UPDATE `utenti` 
                SET `nome` = :nome, `cognome` = :cognome, `email` = :email, `codice_fiscale` = :codice_fiscale, `telefono` = :telefono, `data_nascita` = :data_nascita, `id_livello` = :livello 
                WHERE (`id_utente` = :id_utente)");
            } else {
                $query = $this->database->prepare("UPDATE `utenti` 
                SET `nome` = :nome, `cognome` = :cognome, `email` = :email, `codice_fiscale` = :codice_fiscale, `telefono` = :telefono, `data_nascita` = :data_nascita, `id_livello` = :livello, `password` = :password 
                WHERE (`id_utente` = :id_utente)");
            }

            $query->bindValue(":nome", $this->nome);
            $query->bindValue(":cognome", $this->cognome);
            $query->bindValue(":email", $this->email);
            $query->bindValue(":codice_fiscale", $this->codice_fiscale);
            $query->bindValue(":telefono", $this->telefono);
            $query->bindValue(":data_nascita", $this->data_nascita);
            // check if password is setted to update 
            if($this->password != null){
                $query->bindValue(":password", $this->password);
            }
            $query->bindValue(":livello", $this->livello);
            $query->bindValue(":id_utente", $this->id_utente);

            $query->execute();
            $rows = $query->rowCount();
        } catch (PDOException $e) {
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }

        return $rows;
    }

    public function check_email()
    {
        try {
            $query = $this->database->prepare("SELECT email FROM `utenti` WHERE email = :email");
            $query->bindValue(":email", $this->email);

            $query->execute();
            $result = $query->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }

        return $result;
    }

    public function insert_user()
    {
        try {
            $query = $this->database->prepare("INSERT INTO `utenti` (`nome`, `cognome`, `email`, `codice_fiscale`, `telefono`, `data_nascita`, `password`, `id_livello`, `attivo`) 
            VALUES (:nome, :cognome, :email, :codice_fiscale, :telefono, :data_nascita, :password, :livello, :attivo) ");
            $query->bindValue(":nome", $this->nome);
            $query->bindValue(":cognome", $this->cognome);
            $query->bindValue(":email", $this->email);
            $query->bindValue(":codice_fiscale", $this->codice_fiscale);
            $query->bindValue(":telefono", $this->telefono);
            $query->bindValue(":data_nascita", $this->data_nascita);
            $query->bindValue(":password", $this->password);
            $query->bindValue(":livello", $this->livello);
            $query->bindValue(":attivo", $this->attivo);

            $query->execute();
            $rows = $query->rowCount();

        } catch (PDOException $e) {
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }

        return $rows;
    }

    public function get_utenti()
    {
        // cerca tutti gli utenti
        try {
            $query = $this->database->prepare("SELECT id_utente, nome, cognome, email, codice_fiscale, telefono, data_nascita, id_livello, attivo  FROM `utenti`");
            $query->execute();
            $query->setFetchMode(PDO::FETCH_ASSOC);
            $result = $query->fetchAll();
        } catch (PDOException $e) {
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }

        return $result;
    }
    
    public function get_user_data()
    {
        try {
        $query = $this->database->prepare("SELECT `nome`, `cognome`, `email`, `codice_fiscale`, `data_nascita`, `telefono`, `id_livello` FROM `utenti` WHERE id_utente = :id_utente");
            $query->bindValue(":id_utente", $this->id_utente);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }

        return $result;
    }

    public function get_user_attivo()
    {
        // controlla se l'utente è attivo 
        try {
            $query = $this->database->prepare("SELECT attivo FROM `utenti` WHERE id_utente = :id_utente");
            $query->bindValue(":id_utente", $this->id_utente);
            $query->execute();
            $result = $query->fetchColumn(0);
        } catch (PDOException $e) {
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }

        return $result; 
    }
    public function toggle_user_attivo()
    {
        try {
            $query = $this->database->prepare("UPDATE `utenti` SET attivo = :attivo WHERE id_utente = :id_utente");
            $query->bindValue(":attivo", !$this->attivo);
            $query->bindValue(":id_utente", $this->id_utente);
            $query->execute();
        } catch (PDOException $e) {
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }
    }
}
