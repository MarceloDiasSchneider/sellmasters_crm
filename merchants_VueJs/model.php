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

include_once('merchants_class.php');
$merchants = new merchantsClass();

switch ($requestBody['action']) {
    case "insert_or_update_merchants":
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

        try { // check obligatory values
            if(!isset($requestBody['nome']) || !isset($requestBody['merchant_id'])){
                throw new reportException('nome o commerciante non può essere vuoto', 401);
            }
            // setting merchants values
            foreach ($requestBody as $key => $value) {
                if($key != 'action' && $key != 'codiceSessione' && $value != ''){
                    $merchants->$key = $value;
                }
            }
        } catch (reportException $e) {
            $e->reportError();
        }

        // check if the ID is setted to switch between insert or update the merchant
        switch ($merchants->id) {
            case null; // Insert new merchant
                try {
                    // check if nome and merchant_id is already used
                    $nameAndIdMerchant = $merchants->check_nome_merchant_id();
                    if (isset($nameAndIdMerchant['catchError'])) { 
                        // report a try catch error on database
                        throw new reportException($nameAndIdMerchant['catchError'], 500);
                    }
                    if (isset($nameAndIdMerchant['nome']) || isset($nameAndIdMerchant['merchant_id'])){
                        // reporting that nome and merchant id is already used
                        throw new reportException('Nome e Commerciante già registrato', 401);
                    }
                    $newMerchant = $merchants->insert_merchant();
                    if (isset($newMerchant['catchError'])) { // reporting an error on try catch
                        // report a try catch error on database
                        throw new reportException($newMerchant['catchError'], 500);
                    } 
                    if (!$newMerchant) {                         // reporting an erro to insert the merchant
                        throw new reportException('Problema! Commerciante non registrato', 400);
                    }
                    // merchant inserted
                    $data['code'] = 201;
                    $data['state'] = 'Created';
                    $data['message'] = 'Nuovo commerciante é registrato';
                    echo json_encode($data);
                } catch (reportException $e) {
                    $e->reportError();
                }
                break;
            case true; // update the merchant
                try {
                    // check if the nome and merchant in not used from another merchant
                    $nameAndIdMerchant = $merchants->check_nome_merchant_id_others();
                    if (isset($nameAndIdMerchant['catchError'])) {
                        // report a try catch error on database
                        throw new reportException($nameAndIdMerchant['catchError'], 500);
                    } 
                    if ($nameAndIdMerchant != false) { 
                        // reporting that nome and merchant id is already used
                        throw new reportException('Nome e Commerciante già registrato', 401);
                    } 
                    $merchant = $merchants->update_merchant();
                    if (isset($merchant['catchError'])) {
                        // report a try catch error on database
                        throw new reportException($nameAndIdMerchant['catchError'], 500);
                    } 
                    if (!$merchant) { 
                        // merchant already registered
                        throw new reportException('Questo commerciante già è registrato', 401);
                    }
                    // merchant inserted
                    $data['code'] = 200;
                    $data['state'] = 'Success';
                    $data['message'] = 'Commerciante é aggiornato';
                    echo json_encode($data);
                } catch (reportException $e) {
                    $e->reportError();
                }
                break;
        }
        break;
    case "get_merchants";
        try {
            $allMerchant = $merchants->get_merchants();
            if (isset($allMerchant['catchError'])) {
                // report a try catch error on database
                throw new reportException($allMerchant['catchError'], 500);
            } 
            // format the data to datatables
            $data = array();
            $info = array();
            foreach ($allMerchant as $key => $value) { // set the data as datatables need
                // variables to use on actions
                $fa_lock = $value['attivo'] ? 'fas fa-lock-open' : 'fas fa-lock';
                $title = $value['attivo'] ? 'disabilitare' : 'attivare';
                foreach ($value as $k => $v) {
                    if ($k == 'attivo') { // Convert number attivo to Sì or No
                        if ($v == 1) {
                            $info[$k] = 'Sì';
                        } else {
                            $info[$k] = 'No';
                        }
                    } else {
                        $info[$k] = $v;
                    }
                }
                $data[] = $info;
            }
            echo json_encode($data);        
        } catch (reportException $e) {
            $e->reportError();
        }
        break;
    case "get_merchant_data":
        // set the id of the merchant to get the data
        $merchants->id = $requestBody['id'];
        try {
            $merchant = $merchants->get_merchant_data();
            if (isset($merchant['catchError'])) {
                // report a try catch error on database
                throw new reportException($merchant['catchError'], 500);
            } 
            if (!$merchant) {
                // merchant not found
                throw new reportException('Commerciante non trovato', 400);
            }
            // remove all empty values
            foreach ($merchant as $key => $value) {
                if ($value != '') {
                    $info[$key] = $value;
                }
            }
            $data['merchant'] = $info;
            $data['state'] = 'Success';
            $data['code'] = 200;
            $data['message'] = 'Commerciante pronto per essere aggiornato';
            echo json_encode($data);
        } catch (reportException $e) {
            $e->reportError();
        }
        break;

    case "toggle_merchant";
        // set merchant id
        $merchants->id = $requestBody['id'];
        try {
            // get the merchant active
            $attivo = $merchants->get_merchant_attivo();
            if (isset($attivo['catchError'])) {
                // report a try catch error on database
                throw new reportException($attivo['catchError'], 500);
            }
            if (!isset($attivo['nome'])) {
                // problem to active or disable the merchant
                throw new reportException('Problema! commerciante non alterato', 401);
            }
            // set the active
            $merchants->attivo = $attivo['attivo'];
            $marchantToggled = $merchants->toggle_merchant_attivo();
            if (isset($marchantToggled['catchError'])) {
                // report a try catch error on database
                throw new reportException($merchant['catchError'], 500);
            }
            if (!$marchantToggled) {
                // problem to active or disable the merchant
                throw new reportException('Problema! commerciante non alterato', 401);
            }
            // confirm the request
            $data['code'] = 200;
            $data['state'] = 'Success';
            $data['attivo'] = $merchants->attivo;
            if (!$merchants->attivo) {
                $data['message'] = "Commerciante attivo";
            } else {
                $data['message'] = "Commerciante disabilitato";
            }
            echo json_encode($data);
        } catch (reportException $e) {
            $e->reportError();
        }
        break;
    case "get_merchants_active";
        // this method is called from ordes/model.php
        $merchants->attivo = 1;
        $allMerchant = $merchants->get_merchants_active();
        break;

    default:
        # code...
        break;
}
