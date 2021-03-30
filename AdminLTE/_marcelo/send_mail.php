<?php
// The message
$message = "Fare clic <a href='gasfacil.app.br/teste/autenticazione/change-password.php?code=123'>qui</a> per modificare la password";

echo mail('marcelo.d.schneider@gasfacil.app.br', 'Uma mensagem', $message);