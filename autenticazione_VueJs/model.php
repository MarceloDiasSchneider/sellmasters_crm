<?php

if (isset($_SERVER['REQUEST_METHOD'])) {
    // get resquet body data  
    if (!isset($requestBody)) {
        $requestBody = json_decode(file_get_contents('php://input'), true);
    }
} else {
    $data['code'] = '406';
    $data['state'] = 'Not Acceptable';
    $data['message'] = 'Request method not defined';
    echo json_encode($data);

    exit;
}

include_once('autenticazione_class.php');
$autenticazione = new autenticazioneClass();

$action = $requestBody['action'];
switch ($action) {
    case "autenticazione":

        /* crittografa la password */
        $autenticazione->email = $requestBody['email'];
        $autenticazione->password = $requestBody['password'];
        $autenticazione->crypt_password();

        /* Fa la verifica del e-mail e della password */
        $utente = $autenticazione->verifica_accesso();
        // check if an error occurred on try catch
        if (isset($utente['catchError'])) {
            $data['code'] = '500';
            $data['state'] = 'Internal Server Error';
            $data['message'] = $utente['catchError'];
        } else {
            /* Verifica se é trovato un utente */
            if (isset($utente['id_utente'])) {
                if ($utente['attivo'] == 1) {
                    $autenticazione->id_utente = $utente['id_utente'];
                    $autenticazione->nome = $utente['nome'];
                    $autenticazione->id_profile = $utente['id_profile'];

                    $data['code'] = '200';
                    $data['state'] = 'Success';
                    $data['message'] = 'Email e password trovato';

                    // Se autenticazione é riuscita fa un registro di log 
                    if ($data['state'] == 'Success') {
                        include_once('../profile_VueJs/model.php');
                        // check if an error occurred on try catch
                        if (isset($permissione['catchError'])) {
                            $data['code'] = '500';
                            $data['state'] = 'Internal Server Error';
                            $data['message'] = $permissione['catchError'];
                        } else {
                            // controlla se ha profile dell'utente 
                            if (isset($permissione['id_profile'])) {
                                $profile->permissione = $permissione['id_profile'];
                                // Messaggio di riuscito a trovare un profile
                                $data['code'] = '200';
                                $data['state'] = 'Success';
                                $data['message'] = 'Riuscito a trovare un profile';
                            } else {
                                // Messaggio di errore se l'utente non dispone del profile di autorizzazione
                                $data['code'] = '401';
                                $data['state'] = 'Unauthorized';
                                $data['message'] = 'Utente senza profilo di permissione';
                            }
                            // Controlla la risposta del profile 
                            if ($data['state'] == 'Success') {
                                include_once('../registro_accesso_VueJs/model.php');
                                if (isset($accessoRegistrato['catchError'])) {
                                    $data['code'] = '500';
                                    $data['state'] = 'Internal Server Error';
                                    $data['message'] = $accessoRegistrato['catchError'];
                                } else {

                                    /* preparare la data che inizia la sessione */
                                    $timezone = new DateTimeZone('Europe/Rome');
                                    $now = new DateTime('now', $timezone);
                                    $datatime = $now->format('Y-m-d H:i:s');

                                    // random a code to set the session
                                    $autenticazione->random_code();

                                    // Inizia la sessione dell'utente
                                    $_SESSION["codiceSessione"] = $autenticazione->codice;
                                    $_SESSION["id_utente"] = $autenticazione->id_utente;
                                    $_SESSION["nome"] = $autenticazione->nome;
                                    $_SESSION["id_profile"] = $profile->permissione;
                                    $_SESSION['data'] = $datatime;

                                    $data['state'] = 'Success';
                                    $data['code'] = '201';
                                    $data['message'] = 'Acesso registrato';
                                    $data['url'] = '../utente';
                                }
                            }
                        }
                    }
                } else {
                    $data['code'] = '401';
                    $data['state'] = 'Unauthorized';
                    $data['message'] = 'Utente disabilitato';

                    $data;
                }
            } else {
                // Messaggio di errore se autenticazione non é riuscita
                $data['code'] = '401';
                $data['state'] = 'Unauthorized';
                $data['message'] = 'Email o password errate';

                $data;
            }
        }

        echo json_encode($data);

        break;

    case "insert_or_update_user":
        // encrypt the password when registering or updating a user 
        // this methos is called from utente/model.php
        $autenticazione->password = $requestBody['password'];
        $autenticazione->crypt_password();

        break;

    case "forgot_password":

        // get the email to recovey the password
        $autenticazione->email = $requestBody['email'];
        $check_email = $autenticazione->check_email();

        if (isset($check_email['catchError'])) {
            $data['state'] = 'Internal Server Error';
            $data['code'] = '500';
            $data['message'] = $check_email['catchError'];

            echo json_encode($data);
        } else {
            if (isset($check_email['email'])) {
                // set the deadline to change the password
                $timezone = new DateTimeZone('Europe/Rome');
                $now = new DateTime('tomorrow', $timezone);
                $autenticazione->scadenza = $now->format('Y-m-d H:i:s');

                // draw a code to use as a key to recovery the password 
                $autenticazione->random_code();

                // check if the email is registred
                $result = $autenticazione->forgot_password();
                if (isset($result['catchError'])) {
                    $data['state'] = 'Internal Server Error';
                    $data['code'] = '500';
                    $data['message'] = $result['catchError'];

                    echo json_encode($data);
                } else {
                    $result = $autenticazione->send_email();
                    if ($result == 1) {
                        $data['state'] = 'Success';
                        $data['code'] = '200';
                        $data['message'] = 'Ti abbiamo inviato una e-mail per recuperare la tua password';

                        echo json_encode($data);
                    } else {
                        $data['state'] = 'Internal Server Error';
                        $data['code'] = '500';
                        $data['message'] = 'Problemi nel recupero della password';

                        echo json_encode($data);
                    }
                }
            } else {
                $data['state'] = 'Unauthorized';
                $data['code'] = '401';
                $data['message'] = 'Questo email non é registrato';

                echo json_encode($data);
            }
        }

        break;
    case "recover_password":
        if ($requestBody['password'] == $requestBody['confirm-password']) {
            // set the values to the variables
            $autenticazione->email = $requestBody['email'];
            $autenticazione->codice = $requestBody['code'];
            $autenticazione->password = $requestBody['password'];
            // crypt the password to update
            $autenticazione->crypt_password();

            // check if the code match with the email 
            $id_utente = $autenticazione->check_code_email();
            if (isset($id_utente['catchError'])) {
                $data['state'] = 'Internal Server Error';
                $data['code'] = '500';
                $data['message'] = $id_utente['catchError'];
            } else if (isset($id_utente['id_utente'])) {
                // check if the code still valid 
                $autenticazione->id_utente = $id_utente['id_utente'];
                // set the date timezone to work with dates
                date_default_timezone_set('Europe/Rome');
                $autenticazione->scadenza = new DateTime($id_utente['scadenza']);
                $now = new DateTime('now');

                if ($now < $autenticazione->scadenza) {
                    $row = $autenticazione->update_password();
                    if (isset($row['catchError'])) {
                        $data['state'] = 'Internal Server Error';
                        $data['code'] = '500';
                        $data['message'] = $row['catchError'];
                    } else if ($row == 1) {
                        $data['state'] = 'Success';
                        $data['code'] = '200';
                        $data['message'] = 'La password é aggiornata';
                    }
                } else {
                    $data['state'] = 'Unauthorized';
                    $data['code'] = '401';
                    $data['message'] = 'Il collegamento è scaduto';
                }
            } else {
                $data['state'] = 'Unauthorized';
                $data['code'] = '401';
                $data['message'] = 'Email e codice non validi';
            }
        } else {
            $data['state'] = 'Bad Request';
            $data['code'] = '400';
            $data['message'] = 'Le password non corrispondono';
        }
        echo json_encode($data);

        break;

    case "get_session":
        // check if the session open
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (!isset($_SESSION['id_utente'])) {
            $data['code'] = '204';
            $data['state'] = 'No Content';
            $data['message'] = 'La sessione non è attiva, fa l\'accesso un\'altra volta';
            $data['url'] = '../autenticazione_VueJs';
        } else {
            // get pages to set the menu
            include_once('../pages_VueJs/model.php');
            
            $data['code'] = '200';
            $data['state'] = 'Success';
            $data['message'] = 'Session is defined';
            $data['codiceSessione'] = $_SESSION['codiceSessione'];
            $data['id_utente'] = $_SESSION['id_utente'];
            $data['nome'] = $_SESSION['nome'];
            $data['id_profile'] = $_SESSION['id_profile'];
            $data['data'] = $_SESSION['data'];
        }
        echo json_encode($data);

        break;

    default:
        # code...
        break;
}
