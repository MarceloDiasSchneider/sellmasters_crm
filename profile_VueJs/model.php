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

include_once('profile_class.php');
$profile = new profileClass();

switch ($requestBody['action']) {
    case "insert_or_update_profile":
        try {
            // get the code session to verify if is the same 
            $form = $requestBody['codiceSessione'];
            $session = $_SESSION['codiceSessione'];

            // if the code session does not match, unauthorized the insert or update
            if ($form != $session) {
                throw new reportException('Session code doesn\'t match', 406);
            }
        } catch (reportException $e) {
            $e->reportError();
        }

        switch ($requestBody['id_profile']) {
            case null: // insert new profile
                // set the value to the description
                $profile->descrizione = $requestBody['description'];
                $profile->attivo = $requestBody['active'];
                try {
                    // check if this description is already used
                    $description = $profile->check_description();
                    if (isset($description['catchError'])) {
                        // report a try catch error on database
                        throw new reportException($description['catchError'], 500);
                    }
                    if (isset($description['descrizione'])) {
                        // report that the description is already used
                        throw new reportException('Descrizione già registrata', 401);
                    }
                    // execute the insert of new profile
                    $id_profile = $profile->insert_profile();
                    if (isset($id_profile['catchError'])) {
                        // report a try catch error on database
                        throw new reportException($id_profile['catchError'], 500);
                    }
                    if (!$id_profile) {
                        // report an error to insert the profile
                        throw new reportException('Problema! profilo non registrato', 500);
                    }
                    // call a method in pages_VueJs/model.php to bind all pages
                    include_once('../pages_VueJs/model.php');
                    $data['$pagesInserted'] = $pagesInserted;
                    if (isset($pagesInserted['catchError'])) {
                        // report a try catch error on database
                        throw new reportException($pagesInserted['catchError'], 500);
                    } 
                    if (!$pagesInserted) {
                        // report profile is not registred
                        throw new reportException('Problema! profile non registrato', 400);   
                    }
                    // report profile registred successfully
                    $data['code'] = 201;
                    $data['state'] = 'Success';
                    $data['message'] = 'Nuovo profile registrato';
                    echo json_encode($data);
                } catch (reportException $e) {
                    $e->reportError();
                }
                break;

            case true: // update a profile
                $profile->id_profile = $requestBody['id_profile'];
                $profile->descrizione = $requestBody['description'];
                try {
                    // check if the new description is alread used
                    $description = $profile->check_description_others();
                    if (isset($description['catchError'])) {
                        // report a try catch error on database
                        throw new reportException($description['catchError'], 500);
                    } 
                    if (isset($description['descrizione'])) {
                        // report that the description is already used
                        throw new reportException('Descrizione già registrata', 401);  
                    } 
                    // check if the description is changed
                    $description = $profile->check_description_self();
                    if (isset($description['catchError'])) {
                        // report a try catch error on database
                        throw new reportException($description['catchError'], 500);
                    } 
                    $profile->database->beginTransaction();
                    if (!isset($description['descrizione'])) {
                        // execute the profile update 
                        $description = $profile->update_profile();
                        if (isset($description['catchError'])) {
                            $profile->database->rollback();
                            // report a try catch error on database
                            throw new reportException($description['catchError'], 500);
                        } else if ($description < 1) {
                            // report an error to update the description
                            throw new reportException('Problema!! profilo non registrato', 400);  
                        }
                    }
                    // call a method in pages_VueJs/model.php to bind all pages
                    include_once('../pages_VueJs/model.php');
                    // check if the pages was updated successfully
                    if(!isset($commit)){
                        $profile->database->rollback();
                        // report a try catch error on database
                        throw new reportException('rollback', 500);
                        // throw new reportException($description['catchError'], 500);
                    }
                    $profile->database->commit();
                    $data['state'] = 'Success';
                    $data['code'] = 200;
                    $data['message'] = 'profile aggiornato';
                    echo json_encode($data);
                    } catch (reportException $e) {
                        $e->reportError();
                    }
                break;

            default:
                # code...
                break;
        }
        break;
    case "get_profiles":
        // get profile to set option on the form | called from utente/model.php
        $profiles = $profile->get_profiles();
        if (isset($profiles['catchError'])) {
            $data['code'] = '500';
            $data['state'] = 'Internal Server Error';
            $data['message'] = $profiles['catchError'];
        } else {
            foreach ($profiles as $key => $value) {
                $fa_lock = $value['attivo'] ? 'fas fa-lock-open' : 'fas fa-lock';
                $title = $value['attivo'] ? 'disabilitare' : 'attivare';
                foreach ($value as $k => $v) {
                    if ($k == 'id_profile') {
                        $profileData['azione'] = "
                             <span class='update_profile' id='lv_$v'><i class='fas fa-edit' title='modificare'></i></span> 
                             <span class='disable_profile' id='lv_$v'><i class='$fa_lock' title='$title'></i></span>";
                    } else if ($k == 'attivo') {
                        if ($v == 1) {
                            $profileData[$k] = 'Sì';
                        } else {
                            $profileData[$k] = 'No';
                        }
                    } else if ($k == 'id_profile') {
                        $profileData[$k] = $descrizioni[$v];
                    } else {
                        $profileData[$k] = $v;
                    }
                }
                $data[] = $profileData;
            }
        }
        echo json_encode($data);

        break;

    case "get_profiles_active":
        // set the attivo
        $profile->attivo = 1;
        // get profile to set option on the form | called from utente/model.php
        $profiles = $profile->get_profiles_active();

        break;

    case "get_profile_data":
        $profile->id_profile = $requestBody['id_profile'];
        try {
            $profile = $profile->get_profile_data();
            if (isset($profile['catchError'])) {
                    // report a try catch error on database
                    throw new reportException($profile['catchError'], 500);
            } 
            // call a method in pages_VueJs/model.php 
            include_once('../pages_VueJs/model.php');
            $pages = $page->get_pages_by_id_profile();
            if (isset($pages['catchError'])) {
                // report a try catch error on database
                throw new reportException($pages['catchError'], 500);
            } 
            // report profile registred successfully
            $data['code'] = 200;
            $data['state'] = 'Success';
            $data['message'] = 'profile pronto per essere aggiornato';
            $data['profile'] = $profile['descrizione'];
            $data['pages'] = $pages;
            echo json_encode($data);
        } catch (reportException $e) {
            $e->reportError();
        }
        break;
    case 'pages_permission':
        try {
            // call a pages method that return all pages
            include_once('../pages_VueJs/model.php');
            if (isset($pages['catchError'])) {
                // report a try catch error on database
                throw new reportException($pages['catchError'], 500);
            } 
            $data['code'] = 200;
            $data['state'] = 'Success';
            $data['message'] = 'All pages found';
            $data['pages'] = $pages;
            echo json_encode($data);
        } catch (reportException $e) {
            $e->reportError();
        }
        break;
    case "get_all_users";
        // get profile to show description on the datatables on utente/model.php
        $profiles = $profile->get_profiles();
        break;

    case "autenticazione":
        // this method is called from autenticazione/model.php to check if the user's profile is active
        $profile->id_profile = $autenticazione->id_profile;
        $profileActive = $profile->get_utente_profile();
        break;
    case "toggle_profile_active":
        // get profile id from the session
        $profile->id_profile = $requestBody['id_profile'];
        try {
             // get the profile attivo value
            $attivo = $profile->get_profile_attivo();
            if (isset($attivo['catchError'])) {
                // report a try catch error on database
                throw new reportException($attivo['catchError'], 500);
            } 
            // set the profile attivo value
            $profile->attivo = $attivo['attivo'];
            // toggle the value into database
            $result = $profile->toggle_profile_attivo();
            if (isset($result['catchError'])) {
                // report a try catch error on database
                throw new reportException($result['catchError'], 500);
            } 
            // report the success message
            if ($profile->attivo) {
                $data['message'] = "L'profilo è disabilitato";
            } else {
                $data['message'] = "L'profilo è attivo";
            }
            $data['state'] = 'Success';
            $data['code'] = 200;
            echo json_encode($data);
        } catch (reportException $e) {
            $e->reportError();
        }
        break;

    default:
        # code...
        break;
}
