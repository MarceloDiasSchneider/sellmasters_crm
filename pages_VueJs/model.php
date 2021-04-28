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
        switch ($requestBody['id_profile']) {
            case null:
                // insert all pages
                $allPages = $requestBody['checked_pages'];
                $page->id_profile = $id_profile;
                foreach ($allPages as $key => $value) {
                    $page->id_page = $value['idPage'];
                    $page->access =  isset($value['checked']) ? $value['checked'] : 0;
                    $result = $page->insert_page_access();
                    if (isset($result['catchError'])) {
                        // report a try catch error
                        $data['code'] = '500';
                        $data['state'] = 'Internal Server Error';
                        $data['message'] = $result['catchError'];
                        break;
                    } elseif ($result) {
                        // report profile registred successfully
                        $data['code'] = '201';
                        $data['state'] = 'Success';
                        $data['message'] = 'Nuovo profile registrato';
                    } else {
                        // report a try catch error
                        $data['code'] = '500';
                        $data['state'] = 'Internal Server Error';
                        $data['message'] = 'Nuovo profile non registrato';
                    }
                }

                break;

            case true:
                $page->id_profile = $requestBody['id_profile'];
                // select all pages already registered to this profile
                $registered_pages = $page->get_pages_by_id_profile();
                if (isset($registered_pages['catchError'])) {
                    // report a try catch error
                    $data['code'] = '500';
                    $data['state'] = 'Internal Server Error';
                    $data['message'] = $registered_pages['catchError'];
                } else {
                    $checked_pages = $requestBody['checked_pages'];
                    // create an array only with the registered pages id
                    foreach ($registered_pages as $key => $registered_page) {
                        $registered_ids[] = $registered_page['id_page'];
                    }
                    // create an array only with the checked pages id
                    foreach ($checked_pages as $key => $checked_page) {
                        $checked_ids[] = $checked_page['idPage'];
                    }
                    // compare the arrays and update the pages already registered
                    if(isset($checked_ids)){
                        $to_update = array_intersect($checked_ids, $registered_ids);
                    }
                    foreach ($to_update as $key => $value) {
                        $page->id_page = $checked_pages[$key]['idPage']; 
                        $page->access = $checked_pages[$key]['checked'];
                        $updated_page = $page->update_page_access();
                        if (isset($updated_page['catchError'])) {
                            // report a try catch error
                            $data['code'] = '500';
                            $data['state'] = 'Internal Server Error';
                            $data['message'] = $updated_page['catchError'];

                            echo json_encode($data);
                            exit;
                        } elseif ($updated_page) {
                            # code.. 
                        }
                    }
                    // compare the arrays and insert the pages not registered
                    $to_insert = array_diff($checked_ids, $registered_ids);
                    foreach ($to_insert as $key => $value) {
                        $page->id_page = $checked_pages[$key]['idPage']; 
                        $page->access = $checked_pages[$key]['checked'];
                        $inserted_page = $page->insert_page_access();
                        if (isset($inserted_page['catchError'])) {
                            // report a try catch error
                            $data['code'] = '500';
                            $data['state'] = 'Internal Server Error';
                            $data['message'] = $inserted_page['catchError'];

                            echo json_encode($data);
                            exit;
                        } elseif ($inserted_page) {
                            # code.. 
                        }
                    }
                    
                    $data['to_update'] = array_intersect($checked_ids, $registered_ids);
                    $data['to_insert'] = array_diff($checked_ids, $registered_ids);
                    $data['registered_ids'] = $registered_ids;
                    $data['checked_ids'] = $checked_ids;
                    $data['state'] = 'Success';
                    $data['code'] = '200';
                    $data['message'] = 'profile aggiornato';
                }

                break;

            default:
                # code..
                break;
        }

        break;

    case 'get_profile_data':
        $page->id_profile = $requestBody['id_profile'];
        $pages = $page->get_pages_by_id_profile();
        if (isset($pages['catchError'])) {
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
