CREATE TABLE utente (
	utente_id INT AUTO_INCREMENT PRIMARY KEY,
	stato ENUM('attivo', 'bannato') DEFAULT 'attivo' NOT NULL,
	password varchar(255) NOT NULL,
	email varchar(255) NOT NULL,
	ruolo ENUM('studente', 'tecnico', 'amministratore', 'ata') DEFAULT 'studente' NOT NULL,
	nome varchar(255) NOT NULL,
	cognome varchar(255) NOT NULL,
	piano ENUM('piano terra', 'primo piano', 'secondo piano')
);

CREATE TABLE aula (
	aula_id int AUTO_INCREMENT PRIMARY KEY,
	numero int,
	piano ENUM('piano terra', 'primo piano', 'secondo piano') NOT NULL,
	nome varchar(255),
	tipo ENUM('classe', 'laboratorio', 'wc', 'palestra', 'spogliatoio', 'ufficio', 'assistente tecnico', 'bar', 'aula', 'sala server', 'magazzino') NOT NULL
);

CREATE TABLE dispositivo (
	dispositivo_id varchar(255) PRIMARY KEY,
    	aula_id int,
	tipo ENUM('scolastico', 'portatile') NOT NULL,
	FOREIGN KEY (aula_id) REFERENCES aula(aula_id)
);

CREATE TABLE segnalazione (
	segnalazione_id int AUTO_INCREMENT PRIMARY KEY,
	utente_id int,
    	dispositivo_id varchar(255),
    	aula_id int,
	data_ultima_modifica datetime NOT NULL,
	data_creazione datetime NOT NULL,
	titolo varchar(255) NOT NULL,
	descrizione text NOT NULL,
	stato ENUM('in attesa', 'fallita', 'completata') DEFAULT 'in attesa' NOT NULL,
	tipo ENUM('Tecnici', 'ATA') NOT NULL,
	FOREIGN KEY (utente_id) REFERENCES utente(utente_id),
	FOREIGN KEY (dispositivo_id) REFERENCES dispositivo(dispositivo_id),
	FOREIGN KEY (aula_id) REFERENCES aula(aula_id)
);

CREATE TABLE alert (
	alert_id int AUTO_INCREMENT PRIMARY KEY,
	utente_id int,
	aula_id int,
	dispositivo_id varchar(255),
	FOREIGN KEY (utente_id) REFERENCES utente(utente_id),
	FOREIGN KEY (aula_id) REFERENCES aula(aula_id),
	FOREIGN KEY (dispositivo_id) REFERENCES dispositivo(dispositivo_id)
);

CREATE TABLE notifica (
	notifica_id int AUTO_INCREMENT PRIMARY KEY,
	segnalazione_id int,
	FOREIGN KEY (segnalazione_id) REFERENCES segnalazione(segnalazione_id)
);

CREATE TABLE notifiche_utente (
	notifiche_utente int AUTO_INCREMENT PRIMARY KEY,
	notifica_id int,
	utente_id int,
	FOREIGN KEY (utente_id) REFERENCES utente(utente_id),
	FOREIGN KEY (notifica_id) REFERENCES notifica(notifica_id)
);
