<?php
// The message
$message = "Você esta recebendo uma mensagem ";

// In case any of our lines are larger than 70 characters, we should use wordwrap()
$message = wordwrap($message, 70, "\r\n");

// Send

echo mail('marcelo.d.schneider@gasfacil.app.br', 'Uma mensagem', $message);