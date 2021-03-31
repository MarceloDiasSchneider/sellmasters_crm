<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

include_once('livello_class.php');
$livello = new livelloClass();

$action = $_REQUEST['action'];
// echo __LINE__. $action;
switch ($action) {
    case "get_livelli":

        // get livello to set option on the form | called from utente/model.php
        $livelli = $livello->get_livelli();
        if (isset($livelli['catchError'])){
            $data['code'] = '500'; 
            $data['state'] = 'error';
            $data['message'] = $livelli['catchError'];
        } else {
            $data['code'] = '200'; 
            $data['state'] = 'success';
            $data['message'] = 'all livelli is found';
            $data['livelli'] = $livelli;
        }
        echo json_encode($data);

        break;

    case "get_utenti";

        // get livello to show description on the datatables on utente/model.php
        $livelli = $livello->get_livelli();
        break;

    case "autenticazione":
        // this class is called from autenticazione/model.php to verify if the user has permission
        $livello->id_livello = $autenticazione->id_livello;
        $permissione = $livello->get_utente_livello();

    default:
        # code...
        //echo "not passing";
        break;
}
