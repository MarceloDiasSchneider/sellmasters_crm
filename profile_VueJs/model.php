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

include_once('profile_class.php');
$profile = new profileClass();

switch ($requestBody['action']) {
    case "insert_or_update_profile":
        // get the code session to verify if is the same 
        $form = $requestBody['codiceSessione'];
        $session = $_SESSION['codiceSessione'];

        // if the code session does not match, unauthorized the insert or update
        if ($form != $session) {
            $data['state'] = 'Unauthorized';
            $data['code'] = '406';
            $data['message'] = 'Session code doesn\'t match';

            echo json_encode($data);
            exit;
        }

        switch ($requestBody['id_profile']) {
            case null: // insert new profile
                // set the value to the description
                $profile->descrizione = $requestBody['description'];
                // check id this description is already used
                $description = $profile->check_description();
                if (isset($description['catchError'])) {
                    // report a try catch error
                    $data['code'] = '500';
                    $data['state'] = 'Internal Server Error';
                    $data['message'] = $description['catchError'];
                } else if (isset($description['descrizione'])) {
                    // report that the description is already used
                    $data['code'] = '401';
                    $data['state'] = 'Unauthorized';
                    $data['message'] = 'Descrizione già registrata';
                } else {
                    // execute the insert of new profile
                    $id_profile = $profile->insert_profile();
                    if (isset($id_profile['catchError'])) {
                        // report a try catch error
                        $data['code'] = '500';
                        $data['state'] = 'Internal Server Error';
                        $data['message'] = $id_profile['catchError'];
                    } else if($id_profile > 0) {
                        // call a method in pages_VueJs/model.php to bind all pages
                        include_once('../pages_VueJs/model.php');
                    }
                }
                echo json_encode($data);

                break;

            case true: // update a profile
                $profile->id_profile = $requestBody['id_profile'];
                $profile->descrizione = $requestBody['description'];
                // check if the new description is alread used
                $description = $profile->check_description();
                if (isset($description['catchError'])) {
                    // report a try catch error
                    $data['code'] = '500';
                    $data['state'] = 'Internal Server Error';
                    $data['message'] = $description['catchError'];
                } else if (isset($description['descrizione'])) {
                    // report that the description is already used
                    $data['code'] = '401';
                    $data['state'] = 'Unauthorized';
                    $data['message'] = 'Descrizione già registrata';
                } else {
                    // execute the updated
                    $result = $profile->update_profile();
                    if (isset($result['catchError'])) {
                        // report a try catch error
                        $data['code'] = '500';
                        $data['state'] = 'Internal Server Error';
                        $data['message'] = $result['catchError'];
                    } else if ($result > 0) {
                        // check if the profile was updated successfully
                        $data['state'] = 'Success';
                        $data['code'] = '200';
                        $data['message'] = 'profile aggiornato';
                    } else {
                        $data['code'] = '400';
                        $data['state'] = 'Bad request';
                        $data['message'] = 'Utente non aggiornato';
                    }
                }
                echo json_encode($data);
                break;

            default:
                # code...
                break;

                echo json_encode($data);
        }
        break;
    case "get_profiles":
        // get profile to set option on the form | called from utente/model.php
        $profiles = $profile->get_profiles();
        if (isset($profiles['catchError'])) {
            // report a try catch error
            $data['code'] = '500';
            $data['state'] = 'Internal Server Error';
            $data['message'] = $profiles['catchError'];
        } else {
            // return the data
            $data['code'] = '200';
            $data['state'] = 'Success';
            $data['message'] = 'All options was found';
            $data['profiles'] = $profiles;
        }
        echo json_encode($data);
        break;

    case "get_profile_data":
        $profile->id_profile = $requestBody['id_profile'];
        $profile = $profile->get_profile_data();
        if (isset($profile['catchError'])) {
            // report a try catch error
            $data['code'] = '500';
            $data['state'] = 'Internal Server Error';
            $data['message'] = $profile['catchError'];
        } else {
            // call a method in pages_VueJs/model.php 
            include_once('../pages_VueJs/model.php');
        }
        echo json_encode($data);
        break;   

    case 'pages_permission': 
        // call a pages method that return all pages
        include_once('../pages_VueJs/model.php');
        if(isset($pages['catchError'])){
            // report a try catch error
            $data['code'] = '500';
            $data['state'] = 'Internal Server Error';
            $data['message'] = $pages['catchError'];
        } else {
            $data['pages'] = $pages;
            $data['code'] = '200';
            $data['id'] = $requestBody['id_profile'];
        }
        // switch ($requestBody['id_profile']) {
        //     case null: // get default pages
                
        //         break;
        //     case true: // get the pages setting of profile

        //         break;
        //     default:
        //         # code...
        //         break;
        // }

        echo json_encode($data);

        break;

    case "get_all_users";
        // get profile to show description on the datatables on utente/model.php
        $profiles = $profile->get_profiles();
        break;

    case "autenticazione":
        // this class is called from autenticazione/model.php to verify if the user has permission
        $profile->id_profile = $autenticazione->id_profile;
        // devo ir para access_profile
        // pego as paginas e retono a autenticazione/model.php
        $permissione = $profile->get_utente_profile();
        break;

    default:
        # code...
        break;
}
