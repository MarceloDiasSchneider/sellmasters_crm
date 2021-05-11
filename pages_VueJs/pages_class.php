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

    public function insert_page_access($allPages)
    {
        try {
            $query = $this->database->prepare("INSERT INTO `access_profile` (`id_page`, `id_profile`, `access`)
            VALUES ( :id_page, :id_profile, :access )");
            $this->database->beginTransaction();
            foreach ($allPages as $key => $value) {
                $this->id_page = $value['idPage'];
                $this->access =  isset($value['checked']) ? $value['checked'] : 0;
                $query->bindValue(":id_page", $this->id_page);
                $query->bindValue(":id_profile", $this->id_profile);
                $query->bindValue(":access", $this->access);
                $query->execute();
                $result = $query->rowCount();
            }
            $this->database->commit();
        } catch (PDOException $e) {
            $this->database->rollback();
            $result['catchError'] = 'code => ' . $e->getCode() . ' | message => ' . $e->getMessage();
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }
        return $result;
    }

    public function update_page_access($to_update, $checked_pages)
    {
        try {
            $query = $this->database->prepare("UPDATE `access_profile` 
                SET `access` = :access
                WHERE id_page = :id_page AND id_profile = :id_profile");
            foreach ($to_update as $key => $value) {
                $this->id_page = $checked_pages[$key]['idPage'];
                $this->access = $checked_pages[$key]['checked'];
                $query->bindValue(":id_page", $this->id_page);
                $query->bindValue(":id_profile", $this->id_profile);
                $query->bindValue(":access", $this->access);
                $query->execute();
                $result = $query->rowCount();
            }
        } catch (PDOException $e) {
            $result['catchError'] = 'code => ' . $e->getCode() . ' | message => ' . $e->getMessage();
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }

        return $result;
    }

    public function insert_missing_page_access($to_insert, $checked_pages)
    {
        try {
            $query = $this->database->prepare("INSERT INTO `access_profile` (`id_page`, `id_profile`, `access`)
            VALUES ( :id_page, :id_profile, :access )");
            foreach ($to_insert as $key => $value) {
                $this->id_page = $checked_pages[$key]['idPage'];
                $this->access = $checked_pages[$key]['checked'];
                $query->bindValue(":id_page", $this->id_page);
                $query->bindValue(":id_profile", $this->id_profile);
                $query->bindValue(":access", $this->access);
                $query->execute();
                $result = $query->rowCount();
            }
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

    public function get_access_pages_by_id_profile()
    {
        try {
            $query = $this->database->prepare("SELECT `pages`.`main`, `pages`.`subpage`, `pages`.`link`, `pages`.`nav_icon`
            FROM `access_profile` 
            LEFT JOIN `pages`
            ON `access_profile`.`id_page` = `pages`.`id_page`
            WHERE `id_profile` = :id_profile AND `access` = :access");
            $query->bindValue(":id_profile", $this->id_profile);
            $query->bindValue(":access", $this->access);
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
