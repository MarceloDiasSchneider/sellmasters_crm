<?php

if (isset($_SERVER['REQUEST_METHOD'])) {
    // get resquet body data  
    if (!isset($requestBody)) {
        $requestBody = json_decode(file_get_contents('php://input'), true);
    }
} else {
    // report an error if there is no request method
    $data['code'] = '406';
    $data['state'] = 'Not Acceptable';
    $data['message'] = 'Request method not defined';

    echo json_encode($data);
    exit;
}

include_once('utente_class.php');
$utente = new utenteClass();

switch ($requestBody['action']) {
    case 'insert_or_update_user':
        // get the code session to verify if is the same 
        $form = $requestBody['codiceSessione'];
        $session = $_SESSION['codiceSessione'];

        // if the code session does not match, unauthorized the insert or update
        if ($form != $session) {
            $data['state'] = 'Unauthorized';
            $data['code'] = '406';
            $data['message'] = 'Session code doesn\'t match';

            echo json_encode($data);
            exit;
        }

        // check if the password is not blank
        if ($requestBody['password'] != $requestBody['verificaPassword']) {
            $data['code'] = '401';
            $data['state'] = 'Unauthorized';
            $data['message'] = 'Le password non corrispondono';

            echo json_encode($data);
            exit;
        }
        // Check if it's have an ID to perform an update instead of insert 
        switch ($requestBody['user_id']) {
            case null: //Inser a new user
                // Set the value to the variables class
                // Use an authentication method to encrypt the password. 
                include_once('../autenticazione_VueJs/model.php');
                $utente->password = $autenticazione->password;
                $utente->nome = $requestBody['nome'];
                $utente->cognome = $requestBody['cognome'];
                $utente->email = $requestBody['email'];
                $utente->codice_fiscale = $requestBody['codice_fiscale'];
                $utente->telefono = $requestBody['telefono'];
                $utente->data_nascita = $requestBody['data_nascita'];
                $utente->profile = $requestBody['profile'];
                $utente->attivo = $requestBody['attivo'];
                // check if email is already registered
                $email = $utente->check_email();
                if (isset($email['catchError'])) {
                    // report a try catch error
                    $data['code'] = '500';
                    $data['state'] = 'Internal Server Error';
                    $data['message'] = $email['catchError'];
                } else if (isset($email['email'])) {
                    // report that the email is already used
                    $data['code'] = '401';
                    $data['state'] = 'Unauthorized';
                    $data['message'] = 'Email già registrato';
                } else {
                    $rows = $utente->insert_user();
                    if (isset($rows['catchError'])) {
                        // report a try catch error
                        $data['code'] = '500';
                        $data['state'] = 'Internal Server Error';
                        $data['message'] = $rows['catchError'];
                    } else if ($rows > 0) {
                        // report user registred successfully
                        $data['code'] = '201';
                        $data['state'] = 'Success';
                        $data['message'] = 'Nuovo utente registrato';
                    } else {
                        $data['code'] = '400';
                        $data['state'] = 'Bad request';
                        $data['message'] = 'Problema!! utente non registrato';
                    }
                }
                echo json_encode($data);
                break;

            case true: // Update an user
                // Set the value to the variables class
                // Use an authentication method to encrypt the password. 
                include_once('../autenticazione_VueJs/model.php');
                if ($requestBody['password'] != null) {
                    $utente->password = $autenticazione->password;
                }
                $utente->id_utente = $requestBody['user_id'];
                $utente->nome = $requestBody['nome'];
                $utente->cognome = $requestBody['cognome'];
                $utente->data_nascita = $requestBody['data_nascita'];
                $utente->codice_fiscale = $requestBody['codice_fiscale'];
                $utente->telefono = $requestBody['telefono'];
                $utente->profile = $requestBody['profile'];
                $utente->email = $requestBody['email'];
                $utente->attivo = $requestBody['attivo'];
                // check if the email is already used from another user
                $email = $utente->check_email_other_user();
                if (isset($email['catchError'])) {
                    // report a try catch error
                    $data['code'] = '500';
                    $data['state'] = 'Internal Server Error';
                    $data['message'] = $email['catchError'];
                } else if (isset($email['email'])) {
                    // report an error if the email has already been used from another user
                    $data['state'] = 'Unauthorized';
                    $data['code'] = '401';
                    $data['message'] = 'Email già registrato per altro utente';
                } else {
                    // executer the update
                    if ($requestBody['password'] != null) {
                        $result = $utente->update_user_with_password();
                        $data['password'] = 'true';
                    } else {
                        $result = $utente->update_user_no_password();
                        $data['password'] = 'false';
                    }
                    $data['$result'] = $result;

                    if (isset($result['catchError'])) {
                        // report a try catch error
                        $data['code'] = '500';
                        $data['state'] = 'Internal Server Error';
                        $data['message'] = $result['catchError'];
                    } else if ($result > 0) {
                        // check if the user was updated successfully
                        $data['state'] = 'Success';
                        $data['code'] = '200';
                        $data['message'] = 'Utente aggiornato';
                    } else {
                        $data['code'] = '400';
                        $data['state'] = 'Bad request';
                        $data['message'] = 'Utente non aggiornato';
                    }
                }
                echo json_encode($data);
                break;

            default:
                # code...
                break;
        }

        break;
    case 'get_all_users':
        $utenti = $utente->get_all_users();

        // get profile to show description on the datatables
        include_once('../profile_VueJs/model.php');
        if (isset($profiles['catchError'])) {
            // check if an error occurred on try catch
            $data['code'] = '500';
            $data['state'] = 'error';
            $data['message'] = $profiles['catchError'];

            echo json_encode($data);
        } else {
            foreach ($profiles as $key => $value) {
                $descrizioni[$value['id_profile']] = $value['descrizione'];
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
                    } else if ($k == 'id_profile') {
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
    case 'get_user_data':
        // set the value to class variable
        $utente->id_utente = $requestBody['id_utente'];
        // call the class to get the data 
        $result = $utente->get_user_data();
        if (isset($result['catchError'])) {
            // report a try catch error
            $data['code'] = '500';
            $data['state'] = 'Internal Server Error';
            $data['message'] = $result['catchError'];
        } else {
            // prepare the data to return to front-end
            foreach ($result as $key => $value) {
                // format the date
                if ($key == 'data_nascita') {
                    if ($value != '0000-00-00') {
                        $user[$key] = $value;
                    }
                    // fill all value as null
                } else if ($value != '' || $value != null) {
                    $user[$key] = $value;
                }
            }
            $data['code'] = '200';
            $data['state'] = 'Success';
            $data['message'] = 'Utente pronto per essere aggiornato';
            $data['user'] = $user;
        }
        echo json_encode($data);

        break;
    case 'toggle_user_active':
        // get user id from the session
        $utente->id_utente = $requestBody['id_utente'];
        // get the user attivo value
        $attivo = $utente->get_user_attivo();
        if (isset($attivo['catchError'])) {
            // report a try catch error
            $data['code'] = '500';
            $data['state'] = 'Internal Server Error';
            $data['message'] = $attivo['catchError'];
        } else {
            // set the user attivo value
            $utente->attivo = $attivo;
            // toggle the value into database
            $result = $utente->toggle_user_attivo();
            if (isset($result['catchError'])) {
                // report a try catch error
                $data['code'] = '500';
                $data['state'] = 'Internal Server Error';
                $data['message'] = $result['catchError'];
            } else {
                // report the success message
                if ($utente->attivo) {
                    $data['message'] = "L'utente è disabilitato";
                } else {
                    $data['message'] = "L'utente è attivo";
                }
                $data['state'] = 'Success';
                $data['code'] = '200';
            }
        }

        echo json_encode($data);
        break;
    case 'get_profiles':
        // call a profile method return the options to the select
        include_once('../profile_VueJs/model.php');

        break;
    default:
        # code...
        break;
}
