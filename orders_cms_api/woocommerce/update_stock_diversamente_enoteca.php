<style>
    table {
    font-family: arial, sans-serif;
    border-collapse: collapse;
    width: 100%;
    }

    td, th {
    border: 1px solid #dddddd;
    text-align: left;
    padding: 8px;
    }

    tr:nth-child(even) {
    background-color: #dddddd;
    }
</style>
<?php

class readFtpFile 
{
    protected $ftp;
    protected $username;
    protected $password;

    function __construct()
    {
        // set the login credentials 
        $this->ftp = 'www.slmrs.com';
        $this->username = 'ftp_grandea';
        $this->password = 'tkCEYBt$aRyh';
    }

    public function read_ftp_file()
    {
        // connect to ftp
        $conn_id = ftp_connect($this->ftp);
        ftp_login($conn_id, $this->username, $this->password);
        ftp_pasv($conn_id, true);
        // list all folders and files
        $contents = ftp_nlist($conn_id, ".");
        # echo '<pre>';
        # echo print_r($contents);
        # echo '</pre>';
        // handle a temp file
        $handle = fopen('php://temp', 'r+');
        $success = ftp_fget($conn_id, $handle, '/ArticoliGrandeA.csv', FTP_BINARY, 0);
        if (!$success) {
            echo "<p>Failed to recover file</p>";
            exit;
        }
        echo "<p>Recovered file " . $success . "</p>";
        // gather file statistics
        $fstats = fstat($handle);
        fseek($handle, 0);
        $buff = ftp_mdtm($conn_id, '/ArticoliGrandeA.csv');
        // create a copy
        if ($buff != -1) {
            // somefile.txt was last modified on: March 26 2003 14:16:41.
            echo "<p>$handle was last modified on : " . date("F d Y H:i:s.", $buff) . "</p>" ;
        } else {
            echo "Couldn't get mdtime";
        }
        # echo '<pre>';
        # print_r($fstats);
        # print_r($handle);
        # echo "</pre>";
        while (($data = fgetcsv($handle, 4096, ";")) !== false) {
            $products_file_ftp[] = [
                'sku' => $data[0], // Codice
                'name' => $data[1], // Descrizione1
                'barcode' => $data[2], // BarCode
                'stock_quantity' => $data[9], // EsistenzaNetta
                'regular_price' => $data[12], // Listino 2
            ];
        }
        // unset the header's data
        unset($products_file_ftp[0]);
        
        return $products_file_ftp;
    }

    public function donwload_ftp_file()
    {
        // connect to ftp
        $conn_id = ftp_connect($this->ftp);
        ftp_login($conn_id, $this->username, $this->password);
        ftp_pasv($conn_id, true);
        echo 'donwload';        
        $success = ftp_get($conn_id, 'ArticoliGrandeA.csv', '/ArticoliGrandeA.csv', FTP_BINARY, 0);
    }
}

class woocommerceApiClass
{
    public $api_url;
    public $consumer_key;
    public $consumer_secret;
    public $api_version;
    public $resource;
    public $method;
    public $postFields;

    public function __construct()
    {
        $this->api_url = 'https://diversamentenoteca.com/wp-json/';
        $this->consumer_key = 'ck_c47874c9eb1afcc09d44907db9778d95e609907e';
        $this->consumer_secret = 'cs_3a5abbc9081d996fe321c0189be07ba33aa9ae63';
        $this->api_version = 'wc/v3/';
    }
    
