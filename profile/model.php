<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

include_once('profile_class.php');
$profile = new profileClass();

$action = $_REQUEST['action'];
// echo __LINE__. $action;
switch ($action) {
    case "get_profiles":

        // get profile to set option on the form | called from utente/model.php
        $profiles = $profile->get_profiles();
        if (isset($profiles['catchError'])){
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

    case "get_utenti";

        // get profile to show description on the datatables on utente/model.php
        $profiles = $profile->get_profiles();
        break;

    case "autenticazione":
        // this class is called from autenticazione/model.php to verify if the user has permission
        $profile->id_profile = $autenticazione->id_profile;
        $permissione = $profile->get_utente_profile();

    default:
        # code...
        //echo "not passing";
        break;
}
