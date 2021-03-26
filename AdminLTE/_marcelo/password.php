<?php 
    $password = "Marcelo7";
    $salt = 'QLd9k@&l^atBpqM';
    $cryptPassword = crypt($password, $salt);
    echo $password . '<br>';
    echo $salt . '<br>';
    echo $cryptPassword;