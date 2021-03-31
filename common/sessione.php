<?php
session_start();
if (!$_SESSION['id_utente']){
    header('Location: ../autenticazione');
}
// echo '<pre>';
// var_dump($_SESSION);
// echo '</pre>';