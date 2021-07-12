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
        // set curl attribute 
        $woocommerceApi->resource = 'products';
        $woocommerceApi->method = 'GET';
        $parameters = ['per_page' => 100];
        $woocommerceApi->postFields = json_encode($parameters);
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
        $product_error = [];
        $product_success = [];
        foreach ($requestBody['product'] as $product) {
            // set curl attributes
            $woocommerceApi->resource = 'products/categories';
            $woocommerceApi->method = 'GET';
            // retrieve existing product categories
            $categories = $woocommerceApi->woocommerceApi();
            $categories = json_decode($categories);
            // check if the category already exist, and get it id
            if (!isset($categories->message)) {
                $category = array_search($product['category'], array_column($categories, 'name', 'id'));
            }
            if (!$category) {
                // if category not exist, create it
                // set curl attributes
                $category_to_create = [
                    'name' => $product['category']
                ];
                $woocommerceApi->postFields = json_encode($category_to_create);
                $woocommerceApi->resource = 'products/categories';
                $woocommerceApi->method = 'POST';
                // post the new category
                $new_category = $woocommerceApi->woocommerceApi();
                $new_category = json_decode($new_category);
                if (!isset($new_category->message)) {
                    // get the created category's id 
                    $category = $new_category->id;
                }
            }
            // prepare the options to create the product
            $product_to_create = [];
            $product_to_create["sku"] = $product['sku'];
            $product_to_create["name"] = $product['name'];
            isset($product['description']) ? $product_to_create["description"] = $product['description'] : '';
            $product_to_create["categories"][] = ['id' => $category];
            $product_to_create["regular_price"] = '"'.$product['regular_price'].'"';
            $product_to_create["manage_stock"] = true;
            $product_to_create["stock_quantity"] = $product['stock_quantity'];
            $product_to_create["status"] = $product['status'];
            $product_to_create["images"] = [
                [
                    "src" => '"'.$product['url_image'].'"',
                    'name' => $product['name'],
                    'alt' => $product['name'],
                ]
            ];
            // set curl attributes
            $woocommerceApi->postFields = json_encode($product_to_create);
            $woocommerceApi->resource = 'products';
            $woocommerceApi->method = 'POST';
            // post the new product
            $new_product = $woocommerceApi->woocommerceApi();
            $new_product = json_decode($new_product);
            // check if a message error exist
            if (isset($new_product->message)) {
                $product['error_message'] = $new_product->message;
                $product_error[] = $product;
            } else {
                $product_success[] = $new_product;
            }
        }

        $data['data']['product_success'] = $product_success;
        $data['data']['product_error'] = $product_error;
        $data['code'] = 201;
        $data['state'] = 'Created';
        $data['message'] = 'New product created';
        
        echo json_encode($data);
        break;
    case 'update_stock_quantity':
        // prepare the sku to find the product's id
        $stock = $requestBody['stock'];
        $skuToUpdate = '';
        foreach ($stock as $key => $sku) {
            ($key !== 0) ? $skuToUpdate .= ',' : '' ;
            $skuToUpdate .= $sku['sku'];
        }
        $stock = array_column($stock, 'stock_quantity', 'sku');
        // set curl attribute 
        $woocommerceApi->resource = 'products';
        $woocommerceApi->method = 'GET';
        $woocommerceApi->postFields = json_encode([
            'per_page' => 100,
            'sku' => $skuToUpdate,
        ]);
        # $woocommerceApi->postFields = '{"per_page": 100, "sku": "' . $skuToUpdate . '"}';
        // retrieve the products that match with the skus
        $products = $woocommerceApi->woocommerceApi();
        $products = json_decode($products, true);
        if (!isset($products->message) || !$products == null) {
            $products = array_column($products, 'id', 'sku');
        }
        // set curl attribute 
        $woocommerceApi->resource = 'products/batch';
        $woocommerceApi->method = 'POST';
        $stock_success = [];
        $stock_error = [];
        foreach ($stock as $sku => $quantity) {
            if (isset($products[$sku])) {
                $batchToUpdate['update'][] = [
                    'id' => $products[$sku],
                    'stock_quantity' => $quantity
                ];
                $stock_success[] = ['sku' => $sku];
            } else {
                $stock_error[] = ['sku' => $sku, 'error' => 'sku not found'];
            }
        }
        $woocommerceApi->postFields = json_encode($batchToUpdate);
        
        // send a batch of products to update the stock quantity 
        $products_stock_updated = $woocommerceApi->woocommerceApi();
        if (isset($products->message) ) {
            $data['error'] = $products->message;
        } else {
            $data['data']['stock_success'] = $stock_success;
            $data['data']['stock_error'] = $stock_error;
        }
        
        // return a response
        $data['code'] = 200;
        $data['state'] = 'Ok';
        $data['message'] = 'Stock updated';
        
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
