<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

include_once('registro_accesso_class.php');
$registriAccesso = new registroAccessoClass();

$action = $_REQUEST['action'];
// echo __LINE__. $action;
switch ($action) {
    case "autenticazione":

        // preparare la data 
        $timezone = new DateTimeZone('Europe/Rome');
        $now = new DateTime('now', $timezone);
        $registriAccesso->datatime = $now->format('Y-m-d H:i:s');

        // ottenendo informazioni da utente 
        $info = $_SERVER;
        $registriAccesso->ip_server = $info["REMOTE_ADDR"];
        $registriAccesso->remote_port = $info["REMOTE_PORT"];
        $registriAccesso->user_agent = $info["HTTP_USER_AGENT"];

        $accessoRegistrato = $registriAccesso->regristrare_accesso($autenticazione->id_utente);

        break;

    case "registri_accessi":

        // look for all login data 
        $result = $registriAccesso->cerca_registri_accessi();
        // check if an error occurred on try catch
        if (isset($result['catchError'])) {
            $data['code'] = '500';
            $data['state'] = 'error';
            $data['message'] = $result['catchError'];

            echo json_encode($data);
        } else {
            $data['code'] = '200';
            $data['state'] = 'success';
            $data['message'] = 'All logs found';

            // set the data as datatables needs
            foreach ($result as $key => $value) {
                $data['logs'][] = $value;
            }
            echo json_encode($data);
        }


        break;


    default:
        # code...
        break;
}
