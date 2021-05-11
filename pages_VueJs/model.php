<?php

include_once('../common_VueJs/report_exception_class.php');

// check if the request method is setted
try {
    if (isset($_SERVER['REQUEST_METHOD'])) {
        // get resquet body data  
        if (!isset($requestBody)) {
            $requestBody = json_decode(file_get_contents('php://input'), true);
        }
    } else {
        throw new reportException('Request method not defined', 406);
    }
} catch (reportException $e) {
    $e->reportError();
}

include_once('pages_class.php');
$page = new pagesClass();

switch ($requestBody['action']) {
    case 'insert_or_update_profile': 
        // this method is called from profile_VueJs/model.php create and bind all pages
        switch ($requestBody['id_profile']) {
            case null:
                // insert all pages
                $allPages = $requestBody['checked_pages'];
                $page->id_profile = $id_profile;
                $pagesInserted = $page->insert_page_access($allPages);           
                break;
            case true:
                // set the id profile
                $page->id_profile = $requestBody['id_profile'];
                try {
                    // select all pages already registered to this profile
                    $registered_pages = $page->get_pages_by_id_profile();
                    if (isset($registered_pages['catchError'])) {
                        // report a try catch error on database
                        throw new reportException($registered_pages['catchError'], 500);
                    }
                    $checked_pages = $requestBody['checked_pages'];
                    // create an array only with the registered pages id
                    $registered_ids = [];
                    foreach ($registered_pages as $key => $registered_page) {
                        $registered_ids[] = $registered_page['id_page'];
                    }
                    // create an array only with the checked pages id
                    $checked_ids = [];
                    foreach ($checked_pages as $key => $checked_page) {
                        $checked_ids[] = $checked_page['idPage'];
                    }
                    $page->database->beginTransaction();
                    // compare the arrays and update the pages already registered
                    $to_update = array_intersect($checked_ids, $registered_ids);
                    if (count($to_update)) {
                        $updated_pages = $page->update_page_access($to_update, $checked_pages);
                        if (isset($updated_pages['catchError'])) {
                            $page->database->rollback();
                            // report a try catch error on database
                            throw new reportException($updated_pages['catchError'], 500);
                        }
                    }
                    // compare the arrays and insert the pages not registered
                    $to_insert = array_diff($checked_ids, $registered_ids);
                    if (count($to_insert)){
                        $inserted_page = $page->insert_missing_page_access($to_insert, $checked_pages);
                        if (isset($inserted_page['catchError'])) {
                            // report a try catch error on database
                            $page->database->rollback();
                            throw new reportException($inserted_page['catchError'], 500);
                        }
                    }
                    $commit = $page->database->commit();
                    // $data['$registered_pages'] = $registered_pages;
                    // $data['Re Bo checked_pages'] = $checked_pages;
                    // $data['to_update'] = array_intersect($checked_ids, $registered_ids);
                    // $data['to_insert'] = array_diff($checked_ids, $registered_ids);
                    // $data['registered_ids'] = $registered_ids;
                    // $data['checked_ids'] = $checked_ids;
                } catch (reportException $e) {
                    $e->reportError();
                }
                break;
            default:
                # code..
                break;
        }

        break;

    case 'get_profile_data':
        // this class is called from autenticazione_VueJs/model.php 
        $page->id_profile = $requestBody['id_profile'];
        break;
    case 'autenticazione': 
        // this method is called from autunticazione/model.php
        $page->id_profile = $autenticazione->id_profile;
        $page->access = 1;
        $accessPages = $page->get_access_pages_by_id_profile();
        break;
    case 'pages_permission': 
        // this method is called from profile_VueJs/model.php to get all pages to create or update a profile
        // create the variable pages with all pages
        $pages = $page->get_pages();
        break;
    default:
        # code...
        break;
}
