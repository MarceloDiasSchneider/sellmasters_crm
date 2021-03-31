<?php

class autenticazioneClass
{
    public $email;
    public $password;
    public $id_utente;
    public $nome;
    public $id_livello;
    public $codice;
    public $scadenza;

    // possibility of characters to generate a code
    // this method is used to verify the session or when user request a link to reset the passwor 
    private $possibilita = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz!@$%&*()_-\/|";

    public $database;

    function __construct()
    {
        // Conect with the database
        include_once("../connessione/database_pdo_sing.php");
        $obj = DatabasePdoClass::getInstance();
        $this->database = $obj->creaConnessione();

        // Star the session if is not started
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public function crypt_password()
    {
        /* crypt the password */
        $salt = '$2a$09$iWHICrsXJA0JvEjJdUri5p';
        $this->password = crypt($this->password, $salt);
    }

    public function random_code()
    {
        // this method is used to verify the session or when user request a link to reset the passwor 
        $code = "";
        for ($i = 1; $i <= 15; $i++) {
            $code .= substr($this->possibilita, rand(1, strlen($this->possibilita)) - 1, 1);
        }
        $this->codice = $code;
    }

    public function verifica_accesso()
    {
        /* verify if user is registered */
        try {
            $query = $this->database->prepare("SELECT `id_utente`, `nome`, `id_livello`, attivo FROM `utenti` WHERE `email` = :email AND `password` = :cryptedPassword");
            $query->bindValue(":email", $this->email);
            $query->bindValue(":cryptedPassword", $this->password);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // report a error to the user
            $result['catchError'] = 'code => ' . $e->getCode() . ' | message => ' . $e->getMessage();
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }

        return $result;
    }

    public function check_email()
    {
        // check if the email is registred
        try {
            $query = $this->database->prepare("SELECT email FROM `utenti` WHERE email = :email");
            $query->bindValue(":email", $this->email);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $result['catchError'] = 'code => ' . $e->getCode() . ' | message => ' . $e->getMessage();
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }

        return $result;
    }

    public function forgot_password()
    {
        try {
            $query = $this->database->prepare("UPDATE utenti 
                SET codice_recupera = :codice_recupera, scadenza = :scadenza
                WHERE email = :email");
            $query->bindValue(":codice_recupera", $this->codice);
            $query->bindValue(":scadenza", $this->scadenza);
            $query->bindValue(":email", $this->email);
            $result = $query->execute();
        } catch (PDOException $e) {
            $result['catchError'] = 'code => ' . $e->getCode() . ' | message => ' . $e->getMessage();
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }

        return $result;
    }

    public function send_email()
    {
        // The headers to set some configuration
        $headers = "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: marcelo.d.schneider@gasfacil.app.br\r\n";
        $headers .= "Reply-To: marcelo.d.schneider@gasfacil.app.br\r\n";

        // The subject of the mail
        $subject = 'Sell Masters - Recupero di password';
        // The message
        $message = "
        <!DOCTYPE html>
        <html lang=\"en\">
        <head>
            <meta charset=\"UTF-8\">
            <title>Recupero di password</title>
        </head>
        <body>
            <p>Fare clic <a href=\"gasfacil.app.br/teste/autenticazione/recupera-password.php?email=$this->email&code=$this->codice\">qui</a> per modificare la password</p>
        </body>
        </html>";
        $result = mail($this->email, $subject, $message, $headers);

        return $result;
    }

    public function check_code_email()
    {
        try {
            $query = $this->database->prepare("SELECT id_utente, scadenza FROM `utenti` 
            WHERE email = :email AND codice_recupera = :codice");
            $query->bindValue(":email", $this->email);
            $query->bindValue(":codice", $this->codice);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $result['catchError'] = 'code => ' . $e->getCode() . ' | message => ' . $e->getMessage();
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }

        return $result;
    }

    public function update_password()
    {
        try {
            $query = $this->database->prepare("UPDATE `utenti` 
            SET `password` = :password, codice_recupera = null
            WHERE id_utente = :id_utente AND email = :email AND codice_recupera = :codice");
            $query->bindValue(":id_utente", $this->id_utente);
            $query->bindValue(":password", $this->password);
            $query->bindValue(":email", $this->email);
            $query->bindValue(":codice", $this->codice);
            $query->execute();
            $result = $query->rowCount();
        } catch (PDOException $e) {
            $result['catchError'] = 'code => ' . $e->getCode() . ' | message => ' . $e->getMessage();
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }

        return $result;
    }

    public function chiude_sessione()
    {
        session_start();
        session_destroy();
    }
}
