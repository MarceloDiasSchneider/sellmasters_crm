<?php 
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

include_once('orders_class.php');
$miei_orders = new orderclass();

$action=$_REQUEST['action'];
//echo __LINE__. $action;
switch ($action) {
    case "recupera_ordini":
       // amazon_id        merchant        data_order
//echo "passing";
        $miei_orders->merchant_id=$_REQUEST['selected_merchant_id'];
        $miei_orders->order_id=$_REQUEST['amazon_id'];
        $lista_ordini=$miei_orders->get_order_by_id();
        echo  json_encode($lista_ordini);

        break;
    
    default:
        # code...
        //echo "not passing";
        break;
}




