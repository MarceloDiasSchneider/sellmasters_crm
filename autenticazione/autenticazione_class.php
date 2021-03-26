<?php

class autenticazioneClass
{
    public $email;
    public $password;
    public $id_utente;
    public $nome;
    public $id_livello;
    // public $permissione;

    /* possibilità di caratteri per generare il codice di sessione */
    private $possibilita = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz!@#$%&*()_-\/|";

    public $database;

    function __construct()
    {
        include_once("../connessione/database_pdo_sing.php");
        $obj = DatabasePdoClass::getInstance();
        $this->database = $obj->creaConnessione();

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public function crypt_password($password)
    {
        /* fa la crittografia della password */
        $salt = 'QLd9k@&l^atBpqM';
        $cryptPassword = crypt($password, $salt);

        return $cryptPassword;
    }

    public function codice_sessione()
    {
        /* crea il codice di sessione per confrontare ogni richiesta */
        $codice = "";
        for ($i = 1; $i <= 10; $i++) {
            $codice .= substr($this->possibilita, rand(1, strlen($this->possibilita)) - 1, 1);
        }

        return $codice;
    }

    public function verifica_accesso()
    {
        /* controlla le credenziali dell'utente */
        try {
            $verifica = $this->database->prepare("SELECT `id_utente`, `nome`, `id_livello` FROM `utenti` WHERE `email` = :email AND `password` = :cryptedPassword AND `attivo` = :attivo");
            $verifica->bindValue(":email", $this->email);
            $verifica->bindValue(":cryptedPassword", $this->password);
            $verifica->bindValue(":attivo", 1);
            $verifica->execute();
            $resultUtente = $verifica->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Errore" . __LINE__ . __FILE__ . __FUNCTION__ . " errore " . $e->getMessage(), 3, "/var/www/html/sellma_crm/sellmaster_errors.log");
        }

        /* Verifica se é trovato un utente */
        if (isset($resultUtente['id_utente'])) {
            $this->id_utente = $resultUtente['id_utente'];
            $this->nome = $resultUtente['nome'];
            $this->id_livello = $resultUtente['id_livello'];

            $data['code'] = '200';
            $data['state'] = 'success';
            $data['message'] = 'Email e password trovato';

            return $data;
        } else {
            // Messaggio di errore se autenticazione non é riuscita
            $data['code'] = '401';
            $data['state'] = 'error';
            $data['message'] = 'Email o password errate';

            return $data;
        }
    }

    public function chiude_sessione()
    {
        session_start();
        session_destroy();
    }
}
