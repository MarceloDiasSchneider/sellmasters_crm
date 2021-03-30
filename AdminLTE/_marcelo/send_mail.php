<?php
// The message
$message = "
<html>
    <p>Fare clic <a href='gasfacil.app.br/teste/autenticazione/change-password.php?code=123'>qui</a> per modificare la password</p>
</html>    
    ";

echo mail('marcelo.d.schneider@gasfacil.app.br', 'Uma mensagem', $message);
