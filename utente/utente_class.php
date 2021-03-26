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

    public function insert_or_update_user()
    {
        // controlla se c'è id_utente e fa un aggiornamento
        if (isset($this->id_utente)) {
            // check if email is already registered to other user
            try {
                $getEmail = $this->database->prepare("SELECT email FROM `utenti` WHERE email = :email AND id_utente != :id_utente");
                $getEmail->bindValue(":email", $this->email);
                $getEmail->bindValue(":id_utente", $this->id_utente);

                $getEmail->execute();
                $resultEmail = $getEmail->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
            }
            if (isset($resultEmail['email'])) {
                $data['state'] = 'error';
                $data['message'] = 'Email già registrato per altro utente';

                return $data;
            }
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
                echo $e->getMessage();
                echo $e->getCode();
                error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
            }

            if ($rows > 0) {
                $data['state'] = 'success';
                $data['code'] = '200';
                $data['message'] = 'Utente aggiornato';

                return $data;
            } else {
                $data['pass'] = $this->password;
                $data['state'] = 'error';
                $data['message'] = 'Problema! utente non aggiornato';

                return $data;
            }
        }
        // se non c'è id_utente fa un nuovo registro
        // check if email is already registered
        try {
            $getEmail = $this->database->prepare("SELECT email FROM `utenti` WHERE email = :email");
            $getEmail->bindValue(":email", $this->email);

            $getEmail->execute();
            $resultEmail = $getEmail->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }

        if (isset($resultEmail['email'])) {
            $data['state'] = 'error';
            $data['message'] = 'Email già registrato';

            return $data;
        } else {

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
            } catch (PDOException $e) {
                error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
            }

            // controlla se l'utente è stato registrato
            $rows = $query->rowCount();
            if ($rows > 0) {
                $data['state'] = 'success';
                $data['message'] = 'Nuovo utente registrato';
            } else {
                $data['state'] = 'error';
                $data['message'] = 'Problema!! utente non registrato';
            }

            return $data;
        }
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

        // prepare i dati per creare il json
        $utenti = array();
        $utente = array();
        foreach ($result as $key => $value) {
            $fa_lock = $value['attivo'] ? 'fas fa-lock' : 'fas fa-lock-open';
            $title = $value['attivo'] ? 'disabilitare' : 'attivare';
            foreach ($value as $k => $v) {
                if ($k == 'id_utente') {
                    $utente['azione'] = "
                        <span class='update_user' id='ut_$v'><i class='fas fa-edit' title='modificare'></i></span> 
                        <span class='disable_user' id='ut_$v'><i class='$fa_lock' title='$title'></i></span>";
                } else {
                    $utente[$k] = $v;
                }
            }
            $utenti[] = $utente;
        }

        return $utenti;
    }
    public function get_user_data()
    {
        try {
        $query = $this->database->prepare("SELECT `nome`, `cognome`, `email`, `codice_fiscale`, `data_nascita`, `telefono`, `id_livello` FROM `utenti` WHERE id_utente = :id_utente");
            $query->bindValue(":id_utente", $this->id_utente);
            $query->execute();
            $query->setFetchMode(PDO::FETCH_ASSOC);
            $result = $query->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }

        foreach ($result as $key => $value) {
            if ($key == 'data_nascita' ){
                if ($value != '0000-00-00'){
                    $user[$key] = $value;
                }
            } else if($value != ''){
                $user[$key] = $value;
            }
        }
        $data['user'] = $user;
        $data['state'] = 'success';
        $data['code'] = 200;
        $data['message'] = 'Utente pronto per essere aggiornato';

        return $data;
    }

    public function toggle_utente()
    {
        // controlla se l'utente è attivo 
        try {
            $query = $this->database->prepare("SELECT attivo FROM `utenti` WHERE id_utente = :id_utente");
            $query->bindValue(":id_utente", $this->id_utente);
            $query->execute();
            $this->attivo = $query->fetchColumn(0);
        } catch (PDOException $e) {
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }

        // esegue l'aggiornamento con risultato inverso
        try {
            $query = $this->database->prepare("UPDATE `utenti` SET attivo = :attivo WHERE id_utente = :id_utente");
            $query->bindValue(":attivo", !$this->attivo);
            $query->bindValue(":id_utente", $this->id_utente);
            $query->execute();
        } catch (PDOException $e) {
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }

        if(!$this->attivo){
            $data['message'] = "L'utente è attivo";
        } else {
            $data['message'] = "L'utente è disabilitato";
        }
        $data['state'] = 'success';
        $data['code'] = 200;

        return $data;
    }
}
