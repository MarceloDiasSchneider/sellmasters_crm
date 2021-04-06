<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

include_once('autenticazione_class.php');
$autenticazione = new autenticazioneClass();

$action = $_REQUEST['action'];
// echo __LINE__. $action;
switch ($action) {
    case "autenticazione":

        /* crittografa la password */
        $autenticazione->email = $_REQUEST['email'];
        $autenticazione->password = $_REQUEST['password'];
        $autenticazione->crypt_password();

        /* Fa la verifica del e-mail e della password */
        $utente = $autenticazione->verifica_accesso();
        // check if an error occurred on try catch
        if (isset($utente['catchError'])) {
            $data['code'] = '500';
            $data['state'] = 'error';
            $data['message'] = $utente['catchError'];
        } else {
            /* Verifica se é trovato un utente */
            if (isset($utente['id_utente'])) {
                if ($utente['attivo'] == 1) {
                    $autenticazione->id_utente = $utente['id_utente'];
                    $autenticazione->nome = $utente['nome'];
                    $autenticazione->id_livello = $utente['id_livello'];

                    $data['code'] = '200';
                    $data['state'] = 'success';
                    $data['message'] = 'Email e password trovato';

                    // Se autenticazione é riuscita fa un registro di log 
                    if ($data['state'] == 'success') {
                        include_once('../livello/model.php');
                        // check if an error occurred on try catch
                        if (isset($permissione['catchError'])) {
                            $data['code'] = '500';
                            $data['state'] = 'error';
                            $data['message'] = $permissione['catchError'];
                        } else {
                            // controlla se ha livello dell'utente 
                            if (isset($permissione['permissione'])) {
                                $livello->permissione = $permissione['permissione'];

                                // Messaggio di riuscito a trovare un livello
                                $data['code'] = '200';
                                $data['state'] = 'success';
                                $data['message'] = 'Riuscito a trovare un livello';
                            } else {
                                // Messaggio di errore se l'utente non dispone del livello di autorizzazione
                                $data['code'] = '401';
                                $data['state'] = 'unauthorized';
                                $data['message'] = 'Utente senza livello di permissione';
                            }
                            // Controlla la risposta del livello 
                            if ($data['state'] == 'success') {
                                include_once('../registro_accesso/model.php');
                                if (isset($accessoRegistrato['catchError'])) {
                                    $data['code'] = '500';
                                    $data['state'] = 'error';
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
                                    $_SESSION["permissione"] = $livello->permissione;
                                    $_SESSION['data'] = $datatime;
                                    // $_SESSION['started'] = true; // deletar

                                    $data['state'] = 'success';
                                    $data['code'] = '201';
                                    $data['message'] = 'Acesso registrato';
                                    $data['url'] = '../utente';
                                }
                            }
                        }
                    }
                } else {
                    $data['code'] = '401';
                    $data['state'] = 'unauthorized';
                    $data['message'] = 'Utente disabilitato';

                    $data;
                }
            } else {
                // Messaggio di errore se autenticazione non é riuscita
                $data['code'] = '401';
                $data['state'] = 'unauthorized';
                $data['message'] = 'Email o password errate';

                $data;
            }
        }

        echo json_encode($data);

        break;

    case "insert_or_update_user":

        // encrypt the password when registering or updating a user 
        // this methos is called from utente/model.php
        $autenticazione->email = $_REQUEST['email'];
        $autenticazione->password = $_REQUEST['password'];
        $autenticazione->crypt_password();

        break;

    case "forgot_password":

        // get the email to recovey the password
        $autenticazione->email = $_REQUEST['email'];
        $check_email = $autenticazione->check_email();

        if (isset($check_email['catchError'])) {
            $data['state'] = 'error';
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
                    $data['state'] = 'error';
                    $data['code'] = '500';
                    $data['message'] = $result['catchError'];

                    echo json_encode($data);
                } else {
                    $result = $autenticazione->send_email();
                    if ($result == 1) {
                        $data['state'] = 'success';
                        $data['code'] = '200';
                        $data['message'] = 'Ti abbiamo inviato una e-mail per recuperare la tua password';

                        echo json_encode($data);
                    } else {
                        $data['state'] = 'Internal server error';
                        $data['code'] = '500';
                        $data['message'] = 'Problemi nel recupero della password';

                        echo json_encode($data);
                    }
                }
            } else {
                $data['state'] = 'unauthorized';
                $data['code'] = '401';
                $data['message'] = 'Questo email non é registrato';

                echo json_encode($data);
            }
        }

        break;
    case "recover_password":
        if ($_REQUEST['password'] == $_REQUEST['confirm-password']) {
            $autenticazione->email = $_REQUEST['email'];
            $autenticazione->codice = $_REQUEST['code'];
            // crypt the password to update
            $autenticazione->password = $_REQUEST['password'];
            $autenticazione->crypt_password();

            // check if the code match with the email 
            $id_utente = $autenticazione->check_code_email();
            if (isset($id_utente['catchError'])) {
                $data['state'] = 'error';
                $data['code'] = '500';
                $data['message'] = $id_utente['catchError'];

                echo json_encode($data);
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
                        $data['state'] = 'error';
                        $data['code'] = '500';
                        $data['message'] = $row['catchError'];
    
                        echo json_encode($data);
                    } else if ($row == 1) {
                        $data['state'] = 'success';
                        $data['code'] = '200';
                        $data['message'] = 'La password é aggiornata';

                        echo json_encode($data);
                    }
                } else {
                    $data['state'] = 'unauthorized';
                    $data['code'] = '401';
                    $data['message'] = 'Il collegamento è scaduto';

                    echo json_encode($data);
                }
            } else {
                $data['state'] = 'unauthorized';
                $data['code'] = '401';
                $data['message'] = 'email e codice non validi';

                echo json_encode($data);
            }
        } else {
            $data['state'] = 'bad request';
            $data['code'] = '400';
            $data['message'] = 'Le password non corrispondono';

            echo json_encode($data);
        }
        break;

    default:
        # code...
        //echo "not passing";
        break;
}
