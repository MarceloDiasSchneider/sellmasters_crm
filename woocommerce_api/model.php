<?php

include_once('../common_VueJs/report_exception_class.php');

include_once('woocommerce_class.php');
$woocommerceApi = new woocommerceApiClass();

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

// set default attributes to woocommerce API
$woocommerceApi->api_url = $requestBody['api_url'];
$woocommerceApi->consumer_key = $requestBody['consumer_key'];
$woocommerceApi->consumer_secret = $requestBody['consumer_secret']; 
$woocommerceApi->api_version = $requestBody['api_version'];

switch ($requestBody['action']) {
    case 'get_products':
        // set attribute 
        $woocommerceApi->resource = 'products';
        $woocommerceApi->method = 'GET';
        // retrieve the products
        $products = $woocommerceApi->woocommerceApi();
        $products = json_decode($products, true);
        // return a response
        $data['data'] = $products;
        $data['code'] = 200;
        $data['state'] = 'Ok';
        $data['message'] = 'Showing products';
        
        echo json_encode($data);

        break;
    case 'create_product':
        try {
            // set attributes
            $woocommerceApi->resource = 'products/categories';
            $woocommerceApi->method = 'GET';
            // retrieve existing product categories
            $categories = $woocommerceApi->woocommerceApi();
            $categories = json_decode($categories);
            if (isset($categories->message)) {
                throw new reportException($categories->message, 400);
            }
            // check if the category already exist, and get it id
            $category = array_search($requestBody['product']['category'], array_column($categories, 'name', 'id'));
            if (!$category) {
                // if category not exist, create it
                // set attributes
                $category = [
                    'name' => $requestBody['product']['category']
                ];
                $woocommerceApi->postFields = json_encode($category);
                $woocommerceApi->resource = 'products/categories';
                $woocommerceApi->method = 'POST';
                // post the new category
                $new_category = $woocommerceApi->woocommerceApi();
                $new_category = json_decode($new_category);
                if (isset($new_category->message)) {
                    throw new reportException($new_category->message, 400);
                }
                // get the created category's id 
                $category = $new_category->id;
            }
            // prepare the options to create the product
            $product_to_create = [];
            $product_to_create["sku"] = $requestBody['product']['sku'];
            $product_to_create["name"] = $requestBody['product']['name'];
            // isset($requestBody['product']['description']) ? $product_to_create["description"] = $requestBody['product']['description'] : '';
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
            // set attributes
            $woocommerceApi->postFields = json_encode($product_to_create);
            $woocommerceApi->resource = 'products';
            $woocommerceApi->method = 'POST';
            // post the new product
            $new_product = $woocommerceApi->woocommerceApi();
            $new_product = json_decode($new_product);
            // check if a message error exist
            if (isset($new_product->message)) {
                throw new reportException($new_product->message, 400);
            }
        } catch (reportException $e) {
            $e->reportError();
        }
        $data['data'] = $new_product;
        $data['code'] = 201;
        $data['state'] = 'Created';
        $data['message'] = 'New product created';
        
        echo json_encode($data);
        break;
    default:
        try {
            throw new reportException('Internal server error', 500);
        } catch (reportException $e) {
            $e->reportError();
        }
        break;
}
