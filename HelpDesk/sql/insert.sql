INSERT INTO `utente` (stato, password, email, ruolo, nome, cognome, piano) VALUES
('attivo', 'password1', 'alexandru.baidoctheodor@itiszuccante.edu.it', 'studente', 'Alexandru Baidoc Theodor', 'Baidoc Theodor', NULL),
('attivo', 'password2', 'luca.bussetto@itiszuccante.edu.it', 'studente', 'Luca', 'Busetto', NULL),
('attivo', 'password3', 'alex.chenlyseng@itiszuccante.edu.it', 'studente', 'Alex', 'Chen Ly Seng', NULL),
('attivo', 'password4', 'alessandro.corliano@itiszuccante.edu.it', 'studente', 'Alessandro', 'Corlianò', NULL),
('attivo', 'password5', 'riccardo.costantini@itiszuccante.edu.it', 'studente', 'Riccardo', 'Costantini', NULL),
('attivo', 'password6', 'diego.denunzio@itiszuccante.edu.it', 'studente', 'Diego', 'De nunzio', NULL),
('attivo', 'password7', 'michele.diserio@itiszuccante.edu.it', 'studente', 'Michele', 'Di Serio', NULL),
('attivo', 'password8', 'giacomo.esposito@itiszuccante.edu.it', 'studente', 'Giacomo', 'Esposito', NULL),
('attivo', 'password9', 'francesco.fassino@itiszuccante.edu.it', 'studente', 'Francesco', 'Fassino', NULL),
('attivo', 'password10', 'marco.isandelli@itiszuccante.edu.it', 'studente', 'Marco', 'Isandelli', NULL),
('attivo', 'password11', 'massimiliano.marangon@itiszuccante.edu.it', 'studente', 'Massimiliano', 'Marangon', NULL),
('attivo', 'password12', 'matteo.piazzon@itiszuccante.edu.it', 'studente', 'Matteo', 'Piazzon', NULL),
('attivo', 'password13', 'agnese.ponga@itiszuccante.edu.it', 'studente', 'Agnese', 'Ponga', NULL),
('attivo', 'password14', 'simone.riggio@itiszuccante.edu.it', 'studente', 'Simone', 'Riggio', NULL),
('attivo', 'password15', 'filippo.schierato@itiszuccante.edu.it', 'studente', 'Filippo', 'Schierato', NULL),
('attivo', 'password16', 'giulio.semenzato@itiszuccante.edu.it', 'studente', 'Giulio', 'Semenzato', NULL),
('attivo', 'password17', 'carlotta.serena@itiszuccante.edu.it', 'studente', 'Carlotta', 'Serena', NULL),
('attivo', 'password18', 'andrea.sponchiado@itiszuccante.edu.it', 'studente', 'Andrea', 'Sponchiado', NULL),
('attivo', 'password19', 'matteo.valerii@itiszuccante.edu.it', 'studente', 'Matteo', 'Valerii', NULL),
('attivo', 'password20', 'andrea.verdicchio@itiszuccante.edu.it', 'amministratore', 'Andrea', 'Verdicchio', NULL),
('attivo', 'password21', 'andrea.vernole@itiszuccante.edu.it', 'studente', 'Andrea', 'Vernole', NULL),
('attivo', 'password22', 'davide.yeh@itiszuccante.edu.it', 'studente', 'Davide', 'Yeh', NULL),
('attivo', 'password1', 'fiorenzo.donofrio@itiszuccante.edu.it', 'tecnico', 'Fiorenzo', 'D Onofrio', 'primo piano'),
('attivo', 'password1', 'massimo.ballin@itiszuccante.edu.it', 'tecnico', 'Massimo', 'Ballin', 'piano terra'),
('attivo', 'password1', 'gianluca.masetti@itiszuccante.edu.it', 'tecnico', 'Gianluca', 'Masetti', 'secondo piano'),
('attivo', 'password1', 'matteo.baldan@itiszuccante.edu.it', 'tecnico', 'Matteo', 'Baldan', 'piano terra');

