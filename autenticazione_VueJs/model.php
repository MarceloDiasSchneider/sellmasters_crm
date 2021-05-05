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

include_once('autenticazione_class.php');
$autenticazione = new autenticazioneClass();

$action = $requestBody['action'];
switch ($action) {
    case "autenticazione":
        $autenticazione->email = $requestBody['email'];
        $autenticazione->password = $requestBody['password'];
        // encrypt the password
        $autenticazione->crypt_password();
        try {
            // check e-mail and password match to a user
            $utente = $autenticazione->verifica_accesso();
            if (isset($utente['catchError'])) {
                // report a try catch error on database
                throw new reportException($utente['catchError'], 500);
            }
            if (!isset($utente['id_utente'])) {
                // if user not found report a error
                throw new reportException('Email o password errate', 401);
            }
            if ($utente['attivo'] != 1) {
                // if user is desabled report a error
                throw new reportException('Utente disabilitato', 401);
            }
            // set the user data to open the session
            $autenticazione->id_utente = $utente['id_utente'];
            $autenticazione->nome = $utente['nome'];
            $autenticazione->id_profile = $utente['id_profile'];

            // if authentication is successfully get the profile
            include_once('../profile_VueJs/model.php');
            // check if an error occurred on try catch
            if (isset($profileActive['catchError'])) {
                // report a try catch error on database
                throw new reportException($profileActive['catchError'], 500);
            }
            if ($profileActive['attivo'] != 1) {
                // if profile is desabled report a error
                throw new reportException('Profilo dell\'utente disabilitato', 401);
            }
            include_once('../pages_VueJs/model.php');
            if (isset($accessPages['catchError'])) {
                // report a try catch error on database
                throw new reportException($accessPages['catchError'], 500);
            }
            if (!count($accessPages)) {
                // report a try catch error on database
                throw new reportException('Profilo senza permissone', 401);
            }
            include_once('../registro_accesso_VueJs/model.php');
            if (isset($accessoRegistrato['catchError'])) {
                // report a try catch error on database
                throw new reportException($accessoRegistrato['catchError'], 500);
            }
            // prepare the date that the session begins
            $timezone = new DateTimeZone('Europe/Rome');
            $now = new DateTime('now', $timezone);
            $datatime = $now->format('Y-m-d H:i:s');
            // random a code to set the session
            $autenticazione->random_code();
            // user session starts
            $_SESSION["codiceSessione"] = $autenticazione->codice;
            $_SESSION["id_utente"] = $autenticazione->id_utente;
            $_SESSION["nome"] = $autenticazione->nome;
            $_SESSION['data'] = $datatime;
            $_SESSION['accessPages'] = $accessPages;
            // send a response
            $data['state'] = 'Success';
            $data['code'] = 201;
            $data['message'] = 'Acesso registrato';
            $data['url'] = $accessPages[0]['link'];
            echo json_encode($data);
        } catch (reportException $e) {
            $e->reportError();
        }
        break;
    case "insert_or_update_user":
        // this method is called from utente/model.php
        // encrypt the password when registering or updating a user 
        $autenticazione->password = $requestBody['password'];
        $autenticazione->crypt_password();
        break;
    case "forgot_password":
        // get the email to recovey the password
        $autenticazione->email = $requestBody['email'];
        try {
            $check_email = $autenticazione->check_email();
            if (isset($check_email['catchError'])) {
                // report a try catch error on database
                throw new reportException($check_email['catchError'], 500);
            } 
            if (!isset($check_email['email'])) {
                // check if the email is registred
                throw new reportException('Questo email non é registrato', 401);
            }
            // set the deadline to change the password
            $timezone = new DateTimeZone('Europe/Rome');
            $now = new DateTime('tomorrow', $timezone);
            $autenticazione->scadenza = $now->format('Y-m-d H:i:s');
            // draw a code to use as a key to recovery the password 
            $autenticazione->random_code();
            $codeRegisterd = $autenticazione->forgot_password();
            if(isset($codeRegisterd['catchError'])) {
                // report a try catch error on database
                throw new reportException($codeRegisterd['catchError'], 500);
            } 
            $emalSent = $autenticazione->send_email(); 
            if (!$emalSent) {
                // report a try catch error on database
                throw new reportException('Problemi nel recupero della password', 500);
            }
            $data['state'] = 'Success';
            $data['code'] = 200;
            $data['message'] = 'Ti abbiamo inviato una e-mail per recuperare la tua password';
            echo json_encode($data);
        } catch (reportException $e) {
            $e->reportError();
        }
        break;
    case "recover_password":
        try {
            if ($requestBody['password'] != $requestBody['confirm-password']) {
                throw new reportException('Le password non corrispondono', 400);
            }
            // set the values to the variables
            $autenticazione->email = $requestBody['email'];
            $autenticazione->codice = $requestBody['code'];
            $autenticazione->password = $requestBody['password'];
            // crypt the password to update
            $autenticazione->crypt_password();
            // check if the code match with the email 
            $id_utente = $autenticazione->check_code_email();
            if (isset($id_utente['catchError'])) {
                // report a try catch error on database
                throw new reportException($id_utente['catchError'], 500);
            }
            if (!isset($id_utente['id_utente'])) {
                // report that email and recovery code doesn't match 
                throw new reportException('Email e codice non validi', 400);
            }
            $autenticazione->id_utente = $id_utente['id_utente'];
            // set the date timezone to work with dates
            date_default_timezone_set('Europe/Rome');
            $autenticazione->scadenza = new DateTime($id_utente['scadenza']);
            $now = new DateTime('now');
            
            if ($now > $autenticazione->scadenza) {
                // check if the code still valid
                throw new reportException('Il collegamento è scaduto', 401);
            }
            $passwordUpdeted = $autenticazione->update_password();
            if (isset($passwordUpdeted['catchError'])) {
                // report a try catch error on database
                throw new reportException($id_utente['catchError'], 500);
            } 
            if (!$passwordUpdeted) {
                // check if the code still valid
                throw new reportException('Password non aggiornata', 500);
            }
            $data['state'] = 'Success';
            $data['code'] = 200;
            $data['message'] = 'La password é aggiornata';
            echo json_encode($data);
        } catch (reportException $e) {
            $e->reportError();
        }    
        break;

    case "get_session":
        try {
            if (!isset($_SESSION['id_utente'])) {
                // redirect the page to new authentication
                throw new reportException('La sessione non è attiva, fa l\'accesso un\'altra volta', 406);
            }
            $data['code'] = 200;
            $data['state'] = 'Success';
            $data['message'] = 'Session is defined';
            $data['codiceSessione'] = $_SESSION['codiceSessione'];
            $data['id_utente'] = $_SESSION['id_utente'];
            $data['nome'] = $_SESSION['nome'];
            $data['accessPages'] = $_SESSION['accessPages'];
            echo json_encode($data);
        } catch (reportException $e) {
            $e->reportError();
        }
        break;

    default:
        # code...
        break;
}
