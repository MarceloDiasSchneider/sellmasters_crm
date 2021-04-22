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

include_once('orders_class.php');
$order = new ordersClass();

switch ($requestBody['action']) {
    case 'get_merchants': 
        // call a merchants method to get all merchants
        include_once('../merchants_VueJs/model.php');
        break;

    default:
        $data['code'] = '501';
        $data['state'] = 'Not Implemented';
        $data['message'] = 'The server does not support the functionality required to fulfill the request.';

        echo json_encode($data);
        break;
}
