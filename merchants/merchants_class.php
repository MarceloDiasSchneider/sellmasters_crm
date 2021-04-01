<?php

class merchantsClass
{
    public $id;
    public $nome;
    public $nome_sociale;
    public $merchant_id;
    public $mws;
    public $interval_between_check;
    public $nome_contatto;
    public $telefono;
    public $email;
    public $indirizzo;
    public $numero_civico;
    public $citta;
    public $cap;
    public $stato;
    public $provincia;
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

    public function check_nome_merchant_id()
    {
        // check if the nome and merchant_id is already used from another merchant
        try {
            $query = $this->database->prepare("SELECT nome, merchant_id FROM `merchants` WHERE nome = :nome AND merchant_id = :merchant_id");
            $query->bindValue(":nome", $this->nome);
            $query->bindValue(":merchant_id", $this->merchant_id);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $result['catchError'] = 'code => ' . $e->getCode() . ' | message => ' . $e->getMessage();
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }

        return $result;
    }

    public function insert_merchant()
    {
        try {
            $query = $this->database->prepare("INSERT INTO `merchants` 
            (`nome`, `merchant_id`, `mws`, `interval_between_check`, `indirizzo`, `telefono`, `nome_sociale`, `stato`, `citta`, `numero_civico`, `cap`, `email`, `nome_contatto`, `provincia`, `attivo`) 
            VALUES (:nome, :merchant_id, :mws, :interval_between_check, :indirizzo, :telefono, :nome_sociale, :stato, :citta, :numero_civico, :cap, :email, :nome_contatto, :provincia, :attivo)");
            $query->bindValue(":nome", $this->nome);
            $query->bindValue(":merchant_id", $this->merchant_id);
            $query->bindValue(":mws", $this->mws);
            $query->bindValue(":interval_between_check", $this->interval_between_check);
            $query->bindValue(":indirizzo", $this->indirizzo);
            $query->bindValue(":telefono", $this->telefono);
            $query->bindValue(":nome_sociale", $this->nome_sociale);
            $query->bindValue(":stato", $this->stato);
            $query->bindValue(":citta", $this->citta);
            $query->bindValue(":numero_civico", $this->numero_civico);
            $query->bindValue(":cap", $this->cap);
            $query->bindValue(":email", $this->email);
            $query->bindValue(":nome_contatto", $this->nome_contatto);
            $query->bindValue(":provincia", $this->provincia);
            $query->bindValue(":attivo", $this->attivo);
            $result = $query->execute();
        } catch (PDOException $e) {
            $result['catchError'] = 'code => ' . $e->getCode() . ' | message => ' . $e->getMessage();
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }

        return $result;
    }

    public function update_merchant()
    {
        try {
            $query = $this->database->prepare("UPDATE `merchants` 
                SET `nome` = :nome, `merchant_id` = :merchant_id, `mws` = :mws, `interval_between_check` = :interval_between_check, `indirizzo` = :indirizzo, `telefono` = :telefono, `nome_sociale` = :nome_sociale, `stato` = :stato, `citta` = :citta, `numero_civico` = :numero_civico, `cap` = :cap, `email` = :email, `nome_contatto` = :nome_contatto, `provincia` = :provincia WHERE `id` = :id");

            $query->bindValue(":nome", $this->nome);
            $query->bindValue(":merchant_id", $this->merchant_id);
            $query->bindValue(":mws", $this->mws);
            $query->bindValue(":interval_between_check", $this->interval_between_check);
            $query->bindValue(":indirizzo", $this->indirizzo);
            $query->bindValue(":telefono", $this->telefono);
            $query->bindValue(":nome_sociale", $this->nome_sociale);
            $query->bindValue(":stato", $this->stato);
            $query->bindValue(":citta", $this->citta);
            $query->bindValue(":numero_civico", $this->numero_civico);
            $query->bindValue(":cap", $this->cap);
            $query->bindValue(":email", $this->email);
            $query->bindValue(":nome_contatto", $this->nome_contatto);
            $query->bindValue(":provincia", $this->provincia);
            $query->bindValue(":id", $this->id);

            $query->execute();
            $result = $query->rowCount();
        } catch (PDOException $e) {
            $result['catchError'] = 'code => ' . $e->getCode() . ' | message => ' . $e->getMessage();
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }

        return $result;
    }

    public function get_merchants()
    {
        // get all merchants
        try {
            $query = $this->database->prepare("SELECT `id`,`nome`, `merchant_id`, `mws`, `interval_between_check`, `indirizzo`, `telefono`, `nome_sociale`, `stato`, `citta`, `numero_civico`, `cap`, `email`, `nome_contatto`, `provincia`, `attivo` FROM `merchants`;
            ");
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $result['catchError'] = 'code => ' . $e->getCode() . ' | message => ' . $e->getMessage();
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }

        return $result;
    }

    public function get_merchant_data()
    {
        try {
            $query = $this->database->prepare("SELECT `id`,`nome`, `merchant_id`, `mws`, `interval_between_check`, `indirizzo`, `telefono`, `nome_sociale`, `stato`, `citta`, `numero_civico`, `cap`, `email`, `nome_contatto`, `provincia`, `attivo` FROM `merchants` WHERE id = :id");
            $query->bindValue(":id", $this->id);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $result['catchError'] = 'code => ' . $e->getCode() . ' | message => ' . $e->getMessage();
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }

        return $result;
    }

    public function get_merchant_attivo()
    {
        // get the merchant attivo value 
        try {
            $query = $this->database->prepare("SELECT attivo FROM `merchants` WHERE id = :id");
            $query->bindValue(":id", $this->id);
            $query->execute();
            $result = $query->fetchColumn(0);
        } catch (PDOException $e) {
            $result['catchError'] = 'code => ' . $e->getCode() . ' | message => ' . $e->getMessage();
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }

        return $result;
    }

    public function toggle_merchant_attivo()
    {
        try {
            $query = $this->database->prepare("UPDATE `merchants` SET attivo = :attivo WHERE id = :id");
            $query->bindValue(":attivo", !$this->attivo);
            $query->bindValue(":id", $this->id);
            $result = $query->execute();
        } catch (PDOException $e) {
            $result['catchError'] = 'code => ' . $e->getCode() . ' | message => ' . $e->getMessage();
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }

        return $result;
    }
}
