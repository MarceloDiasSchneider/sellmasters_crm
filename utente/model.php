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

        // // get the code session to verify if is the same 
        // $form = $_REQUEST['codiceSessione'];
        // $session = $_SESSION['codiceSessione'];

        // // if the code session does not match, unauthorized the insert or update
        // if ($form != $session) {
        //     $data['state'] = 'Unauthorized';
        //     $data['code'] = '406';
        //     $data['message'] = 'Session code doesn\'t match';

        //     echo json_encode($data);
        //     exit;
        // }

        // check if the password is not blank
        if ($_REQUEST['password'] == $_REQUEST['verificaPassword']) {

            // Use an authentication method to encrypt the password. 
            include_once('../autenticazione/model.php');
            if ($_REQUEST['password'] != '') {
                $utente->password = $autenticazione->password;
            }

            // Check if it's have an ID to perform an update instead of insert 
            if (isset($_REQUEST['id_utente'])) {
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

            // check if there is an id_utente to update 
            if (isset($utente->id_utente)) {

                // check if the email is already used from another user
                $resultEmail = $utente->check_email_other_user();

                // if the email has already been used, give an error
                if (isset($resultEmail['email'])) {
                    $data['state'] = 'Unauthorized';
                    $data['code'] = '401';
                    $data['message'] = 'Email già registrato per altro utente';
                } else {
                    $rows = $utente->update_user();

                    // check if the user was updated successfully
                    if ($rows > 0) {
                        $data['state'] = 'Success';
                        $data['code'] = '200';
                        $data['message'] = 'Utente aggiornato';
                    } else {
                        $data['code'] = '400';
                        $data['state'] = 'Bad request';
                        $data['message'] = 'Utente non aggiornato';
                    }
                }
            } else {
                // check if email is already registered
                $resultEmail = $utente->check_email();

                // if the email has already been used
                if (isset($resultEmail['email'])) {
                    $data['code'] = '401';
                    $data['state'] = 'Unauthorized';
                    $data['message'] = 'Email già registrato';
                } else {
                    // insert the new user
                    $rows = $utente->insert_user();
                    if ($rows > 0) {
                        $data['code'] = '201';
                        $data['state'] = 'Success';
                        $data['message'] = 'Nuovo utente registrato';
                    } else {
                        $data['code'] = '400';
                        $data['state'] = 'Bad request';
                        $data['message'] = 'Problema!! utente non registrato';
                    }
                }
            }

        } else {
            // report a error if passwords doesn't match
            $data['code'] = '401';
            $data['state'] = 'Unauthorized';
            $data['message'] = 'Le password non corrispondono';

        }
        echo json_encode($data);

        break;

    case "get_utenti";

        $utenti = $utente->get_utenti();

        // get livello to show description on the datatables
        include_once('../livello/model.php');
        if (isset($livelli['catchError'])) {
            
            // check if an error occurred on try catch
            $data['code'] = '500';
            $data['state'] = 'error';
            $data['message'] = $livelli['catchError'];
            
            echo json_encode($data);

        } else {
            foreach ($livelli as $key => $value) {
                $descrizioni[$value['id_livello']] = $value['descrizione'];
            }

            // prepare i dati per creare il json
            $dati = array();
            $data = array();
            foreach ($utenti as $key => $value) {
                $fa_lock = $value['attivo'] ? 'fas fa-lock-open' : 'fas fa-lock';
                $title = $value['attivo'] ? 'disabilitare' : 'attivare';
                foreach ($value as $k => $v) {
                    if ($k == 'id_utente') {
                        $data['azione'] = "
                             <span class='update_user' id='ut_$v'><i class='fas fa-edit' title='modificare'></i></span> 
                             <span class='disable_user' id='ut_$v'><i class='$fa_lock' title='$title'></i></span>";
                    } else if ($k == 'data_nascita' && $v != '0000-00-00') {
                        $data[$k] = date('d/m/Y', strtotime($v));
                    } else if ($k == 'attivo') {
                        if ($v == 1) {
                            $data[$k] = 'Sì';
                        } else {
                            $data[$k] = 'No';
                        }
                    } else if ($k == 'id_livello') {
                        $data[$k] = $descrizioni[$v];
                    } else {
                        $data[$k] = $v;
                    }
                }
                $dati[] = $data;
            }
            echo json_encode($dati);
        }

        break;

    case "get_user_data":

        $utente->id_utente = $_REQUEST['id_utente'];

        $result = $utente->get_user_data();
        if(isset($result['catchError'])){
            $data['code'] = '500'; 
            $data['state'] = 'Internal Server Error';
            $data['message'] = $result['catchError'];
        } else {
            foreach ($result as $key => $value) {
                if ($key == 'data_nascita') {
                    if ($value != '0000-00-00') {
                        $user[$key] = $value;
                    }
                } else if ($value != '') {
                    $user[$key] = $value;
                }
            }
            $data['user'] = $user;
            $data['state'] = 'Success';
            $data['code'] = '200';
            $data['message'] = 'Utente pronto per essere aggiornato';
        }

        echo json_encode($data);

        break;

    case "toggle_utente";

        // get user id from the session
        $utente->id_utente = $_REQUEST['id_utente'];

        // get the user attivo value
        $utente->attivo = $utente->get_user_attivo();

        // toggle the value into database
        $utente->toggle_user_attivo();
        // esegue l'aggiornamento con risultato inverso
        if (!$utente->attivo) {
            $data['message'] = "L'utente è attivo";
        } else {
            $data['message'] = "L'utente è disabilitato";
        }
        $data['state'] = 'Success';
        $data['code'] = '200';

        echo json_encode($data);
        break;

    case "get_livelli":

        // call a livello method return the options to the select
        include_once('../livello/model.php');

        break;

    default:
        # code...
        //echo "not passing";
        break;
}
