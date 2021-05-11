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

include_once('orders_class.php');
$order = new ordersClass();

switch ($requestBody['action']) {
    case 'get_merchants_active': 
        // call a merchants method to get all merchants
        include_once('../merchants_VueJs/model.php');
        if (isset($allMerchant['catchError'])) { 
            // reporting an error on try catch
            $data['code'] = '500';
            $data['state'] = 'Internal server error';
            $data['message'] = $allMerchant['catchError'];
        } else {
            $data['merchatsData'] = $allMerchant;
            $data['code'] = '200';
            $data['state'] = 'Success';
            $data['message'] = 'All merchants active';
        }
        echo json_encode($data);
        break;

    default:
        $data['code'] = '501';
        $data['state'] = 'Not Implemented';
        $data['message'] = 'The server does not support the functionality required to fulfill the request.';

        echo json_encode($data);
        break;
}
