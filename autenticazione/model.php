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
        $autenticazione->password = $autenticazione->crypt_password($_REQUEST['password']);

        /* Fa la verifica del e-mail e della password */
        $data = $autenticazione->verifica_accesso();
        
        /* Se autenticazione é riuscita fa un registro di log */
        if ($data['state'] == 'success') {
            include_once('../livello/model.php');
            
            /* Controlla la risposta del livello */
            if ($data['state'] == 'success'){
                include_once('../registro_accesso/model.php');
                
                /* preparare la data che inizia la sessione */
                $timezone = new DateTimeZone('Europe/Rome');
                $now = new DateTime('now', $timezone);
                $datatime = $now->format('Y-m-d H:i:s');
                
                // Inizia la sessione dell'utente
                $_SESSION["codiceSessione"] = $autenticazione->codice_sessione();
                $_SESSION["id_utente"] = $autenticazione->id_utente;
                $_SESSION["nome"] = $autenticazione->nome;
                $_SESSION["permissione"] = $livello->permissione;
                $_SESSION['data'] = $datatime;
                $_SESSION['started'] = true; // deletar
                
                $data['state'] = 'success';
                $data['code'] = '200';
                $data['url'] = '../utente';
            }
        }
        echo json_encode($data);
        
        break;

    case "insert_or_update_user":

        /* crittografa la password quando registra un utente */
        $passwordCrypted = $autenticazione->crypt_password($_REQUEST['password']);

        break;

    default:
        # code...
        //echo "not passing";
        break;
}
