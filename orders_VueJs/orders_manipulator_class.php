<?php

$merchant_id = $_REQUEST['merchant_id'];
$startDate = $_REQUEST['startDate'];
$endDate = $_REQUEST['endDate'];
$url = 'http://51.91.97.200/sellmaster/api_sellmasters/ordini_mondotop.php';

class ordersManipulatorClass
{
    public $url;
    public $startDate;
    public $endDate;
    public $merchant_id;

    function __construct($url, $merchant_id, $startDate, $endDate)
    {
        $this->url = $url;
        $this->merchant_id  = $merchant_id;
        $this->startDate  = $startDate;
        $this->endDate = $endDate;
    }

    // get all orders
    public function get_orders()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->url . '?data_inizio=' . $this->startDate . '&data_fine=' . $this->endDate,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response, true);
    }

    // check if dati_finanziari is equal a refund
    public function dati_finanziari_refund($orders)
    {
        foreach ($orders as $key => $order) {
            $found = false;
            foreach ($order['dati_finanziari'] as $key => $value) {
                if ($value['type'] == 'RefundCommission') {
                    $found = true;
                }
            }
            if ($found) {
                $order['market_status'] = 'Refund';
            }
            $ordersRefund[] = $order;
        }
        return $ordersRefund;
    }

    // set the formation foreach market_status 
    public function market_status_formation($orders)
    {
        foreach ($orders as $key => $order) {
            switch ($order['market_status']) {
                case 'Pending':
                    $order['market_status'] = '<span class="bg-info py-1 px-3 rounded">' . $order['market_status'] . '</span>';
                    break;
                case 'Unshipped':
                    $order['market_status'] = '<span class="bg-orange py-1 px-3 rounded">' . $order['market_status'] . '</span>';
                    break;
                case 'Shipped':
                    $order['market_status'] = '<span class="bg-success py-1 px-3 rounded">' . $order['market_status'] . '</span>';
                    break;
                case 'Cancelled':
                    $order['market_status'] = '<span class="bg-lightblue py-1 px-3 rounded">' . $order['market_status'] . '</span>';
                    break;
                case 'Refund':
                    $order['market_status'] = '<span class="bg-danger py-1 px-3 rounded">' . $order['market_status'] . '</span>';
                    break;
                default:
                    break;
            }
            $ordersFormated[] = $order;
        }
        return $ordersFormated;
    }
    // create the column total_order with = item_price + shipping_prici + item_promotion_discount 
    public function total_order($orders)
    {
        foreach ($orders as $key => $order) {
            // use floatval to avoid non-numeric warning
            $item_price = floatval($order['item_price']);
            $shipping_price = floatval($order['shipping_price']);
            $item_promotion_discount = floatval($order['item_promotion_discount']);
            $total = $item_price + $shipping_price + $item_promotion_discount;
            $order['total_order'] = "<span title='item price + shipping price + item promotion discount'>" . number_format($total, 2, '.', '') . "</span>";
            $totalOrder[] = $order;
        }
        return $totalOrder;
    }
    // if the currency is equal to GBP divides by 0.9
    public function currency_convert($orders)
    {
        foreach ($orders as $key => $order) {
            if ($order['currency'] == 'GBP') {
                $order['total_order'] = $order['total_order'] / 0.9;
            }
            $currencyUpdated[] = $order;
        }
        return $currencyUpdated;
    }

    // format the array to show all dati_finaziari
    public function format_dati_finanziare($orders)
    {
        foreach ($orders as $key => $order) {
            $dati_formated = null; 
            foreach ($order['dati_finanziari'] as $key => $value) {
                // switch the currency
                $currency = null;
                switch ($value['currency']) {
                    case 'EUR':
                        $currency = ' &euro;';
                        break;
                    case 'GBP':
                        $currency = ' &pound;';
                        break;
                    default:
                        $currency = ' Undefined';
                        break;
                }
                $dati_formated = $dati_formated . '<span class="bg-info py-1 px-3 ml-1 rounded">' . $value['type'] . $currency . ' ' . $value['amount'] . '</span>';
            }
            $order['dati_finanziari'] = $dati_formated;
            $dati_finanziari_formated[] = $order; 
        }
        return $dati_finanziari_formated;
    }
}

$order = new ordersManipulatorClass($url, $merchant_id, $startDate, $endDate);
$orders = $order->get_orders();
$ordersRefund = $order->dati_finanziari_refund($orders);
$ordersFormated = $order->market_status_formation($ordersRefund);
$totalOrder = $order->total_order($ordersFormated);
$currencyUpdated = $order->currency_convert($totalOrder);
$dati_finanziari_formated = $order->format_dati_finanziare($currencyUpdated);

echo json_encode($dati_finanziari_formated);
