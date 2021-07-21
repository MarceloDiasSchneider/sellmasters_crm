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
    public $date;

    function __construct()
    {
        // set default setting to make a resquest
        $this->url = 'https://francavilla-moda.myshopify.com/admin/api/2021-04/';
        $this->key = 'c983b3ce824a163685cc22d6bd2e683f';
        $this->password = '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';
        $this->merchant_id = 'not defined'; // id merchant on amazon
        $this->marketplace = 'Shopify';
        $this->date = date('Y-m-d',strtotime("-1 days"));
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
            'status' => 'any',
            'limit' => 250,
            // 'page' => 1,
            'created_at_min' => '2021-06-01',
            'created_at_max' => '2021-07-01',
            // 'created_at_min' => $this->date,
        );
        $this->resource = 'orders.json';
        $this->parameter = http_build_query($params);
        $orders = $this->resquest_shopify_api();

        return json_decode($orders, true);
    }

    public function products_data($orders)
    {
        // find the products id to get the product variante data 
        $product_ids = '';
        foreach ($orders['orders'] as $key => $order) {
            foreach ($order['line_items'] as $products) {
                if ($key != 0) {
                    $product_ids .= ',';
                }
                $product_ids .= $products['product_id'];
            }
        }
        // get orders according with the parameters
        $params = array(
            'limit' => 250,
            'fields' => 'id,variants',
            'ids' => $product_ids,
        );
        $this->resource = 'products.json';
        $this->parameter = http_build_query($params);
        $products_data = $this->resquest_shopify_api();
        
        return json_decode($products_data, true);
    }

    public function merge_and_organize_data($orders, $products_data)
    {
        $array_column_products = array_column($products_data['products'], 'id');
        // $products_id_index = array_column($products_data, product_id);
        foreach ($orders['orders'] as $key => $order) {
            // rename key to not overwrite
            $order['ordine_marketplace'] = $order['name'];
            // set the data defined in __construc 
            $order['merchant_id'] = $this->merchant_id;
            $order['marketplace'] = $this->marketplace;
            $order['products_rows'] = count($order['line_items']);
            $order['created_at_formated'] = date('d-m-Y', strtotime($order['created_at']));
            if(!isset($order['shipping_address']['country_code'])){
                $order['shipping_address']['country_code'] = 'not found';
            }
            if(isset($order['refunds'][0]['created_at'])) {
                $order['refund_created_at_formated'] = date('d-m-Y', strtotime($order['refunds'][0]['created_at']));
            } else {
                $order['refund_created_at_formated'] = null;
            }
            // create a row foreach products
            foreach ($order['line_items'] as $key => $product) {
                // find the sku or barcode of each product;
                $product_index = array_search($product['product_id'], $array_column_products );
                $variant_index = array_search($product['variant_id'], array_column($products_data['products'][$product_index]['variants'], 'id'));
                $product['sku_barcode'] = $products_data['products'][$product_index]['variants'][$variant_index]['barcode'];
                // if barcode is null get the sku
                if ($product['sku_barcode'] == null) {
                    $product['sku_barcode'] = $products_data['products'][$product_index]['variants'][$variant_index]['sku'];
                }
                // set the product_fulfillment_status to not overwrite on merge
                $product['product_fulfillment_status'] = $product['fulfillment_status'];
                // setting values required to exporte
                // sum all discount of this product
                $product['discount_allocations_summed'] = 0;
                foreach ($product['discount_allocations'] as $discount) {
                    $product['discount_allocations_summed'] += $discount['amount_set']['shop_money']['amount'];
                }
                if ($order['financial_status'] == 'refunded' || $order['cancel_reason'] != null) {
                    $product['total'] = 0;
                } else {
                    $product['total'] = $product['price_set']['shop_money']['amount']
                        - $product['discount_allocations_summed']
                        + ($order['total_shipping_price_set']['shop_money']['amount'] / $order['products_rows']);
                }
                $product['total_formated'] = number_format($product['total'], 2, ',', '');
                switch ($order['subtotal_price_set']['shop_money']['currency_code']) {
                    case 'EUR':
                            $product['total_converted'] = $product['total'];
                            $product['total_converted_fomated'] = number_format($product['total_converted'], 2, ',', '');
                        break;
                    case 'GBP':
                            $product['total_converted'] = $product['total'] / 0.87 ;
                            $product['total_converted_fomated'] = number_format($product['total_converted'], 2, ',', '');
                        break;
                    default:
                        $product['total_converted'] = 0;
                        break;
                }
                $product['fee_vendor'] = $product['total_converted'] * 0.1;
                $product['fee_vendor_formated'] = number_format($product['fee_vendor'], 2, ',', '');
                unset($order['line_items']);
                // finally merge the data
                $products_rows[] = array_merge(
                    $order,
                    $product
                );
            }
        }
        // $this->debug($orders);
        // use array_map to retrieve only relevant data and organize it
        $products_rows = array_map(function($data) {
            return array(
                // // model to match with paolo xlsx 
                // 'note' => '',
                // 'feedback' => '',
                // 'paese' => $data['shipping_address']['country_code'],
                // 'data' => $data['created_at_formated'],
                // 'ordine_marketplace' => $data['ordine_marketplace'],
                // 'nome_cliente' => $data['customer']['first_name'] . ' ' . $data['customer']['last_name'],
                // 'total' => $data['total_formated'],
                // 'sku' => $data['sku_barcode'],
                // 'brand' => $data['vendor'],
                // 'currency' => $data['subtotal_price_set']['shop_money']['currency_code'],
                // 'merketplace' => $this->marketplace,
                // 'total_converted' => $data['total_converted_fomated'],
                // 'status_consegna' => $data['product_fulfillment_status'],
                // 'status_finanziario' => $data['financial_status'],
                // 'price bd' => '',
                // '%' => '',
                // 'fee_amazon' => '',
                // 'fee_vendor' => $data['fee_vendor_formated'] ,
                // 'spedizione_tatiffa_tnt_fedex' => '',
                // 'spedizione_tariffa_sellmasters' => '',
                // 'differenza' => '',
                // 'saldo' => '',
                // 'quantita' => $data['quantity'],
                // 'payment ' => $data['gateway'],
                // 'importo_corrispettivo' => '',
                // 'importo_nac' => '',
                // 'data_rimborso' => $data['refund_created_at_formated'],

                // model to match with arnaldo's google sheet
                'paese' => $data['shipping_address']['country_code'],
                'data' => $data['created_at'],
                'updated_at' => $data['updated_at'],
                'purchase_order_number' => $data['ordine_marketplace'],
                'buyer_name' => $data['customer']['first_name'] . ' ' . $data['customer']['last_name'],
                'item_tax' => $data['price_set']['shop_money']['amount'],
                'item_promotion_discount' => $data['discount_allocations_summed'],
                'shipping_tax' => $data['total_shipping_price_set']['shop_money']['amount'],
                'sku' => $data['sku_barcode'],
                'note' => $data['vendor'],
                'currency' => $data['subtotal_price_set']['shop_money']['currency_code'],
                'marketplace' => $this->marketplace,
                'item_status' => $data['product_fulfillment_status'],
                'quantity_purchased' => $data['quantity'],
                'order_status' => $data['fulfillment_status'],
                'payment_method' => $data['gateway'],
            );
        }, $products_rows);

        return $products_rows;
    }

    public function export_as_cvs($products)
    {
        // create a header to csv
        foreach ($products[0] as $key => $value) {
            $header['header'][] = $key;
        }
        // merge the header to the orders data
        $products = array_merge($header, $products);

        $fp = fopen('francavilla.csv', 'w');
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
    $products_data = $shopifyApi->products_data($orders);
    if (empty($products_data)) {
        throw new reportException('No products data found');
    }
    // execute the method to merge all data and create a row for each product 
    $products_rows = $shopifyApi->merge_and_organize_data($orders, $products_data);
    
    // execute the method export to cvs 
    $shopifyApi->export_as_cvs($products_rows);
    
    // return the orders data as product row in json format
    $data['data'] = $products_rows;
    echo json_encode($data);
} catch (reportException $e) {
    $e->reportError();
}


