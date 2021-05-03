<?php

class profileClass
{

    public $id_profile;
    public $descrizione;
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

    public function insert_profile()
    {
        try {
            $query = $this->database->prepare("INSERT INTO `profiles` ( `descrizione`, `attivo` ) VALUES ( :descrizione, :attivo ) ");
            $query->bindValue(":attivo", $this->attivo);
            $query->bindValue(":descrizione", $this->descrizione);
            $query->execute();
            $result = $this->database->lastInsertId();
        } catch (PDOException $e) {
            $result['catchError'] = 'code => ' . $e->getCode() . ' | message => ' . $e->getMessage();
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }

        return $result;
    }

    public function update_profile()
    {
        try {
            $query = $this->database->prepare("UPDATE `profiles`  
                SET `descrizione` = :descrizione
                WHERE (`id_profile` = :id_profile)");
            $query->bindValue(":id_profile", $this->id_profile);
            $query->bindValue(":descrizione", $this->descrizione);
            $query->execute();
            $result = $query->rowCount();
        } catch (PDOException $e) {
            $result['catchError'] = 'code => ' . $e->getCode() . ' | message => ' . $e->getMessage();
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }

        return $result;
    }

    public function check_description()
    {
        try {
            $query = $this->database->prepare("SELECT `descrizione` FROM profiles WHERE `descrizione` = :descrizione ");
            $query->bindValue(":descrizione", $this->descrizione);
            $query->execute();
            $result = $query->fetch();
        } catch (PDOException $e) {
            $result['catchError'] = 'code => ' . $e->getCode() . ' | message => ' . $e->getMessage();
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }

        return $result;
    }

    public function check_description_others()
    {
        try {
            $query = $this->database->prepare("SELECT `descrizione` FROM profiles WHERE `descrizione` = :descrizione AND id_profile != :id_profile");
            $query->bindValue(":id_profile", $this->id_profile);
            $query->bindValue(":descrizione", $this->descrizione);
            $query->execute();
            $result = $query->fetch();
        } catch (PDOException $e) {
            $result['catchError'] = 'code => ' . $e->getCode() . ' | message => ' . $e->getMessage();
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }

        return $result;
    }

    public function check_description_self()
    {
        try {
            $query = $this->database->prepare("SELECT `descrizione` FROM profiles WHERE `descrizione` = :descrizione AND id_profile = :id_profile");
            $query->bindValue(":id_profile", $this->id_profile);
            $query->bindValue(":descrizione", $this->descrizione);
            $query->execute();
            $result = $query->fetch();
        } catch (PDOException $e) {
            $result['catchError'] = 'code => ' . $e->getCode() . ' | message => ' . $e->getMessage();
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }

        return $result;
    }

    public function get_profile_data()
    {
        try {
            $query = $this->database->prepare("SELECT `descrizione` FROM `profiles` WHERE `id_profile` = :id_profile");
            $query->bindValue(":id_profile", $this->id_profile);
            $query->execute();
            $result = $query->fetch();
        } catch (PDOException $e) {
            $result['catchError'] = 'code => ' . $e->getCode() . ' | message => ' . $e->getMessage();
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }

        return $result;
    }

    public function get_profile_attivo()
    {
        try {
            $query = $this->database->prepare("SELECT `attivo` FROM `profiles` WHERE `id_profile` = :id_profile");
            $query->bindValue(":id_profile", $this->id_profile);
            $query->execute();
            $result = $query->fetch();
        } catch (PDOException $e) {
            $result['catchError'] = 'code => ' . $e->getCode() . ' | message => ' . $e->getMessage();
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }

        return $result;
    }

    public function toggle_profile_attivo()
    {
        try {
            $query = $this->database->prepare("UPDATE `profiles`
                SET `attivo` = :attivo
                WHERE (`id_profile` = :id_profile)");
            $query->bindValue(":attivo", !$this->attivo);
            $query->bindValue(":id_profile", $this->id_profile);
            $query->execute();
            $result = $query->rowCount();
        } catch (PDOException $e) {
            $result['catchError'] = 'code => ' . $e->getCode() . ' | message => ' . $e->getMessage();
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }

        return $result;
    }

    public function get_profiles()
    {
        try {
            $query = $this->database->prepare("SELECT `id_profile`, `descrizione` FROM `profiles`");
            $query->execute();
            $result = $query->fetchAll();
        } catch (PDOException $e) {
            $result['catchError'] = 'code => ' . $e->getCode() . ' | message => ' . $e->getMessage();
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }

        return $result;
    }

    public function get_utente_profile()
    {
        // cerca il profile del'utente 
        try {
            $query = $this->database->prepare("SELECT id_profile FROM `profiles` WHERE `id_profile` = :id_profile");
            $query->bindValue(":id_profile", $this->id_profile);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $result['catchError'] = 'code => ' . $e->getCode() . ' | message => ' . $e->getMessage();
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }

        return $result;
    }
}