    public function woocommerceApiConection()
    {
        $curl = curl_init();
        $options = array(
            CURLOPT_URL => $this->api_url . $this->api_version . $this->resource,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $this->method,
            CURLOPT_USERPWD => $this->consumer_key . ":" . $this->consumer_secret,
            CURLOPT_POSTFIELDS => $this->postFields,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
            ),
        );
        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }

    public function retrieveAllProducts()
    {
        // set the attributes of woocommerce class
        $this->resource = 'products/';
        $this->method = 'GET';
        $page = 1;
        while ($page) {
            $this->postFields = json_encode([
                'per_page' => 100,
                'page' => $page,
            ]);
            # echo $this->postFields . '<br>';
            $products = $this->woocommerceApiConection();
            $products = json_decode($products, true);
            $page++;
            if (count($products)) {
                foreach ($products as $key => $product) {
                    $all_products[] = $product;
                }
            } else {
                $page = null;
            }
        }
        
        $all_products = array_column($all_products, 'id', 'sku');
        return $all_products;
    }

    public function update_products_stock($products_woocommerce, $products_file_ftp)
    {
        // use array_column to organze the data as array [sku => stock_quantity]
        // and remove the duplicated sku
        $stock_to_update = array_column($products_file_ftp, 'stock_quantity', 'sku');
        $stocks_to_update = [];
        foreach ($products_woocommerce as $sku => $id) {
            $stock_quantity = 0;
            // if the sku is found in stock_to_update set the quantity
            if (isset($stock_to_update[$sku])) {
                if ($stock_to_update[$sku] > 0) {
                    // $stock_quantity = 0;
                    $stock_quantity = $stock_to_update[$sku];
                }
            }
            $stocks_to_update[] = [
                'id' => $id,
                'sku' => $sku,
                'stock_quantity' => $stock_quantity
            ];
        }
        // create a batch of 100 rows foreach request
        // woocommerce don't accept request over 100 rows
        $batch_of_products = array_chunk($stocks_to_update, 100);
        // set the attributes of woocommerce class
        $this->resource = 'products/batch';
        $this->method = 'POST';
        foreach ($batch_of_products as $key => $product_batch) {
            $update = [];
            foreach ($product_batch as $k => $product) {
                $update[] = [
                    'id' => $product['id'],
                    'stock_quantity' => $product['stock_quantity'],
                    'manage_stock' => true
                ];
            }
            // prepare the postFiels
            $this->postFields = json_encode([
                'update' => $update
            ]);
            echo '<hr>batch ' . $key;
            # echo $this->postFields . '<br>';
            // execut the update
            $products_updated = $this->woocommerceApiConection();
            $products_updated = json_decode($products_updated, true);
            foreach ($products_updated['update'] as $key => $produtc) {
                $all_products_updated[] = [
                    'id' => $produtc['id'],
                    'name' => $produtc['name'],
                    'sku' => $produtc['sku'],
                    'stock_quantity' => $produtc['stock_quantity'],
                    'manage_stock' => $produtc['manage_stock'],
                ];
            }
        }

        return $all_products_updated;
    }

    public function create_new_products($products_woocommerce, $products_file_ftp)
    {
        // use array_column to organze the data as array [sku => stock_quantity]
        // and remove the duplicated sku
        $products_ftp = array_column($products_file_ftp, 'sku');
        $product_to_create = [];
        foreach ($products_ftp as $key => $sku) {
            // get only products which is not in woocommerce
            if ( !isset( $products_woocommerce[$sku] ) && !in_array(
                        $products_file_ftp[$key + 1]['sku'], array_column( $product_to_create, 'sku' ) 
                    ) 
                ) 
            {
                // add + 1 to key because $products_file_ftp[0] which is the header was unsettled
                $product_to_create[] = $products_file_ftp[$key + 1];
            } 
        }
        // create a batch of 100 rows foreach request
        // woocommerce don't accept request over 100 rows
        $batch_of_products_to_create = array_chunk($product_to_create, 100);
        // set attributes to conect to woocommerce
        $this->resource = 'products/batch';
        $this->method = 'POST';
        foreach ($batch_of_products_to_create as $key => $product_batch) {
            $create = [];
            foreach ($product_batch as $k => $product) {
                // format and increase a 22% of tax to the product price
                $price_plus_tax = str_replace(',', '.', $product["regular_price"]) / 0.78;
                $regular_price = number_format($price_plus_tax, 2, '.', '');
                // if stock is under zero, set as zero
                if($product["stock_quantity"] < 0) {
                    $product["stock_quantity"] = 0;
                }
                // remove "///" from the product name
                $product['name'] = str_replace('///', '', $product['name']);
                $create[] = [
                    "sku"=> $product["sku"],
                    "name"=> $product["name"],
                    "type"=> "simple",
                    "status"=> "draft",
                    "meta_data"=> [
                        [
                            "key"=> "_ebay_ean",
                            "value"=> $product["barcode"]
                        ]
                    ],
                    "stock_quantity"=> $product["stock_quantity"],
                    "manage_stock"=> true,
                    "regular_price"=> $regular_price
                ];
            }
            // prepare the postFiels
            $this->postFields = json_encode([
                'create' => $create
            ]);
            // execut the update
            $products_created = $this->woocommerceApiConection();
            $products_created = json_decode($products_created, true);
            foreach ($products_created['create'] as $key => $produtc) {
                $all_products_created[] = [
                    'id' => $produtc['id'],
                    'name' => $produtc['name'],
                    'sku' => $produtc['sku'],
                    'stock_quantity' => $produtc['stock_quantity'],
                    'manage_stock' => $produtc['manage_stock'],
                ];
            }
        }

        return $all_products_created;
    }

    public function export_as_cvs($products)
    {
        // create a header to csv
        foreach ($products[0] as $key => $value) {
            $header['header'][] = $key;
        }
        // merge the header to the orders data
        $products = array_merge($header, $products);

        $fp = fopen('prodotto_da_creare.csv', 'w');
        foreach ($products as $fields) {
            fputcsv($fp, $fields);
        }
        fclose($fp);
    }

    public function debug($data)
    {
        echo json_encode($data);
        exit;
    }
}
echo 'starting at ' . date('d-m-Y h:i:sa') . '<br>';
// instances of classes
$woocommerceApi = new woocommerceApiClass();
$file = new readFtpFile();
// reade the ftp file
$products_file_ftp = $file->read_ftp_file();
// echo 'Found ' . count($stock_to_update) . ' products to update';
// echo '<pre>';
// print_r($stock_to_update);
// echo '</pre>';
// retrieve all products from woocommerce
$products_woocommerce = $woocommerceApi->retrieveAllProducts();
// echo 'All ' . count($products_woocommerce) . ' products retrieved';
// echo '<pre>';
// print_r($products_woocommerce);
// echo '</pre>';
#### $products_updated = $woocommerceApi->update_products_stock($products_woocommerce, $products_file_ftp);
// echo count($products_updated) . ' products updated';
// echo '<pre>';
// print_r($products_updated);
// echo '</pre>';
$create_new_product = $woocommerceApi->create_new_products($products_woocommerce, $products_file_ftp);
// showing relevants data from products created
echo '<hr><p>data from the created products </p>';
echo '<table>';
    echo '<tr>';
        echo '<th>index</th>';
        echo '<th>id</th>';
        echo '<th>name</th>';
        echo '<th>sku</th>';
        echo '<th>stock_quantity</th>';
        echo '<th>manage_stock</th>';
    echo '</tr>';
