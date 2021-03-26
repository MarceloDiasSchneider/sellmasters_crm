<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

include_once('utente_class.php');
$utente = new utenteClass();

$action = $_REQUEST['action'];
// echo __LINE__. $action;
switch ($action) {
    case "insert_or_update_user":

        /* get the code session to verify that it is the same */
        $form = $_REQUEST['codiceSessione'];
        $session = $_SESSION['codiceSessione'];

        if ($form != $session) {
            $data['state'] = 'unauthorized';
            $data['code'] = '401';
            $data['message'] = 'Unauthorized : session code doesn\'t match';

            echo json_encode($data);
            exit;
        }

        if ($_REQUEST['password'] == $_REQUEST['verificaPassword']) {

            /* Utilizza un metodo di autenticazione per crittografare la password. */
            include_once('../autenticazione/model.php');
            if($_REQUEST['password'] != ''){
                $utente->password = $passwordCrypted;
            }

            /* Controlla se hai un ID per eseguire un aggiornamento invece dell'inserimento */
            if(isset($_REQUEST['id_utente'])){
                $utente->id_utente = $_REQUEST['id_utente'];
            }

            $utente->nome = $_REQUEST['nome'];
            $utente->cognome = $_REQUEST['cognome'];
            $utente->email = $_REQUEST['email'];
            $utente->codice_fiscale = $_REQUEST['codice_fiscale'];
            $utente->telefono = $_REQUEST['telefono'];
            $utente->data_nascita = $_REQUEST['data_nascita'];
            $utente->livello = $_REQUEST['livello'];
            $utente->attivo = $_REQUEST['attivo'];

            $data = $utente->insert_or_update_user();

            echo json_encode($data);
        } else {
            $data['state'] = 'error';
            $data['message'] = 'Le password non corrispondono';

            echo json_encode($data);
        }
        break;

    case "get_utenti";

        $utenti = $utente->get_utenti();
        echo json_encode($utenti);
        
        break;
        
    case "get_user_data":
        
        $utente->id_utente = $_REQUEST['id_utente'];
        $data = $utente->get_user_data();
        echo json_encode($data);

        break;
    
    case "toggle_utente";
        $utente->id_utente = $_REQUEST['id_utente'];
        $data = $utente->toggle_utente();
        
        echo json_encode($data);
        break;

    case "get_livelli":

        include_once('../livello/model.php');

        break;

    default:
        # code...
        //echo "not passing";
        break;
}
