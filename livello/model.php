<?php 
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

include_once('livello_class.php');
$livello = new livelloClass();

$action=$_REQUEST['action'];
// echo __LINE__. $action;
switch ($action) {
    case "get_livelli":

        $livelli = $livello->get_livelli();
        echo json_encode($livelli);

        break;
    
    case "autenticazione":

        $livello->id_livello = $autenticazione->id_livello;
        $data = $livello->get_utente_livello();
    
    default:
        # code...
        //echo "not passing";
        break;
}




