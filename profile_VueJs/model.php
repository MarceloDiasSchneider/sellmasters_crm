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
                $profile->attivo = $requestBody['active'];
                // check if this description is already used
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
                $description = $profile->check_description_others();
                if (isset($description['catchError'])) {
                    // report a try catch error
                    $data['code'] = '500';
                    $data['state'] = 'Internal Server Error';
                    $data['message'] = $description['catchError'];
                } elseif (isset($description['descrizione'])) {
                    // report that the description is already used
                    $data['code'] = '401';
                    $data['state'] = 'Unauthorized';
                    $data['message'] = 'Descrizione già registrata';
                } else {
                    $description = $profile->check_description_self();
                    if (isset($description['catchError'])) {
                        // report a try catch error
                        $data['code'] = '500';
                        $data['state'] = 'Internal Server Error';
                        $data['message'] = $description['catchError'];
                    } elseif (!isset($description['descrizione'])) {
                        // execute the profile update 
                        $result = $profile->update_profile();
                        if (isset($result['catchError'])) {
                            // report a try catch error
                            $data['code'] = '500';
                            $data['state'] = 'Internal Server Error';
                            $data['message'] = $result['catchError'];

                            echo json_encode($data);
                            exit;
                        } else if ($result < 1) {
                            // report a try catch error
                            $data['code'] = '400';
                            $data['state'] = 'Bad request';
                            $data['message'] = 'Problema!! profilo non registrato';

                            echo json_encode($data);
                            exit;
                        }
                    } 
                    // call a method in pages_VueJs/model.php to bind all pages
                    include_once('../pages_VueJs/model.php');
                }
                echo json_encode($data);
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
            // report profile registred successfully
            $data['code'] = '200';
            $data['state'] = 'Success';
            $data['message'] = 'profile pronto per essere aggiornato';
            $data['profile'] = $profile['descrizione'];
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
            $data['code'] = '200';
            $data['state'] = 'Success';
            $data['message'] = 'All pages found';
            $data['pages'] = $pages;
        }

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
    case "toggle_profile_active":
        // get profile id from the session
        $profile->id_profile = $requestBody['id_profile'];
        // get the profile attivo value
        $attivo = $profile->get_profile_attivo();
        if (isset($attivo['catchError'])) {
            // report a try catch error
            $data['code'] = '500';
            $data['state'] = 'Internal Server Error';
            $data['message'] = $attivo['catchError'];
        } else {
            // set the profile attivo value
            $profile->attivo = $attivo['attivo'];
            // toggle the value into database
            $result = $profile->toggle_profile_attivo();
            if (isset($result['catchError'])) {
                // report a try catch error
                $data['code'] = '500';
                $data['state'] = 'Internal Server Error';
                $data['message'] = $result['catchError'];
            } else {
                // report the success message
                if ($profile->attivo) {
                    $data['message'] = "L'profilo è disabilitato";
                } else {
                    $data['message'] = "L'profilo è attivo";
                }
                $data['state'] = 'Success';
                $data['code'] = '200';
            }
        }
        echo json_encode($data);
        break;

    default:
        # code...
        break;
}
