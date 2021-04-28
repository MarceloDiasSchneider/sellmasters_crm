<?php

class pagesClass
{
    public $id_access_profile;
    public $id_page;
    public $id_profile;
    public $access;

    public $page_parent_id;
    
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

    public function get_pages()
    {
        try {
            $query = $this->database->prepare("SELECT `id_page` as idPage, `main`, `subpage` FROM `pages` ");
            $query->execute();
            $query->setFetchMode(PDO::FETCH_ASSOC);
            $result = $query->fetchAll();
        } catch (PDOException $e) {
            $result['catchError'] = 'code => ' . $e->getCode() . ' | message => ' . $e->getMessage();
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }

        return $result;
    }

    public function insert_page_access()
    {
        try {
            $query = $this->database->prepare("INSERT INTO `access_profile` (`id_page`, `id_profile`, `access`)
                VALUES ( :id_page, :id_profile, :access )");
            $query->bindValue(":id_page", $this->id_page);
            $query->bindValue(":id_profile", $this->id_profile);
            $query->bindValue(":access", $this->access);
            $query->execute();
            $result = $this->database->lastInsertId();
        } catch (PDOException $e) {
            $result['catchError'] = 'code => ' . $e->getCode() . ' | message => ' . $e->getMessage();
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }

        return $result;
    }

    public function update_page_access()
    {
        try {
            $query = $this->database->prepare("UPDATE `access_profile` 
                SET `access` = :access
                WHERE id_page = :id_page AND id_profile = :id_profile");
            $query->bindValue(":id_page", $this->id_page);
            $query->bindValue(":id_profile", $this->id_profile);
            $query->bindValue(":access", $this->access);
            $query->execute();
            $result = $query->rowCount();
        } catch (PDOException $e) {
            $result['catchError'] = 'code => ' . $e->getCode() . ' | message => ' . $e->getMessage();
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }

        return $result;
    }

    public function get_pages_by_id_profile()
    {
        try {
            $query = $this->database->prepare("SELECT `id_access_profile`, `id_page`, `id_profile`, `access` FROM `access_profile` WHERE `id_profile` = :id_profile ");
            $query->bindValue(":id_profile", $this->id_profile);
            $query->execute();
            $query->setFetchMode(PDO::FETCH_ASSOC);
            $result = $query->fetchAll();
        } catch (PDOException $e) {
            $result['catchError'] = 'code => ' . $e->getCode() . ' | message => ' . $e->getMessage();
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }

        return $result;
    }
}
