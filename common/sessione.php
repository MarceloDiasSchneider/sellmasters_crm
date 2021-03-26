<?php
session_start();
$_SESSION['started'] ?: header('Location: ../autenticazione');
// echo '<pre>';
// var_dump($_SESSION);
// echo '</pre>';