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

include_once('merchants_class.php');
$merchants = new merchantsClass();

switch ($requestBody['action']) {
    case "insert_or_update_merchants":
        // get the code session to verify if is the same 
        $form = $requestBody['codiceSessione'];
        $session = $_SESSION['codiceSessione'];

        // if the code session does not match, unauthorized the insert or update
        if ($form != $session) {
            $data['state'] = 'Unauthorized';
            $data['code'] = '401';
            $data['message'] = 'Unauthorized : session code doesn\'t match';

            echo json_encode($data);
            exit;
        }

        // setting merchants variables 
        // obligatory
        $merchants->nome = $requestBody['nome'];
        $merchants->merchant_id = $requestBody['merchant_id'];
        // check if the input is not blank
        // optional 
        if (isset($requestBody['id'])) {
            $merchants->id = $requestBody['id'];
        }
        if ($requestBody['nome_sociale'] != '') {
            $merchants->nome_sociale = $requestBody['nome_sociale'];
        }
        if ($requestBody['mws'] != '') {
            $merchants->mws = $requestBody['mws'];
        }
        if ($requestBody['interval_between_check'] != '') {
            $merchants->interval_between_check = $requestBody['interval_between_check'];
        }
        if ($requestBody['nome_contatto'] != '') {
            $merchants->nome_contatto = $requestBody['nome_contatto'];
        }
        if ($requestBody['telefono'] != '') {
            $merchants->telefono = $requestBody['telefono'];
        }
        if ($requestBody['email'] != '') {
            $merchants->email = $requestBody['email'];
        }
        if ($requestBody['indirizzo'] != '') {
            $merchants->indirizzo = $requestBody['indirizzo'];
        }
        if ($requestBody['numero_civico'] != '') {
            $merchants->numero_civico = $requestBody['numero_civico'];
        }
        if ($requestBody['citta'] != '') {
            $merchants->citta = $requestBody['citta'];
        }
        if ($requestBody['cap'] != '') {
            $merchants->cap = $requestBody['cap'];
        }
        if ($requestBody['stato'] != '') {
            $merchants->stato = $requestBody['stato'];
        }
        if ($requestBody['provincia'] != '') {
            $merchants->provincia = $requestBody['provincia'];
        }
        if ($requestBody['attivo'] != '') {
            $merchants->attivo = $requestBody['attivo'];
        }

        // check if the ID is set to switch between insert or update the merchant
        switch ($merchants->id) {
            case null; // Insert new merchant
                // check if nome and merchant_id is already used
                $result = $merchants->check_nome_merchant_id();
                if (isset($result['catchError'])) { // reporting an error on try catch
                    $data['code'] = '500';
                    $data['state'] = 'Internal server error';
                    $data['message'] = $result['catchError'];

                    echo json_encode($data);
                } else if ($result != false) { // reporting that nome and merchant id is already used
                    $data['code'] = '409';
                    $data['state'] = 'Conflict';
                    $data['message'] = 'Nome e Commerciante già registrato';

                    echo json_encode($data);
                } else { // go to another check
                    $merchant = $merchants->insert_merchant();
                    if (isset($merchant['catchError'])) { // reporting an error on try catch
                        $data['code'] = '500';
                        $data['state'] = 'Internal server error';
                        $data['message'] = $merchant['catchError'];

                        echo json_encode($data);
                    } else if ($merchant) { // merchant inserted
                        $data['code'] = '201';
                        $data['state'] = 'Created';
                        $data['message'] = 'Nuovo commerciante é registrato';

                        echo json_encode($data);
                    } else { // merchant not inserted
                        $data['code'] = '500';
                        $data['state'] = 'Internal server error';
                        $data['message'] = 'The insert is not successfully';

                        echo json_encode($data);
                    }
                }

                break;

            case true; // update the merchant
                // check if the nome and merchant in not used from another merchant
                $result = $merchants->check_nome_merchant_id_others();
                if (isset($result['catchError'])) { // reporting an error on try catch
                    $data['code'] = '500';
                    $data['state'] = 'Internal server error';
                    $data['message'] = $result['catchError'];
                    
                    echo json_encode($data);
                } else if ($result != false) { // reporting that nome and merchant id is already used
                    $data['code'] = '409';
                    $data['state'] = 'Conflict';
                    $data['message'] = 'Nome e Commerciante già registrato';
                    
                    echo json_encode($data);
                } else {
                    $merchant = $merchants->update_merchant();
                    if (isset($merchant['catchError'])) { // reporting an error on try catch
                        $data['code'] = '500';
                        $data['state'] = 'Internal server error';
                        $data['message'] = $merchant['catchError'];
                        
                        echo json_encode($data);
                    } else if ($merchant) { // merchant inserted
                        $data['code'] = '200';
                        $data['state'] = 'Success';
                        $data['message'] = 'Commerciante é aggiornato';
                        $data['test'] = 'Aprovado, Muito bem Marcelo!!!!';
    
                        echo json_encode($data);
                    } else { // merchant not updated
                        $data['code'] = '406';
                        $data['state'] = 'Not Acceptable';
                        $data['message'] = 'Questo dati già ci sono salvate';
    
                        echo json_encode($data);
                    }
                }

                break;
        }

        break;

    case "get_merchants";
        $allMerchant = $merchants->get_merchants();
        if (isset($allMerchant['catchError'])) { // reporting an error on try catch
            $data['code'] = '500';
            $data['state'] = 'Internal server error';
            $data['message'] = $allMerchant['catchError'];

            echo json_encode($data);
        } else { // Organize the info to load in datatables
            $merchatsData = array();
            $info = array();
            foreach ($allMerchant as $key => $value) { // set the data as datatables need
                // variables to use on actions
                $fa_lock = $value['attivo'] ? 'fas fa-lock-open' : 'fas fa-lock';
                $title = $value['attivo'] ? 'disabilitare' : 'attivare';
                foreach ($value as $k => $v) {
                    if ($k == 'id') { // Use the id to set action as update or able and disable
                        $info['azione'] = "
                             <span class='update' id='mc_$v'><i class='fas fa-edit' title='modificare'></i></span> 
                             <span class='able_disable' id='mc_$v'><i class='$fa_lock' title='$title'></i></span>";
                    } else if ($k == 'attivo') { // Convert number attivo to Sì or No
                        if ($v == 1) {
                            $info[$k] = 'Sì';
                        } else {
                            $info[$k] = 'No';
                        }
                    } else {
                        $info[$k] = $v;
                    }
                }
                $merchatsData[] = $info;
            }

            $data['code'] = '200';
            $data['state'] = 'Success';
            $data['message'] = 'All merchants found';
            $data['merchatsData'] = $merchatsData;
            echo json_encode($data);
        }

        break;

    case "get_merchant_data":
        // set the id of the merchant to get the data
        $merchants->id = $requestBody['id'];
        $merchant = $merchants->get_merchant_data();
        if (isset($merchant['catchError'])) { // reporting an error on try catch
            $data['code'] = '500';
            $data['state'] = 'Internal server error';
            $data['message'] = $merchant['catchError'];

            echo json_encode($data);
        } else { // prepare the data to update a merchant
            // removing all empty values
            foreach ($merchant as $key => $value) {
                if ($value != '') {
                    $info[$key] = $value;
                }
            }
            $data['merchant'] = $info;
            $data['state'] = 'Success';
            $data['code'] = '200';
            $data['message'] = 'Commerciante pronto per essere aggiornato';

            echo json_encode($data);
        }

        break;

    case "toggle_merchant";

        // get merchant id
        $merchants->id = $requestBody['id'];
        // get the merchant attivo value
        $attivo = $merchants->get_merchant_attivo();
        if (isset($attivo['catchError'])) { // reporting an error on try catch
            $data['code'] = '500';
            $data['state'] = 'Internal server error';
            $data['message'] = $attivo['catchError'];

            echo json_encode($data);
        } else { // update the attivo of the merchant
            $merchants->attivo = $attivo;

            $result = $merchants->toggle_merchant_attivo();
            if (isset($result['catchError'])) { // reporting an error on try catch
                $data['code'] = '500';
                $data['state'] = 'Internal server error';
                $data['message'] = $result['catchError'];

                echo json_encode($data);
            } else if ($result) { // confirm the request
                $data['code'] = '200';
                $data['state'] = 'Success';
                if (!$merchants->attivo) {
                    $data['message'] = "Commerciante attivo";
                } else {
                    $data['message'] = "Commerciante disabilitato";
                }

                echo json_encode($data);
            } else { // request not procesed
                $data['code'] = '500';
                $data['state'] = 'Internal server error';
                $data['message'] = 'The request is not successfully';

                echo json_encode($data);
            }
        }
        break;

    default:
        # code...
        break;
}