foreach ($create_new_product as $key => $value) {
    echo '<tr>';
        echo '<th>'. $key . '</th>';
        echo '<th>'. $value['id'] . '</th>';
        echo '<th>'. $value['name'] . '</th>';
        echo '<th>'. $value['sku'] . '</th>';
        echo '<th>'. $value['stock_quantity'] . '</th>';
        echo '<th>'. $value['manage_stock'] . '</th>';
    echo '</tr>';
}
echo '</table><hr>';

####
// showing relevants data from products updated
// echo '<hr><p>data from the updeted products</p>';
// echo '<table>';
//     echo '<tr>';
//         echo '<th>index</th>';
//         echo '<th>id</th>';
//         echo '<th>name</th>';
//         echo '<th>sku</th>';
//         echo '<th>stock_quantity</th>';
//         echo '<th>manage_stock</th>';
//     echo '</tr>';
// foreach ($products_updated as $key => $value) {
//     echo '<tr>';
//         echo '<th>'. $key . '</th>';
//         echo '<th>'. $value['id'] . '</th>';
//         echo '<th>'. $value['name'] . '</th>';
//         echo '<th>'. $value['sku'] . '</th>';
//         echo '<th>'. $value['stock_quantity'] . '</th>';
//         echo '<th>'. $value['manage_stock'] . '</th>';
//     echo '</tr>';
// }
// echo '</table><hr>';
####
# $file->donwload_ftp_file();
echo 'finished at ' . date('d-m-Y h:i:sa') . '<br>';
