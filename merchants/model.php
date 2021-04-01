<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

include_once('merchants_class.php');
$merchants = new merchantsClass();

$action = $_REQUEST['action'];
// echo __LINE__. $action;
switch ($action) {
    case "insert_or_update_merchants":
        // get the code session to verify if is the same 
        $form = $_REQUEST['codiceSessione'];
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
        $merchants->nome = $_REQUEST['nome'];
        $merchants->merchant_id = $_REQUEST['merchant_id'];
        // check if the input is not blank
        // optional 
        if (isset($_REQUEST['id'])) {
            $merchants->id = $_REQUEST['id'];
        }
        if ($_REQUEST['nome_sociale'] != '') {
            $merchants->nome_sociale = $_REQUEST['nome_sociale'];
        }
        if ($_REQUEST['mws'] != '') {
            $merchants->mws = $_REQUEST['mws'];
        }
        if ($_REQUEST['interval_between_check'] != '') {
            $merchants->interval_between_check = $_REQUEST['interval_between_check'];
        }
        if ($_REQUEST['nome_contatto'] != '') {
            $merchants->nome_contatto = $_REQUEST['nome_contatto'];
        }
        if ($_REQUEST['telefono'] != '') {
            $merchants->telefono = $_REQUEST['telefono'];
        }
        if ($_REQUEST['email'] != '') {
            $merchants->email = $_REQUEST['email'];
        }
        if ($_REQUEST['indirizzo'] != '') {
            $merchants->indirizzo = $_REQUEST['indirizzo'];
        }
        if ($_REQUEST['numero_civico'] != '') {
            $merchants->numero_civico = $_REQUEST['numero_civico'];
        }
        if ($_REQUEST['citta'] != '') {
            $merchants->citta = $_REQUEST['citta'];
        }
        if ($_REQUEST['cap'] != '') {
            $merchants->cap = $_REQUEST['cap'];
        }
        if ($_REQUEST['stato'] != '') {
            $merchants->stato = $_REQUEST['stato'];
        }
        if ($_REQUEST['provincia'] != '') {
            $merchants->provincia = $_REQUEST['provincia'];
        }
        if ($_REQUEST['attivo'] != '') {
            $merchants->attivo = $_REQUEST['attivo'];
        }

        // check if nome and merchant_id is already used
        $result = $merchants->check_nome_merchant_id();
        if (isset($result['catchError'])) { // reporting an error on try catch
            $data['code'] = '500';
            $data['state'] = 'Internal server error';
            $data['message'] = $result['catchError'];

            echo json_encode($data);
        } else if ($result != false) { // check if nome and merchant_id is already used
            $data['code'] = '409';
            $data['state'] = 'Conflict';
            $data['message'] = 'Nome e Commerciante già registrato';

            echo json_encode($data);
        } else if (!isset($merchants->id)) { // insert a new merchant if the id IS NOT setted
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
        } else if (isset($merchants->id)) { // update a new merchant if the id IS setted 
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
                
                $data['nome'] = $merchants->nome;
                $data['merchant_id'] = $merchants->merchant_id;
                $data['email'] = $merchants->email;
                $data['mws'] = $merchants->mws;
                
                echo json_encode($data);
            }else { // merchant not updated
                $data['id'] = $merchants->id;
                $data['rows'] = $merchant;
                $data['code'] = '500';
                $data['state'] = 'Internal server error';
                $data['message'] = 'The insert is not successfully';

                echo json_encode($data);
            }
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
            $data = array();
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
                $data[] = $info;
            }
            echo json_encode($data);
        }

        break;

    case "get_merchant_data":
        // set the id of the merchant to get the data
        $merchants->id = $_REQUEST['id'];
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
            $data['state'] = 'success';
            $data['code'] = '200';
            $data['message'] = 'Commerciante pronto per essere aggiornato';

            echo json_encode($data);
        }

        break;

    case "toggle_merchant";

        // get merchant id
        $merchants->id = $_REQUEST['id'];
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
                $data['state'] = 'success';
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
        //echo "not passing";
        break;
}
