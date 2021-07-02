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

include_once('shopify_Class.php');
$shopifyApi = new shopifyApiClass();

switch ($requestBody['action']) {
    case 'import_products':
        $shopifyApi->url = $requestBody['api']['url'];
        $shopifyApi->resource = 'products.json';
        $shopifyApi->key = $requestBody['api']['key'];
        $shopifyApi->password = $requestBody['api']['password'];
        $shopifyApi->method = 'POST';

        $products = $requestBody['products'];

        foreach ($products as $key => $product) {
            $shopifyApi->postFields = '
            {
                "product": {
                    "title": "' . $product['title'] . '",
                    "product_type": "' . $product['category'] . '",
                    "variants": [
                        {
                            "price": ' . $product['price'] . ',
                            "sku": ' . $product['sku'] . ',
                            "inventory_quantity": ' . $product['inventory_quantity'] . '
                        }
                    ],
                    "images": [
                        {
                            "src": "' . $product['image_url'] . '"
                        }
                    ]
                }
            }';

            $create_product = $shopifyApi->resquest_shopify_api();
            $created_products[] = $create_product;
        }

        $data['created_products'] = $created_products;
        $data['code'] = 201;
        $data['state'] = 'Success';
        $data['message'] = 'teste';
        echo json_encode($data);

        break;

    default:
        $data['code'] = 501;
        $data['state'] = 'Not Implemented';
        $data['message'] = 'The server does not support the functionality required to fulfill the request.';

        echo json_encode($data);
        break;
}
