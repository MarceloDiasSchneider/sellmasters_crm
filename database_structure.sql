CREATE TABLE `pages` (
    `id_page` int(11) NOT NULL AUTO_INCREMENT,
    `main` VARCHAR(100) NOT NULL,
    `subpage` VARCHAR(100) NOT NULL,
    `link` VARCHAR(100) NOT NULL,
    PRIMARY KEY (`id_page`),
    UNIQUE KEY `nodouble` (`subpage`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

###

CREATE TABLE `profiles`(
	`id_profile` INT AUTO_INCREMENT,
    `descrizione` VARCHAR(20) NOT NULL,
	PRIMARY KEY (`id_profile`),
    UNIQUE KEY `nodouble` (`descrizione`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

ALTER TABLE `profiles`
ADD `attivo` INT NOT NULL;

###

CREATE TABLE `access_profile` (
    `id_access_profile` int(11) NOT NULL AUTO_INCREMENT,
    `id_page` INT NOT NULL,
    `id_profile` INT NOT NULL,
    `access` INT(2) DEFAULT '0',
    PRIMARY KEY (`id_access_profile`),
    UNIQUE KEY `nodouble` (`id_profile`,`id_page`),
	FOREIGN KEY (`id_page`) REFERENCES pages(`id_page`),
    FOREIGN KEY (`id_profile`) REFERENCES profiles(`id_profile`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

###

CREATE TABLE `utenti`(
	`id_utente` INT AUTO_INCREMENT,
    `nome` VARCHAR(30) NOT NULL,
	`cognome` VARCHAR(30),
    `email` VARCHAR(50) UNIQUE NOT NULL,
	`codice_fiscale` VARCHAR(16),
	`telefono` VARCHAR(16),
	`data_nascita` DATE,
    `password` VARCHAR(255) NOT NULL,
    `id_profile` INT NOT NULL,
    `attivo` INT NOT NULL,
	PRIMARY KEY (`id_utente`)   
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

ALTER TABLE utenti
ADD codice_recupera VARCHAR(15),
ADD scadenza TIMESTAMP;

###

CREATE TABLE `accede_logs`(
	`id_log` INT AUTO_INCREMENT,
    `id_utente` INT NOT NULL,
	`datatime` TIMESTAMP NOT NULL,
    `ip_server` VARCHAR(255) NOT NULL,
	`remote_port` INT,
    `user_agent` VARCHAR(255),
	PRIMARY KEY (`id_log`),
	FOREIGN KEY (`id_utente`) REFERENCES utenti(`id_utente`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

###

CREATE TABLE `merchants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(45) DEFAULT NULL,
  `merchant_id` varchar(45) DEFAULT NULL,
  `mws` varchar(450) DEFAULT NULL,
  `interval_between_check` int(2) DEFAULT '24',
  `indirizzo` varchar(100) DEFAULT NULL,
  `telefono` varchar(45) DEFAULT NULL,
  `nome_sociale` varchar(100) DEFAULT NULL,
  `stato` varchar(45) DEFAULT NULL,
  `citta` varchar(45) DEFAULT NULL,
  `numero_civico` varchar(45) DEFAULT NULL,
  `cap` varchar(45) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `nome_contatto` varchar(100) DEFAULT NULL,
  `provincia` varchar(45) DEFAULT NULL,
  `attivo` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nodouble` (`nome`,`merchant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;