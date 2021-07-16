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
        // donwload the file from the ftp
        # $success = ftp_get($conn_id, 'ArticoliGrandeA.csv', '/ArticoliGrandeA.csv', FTP_BINARY, 0);
        # echo 'donwload';        
        # exit;
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
            $data_to_update[] = ['sku' => $data[0], 'stock_quantity' => $data[9]];
        }
        echo '<p>' . count($data_to_update) . ' products to update</p>';
        return $data_to_update;
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
                // 'Authorization: Basic Y2tfYzQ3ODc0YzllYjFhZmNjMDlkNDQ5MDdkYjk3NzhkOTVlNjA5OTA3ZTpjc18zYTVhYmJjOTA4MWQ5OTZmZTMyMWMwMTg5YmUwN2JhMzNhYTlhZTYz',
            ),
        );
        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }

    public function retrieveIdProductsBySKU($batch)
    {
        // prepare the sku to send in post fields
        $sku = '';
        foreach ($batch as $key => $product) {
            if ($key != 0) { $sku .= ','; }
            $sku .= $product['sku'];
        }
        $this->resource = 'products/';
        $this->method = 'GET';
        $this->postFields = json_encode([
            'per_page' => 100,
            'sku' => $sku,
        ]);
        # echo $this->postFields;
        $products = $this->woocommerceApiConection();
        $products = json_decode($products, true);
        $products = array_column($products, 'id', 'sku');
        return $products;
    }

    public function products_update_stock($products_data, $stock_quantity)
    {
        // use array to column to organaze the $stock_quantity as array ['sky' => 'quantity']
        $stock_quantity = array_column($stock_quantity, 'stock_quantity', 'sku');
        # echo '<pre>';
        # print_r($stock_quantity);
        # echo '</pre>';
        // prepare the id, quantity and manage_stock to send in post fields
        $update = [];
        foreach ($products_data as $sku => $id) {
            $update[] = [
                'id' => $id,
                'stock_quantity' => $stock_quantity[$sku],
                'manage_stock' => true
            ];
        }
        $this->resource = 'products/batch';
        $this->method = 'POST';
        $this->postFields = json_encode([
            'per_page' => 100,
            'update' => $update
        ]);
        # echo $this->postFields;
        // execut the update
        $products_updeted = $this->woocommerceApiConection();
        $products_updeted = json_decode($products_updeted);

        return $products_updeted;
    }
}

// instances of classes
$woocommerceApi = new woocommerceApiClass();
$file = new readFtpFile();
// reade the ftp file
$stock_to_update = $file->read_ftp_file();
// unset the header's data
unset($stock_to_update[0]);

    // // array to test, instead of use all file data
    // $test[] = ["sku" => "000000000242810", "stock_quantity" => "1"];
    // $test[] = ["sku" => "000000000234181", "stock_quantity" => "2"];
    // $test[] = ["sku" => "000000000242626", "stock_quantity" => "4"];
    // $test[] = ["sku" => "1754", "stock_quantity" => "4"];
    // $test[] = ["sku" => "000000000242722", "stock_quantity" => "5"];
    // $test[] = ["sku" => "000000000242981", "stock_quantity" => "6"];
    // // echo '<pre>';
    // // print_r($test);
    // // echo '</pre>';

// create a batch of 100 rows foreach request
// woocommerce don't accept request over 100 rows
$batch_of_stock_to_update = array_chunk($stock_to_update, 100);
$total_products_updated = 0;
foreach ($batch_of_stock_to_update as $key => $stock_quantity) {
    $products_data = $woocommerceApi->retrieveIdProductsBySKU($stock_quantity);
    $total_products_updated += count($products_data);
    echo '<hr><p>batch: ' . $key . ' | showing ' . count($products_data) . ' products found by sku:';
    echo '<pre>';
    print_r($products_data);
    echo '</pre>';
    $products_updeted = $woocommerceApi->products_update_stock($products_data, $stock_quantity);
    // showing relevants data from products updated
    echo '<hr><p>data from the products updeted</p>';
    echo '<table>';
        echo '<tr>';
            echo '<th>id</th>';
            echo '<th>name</th>';
            echo '<th>sku</th>';
            echo '<th>stock_quantity</th>';
            echo '<th>manage_stock</th>';
        echo '</tr>';
    foreach ($products_updeted->update as $key => $value) {
        echo '<tr>';
            echo '<th>'. $value->id . '</th>';
            echo '<th>'. $value->name . '</th>';
            echo '<th>'. $value->sku . '</th>';
            echo '<th>'. $value->stock_quantity . '</th>';
            echo '<th>'. $value->manage_stock . '</th>';
        echo '</tr>';
    }
    echo '</table><hr>';
}
echo '<h1>total products updated ' . $total_products_updated . '</h1>';