INSERT INTO aula (numero, piano, nome, tipo) VALUES
(7, 'piano terra', '3IA', 'classe'),
(8, 'piano terra', '4IA', 'classe'),
(9, 'piano terra', '5IA', 'classe'),
(10, 'piano terra', '3IB', 'classe'),
(11, 'piano terra', '4IB', 'classe'),
(12, 'piano terra', '5IB', 'classe'),
(13, 'piano terra', 'Aula relax', 'aula'),
(14, 'piano terra', '4ID', 'classe'),
(15, 'piano terra', '3IE', 'classe'),
(null, 'piano terra', 'LAS', 'laboratorio'),
(null, 'piano terra', 'LLM', 'laboratorio'),
(null, 'piano terra', 'LAP2', 'laboratorio'),
(null, 'piano terra', 'LAM', 'laboratorio'),
(null, 'piano terra', 'Wc', 'wc'),
(null, 'piano terra', 'WC docenti', 'wc'),
(null, 'piano terra', 'LASA', 'laboratorio'),
(null, 'piano terra', 'Aula magna', 'aula'),
(null, 'piano terra', 'OEN1', 'laboratorio'),
(null, 'piano terra', 'Palestra', 'palestra'),
(null, 'piano terra', 'Spogliatoio maschi', 'spogliatoio'),
(null, 'piano terra', 'Spogliatoio femmine', 'spogliatoio'),
(21, 'primo piano', '4EA-TA', 'classe'),
(22, 'primo piano', '4TA', 'classe'),
(23, 'primo piano', '5EA-TA', 'classe'),
(24, 'primo piano', '5TA', 'classe'),
(25, 'primo piano', '3EA', 'classe'),
(26, 'primo piano', '4AB', 'classe'),
(27, 'primo piano', '5AB', 'classe'),
(28, 'primo piano', '4IC', 'classe'),
(29, 'primo piano', '3IC', 'classe'),
(30, 'primo piano', '5IC', 'classe'),
(31, 'primo piano', '3AA', 'classe'),
(32, 'primo piano', '4AA', 'classe'),
(33, 'primo piano', '5AA', 'classe'),
(null, 'primo piano', 'Musica', 'aula'),
(null, 'primo piano', 'Wc', 'wc'),
(null, 'primo piano', 'Wc', 'wc'),
(20, 'primo piano', 'Sala lettura', 'aula'),
(19, 'primo piano', 'Deposito libri', 'aula'),
(34, 'primo piano', 'Aula insegnanti', 'aula'),
(36, 'primo piano', 'LAP1', 'laboratorio'),
(37, 'primo piano', 'Locale A.T. LAP1', 'assistente tecnico'),
(38, 'primo piano', 'CIM | Sala Server', 'sala server'),
(40, 'primo piano', 'OEN2', 'laboratorio'),
(42, 'primo piano', 'Aula Emergenze', 'aula'),
(null, 'primo piano', 'Wc', 'wc'),
(null, 'primo piano', 'Wc', 'wc'),
(null, 'primo piano', 'Bar', 'bar'),
(53, 'secondo piano', 'LEN5', 'laboratorio'),
(51, 'secondo piano', 'LEN4', 'laboratorio'),
(43, 'secondo piano', 'Ufficio tecnico ', 'assistente tecnico'),
(46, 'secondo piano', 'Segreteria Didattica', 'ufficio'),
(44, 'secondo piano', 'PCTO', 'ufficio'),
(52, 'secondo piano', 'Locale A.T.', 'assistente tecnico'),
(48, 'secondo piano', 'Vice Presidenza', 'ufficio'),
(47, 'secondo piano', 'Presidenza', 'ufficio'),
(null, 'secondo piano', 'Locale', 'assistente tecnico'),
(null, 'secondo piano', 'Ufficio DSGA', 'ufficio'),
(45, 'secondo piano', 'Ufficio Personale ', 'ufficio'),
(50, 'secondo piano', 'Magazzino', 'magazzino');

