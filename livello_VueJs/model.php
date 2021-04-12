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

include_once('livello_class.php');
$livello = new livelloClass();

switch ($requestBody['action']) {
    case "get_livelli":
        // get livello to set option on the form | called from utente/model.php
        $livelli = $livello->get_livelli();
        if (isset($livelli['catchError'])) {
            // report a try catch error
            $data['code'] = '500';
            $data['state'] = 'Internal Server Error';
            $data['message'] = $livelli['catchError'];
        } else {
            // return the data
            $data['code'] = '200';
            $data['state'] = 'Success';
            $data['message'] = 'All options was found';
            $data['livelli'] = $livelli;
        }
        echo json_encode($data);

        break;

    case "get_all_users";
        // get livello to show description on the datatables on utente/model.php
        $livelli = $livello->get_livelli();
        break;

    case "autenticazione":
        // this class is called from autenticazione/model.php to verify if the user has permission
        $livello->id_livello = $autenticazione->id_livello;
        $permissione = $livello->get_utente_livello();

    default:
        # code...
        //echo "not passing";
        break;
}
