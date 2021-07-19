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
            $products_to_update[] = ['sku' => $data[0], 'stock_quantity' => $data[9]];
        }
        // unset the header's data
        unset($products_to_update[0]);
        // use array_column to organze the data as array [sku => stock_quantity]
        // and remove the duplicated sku
        $products_to_update = array_column($products_to_update, 'stock_quantity', 'sku');
        
        return $products_to_update;
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
        echo 'Runing at ' . date('d-m-Y',strtotime("-1 days")) . '<br>';
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
                // 'Authorization: Basic Y2tfYzQ3ODc0YzllYjFhZmNjMDlkNDQ5MDdkYjk3NzhkOTVlNjA5OTA3ZTpjc18zYTVhYmJjOTA4MWQ5OTZmZTMyMWMwMTg5YmUwN2JhMzNhYTlhZTYz',
            ),
        );
        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }

    public function retrieveAllProducts()
    {
        // prepare the post fields
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

    public function update_products_stock($all_products_retrieved, $stock_to_update)
    {
        $products_to_update = [];
        foreach ($all_products_retrieved as $sku => $id) {
            $stock_quantity = 0;
            // if the sku is found in stock_to_update set the quantity
            if (isset($stock_to_update[$sku])) {
                if ($stock_to_update[$sku] > 0) {
                    // $stock_quantity = 0;
                    $stock_quantity = $stock_to_update[$sku];
                }
            }
            $products_to_update[] = [
                'id' => $id,
                'sku' => $sku,
                'stock_quantity' => $stock_quantity
            ];
        }
        // create a batch of 100 rows foreach request
        // woocommerce don't accept request over 100 rows
        $batch_of_products = array_chunk($products_to_update, 100);
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
                $products_updated_1[] = [
                    'id' => $produtc['id'],
                    'name' => $produtc['name'],
                    'sku' => $produtc['sku'],
                    'stock_quantity' => $produtc['stock_quantity'],
                    'manage_stock' => $produtc['manage_stock'],
                ];
            }
        }

        return $products_updated_1;
    }
}

// instances of classes
$woocommerceApi = new woocommerceApiClass();
$file = new readFtpFile();
// reade the ftp file
$stock_to_update = $file->read_ftp_file();
// echo 'Found ' . count($stock_to_update) . ' products to update';
// echo '<pre>';
// print_r($stock_to_update);
// echo '</pre>';
// retrieve all products from woocommerce
$all_products_retrieved = $woocommerceApi->retrieveAllProducts();
// echo 'All ' . count($all_products) . ' products retrieved';
// echo '<pre>';
// print_r($all_products);
// echo '</pre>';
$products_updated = $woocommerceApi->update_products_stock($all_products_retrieved, $stock_to_update);
// echo count($products_updated) . ' products updated';
// echo '<pre>';
// print_r($products_updated);
// echo '</pre>';
// exit;

// showing relevants data from products updated
echo '<hr><p>data from the products updeted</p>';
echo '<table>';
    echo '<tr>';
        echo '<th>index</th>';
        echo '<th>id</th>';
        echo '<th>name</th>';
        echo '<th>sku</th>';
        echo '<th>stock_quantity</th>';
        echo '<th>manage_stock</th>';
    echo '</tr>';
foreach ($products_updated as $key => $value) {
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

# $file->donwload_ftp_file();