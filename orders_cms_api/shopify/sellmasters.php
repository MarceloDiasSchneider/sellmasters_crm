<?php

// class that extend exception to report error
class reportException extends Exception
{
    public function reportError()
    {
        $data['message'] = $this->getMessage();

        echo json_encode($data);
        exit;
    }
}

// class to connect with the shopify api
class shopifyApiClass
{
    public $merchant_id;
    public $marketplace;
    public $url;
    public $resource;
    public $key;
    public $password;
    public $method;
    public $postFields;
    public $parameter;

    function __construct()
    {
        // set default setting to make a resquest
        $this->url = 'https://sellmasters-shop.myshopify.com/admin/api/2021-04/';
        $this->key = '4f27cde4b47b99f71ba3368d5fac1f11';
        $this->password = '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';
        $this->method = 'GET';
        $this->merchant_id = 'not defined'; // id merchant on amazon
        $this->marketplace = 'Shopify';
    }

    public function resquest_shopify_api()
    {
        // set the curl to make dynamic requests
        $curl = curl_init();
        $options = array(
            CURLOPT_URL => $this->url . $this->resource . '?' . $this->parameter,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $this->method,
            CURLOPT_USERPWD => $this->key . ":" . $this->password,
            CURLOPT_HTTPHEADER => array(),
        );
        if ($this->method == 'POST' || $this->method == 'PUT') {
            $options[CURLOPT_POSTFIELDS] = $this->postFields;
        }

        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }

    public function orders()
    {
        // get orders according with the parameters
        $params = array(
            'created_at_min' => '2021-01-01',
            'created_at_max' => '2021-06-30',
        );
        $this->resource = 'orders.json';
        $this->parameter = http_build_query($params);
        $orders = $this->resquest_shopify_api();

        return json_decode($orders, true);
    }

    public function merge_and_organize_data($orders)
    {
        $this->debug($orders);
        foreach ($orders['orders'] as $key => $order) {
            // create the order_id to not overwrite on merge
            $order['order_id'] = $order['id'];
            
            // set the data defined in __construc 
            $order['merchant_id'] = $this->merchant_id;
            $order['marketplace'] = $this->marketplace;
            
            // create a row foreach products
            foreach ($order['line_items'] as $key => $product) {
                // create the id_product to not overwrite on merge
                $product['id_product'] = $product['id'];
                unset($order['line_items']);
                // finally merge the data
                $rows[] = array_merge(
                    $order,
                    $product
                );
            }
        }

        // use array_map to retrieve only relevant data and organize it
        $rows = array_map(function($data) {
            return array(
                'order_id' => $data['order_number'],
                // 'paese' => $data['billing_address ']['country_code'],
                'merchant_id' => $data['merchant_id'],
                // 'marketplace' => $data['marketplace'],
                // 'order_item_id' => $data['line_items']['id'],
                'purchase_date' => $data['created_at'],
                // 'buyer_email' => $data['email'],
                // 'buyer_name' => $data['customer']['last_name'] . " " . $data['customer']['first_name'],
                // 'buyer_phone_number' => $data['customer']['phone'],
                // 'sku' => $data['sku'],
                // 'product_name' => $data['title'],
                // 'quantity_purchased' => $data['quantity'],
                // 'currency' => $data['currency'],
                // 'item_price' => $data['price'],
                // 'item_tax' => $data['tax_lines']['price'],
                // 'shipping_price' => $data['shipping_lines']['price'],
                // 'recipient_name' => $data['shipping_address']['last_name'] . $data['shipping_address']['first_name'],
                // 'ship_address_1' => $data['billing_address']['address1'],
                // 'ship_address_2' => $data['billing_address']['address2'],
                // 'ship_city' => $data['billing_address']['city'],
                // 'ship_state' => $data['billing_address']['province_code'],
                // 'ship_postal_code' => $data['billing_address']['zip'],
                // 'ship_country' => $data['billing_address']['country_code'],
                // 'item_promotion_discount' => $data['total_discount'],
                // 'ship_promotion_discount' => $data['shipping_lines ']['discounted_price'],
                // 'sales_channel' => $data['marketplace'],
                // 'purchase_order_number' => $data['order_id'],
                // 'last_updated_date' => $data['fulfillments']['updated_at'],
                // 'totale_ordine' => $data['total_price'],
                // 'order_channel' => $data['marketplace'],
                // 'tracking_number' => $data['fulfillments']['tracking_number'],
                // 'carrier' => $data['fulfillments']['tracking_company'],
            );
        }, $rows);

        return $rows;
    }

    public function export_as_cvs($products)
    {
        // create a header to csv
        foreach ($products[0] as $key => $value) {
            $header['header'][] = $key;
        }
        // merge the header to the orders data
        $products = array_merge($header, $products);

        $fp = fopen('orders.csv', 'w');
        foreach ($products as $fields) {
            fputcsv($fp, $fields);
        }
        fclose($fp);
    }

    public function debug($object)
    {
        // a function to show the object data .. 
        echo json_encode($object);
        exit;
    }
}

try {
    // execute methods to retrieve the data
    $shopifyApi = new shopifyApiClass();
    $orders = $shopifyApi->orders();
    if (empty($orders)) {
        throw new reportException('No orders found');
    }

    // execute the method to merge all data and create a row for each product 
    $data = $shopifyApi->merge_and_organize_data($orders);
    
    // execute the method export to cvs 
    # $shopifyApi->export_as_cvs($data);

    // return the orders data as product row in json format
    $data = json_encode($data);
    echo $data;
} catch (reportException $e) {
    $e->reportError();
}


