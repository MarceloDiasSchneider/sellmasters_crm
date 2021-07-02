<?php

if (isset($_SERVER['REQUEST_METHOD'])) {
    // get resquet body data  
    if (!isset($requestBody)) {
        $requestBody = json_decode(file_get_contents('php://input'), true);
    }
} else {
    // report an error if there is no request method
    $data['code'] = '406';
    $data['state'] = 'Not Acceptable';
    $data['message'] = 'Request method not defined';
    
    echo json_encode($data);
    exit;
}

class ordersManipulatorClass
{
    public $endpoint;
    public $access_token;
    public $merchant_id;
    public $startDate;
    public $endDate;

    // get all orders
    public function get_orders()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->endpoint . 'start/' . $this->startDate . '/end/' . $this->endDate .'/merchant/' . $this->merchant_id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $this->access_token,
            ),
        ));

        $response = curl_exec($curl);
        $response = json_decode($response, true);
        curl_close($curl);

        // if no ordes was found return a empty response
        if (!(is_array($response) || is_object($response))) {
            echo json_encode(array());
            exit;
        }

        return $response;
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
                    $order['market_status'] = '<span class="bg-maroon py-1 px-3 rounded">' . 'Without status' . '</span>';
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
    // format the financial_issue to display in a column
    public function financial_issue($orders)
    {
        // echo '<pre>';
        // print_r($orders[0]);
        // echo '<hr>';
        // print_r($orders[55]['financial_issue']);
        // echo '</pre>';
        // exit;
        foreach ($orders as $key => $order) {
            $financial_issue_html = '';
            if ($order['financial_issue'] !== null) {
                // decoding the financial issue 
                $financial_issue = json_decode($order['financial_issue'], true);
                #PostedDate
                $financial_issue_html .= $this->formatAsHTML('PostedDate', $financial_issue['PostedDate'], 'info');   
                # OrderFeeList -> sempre vuoto
                $financial_issue_html .= $this->formatAsHTML('OrderFeeList', '', 'secondary');
                #AmazonOrderId
                $financial_issue_html .= $this->formatAsHTML('AmazonOrderId', $financial_issue['AmazonOrderId'], 'info');
                # MarketplaceName
                $financial_issue_html .= $this->formatAsHTML('MarketplaceName', $financial_issue['MarketplaceName'], 'info');
                # OrderChargeList -> sempre vuoto
                $financial_issue_html .= $this->formatAsHTML('OrderChargeList', '', 'secondary');
                # ShipmentFeeList -> sempre vuoto
                $financial_issue_html .= $this->formatAsHTML('ShipmentFeeList', '', 'secondary');
                # SellerSKU
                if(isset($financial_issue['ShipmentItemList']['ShipmentItem']['SellerSKU']))
                $SellerSKU = $financial_issue['ShipmentItemList']['ShipmentItem']['SellerSKU'];
                $financial_issue_html .= $this->formatAsHTML('SellerSKU', $SellerSKU, 'info');
                # FeeComponent
                if(isset($financial_issue['ShipmentItemList']['ShipmentItem']['ItemFeeList']['FeeComponent'])){
                    $FeeComponent = $financial_issue['ShipmentItemList']['ShipmentItem']['ItemFeeList']['FeeComponent'];
                    foreach ($FeeComponent as $key => $value) {
                        $CurrencyCode_CurrencyAmount = $value['FeeAmount']['CurrencyCode'] . ': ' . $value['FeeAmount']['CurrencyAmount'];
                        $financial_issue_html .= $this->formatAsHTML($value['FeeType'], $CurrencyCode_CurrencyAmount, 'lightblue');
                    }
                    
                }
                # OrderItemId
                if(isset($financial_issue['ShipmentItemList']['ShipmentItem']['OrderItemId'])) {
                    $OrderItemId = $financial_issue['ShipmentItemList']['ShipmentItem']['OrderItemId'];
                    $financial_issue_html .= $this->formatAsHTML('OrderItemId', $OrderItemId, 'info');
                }
                # PromotionList
                if (isset($financial_issue['ShipmentItemList']['ShipmentItem']['PromotionList']['Promotion'])) {
                    $Promotion = $financial_issue['ShipmentItemList']['ShipmentItem']['PromotionList']['Promotion'];
                    foreach ($Promotion as $key => $value) {
                        $financial_issue_html .= $this->formatAsHTML('PromotionId', $value['PromotionId'], 'navy');
                        $CurrencyCode_CurrencyAmount = $value['PromotionAmount']['CurrencyCode'] . ': ' . $value['PromotionAmount']['CurrencyAmount'];
                        $financial_issue_html .= $this->formatAsHTML($value['PromotionType'], $CurrencyCode_CurrencyAmount, 'navy');
                    }
                }
                # ChargeComponent
                if (isset($financial_issue['ShipmentItemList']['ShipmentItem']['ItemChargeList']['ChargeComponent'])) {
                    $ChargeComponent = $financial_issue['ShipmentItemList']['ShipmentItem']['ItemChargeList']['ChargeComponent'];
                    foreach ($ChargeComponent as $key => $value) {
                        $CurrencyCode_CurrencyAmount = $value['ChargeAmount']['CurrencyCode'] . ': ' . $value['ChargeAmount']['CurrencyAmount'];
                        $financial_issue_html .= $this->formatAsHTML($value['ChargeType'], $CurrencyCode_CurrencyAmount, 'lightblue');
                    }
                }
                # QuantityShipped
                if(isset($financial_issue['ShipmentItemList']['ShipmentItem']['QuantityShipped']))
                $QuantityShipped = $financial_issue['ShipmentItemList']['ShipmentItem']['QuantityShipped'];
                $financial_issue_html .= $this->formatAsHTML('QuantityShipped', $QuantityShipped, 'info');
                # ItemTaxWithheldList -> sempre vuoto
                $financial_issue_html .= $this->formatAsHTML('ItemTaxWithheldList', '', 'secondary');
                # ItemFeeAdjustmentList -> sempre vuoto
                $financial_issue_html .= $this->formatAsHTML('ItemFeeAdjustmentList', '', 'secondary');
                # PromotionAdjustmentList -> sempre vuoto
                $financial_issue_html .= $this->formatAsHTML('PromotionAdjustmentList', '', 'secondary');
                # ItemChargeAdjustmentList -> sempre vouto
                $financial_issue_html .= $this->formatAsHTML('ItemChargeAdjustmentList', '', 'secondary');
                # DirectPaymentList -> sempre vouto
                $financial_issue_html .= $this->formatAsHTML('DirectPaymentList', '', 'secondary');
                # OrderFeeAdjustmentList -> sempre vouto
                $financial_issue_html .= $this->formatAsHTML('OrderFeeAdjustmentList', '', 'secondary');
                # OrderChargeAdjustmentList -> sempre vouto
                $financial_issue_html .= $this->formatAsHTML('OrderChargeAdjustmentList', '', 'secondary');
                # ShipmentFeeAdjustmentList -> sempre vouto
                $financial_issue_html .= $this->formatAsHTML('ShipmentFeeAdjustmentList', '', 'secondary');
                # ShipmentItemAdjustmentList -> sempre vouto
                $financial_issue_html .= $this->formatAsHTML('ShipmentItemAdjustmentList', '', 'secondary');

                $order['financial_issue_html'] = $financial_issue_html;
            } else {
                $order['financial_issue_html'] = $this->formatAsHTML('No data', '', 'gray');
            }
            $orders_financial_issue[] = $order;
        }
        return $orders_financial_issue;
    }

    public function formatAsHTML($key, $value, $bg_color)
    {
        return '<span class="bg-' . $bg_color . ' py-1 px-3 rounded">' . $key . ': ' . $value . '</span><br>';
    }
}

$order = new ordersManipulatorClass();
$order->endpoint = 'http://51.38.232.192/api/orders/';
$order->access_token = $requestBody['access_token'];
$order->merchant_id = $requestBody['merchant_id'];
$order->startDate = $requestBody['startDate'];
$order->endDate = $requestBody['endDate'];

$orders = $order->get_orders();
$orders = $order->financial_issue($orders);
// $orders = $order->dati_finanziari_refund($orders);
$orders = $order->market_status_formation($orders);
$orders = $order->total_order($orders);
$orders = $order->currency_convert($orders);
// $orders = $order->format_dati_finanziare($orders);

echo json_encode($orders);
