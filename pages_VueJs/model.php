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

include_once('pages_class.php');
$page = new pagesClass();

switch ($requestBody['action']) {
    // this class is called from profile_VueJs/model.php to get all pages to create or update a profile
    case 'pages_permission': 
        // create the variable pages with all pages
        $pages = $page->get_pages();
        break;

    // this class is called from profile_VueJs/model.php create and bind all pages
    case 'insert_or_update_profile':
        // insert all pages
        $allPages = $requestBody['checked_pages'];
        $page->id_profile = $id_profile;
        foreach ($allPages as $key => $value) {
            $page->id_page = $value['idPage'];
            $page->access =  isset($value['checked']) ? $value['checked'] : 0;
            $result = $page->insert_page_access();
            if(isset($result['catchError'])){
                // report a try catch error
                $data['code'] = '500';
                $data['state'] = 'Internal Server Error';
                $data['message'] = $result['catchError'];
                break;
            } else {
                // report profile registred successfully
                $data['code'] = '201';
                $data['state'] = 'Success';
                $data['message'] = 'Nuovo profile registrato';
            }
        }
        break;

    case 'get_profile_data': 
        $page->id_profile = $requestBody['id_profile'];
        $pages = $page->get_pages_by_id_profile();
        if(isset($pages['catchError'])){
            // report a try catch error
            $data['code'] = '500';
            $data['state'] = 'Internal Server Error';
            $data['message'] = $pages['catchError'];
        } else {
            // report profile registred successfully
            $data['code'] = '200';
            $data['state'] = 'Success';
            $data['message'] = 'profile pronto per essere aggiornato';
            $data['profile'] = $profile['descrizione'];
            $data['pages'] = $pages;
        }

        break;

    default:
        # code...
        break;
}
