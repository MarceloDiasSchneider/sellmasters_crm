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

// class to connect with the prestashop api
class prestashopApiClass
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
        $this->url = 'https://www.studioarch.com/api/';
        $this->key = '6FDIK6STXLIGXIGGC7ZX5CCG2I87JC83';
        $this->method = 'GET';
        $this->merchant_id = 'A1DKBEUHRWP6D7'; // id merchant on amazon
        $this->marketplace = 'Prestashop';
        $this->date = date("Y-m-d");
    }

    public function resquest_prestashop_api()
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
            CURLOPT_USERPWD => $this->key . ":",
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
            'output_format' => 'JSON',
            'filter[date_add]' => '>[' . $this->date . ']',
            'display' => 'full',
            'date' => 1
        ); 
        $this->resource = 'orders';
        $this->parameter = http_build_query($params);
        $orders = $this->resquest_prestashop_api();

        return json_decode($orders, true);
    }

    public function order_states()
    {
        // get all order states data
        $params = array(
            'output_format' => 'JSON',
            'display' => '[id,name]'
        );
        $this->resource = 'order_states';
        $this->parameter = http_build_query($params);
        $order_states = $this->resquest_prestashop_api();
        $order_states = json_decode($order_states, true);

        // use array_map to overwrite object key to void overwrite on merge in merge_and_organize_data()
        $order_states['order_states'] = array_map(function($state) {
            return array(
                'id' => $state['id'],
                'order_state_name' => $state['name']
            );
        }, $order_states['order_states']);

        return $order_states; 
    }

    public function products($orders)
    {
        // get the products id to make a specific request
        $products_id = '[';
        foreach ($orders['orders'] as $key => $order) {
            foreach ($order['associations']['order_rows'] as $key => $product) {
                $products_id = $products_id . $product['product_id'] . '|';
            }
        }
        $products_id = $products_id . ']';

        // get the products data according with the id products retrieved from orders
        $params = array(
            'output_format' => 'JSON',
            'filter[id]' => $products_id,
            'display' => '[id,id_category_default]'
        );
        $this->resource = 'products';
        $this->parameter = http_build_query($params);
        $products = $this->resquest_prestashop_api();

        return json_decode($products, true);
    }

    public function categories($products)
    {
        // get the categories id to make a specific request
        $categories_id = '[';
        foreach ($products['products'] as $key => $product) {
            $categories_id = $categories_id . $product['id_category_default'] . '|';
        }
        $categories_id = $categories_id . ']';

        // get the product categories data according with the id categories retrieved from products
        $params = array(
            'output_format' => 'JSON',
            'filter[id]' => $categories_id,
            'display' => '[id,name]'
        );
        $this->resource = 'categories';
        $this->parameter = http_build_query($params);
        $categories = $this->resquest_prestashop_api();
        $categories = json_decode($categories, true);

        // use array_map to overwrite object key to void overwrite on merge in merge_and_organize_data()
        $categories['categories'] = array_map(function($category) {
            return array(
                'id' => $category['id'],
                'category_name' => $category['name']
            );
        }, $categories['categories']);
        return $categories;
    }

    public function customers($orders)
    {
        // get the customers id to make a specific request
        $customers_id = '[';
        foreach ($orders['orders'] as $key => $order) {
            $customers_id = $customers_id . $order['id_customer'] . '|';
        }
        $customers_id = $customers_id . ']';

        // get the customers data according with the id customers retrieved from orders
        $params = array(
            'output_format' => 'JSON',
            'filter[id]' => $customers_id,
            'display' => '[id,lastname,firstname,email]'
        );
        $this->resource = 'customers';
        $this->parameter = http_build_query($params);
        $customers = $this->resquest_prestashop_api();

        return json_decode($customers, true);
    }
    
    public function addresses($orders)
    {
        // get the address id to make a specific request
        $addresses_id = '[';
        foreach ($orders['orders'] as $key => $order) {
            $addresses_id = $addresses_id . $order['id_address_delivery'] . '|';
        }
        $addresses_id = $addresses_id . ']';

        // get the addresses data according with the id addresses retrieved from orders
        $params = array(
            'output_format' => 'JSON',
            'filter[id]' => $addresses_id,
            'display' => '[id,id_country,id_state,address1,address2,other,city,postcode,phone,phone_mobile]'
        );
        $this->resource = 'addresses';
        $this->parameter = http_build_query($params);
        $addresses = $this->resquest_prestashop_api();

        return json_decode($addresses, true);
    }

    public function countries()
    {
        // get all countries data
        $params = array(
            'output_format' => 'JSON',
            'display' => '[id,iso_code]'
        );
        $this->resource = 'countries';
        $this->parameter = http_build_query($params);
        $countries = $this->resquest_prestashop_api();
        $countries = json_decode($countries, true);

        // use array_map to overwrite object key to void overwrite on merge in merge_and_organize_data()
        $countries['countries'] = array_map(function($country) {
            return array(
                'id' => $country['id'],
                'country_iso_code' => $country['iso_code']
            );
        }, $countries['countries']);

        return $countries;
    }

    public function states()
    {
        // get all states data
        $params = array(
            'output_format' => 'JSON',
            'display' => '[id,iso_code]'
        );
        $this->resource = 'states';
        $this->parameter = http_build_query($params);
        $states = $this->resquest_prestashop_api();
        $states = json_decode($states, true);

        // use array_map to overwrite object key to void overwrite on merge in merge_and_organize_data()
        $states['states'] = array_map(function($state) {
            return array(
                'id' => $state['id'],
                'state_iso_code' => $state['iso_code']
            );
        }, $states['states']);

        return $states;
    }

    public function currencies()
    {
        // get all currencies data
        $params = array(
            'output_format' => 'JSON',
            'display' => '[id,iso_code]'
        );
        $this->resource = 'currencies';
        $this->parameter = http_build_query($params);
        $currencies = $this->resquest_prestashop_api();
        $currencies = json_decode($currencies, true);

        // use array_map to overwrite object key to void overwrite on merge in merge_and_organize_data()
        $currencies['currencies'] = array_map(function($currency) {
            return array(
                'id' => $currency['id'],
                'currency_iso_code' => $currency['iso_code']
            );
        }, $currencies['currencies']);

        return $currencies;
    }

    public function carriers()
    {
        // get all carriers data
        $params = array(
            'output_format' => 'JSON',
            'display' => '[id,name]'
        );
        $this->resource = 'carriers';
        $this->parameter = http_build_query($params);
        $carriers = $this->resquest_prestashop_api();
        $carriers = json_decode($carriers, true);

        // use array_map to overwrite object key to void overwrite on merge in merge_and_organize_data()
        $carriers['carriers'] = array_map(function($carrier) {
            return array(
                'id' => $carrier['id'],
                'carrier_name' => $carrier['name']
            );
        }, $carriers['carriers']);

        return $carriers;
    }

    public function languages()
    {
        // get all languages data
        $params = array(
            'output_format' => 'JSON',
            'display' => '[id,iso_code]'
        );
        $this->resource = 'languages';
        $this->parameter = http_build_query($params);
        $languages = $this->resquest_prestashop_api();
        $languages = json_decode($languages, true);

        // use array_map to overwrite object key to void overwrite on merge in merge_and_organize_data()
        $languages['languages'] = array_map(function($language) {
            return array(
                'id' => $language['id'],
                'languages_iso_code' => $language['iso_code']
            );
        }, $languages['languages']);

        return $languages;
    }

    public function merge_and_organize_data($orders, $order_states, $products, $categories, $customers, $addresses, $countries, $states, $currencies, $languages, $carriers)
    {
        foreach ($orders['orders'] as $key => $order) {
            // create the id_order to not overwrite on merge
            $order['id_order'] = $order['id'];
            // set the data defined in __construc 
            $order['merchant_id'] = $this->merchant_id;
            $order['marketplace'] = $this->marketplace;
                        
            // find the index to marge the data
            $order_state_index = array_search($order['current_state'], array_column($order_states['order_states'], 'id'));
            $customer_index = array_search($order['id_customer'], array_column($customers['customers'], 'id'));
            $address_index = array_search($order['id_address_delivery'], array_column($addresses['addresses'], 'id'));
            $country_index = array_search($addresses['addresses'][$address_index]['id_country'], array_column($countries['countries'], 'id'));
            $state_index = array_search($addresses['addresses'][$address_index]['id_state'], array_column($states['states'], 'id'));
            $currency_index = array_search($order['id_currency'], array_column($currencies['currencies'], 'id'));
            $language_index = array_search($order['id_lang'], array_column($languages['languages'], 'id'));
            $carrier_index = array_search($order['id_carrier'], array_column($carriers['carriers'], 'id'));

            // create a row foreach products
            foreach ($order['associations']['order_rows'] as $key => $product) {
                // find the index to marge the data
                $product_index = array_search($product['product_id'], array_column($products['products'], 'id'));
                $categories_index = array_search($products['products'][$product_index]['id_category_default'], array_column($categories['categories'], 'id'));

                // create the order_item_id to not overwrite on merge
                $product['order_item_id'] = $product['id']; // $product['id'] is the order item id NOT product id 

                // finally merge the data
                $rows[] = array_merge(
                    $order,
                    $product,
                    $order_states['order_states'][$order_state_index],
                    $products['products'][$product_index],
                    $categories['categories'][$categories_index],
                    $customers['customers'][$customer_index],
                    $addresses['addresses'][$address_index],
                    $countries['countries'][$country_index],
                    $states['states'][$state_index],
                    $currencies['currencies'][$currency_index],
                    $languages['languages'][$language_index],
                    $carriers['carriers'][$carrier_index],
                );
            }

        }

        // use array_map to retrieve only relevant data and organize it
        $rows = array_map(function($data) {
            return array(
                'order_id' => $data['id_order'],
                'paese' => $data['country_iso_code'],
                'merchant_id' => $data['merchant_id'],
                'category' => $data['category_name'],
                'marketplace' => $data['marketplace'],
                'order_item_id' => $data['product_id'],
                'purchase_date' => $data['date_add'],
                'payments_date' => $data['invoice_date'],
                'buyer_email' => $data['email'],
                'buyer_name' => $data['lastname'] . " " . $data['firstname'],
                'buyer_phone_number' => $data['phone'],
                'sku' => $data['product_ean13'],
                'product_name' => $data['product_name'],
                'quantity_purchased' => $data['product_quantity'],
                'currency' => $data['currency_iso_code'],
                'item_price' => $data['product_price'],
                'item_tax' => $data['unit_price_tax_incl'],
                'shipping_price' => $data['total_shipping'],
                'shipping_tax' => $data['total_shipping_tax_incl'],
                'ship_address_1' => $data['address1'],
                'ship_address_2' => $data['address2'],
                'ship_address_3' => $data['other'],
                'ship_city' => $data['city'],
                'ship_state' => $data['state_iso_code'],
                'ship_postal_code' => $data['postcode'],
                'ship_country' => $data['country_iso_code'],
                'sales_channel' => $data['marketplace'],
                'market_status' => $data['order_state_name'],
                'payment_method' => $data['payment'],
                'shipping_amount' => $data['total_shipping'],
                'last_updated_date' => $data['date_upd'],
                'totale_ordine' => $data['total_paid'],
                'order_status' => $data['order_state_name'],
                'order_channel' => $data['marketplace'],
                'tracking_number' => $data['shipping_number'],
                'carrier' => $data['carrier_name'],
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
    $prestashopApi = new prestashopApiClass();
    $orders = $prestashopApi->orders();
    if (empty($orders)) {
        throw new reportException('No orders found');
    }
    $order_states = $prestashopApi->order_states();
    if (empty($order_states)) {
        throw new reportException('No order_states found');
    }
    $products = $prestashopApi->products($orders);
    if (empty($products)) {
        throw new reportException('No products found');
    }
    $categories = $prestashopApi->categories($products);
    if (empty($categories)) {
        throw new reportException('No categories found');
    }
    $customers = $prestashopApi->customers($orders);
    if (empty($customers)) {
        throw new reportException('No customers found');
    }
    $addresses = $prestashopApi->addresses($orders);
    if (empty($addresses)) {
        throw new reportException('No addresses found');
    }
    $countries = $prestashopApi->countries();
    if (empty($countries)) {
        throw new reportException('No countries found');
    }
    $states = $prestashopApi->states();
    if (empty($states)) {
        throw new reportException('No states found');
    }
    $currencies = $prestashopApi->currencies();
    if (empty($currencies)) {
        throw new reportException('No currencies found');
    }
    $languages = $prestashopApi->languages();
    if (empty($languages)) {
        throw new reportException('No languages found');
    }
    $carriers = $prestashopApi->carriers();
    if (empty($carriers)) {
        throw new reportException('No carriers found');
    }

    // execute the method to merge all data and create a row for each product 
    $data = $prestashopApi->merge_and_organize_data($orders, $order_states, $products, $categories, $customers, $addresses, $countries, $states, $currencies, $languages, $carriers);
    
    // execute the method export to cvs 
    # $prestashopApi->export_as_cvs($data);

    // return the orders data as product row in json format
    $data = json_encode($data);
    echo $data;
} catch (reportException $e) {
    $e->reportError();
}