INSERT INTO dispositivo (dispositivo_id, aula_id, tipo) VALUES
('LAS-WS01', 10, 'scolastico'),
('LAS-WS02', 10, 'scolastico'),
('LAS-WS03', 10, 'scolastico'),
('LAS-WS04', 10, 'scolastico'),
('LAS-WS05', 10, 'scolastico'),
('LAS-WS06', 10, 'scolastico'),
('LAS-WS07', 10, 'scolastico'),
('LAS-WS08', 10, 'scolastico'),
('LAS-WS09', 10, 'scolastico'),
('LAS-WS10', 10, 'scolastico'),
('LAS-WS11', 10, 'scolastico'),
('LAS-WS12', 10, 'scolastico'),
('LAS-WS13', 10, 'scolastico'),
('LAS-WS14', 10, 'scolastico'),
('LAS-WS15', 10, 'scolastico'),
('LAS-WS16', 10, 'scolastico'),
('LAS-WS17', 10, 'scolastico'),
('LAS-WS18', 10, 'scolastico'),
('LAS-WS19', 10, 'scolastico'),
('LAS-WS20', 10, 'scolastico'),
('LAS-WS21', 10, 'scolastico'),
('LAS-WS22', 10, 'scolastico'),
('LAS-WS23', 10, 'scolastico'),
('LAS-WS24', 10, 'scolastico'),
('LAS-WS25', 10, 'scolastico'),
('LAS-WS26', 10, 'scolastico'),
('LAS-WS27', 10, 'scolastico'),
('LAS-WS28', 10, 'scolastico'),
('LAS-WS29', 10, 'scolastico'),
('LAS-WS30', 10, 'scolastico'),
('LAS-WS31', 10, 'scolastico'),
('OEN1-WS01', 18, 'scolastico'),
('OEN1-WS02', 18, 'scolastico'),
('OEN1-WS03', 18, 'scolastico'),
('OEN1-WS04', 18, 'scolastico'),
('OEN1-WS05', 18, 'scolastico'),
('OEN1-WS06', 18, 'scolastico'),
('OEN1-WS07', 18, 'scolastico'),
('OEN1-WS08', 18, 'scolastico'),
('OEN1-WS09', 18, 'scolastico'),
('OEN1-WS10', 18, 'scolastico'),
('OEN1-WS11', 18, 'scolastico'),
('OEN1-WS12', 18, 'scolastico'),
('OEN1-WS13', 18, 'scolastico'),
('OEN1-WS14', 18, 'scolastico'),
('OEN1-WS15', 18, 'scolastico'),
('OEN1-WS16', 18, 'scolastico'),
('OEN1-WS17', 18, 'scolastico'),
('OEN1-WS18', 18, 'scolastico'),
('OEN1-WS19', 18, 'scolastico'),
('OEN1-WS20', 18, 'scolastico'),
('OEN1-WS21', 18, 'scolastico'),
('OEN1-WS22', 18, 'scolastico'),
('OEN1-WS23', 18, 'scolastico'),
('OEN1-WS24', 18, 'scolastico'),
('OEN1-WS25', 18, 'scolastico'),
('OEN1-WS26', 18, 'scolastico'),
('OEN1-WS27', 18, 'scolastico'),
('OEN1-WS28', 18, 'scolastico'),
('OEN1-CAT01', 18, 'scolastico'),
('OEN1-CAT02', 18, 'scolastico'),
('LAP2-WS01', 12, 'scolastico'),
('LAP2-WS02', 12, 'scolastico'),
('LAP2-WS03', 12, 'scolastico'),
('LAP2-WS04', 12, 'scolastico'),
('LAP2-WS05', 12, 'scolastico'),
('LAP2-WS06', 12, 'scolastico'),
('LAP2-WS07', 12, 'scolastico'),
('LAP2-WS08', 12, 'scolastico'),
('LAP2-WS09', 12, 'scolastico'),
('LAP2-WS10', 12, 'scolastico'),
('LAP2-WS11', 12, 'scolastico'),
('LAP2-WS12', 12, 'scolastico'),
('LAP2-WS13', 12, 'scolastico'),
('LAP2-WS14', 12, 'scolastico'),
('LAP2-WS15', 12, 'scolastico'),
('LAP2-WS16', 12, 'scolastico'),
('LAP2-WS17', 12, 'scolastico'),
('LAP2-WS18', 12, 'scolastico'),
('LAP2-WS19', 12, 'scolastico'),
('LAP2-WS20', 12, 'scolastico'),
('LAP2-WS21', 12, 'scolastico'),
('LAP2-WS22', 12, 'scolastico'),
('LAP2-WS23', 12, 'scolastico'),
('LAP2-WS24', 12, 'scolastico'),
('LAP2-WS25', 12, 'scolastico'),
('LAP2-WS26', 12, 'scolastico'),
('LAP2-WS27', 12, 'scolastico'),
('LAP2-WS28', 12, 'scolastico'),
('LAP2-WS29', 12, 'scolastico'),
('LAP2-WS30', 12, 'scolastico'),
('LLM-WS01', 11, 'scolastico'),
('LLM-WS02', 11, 'scolastico'),
('LLM-WS03', 11, 'scolastico'),
('LLM-WS04', 11, 'scolastico'),
('LLM-WS05', 11, 'scolastico'),
('LLM-WS06', 11, 'scolastico'),
('LLM-WS07', 11, 'scolastico'),
('LLM-WS08', 11, 'scolastico'),
('LLM-WS09', 11, 'scolastico'),
('LLM-WS10', 11, 'scolastico'),
('LLM-WS11', 11, 'scolastico'),
('LLM-WS12', 11, 'scolastico'),
('LLM-WS13', 11, 'scolastico'),
('LLM-WS14', 11, 'scolastico'),
('LLM-WS15', 11, 'scolastico'),
('LLM-WS16', 11, 'scolastico'),
('LLM-WS17', 11, 'scolastico'),
('LLM-WS18', 11, 'scolastico'),
('LLM-WS19', 11, 'scolastico'),
('LLM-WS20', 11, 'scolastico'),
('LLM-WS21', 11, 'scolastico'),
('LLM-WS22', 11, 'scolastico'),
('LLM-WS23', 11, 'scolastico'),
('LLM-WS24', 11, 'scolastico'),
('LLM-WS25', 11, 'scolastico'),
('LLM-WS26', 11, 'scolastico'),
('LLM-WS27', 11, 'scolastico'),
('LLM-WS28', 11, 'scolastico'),
('CAT01', 11, 'scolastico'),
('WS01-3IA', 1, 'scolastico'),
('WS01-4IA', 2, 'scolastico'),
('WS01-5IA', 3, 'scolastico'),
('WS01-3IB', 4, 'scolastico'),
('WS01-4IB', 5, 'scolastico'),
('WS01-5IB', 6, 'scolastico'),
('WS01-Aula relax', 7, 'scolastico'),
('WS01-4ID', 8, 'scolastico'),
('WS01-3IE', 9, 'scolastico'),
('WS01-Aula magna', 17, 'scolastico'),
('WS01-4EA-TA', 22, 'scolastico'),
('WS01-4TA', 23, 'scolastico'),
('WS01-5EA-TA', 24, 'scolastico'),
('WS01-5TA', 25, 'scolastico'),
('WS01-3EA', 26, 'scolastico'),
('WS01-4AB', 27, 'scolastico'),
('WS01-5AB', 28, 'scolastico'),
('WS01-4IC', 29, 'scolastico'),
('WS01-3IC', 30, 'scolastico'),
('WS01-5IC', 31, 'scolastico'),
('WS01-3AA', 32, 'scolastico'),
('WS01-4AA', 33, 'scolastico'),
('WS01-5AA', 34, 'scolastico'),
('WS01-Musica', 35, 'scolastico'),
('WS01-Sala lettura', 38, 'scolastico'),
('WS01-Deposito libri', 39, 'scolastico'),
('WS01-Aula insegnanti', 40, 'scolastico'),
('WS01-Locale A.T. LAP1', 42, 'scolastico'),
('WS01-CIM | Sala Server', 43, 'scolastico'),
('WS01-Aula Emergenze', 45, 'scolastico'),
('WS01-Ufficio tecnico', 51, 'scolastico'),
('WS01-Segreteria Didattica', 52, 'scolastico'),
('WS01-PCTO', 53, 'scolastico'),
('WS01-Locale A.T.', 54, 'scolastico'),
('WS01-Vice Presidenza', 55, 'scolastico'),
('WS01-Presidenza', 56, 'scolastico'),
('WS01-Locale', 57, 'scolastico'),
('WS01-Ufficio DSGA', 58, 'scolastico'),
('WS01-Ufficio Personale', 59, 'scolastico');
