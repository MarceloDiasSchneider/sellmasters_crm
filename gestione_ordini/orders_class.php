<?php

class orderclass
{

    public $merchant_id;
    public $order_id;

    public $database;

    function __construct()
    {
        include_once("../connessione/database_pdo_sing.php");
        $obj = DatabasePdoClass::getInstance();
        $this->database = $obj->creaConnessione();
    }


    public function get_orders()
    {
        if (!$this->merchant_id > 0) {
            $query = $this->database->prepare("SELECT *  from orders order by purchase_date");
        } else {
            $query = $this->database->prepare("SELECT *  from orders  WHERE merchant_id=:merchant_id  order by purchase_date");
            $query->bindValue(":merchant_id", $this->merchant_id);
        }
        try {
            $query->execute();
            $orders = $query->fetchAll();
            //var_dump($merchants);
            return $orders;
        } catch (PDOException $e) {
            //throw $th;
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }
    }
    public function get_order_by_id()
    {
        $query = $this->database->prepare("SELECT *  from orders  WHERE order_id=:order_id  order by purchase_date");
        $query->bindValue(":order_id", $this->order_id);

        try {
            $query->execute();
            $orders = $query->fetchAll();
            //var_dump($merchants);
            return $orders;
        } catch (PDOException $e) {
            //throw $th;
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }
    }
}
