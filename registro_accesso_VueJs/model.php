<?php

include_once('../common_VueJs/report_exception_class.php');

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

include_once('registro_accesso_class.php');
$registriAccesso = new registroAccessoClass();

$action = $requestBody['action'];
switch ($action) {
    case "autenticazione":
        // this method is called from autenticazione/model.php
        // set the date now
        $timezone = new DateTimeZone('Europe/Rome');
        $now = new DateTime('now', $timezone);
        $registriAccesso->datatime = $now->format('Y-m-d H:i:s');
        // get users access data
        $info = $_SERVER;
        $registriAccesso->ip_server = $info["REMOTE_ADDR"];
        $registriAccesso->remote_port = $info["REMOTE_PORT"];
        $registriAccesso->user_agent = $info["HTTP_USER_AGENT"];
        $registriAccesso->id_utente = $autenticazione->id_utente;
        $accessoRegistrato = $registriAccesso->regristrare_accesso();
        break;
    case "registri_accessi":
        try {
            // look for all login data 
            $registers = $registriAccesso->cerca_registri_accessi();
            // check if an error occurred on try catch
            if (isset($registers['catchError'])) {
                // report a try catch error on database
                throw new reportException($registers['catchError'], 500);
            } 
            echo json_encode($registers);
        } catch (reportException $e) {
            $e->reportError();
        }
        break;
    default:
        # code...
        break;
}
