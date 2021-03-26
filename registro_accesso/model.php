<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

include_once('registro_accesso_class.php');
$registriAccesso = new registroAccessoClass();

$action = $_REQUEST['action'];
// echo __LINE__. $action;
switch ($action) {
    case "autenticazione":

        $registriAccesso->regristrare_accesso($autenticazione->id_utente);
        
        break;

    case "registri_accessi":

        $data = $registriAccesso->cerca_registri_accessi();

        echo json_encode($data);
        break;


    default:
        # code...
        break;
}
