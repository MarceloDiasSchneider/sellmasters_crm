<?php

class nuovaClass
{


    public $database;

    function __construct()
    {
        include_once("../connessione/database_pdo_sing.php");
        $obj = DatabasePdoClass::getInstance();
        $this->database = $obj->creaConnessione();
    }

    public function set_method()
    {

    }

}