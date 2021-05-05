<?php

include_once('../common_VueJs/reportException.php');

// check if the request method is setted
try {
    if (isset($_SERVER['REQUEST_METHOD'])) {
        // get resquet body data  
        if (!isset($requestBody)) {
            $requestBody = json_decode(file_get_contents('php://input'), true);
        }
    } else {
        throw new reportException('Request method not defined', 406);
    }
} catch (reportException $e) {
    $e->reportError();
}

include_once('utente_class.php');
$utente = new utenteClass();

switch ($requestBody['action']) {
    case 'insert_or_update_user':
        try { // get the session code to check if match
            $form = $requestBody['codiceSessione'];
            $session = $_SESSION['codiceSessione'];
            // if the session code doesn't match throw a exception
            if ($form != $session) {
                throw new reportException('Session code doesn\'t match', 406);
            }
        } catch (reportException $e) {
            $e->reportError();
        }
        
        try { // check if the passwords match 
            if ($requestBody['password'] != $requestBody['verificaPassword']) {
                throw new reportException('Le password non corrispondono', 401);
            }
        } catch (reportException $e) {
            $e->reportError();
        }

        // check if it's have an ID to perform an update instead of insert 
        switch ($requestBody['user_id']) {
            case null: // insert a new user
                // set the value to the variables class
                $utente->nome = $requestBody['nome'];
                $utente->cognome = $requestBody['cognome'];
                $utente->email = $requestBody['email'];
                $utente->codice_fiscale = $requestBody['codice_fiscale'];
                $utente->telefono = $requestBody['telefono'];
                $utente->data_nascita = $requestBody['data_nascita'];
                $utente->profile = $requestBody['profile'];
                $utente->attivo = $requestBody['attivo'];
                // use an authentication method to encrypt the password. 
                include_once('../autenticazione_VueJs/model.php');
                $utente->password = $autenticazione->password;
                try { 
                    // check if email is already registered
                    $email = $utente->check_email();
                    if (isset($email['catchError'])) {
                        // report a try catch error on database
                        throw new reportException($email['catchError'], 500);
                    } 
                    if (isset($email['email'])) {
                        // report that the email is already used
                        throw new reportException('Email già registrato', 401);
                    }
                    $newUser = $utente->insert_user();
                    if (isset($newUser['catchError'])) {
                        // report a try catch error on database
                        throw new reportException($newUser['catchError'], 500);
                    } 
                    if (!$newUser) {
                        // report user is not registred
                        throw new reportException('Problema! utente non registrato', 400);
                    }
                    // report user registred successfully
                    $data['code'] = 201;
                    $data['state'] = 'Success';
                    $data['message'] = 'Nuovo utente registrato';
                    echo json_encode($data);
                } catch (reportException $e) {
                    $e->reportError();
                }
                break;
            case true: // update an user
                // set the value to the variables class
                $utente->id_utente = $requestBody['user_id'];
                $utente->nome = $requestBody['nome'];
                $utente->cognome = $requestBody['cognome'];
                $utente->data_nascita = $requestBody['data_nascita'];
                $utente->codice_fiscale = $requestBody['codice_fiscale'];
                $utente->telefono = $requestBody['telefono'];
                $utente->profile = $requestBody['profile'];
                $utente->email = $requestBody['email'];
                $utente->attivo = $requestBody['attivo'];
                // use an authentication method to encrypt the password. 
                include_once('../autenticazione_VueJs/model.php');
                if ($requestBody['password'] != null) {
                    $utente->password = $autenticazione->password;
                }
                try {
                    // check if the email is already used from another user
                    $email = $utente->check_email_other_user();
                    if (isset($email['catchError'])) {
                        // report a try catch error on database
                        throw new reportException($email['catchError'], 500);
                    }
                    if (isset($email['email'])) {
                        // report an error if the email has already been used from another user
                        throw new reportException('Email già registrato per altro utente', 401);
                    } 
                    // executer the update 
                    if ($requestBody['password'] != null) {
                        $userUpdated = $utente->update_user_with_password();
                    } else {
                        $userUpdated = $utente->update_user_no_password();
                    }
                    if (isset($userUpdated['catchError'])) {
                        // report a try catch error on database
                        throw new reportException($userUpdated['catchError'], 500);
                    } 
                    if (!$userUpdated) {
                        // report user is not updated
                        throw new reportException('Problema! utente non aggiornato', 400);
                    }
                    // check if the user was updated successfully
                    $data['state'] = 'Success';
                    $data['code'] = 200;
                    $data['message'] = 'Utente aggiornato';
                    echo json_encode($data);
                } catch (reportException $e) {
                    $e->reportError();
                }
                break;

            default:
                try {
                    throw new reportException('Problema nell\'identificazione dell\'utente', 400);
                } catch (reportException $e) {
                    $e->reportError();
                }
                break;
        }
        break;
    case 'get_all_users':
        try {
            $utenti = $utente->get_all_users();
            if(isset($utenti['catchError'])){
                // report a try catch error on database
                throw new reportException($utenti['catchError'], 500);
            }
            // get profile to show description on the datatables
            include_once('../profile_VueJs/model.php');
            if(isset($profiles['catchError'])){
                // report a try catch error on database
                throw new reportException($profiles['catchError'], 500);
            }
            foreach ($profiles as $key => $value) {
                $descrizioni[$value['id_profile']] = $value['descrizione'];
            }
            // format the data to datatables
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
                    } else if ($k == 'data_nascita' && $v != '0000-00-00' && $v != null) {
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
        } catch (reportException $e) {
            $e->reportError();
        }
        break;

    case 'get_user_data':
        try {
            // set the user id
            $utente->id_utente = $requestBody['id_utente'];
            // get the user data
            $userData = $utente->get_user_data();
            if (isset($userData['catchError'])) {
                // report a try catch error on database
                throw new reportException($userData['catchError'], 500);
            } 
            // format the user data
            foreach ($userData as $key => $value) {
                if ($key == 'data_nascita') {
                    // format the date
                    if ($value != '0000-00-00') {
                        $user[$key] = $value;
                    }
                } else if ($value != '' || $value != null) {
                    // fill all value as null
                    $user[$key] = $value;
                }
            }
            $data['code'] = 200;
            $data['state'] = 'Success';
            $data['message'] = 'Utente pronto per essere aggiornato';
            $data['user'] = $user;
            echo json_encode($data);
        } catch (reportException $e) {
            $e->reportError();
        }
        break;

    case 'toggle_user_active':
        try {
            // set the user id
            $utente->id_utente = $requestBody['id_utente'];
            // get the user attivo value
            $attivo = $utente->get_user_attivo();
            if (isset($attivo['catchError'])) {
                // report a try catch error on database
                throw new reportException($attivo['catchError'], 500);
            }
            // set the user attivo value
            $utente->attivo = $attivo;
            // toggle the value into database
            $result = $utente->toggle_user_attivo();
            if (isset($result['catchError'])) {
                // report a try catch error on database
                throw new reportException($result['catchError'], 500);
            } 
            // report the success message
            if ($utente->attivo) {
                $data['message'] = "L'utente è disabilitato";
            } else {
                $data['message'] = "L'utente è attivo";
            }
            $data['state'] = 'Success';
            $data['code'] = 200;
            echo json_encode($data);
        } catch (reportException $e) {
            $e->reportError();
        }
        break;

    case 'get_profiles_active':
        // call a profile method return the options to the select
        include_once('../profile_VueJs/model.php');
        try {
            if (isset($profiles['catchError'])){
                // report a try catch error on database
                throw new reportException($profiles['catchError'], 500);
            } 
            // return the data
            $data['code'] = 200;
            $data['state'] = 'Success';
            $data['message'] = 'All profiles active was found';
            $data['profiles'] = $profiles;
            echo json_encode($data);
        } catch (reportException $e) {
            $e->reportError();
        }
        break;
    default:
        # code...
        break;
}
