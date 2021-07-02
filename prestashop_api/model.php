<?php

include_once('../common_VueJs/report_exception_class.php');

include_once('prestashop_api_class.php');
$prestashopApi = new prestashopApiClass();

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

switch ($requestBody['action']) {
    case 'get_api_permissions':

        $prestashopApi->key = $requestBody['key'];
        $prestashopApi->url = $requestBody['url'];
        $prestashopApi->virtual_uri = $requestBody['virtual_uri'];
        $prestashopApi->method = 'GET';

        $permissions = $prestashopApi->resquest_prestashop_api();

        $data['permissions'] = json_decode($permissions);

        $data['code'] = 200;
        $data['state'] = 'Success';
        $data['message'] = 'API Permission';

        echo json_encode($data);
        break;

    case 'get_shops_data':
        $prestashopApi->key = $requestBody['key'];
        $prestashopApi->url = $requestBody['url'];
        $prestashopApi->virtual_uri = $requestBody['virtual_uri'] . '/shops';
        $prestashopApi->method = 'GET';

        $shops = $prestashopApi->resquest_prestashop_api();
        $shops = json_decode($shops, true);

        foreach ($shops['shops'] as $k => $shop) {
            $prestashopApi->virtual_uri = $requestBody['virtual_uri'] . "shops/" . $shop['id'];
            $shop_data = $prestashopApi->resquest_prestashop_api();
            $sdata = json_decode($shop_data, true);

            $prestashopApi->virtual_uri = $requestBody['virtual_uri'] . "shop_urls/" . $shop['id'];
            $shop_data = $prestashopApi->resquest_prestashop_api();
            $shops_url = json_decode($shop_data, true);

            $shops_data[] = array_merge($sdata['shop'] + $shops_url['shop_url']);
        }

        $data['shops_data'] = $shops_data;
        $data['code'] = 200;
        $data['state'] = 'Success';
        $data['message'] = 'Shops data';

        echo json_encode($data);
        break;

    case 'get_shops_and_products':
        $prestashopApi->key = $requestBody['key'];
        $prestashopApi->url = $requestBody['url'];
        $prestashopApi->virtual_uri = $requestBody['virtual_uri'] . '/shops';
        $prestashopApi->method = 'GET';

        $shops = $prestashopApi->resquest_prestashop_api();
        $shops = json_decode($shops, true);

        foreach ($shops['shops'] as $k => $shop) {
            $prestashopApi->virtual_uri = $requestBody['virtual_uri'] . "shops/" . $shop['id'];
            $shop_data = $prestashopApi->resquest_prestashop_api();
            $sdata = json_decode($shop_data, true);

            $prestashopApi->virtual_uri = $requestBody['virtual_uri'] . "shop_urls/" . $shop['id'];
            $shop_data = $prestashopApi->resquest_prestashop_api();
            $shops_url = json_decode($shop_data, true);

            $shops_data[] = array_merge($sdata['shop'] + $shops_url['shop_url']);
        }

        foreach ($shops_data as $key => $shop) {
            $prestashopApi->virtual_uri = $shop['virtual_uri'] . 'api/products';
            $products = $prestashopApi->resquest_prestashop_api();
            $products = json_decode($products, true);

            foreach ($products['products'] as $k => $product) {
                $prestashopApi->virtual_uri = $shop['virtual_uri'] . 'api/products/' . $product['id'];
                $products_data = $prestashopApi->resquest_prestashop_api();
                $pdata = json_decode($products_data, true);

                $product_data[$shop['id_shop']][] = $pdata['product'];
            }
        }

        $data['products_data'] = $product_data;
        $data['shops_data'] = $shops_data;
        $data['code'] = 200;
        $data['state'] = 'Success';
        $data['message'] = 'Products loaded';

        echo json_encode($data);

        break;

    case 'create_product':
        $prestashopApi->key = $requestBody['key'];
        $prestashopApi->url = $requestBody['url'];
        $prestashopApi->virtual_uri = $requestBody['virtual_uri'] . 'products';
        $prestashopApi->method = 'POST';
        $prestashopApi->postFields = '<?xml version="1.0" encoding="UTF-8"?>
            <prestashop xmlns:xlink="http://www.w3.org/1999/xlink">
                <product>
                    <id_shop_default>' . $requestBody['shop_id'] . '</id_shop_default>
                    <price>' . $requestBody['price'] . '</price>
                    <name>
                        <language id="1">' . $requestBody['name'] . '</language>
                    </name>
                    <description>
                        <language id="1">' . $requestBody['description'] . '</language>
                    </description>
                    <link_rewrite>
                        <language id="1"></language>
                    </link_rewrite>
                    <state>1</state>
                    <active>1</active>
                </product>
            </prestashop>';

        $create_product = $prestashopApi->resquest_prestashop_api();
        $created_product = json_decode($create_product, true);

        $data['created_product'] = $created_product;
        $data['code'] = 201;
        $data['state'] = 'Success';
        $data['message'] = 'Product created';
        echo json_encode($data);
        break;

    case 'import_products':
        $prestashopApi->key = $requestBody['key'];
        $prestashopApi->url = $requestBody['url'];

        foreach ($requestBody['products'] as $key => $product) {
            // prepare the categories to associations
            $categories = $product['associations_categories'];
            $explode = '#';
            $canExplode = strpos($categories, $explode);
            if ($canExplode !== false) {
                $categories = explode($explode, $categories);
                $category = '';
                foreach ($categories as $key => $value) {
                    $category = $category . '<category><id>' . $value . '</id></category>';
                }
                $categories = '<categories>' . $category . '</categories>';
            } else {
                $categories = '<categories><category><id>' . $categories . '</id></category></categories>';
            }
            // prepate the xml to create a product
            $prestashopApi->postFields = '<?xml version="1.0" encoding="UTF-8"?>
                <prestashop xmlns:xlink="http://www.w3.org/1999/xlink">
                    <product>
                        <id_shop_default>' . $requestBody['shop_id'] . '</id_shop_default>
                        <price>' . $product['price'] . '</price>
                        <id_category_default>' . $product['category_defalt'] . '</id_category_default>
                        <name>
                            <language id="1">' . $product['name'] . '</language>
                        </name>
                        <link_rewrite>
                            <language id="1"></language>
                        </link_rewrite>
                        <reference>' . $product['reference'] . '</reference>
                        <associations>' . $categories . '</associations>
                        <state>' . $product['state'] . '</state>
                        <active>' . $product['active'] . '</active>
                    </product>
                </prestashop>';
            // set required settings to crul
            $prestashopApi->method = 'POST';
            $prestashopApi->virtual_uri = $requestBody['virtual_uri'] . 'products';

            try { // create the product
                $create_product = $prestashopApi->resquest_prestashop_api();
                $created_product = json_decode($create_product, true);
                $created_products[] = $created_product;
                
                if (isset($created_product['errors'])) {
                    throw new reportException('Problem to create the product ' . $product['name'], 400);
                }
            } catch (reportException $e) {
                $e->reportError();
            }
            

            // set required settings to crul
            $prestashopApi->method = 'GET';
            $prestashopApi->virtual_uri = $requestBody['virtual_uri']
            . 'stock_availables/?filter[id_product]='
            . $created_product['product']['id'];

            try { // get the id stock availible created with the product
                $id_stock_availible = $prestashopApi->resquest_prestashop_api();
                $id_stock_availible = json_decode($id_stock_availible, true);
            
                if (!isset($id_stock_availible['stock_availables'][0]['id'])) {
                    throw new reportException('Problem to get the id stock availible created on product ' . $product['name'], 400);
                }
            } catch (reportException $e) {
                $e->reportError();
            }

            // set required settings to crul
            $prestashopApi->method = 'PUT';
            $prestashopApi->virtual_uri = $requestBody['virtual_uri'] . 'stock_availables/';
            // prepate the xml to update the product stock availible
            $prestashopApi->postFields = '<?xml version="1.0" encoding="UTF-8"?>
                <prestashop xmlns:xlink="http://www.w3.org/1999/xlink">
                    <stock_available>
                        <id>' . $id_stock_availible['stock_availables'][0]['id'] . '</id>
                        <id_product>' . $created_product['product']['id'] . '</id_product>
                        <id_product_attribute>0</id_product_attribute>
                        <id_shop>' . $requestBody['shop_id'] . '</id_shop>
                        <id_shop_group>0</id_shop_group>
                        <depends_on_stock>0</depends_on_stock>
                        <quantity>' . intval($product['quantity']) . '</quantity>
                        <out_of_stock>2</out_of_stock>
                    </stock_available>
                </prestashop>';

                try { // update the stock to the created product
                    $stock_updated = $prestashopApi->resquest_prestashop_api();
                    $stock_updated = json_decode($stock_updated, true);

                    if (!isset($stock_updated['stock_available'])) {
                        throw new reportException('Problem to set the stock availeble on product ' . $product['name'], 400);
                    }
                } catch (reportException $e) {
                    $e->reportError();
                }

        }

        $data['postFields'] = $prestashopApi->postFields;
        $data['stock_updated'] = $stock_updated;
        $data['id_stock_availible'] = $id_stock_availible;
        $data['created_products'] = $created_products;


        $data['code'] = 201;
        $data['state'] = 'Success';
        $data['message'] = 'Products imported';
        echo json_encode($data);

        break;

    default:
        $data['code'] = 501;
        $data['state'] = 'Not Implemented';
        $data['message'] = 'The server does not support the functionality required to fulfill the request.';

        echo json_encode($data);
        break;
}
