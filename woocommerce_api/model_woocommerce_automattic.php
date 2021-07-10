<?php

include_once('../common_VueJs/report_exception_class.php');

require '../woocommerce_automattic/vendor/autoload.php';

use Automattic\WooCommerce\Client;

// start the session
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// check the request data
try {
    if (isset($_SERVER['REQUEST_METHOD'])) {
        // get resquet body data  
        $requestBody = json_decode(file_get_contents('php://input'), true);
    }
    if (!isset($requestBody)) {
        throw new reportException('Request method not defined', 406);
    }
    if (!isset($requestBody['codice_sessione'])) {
        throw new reportException('Resquest without session code', 406);
    }
    if ($requestBody['codice_sessione'] != $_SESSION['codiceSessione']) {
        throw new reportException('Session code dose\'t match', 406);
    }
} catch (reportException $e) {
    $e->reportError();
}

switch ($requestBody['action']) {
    case 'get_products':
        // instance class woocommerce api
        $woocommerceApi = new Client(
            $requestBody['api_url'], 
            $requestBody['consumer_key'], 
            $requestBody['consumer_secret'], 
            [
                'wp_api' => true,
                'version' => $requestBody['api_version'],
                // Force Basic Authentication as query string true and using under HTTPS
                'query_string_auth' => true 
            ]
        );
        // call method get to retrieve all products 
        $products['data'] = $woocommerceApi->get('products');
         
        $products['code'] = 200;
        $products['state'] = 'Ok';
        $products['message'] = 'Showing products';
        
        echo json_encode($products);

        break;
    case 'create_product':
        // instance class woocommerce api
        $woocommerceApi = new Client(
            $requestBody['api_url'], 
            $requestBody['consumer_key'], 
            $requestBody['consumer_secret'], 
            [
                'wp_api' => true,
                'version' => $requestBody['api_version'],
                // Force Basic Authentication as query string true and using under HTTPS
                'query_string_auth' => true 
            ]
        );
        // call method get to retrieve all categories
        $categories = $woocommerceApi->get('products/categories');
        // check if the category already exist, and get it id
        $category = array_search($requestBody['product']['category'], array_column($categories, 'name', 'id'));
        if (!$category) {
            // if category not exist, create it
            // prepare the options to create the actegory
            $category = [
                'name' => $requestBody['product']['category']
            ];
            // call method post to create the category
            $new_category = $woocommerceApi->post('products/categories', $category);
            // get the created category's id 
            $category = $new_category->id;
        }

        // prepare the options to create the product
        $product_to_create = [];
        $product_to_create["sku"] = $requestBody['product']['sku'];
        $product_to_create["name"] = $requestBody['product']['name'];
        isset($requestBody['product']['description']) ? $product_to_create["description"] = $requestBody['product']['description'] : '';
        $product_to_create["categories"][] = ['id' => $category];
        $product_to_create["regular_price"] = '"'.$requestBody['product']['regular_price'].'"';
        $product_to_create["manage_stock"] = true;
        $product_to_create["stock_quantity"] = $requestBody['product']['stock_quantity'];
        $product_to_create["status"] = $requestBody['product']['status'];
        $product_to_create["images"] = [
                [
                    "src" => '"'.$requestBody['product']['url_image'].'"',
                    'name' => $requestBody['product']['name'],
                    'alt' => $requestBody['product']['name'],
                ]
        ];


        // call method post to create the product
        $created_product['data'] = $woocommerceApi->post('products', $product_to_create);
        
        $created_product['code'] = 201;
        $created_product['state'] = 'Created';
        $created_product['message'] = 'New product created';
        
        echo json_encode($created_product);
        break;
    default:
        try {
            throw new reportException('Internal server error', 500);
        } catch (reportException $e) {
            $e->reportError();
        }
        break;
}
