USE `FoodTruckDB`;

-- Inserisce i nuovi utenti con password semplici e già criptate (hashate)
INSERT INTO `Utenti` (`email`, `password`, `ruolo`, `attivo`, `primo_ordine_effettuato`) VALUES
('cliente1@campus.it', '$2y$10$xH0Ub78satAdYHuUdb3LT.WQhygBufcSn0u7CED1vEy2rD.kWG/zm', 'cliente', TRUE, FALSE),
('venditore@campus.it', '2y$10$xH0Ub78satAdYHuUdb3LT.WQhygBufcSn0u7CED1vEy2rD.kWG/zm', 'venditore', TRUE, FALSE),
('prova@prova.it', '$2y$10$wI4p.h8v/2xG8h.Lg2f7a.Xg.S3h.9j.Zg.Yg.S3h.9j.Zg.Yg.S', 'cliente', TRUE, FALSE);

-- Popolamento Ingredienti
INSERT INTO `Ingredienti` (`nome`, `categoria_ingrediente`, `disponibile`) VALUES
('Pane Arabo', 'pane', TRUE),
('Ciabatta', 'pane', TRUE),
('Pane Integrale', 'pane', TRUE),
('Pane Rosetta', 'pane', TRUE),
('Pane al Carbone', 'pane', TRUE),
('Focaccia', 'pane', TRUE),
('Cotoletta', 'proteina', TRUE),
('Prosciutto Cotto', 'proteina', TRUE),
('Prosciutto Crudo', 'proteina', TRUE),
('Salame Nostrano', 'proteina', TRUE),
('Bresaola', 'proteina', TRUE),
('Salame Piccante', 'proteina', TRUE),
('Insalata', 'contorno', TRUE),
('Pomodoro', 'contorno', TRUE),
('Cipolla', 'contorno', TRUE),
('Rucola', 'contorno', TRUE),
('Pomodorini', 'contorno', TRUE),
('Maionese', 'salsa', TRUE),
('Ketchup', 'salsa', TRUE),
('Salsa BBQ', 'salsa', TRUE),
('Philadelphia', 'salsa', TRUE);

-- Popolamento Prodotti
INSERT INTO `Prodotti` (`nome`, `descrizione`, `prezzo`, `categoria`, `path_immagine`, `disponibile`) VALUES
('Panino con cotolettetta', 'pane arabo, cotoletta, insalata, maionese', 4.00, 'panino_predefinito', 'img/paninocotoletta.png', TRUE),
('Panino con cotto', 'pane ciabatta, cotto, fontina', 3.50, 'panino_predefinito', 'img/paninocotto.png', TRUE),
('Panino con crudo', 'pane integrale, crudo, rucola, squacquerone', 4.00, 'panino_predefinito', 'img/paninocrudo.png', TRUE),
('Panino Bresaola e Philadelphia', 'Pane al carbone con bresaola, pomodorini e Philadelphia', 5.00, 'panino_predefinito', 'img/paninonero.png', TRUE),
('Panino con Salame Nostrano', 'Pane rosetta con salame nostrano', 4.00, 'panino_predefinito', 'img/paninosalame.png', TRUE),
('Margherita', 'pomodoro, mozzarella', 2.50, 'pizzetta', 'img/pizzettamargherita.png', TRUE),
('Pizzetta Salsiccia', 'pomodoro, mozzarella, salsiccia', 3.00, 'pizzetta', 'img/pizzettasalsiccia.png', TRUE),
('Pizzetta Wurstel e Patatine', 'pomodoro, mozzarella, patatine, wurstel', 3.00, 'pizzetta', 'img/pizzettawurstel.png', TRUE),
('Pizzetta Rossa', 'pomodoro', 2.00, 'pizzetta', 'img/pizzettarossa.png', TRUE),
('Pizzetta Cotto', 'mozzarella, cotto', 2.50, 'pizzetta', 'img/pizzettacotto.png', TRUE),
('Pizzetta Salame Piccante', 'Pomodoro, mozzarella e salame piccante', 3.50, 'pizzetta', 'img/pizzettasalamepiccante.png', TRUE),
('Focaccia con Olio e Sale', 'Semplice focaccia con olio extra vergine e sale', 2.50, 'pizzetta', 'img/focaccia.png', TRUE),
('Acqua da 500ml', 'Acqua minerale naturale', 1.50, 'bevanda', 'img/acqua.png', TRUE),
('Coca Cola lattina', 'Lattina 330ml', 2.50, 'bevanda', 'img/cocacola.png', TRUE),
('Succo cartone', 'Succo di frutta vari gusti (cartone 200ml)', 3.00, 'bevanda', 'img/succo.png', TRUE),
('Panino Normale (base)', '1 pane, 1 proteina, 1 contorno, 1 salsa', 4.50, 'panino_componibile', 'img/paninocomponibile.png', TRUE),
('Panino Grande (base)', '1 pane, 1 proteina, 2 contorni, 2 salse', 5.50, 'panino_componibile', 'img/paninocomponibile.png', TRUE),
('Panino Maxi (base)', '1 pane, 2 proteine, 3 contorni, 2 salse', 7.00, 'panino_componibile', 'img/paninocomponibile.png', TRUE);

-- Popolamento Fasce Orarie
INSERT INTO `FasceOrarie` (`giorno_settimana`, `ora_inizio`, `ora_fine`, `attiva`, `capacita_massima`) VALUES
('lunedi', '11:00:00', '11:30:00', TRUE, 10),('lunedi', '11:30:00', '12:00:00', TRUE, 10),('lunedi', '12:00:00', '12:30:00', TRUE, 10),('lunedi', '12:30:00', '13:00:00', TRUE, 10),('lunedi', '13:00:00', '13:30:00', TRUE, 10),('lunedi', '13:30:00', '14:00:00', TRUE, 10),('lunedi', '14:00:00', '14:30:00', TRUE, 10),
('martedi', '11:00:00', '11:30:00', TRUE, 10),('martedi', '11:30:00', '12:00:00', TRUE, 10),('martedi', '12:00:00', '12:30:00', TRUE, 10),('martedi', '12:30:00', '13:00:00', TRUE, 10),
('mercoledi', '11:00:00', '11:30:00', TRUE, 10),('mercoledi', '11:30:00', '12:00:00', TRUE, 10),('mercoledi', '12:00:00', '12:30:00', TRUE, 10),('mercoledi', '12:30:00', '13:00:00', TRUE, 10),
('giovedi', '11:00:00', '11:30:00', TRUE, 10),('giovedi', '11:30:00', '12:00:00', TRUE, 10),('giovedi', '12:00:00', '12:30:00', TRUE, 10),('giovedi', '12:30:00', '13:00:00', TRUE, 10),
('venerdi', '11:00:00', '11:30:00', TRUE, 10),('venerdi', '11:30:00', '12:00:00', TRUE, 10),('venerdi', '12:00:00', '12:30:00', TRUE, 10),('venerdi', '12:30:00', '13:00:00', TRUE, 10),
('sabato', '11:00:00', '11:30:00', TRUE, 10),('sabato', '11:30:00', '12:00:00', TRUE, 10),('sabato', '12:00:00', '12:30:00', TRUE, 10),('sabato', '12:30:00', '13:00:00', TRUE, 10),

('domenica', '11:00:00', '11:30:00', TRUE, 10),('domenica', '11:30:00', '12:00:00', TRUE, 10),('domenica', '12:00:00', '12:30:00', TRUE, 10),('domenica', '12:30:00', '13:00:00', TRUE, 10);


-- Popolamento Stato Fasce Giornaliere (Esempio per una data specifica)
INSERT INTO `StatoFasceGiornaliere` (`id_fascia`, `data_riferimento`, `stato_giornaliero`, `numero_ordini_correnti`) VALUES
(1, CURDATE(), 'disponibile', 3),
(2, CURDATE(), 'piena', 10),
(3, CURDATE(), 'disponibile', 5);

-- Popolamento Ordini
INSERT INTO `Ordini` (`id_ordine`, `id_utente`, `data_ordine`, `totale`, `stato`, `fascia_oraria_consegna`, `aula_consegna`, `nome_ricevente`, `cognome_ricevente`, `note_utente`) VALUES
(1, 1, NOW(), 6.50, 'ricevuto', '12:30 - 13:00', 'Aula G3', 'Anna', 'Rossi', NULL),
(2, 2, NOW() - INTERVAL 1 DAY, 10.50, 'consegnato', '13:00 - 13:30', NULL, 'Marco', 'Bianchi', 'Senza salse extra');

-- Popolamento Dettagli Ordine
INSERT INTO `DettagliOrdine` (`id_dettaglio`, `id_ordine`, `id_prodotto`, `quantita`, `prezzo_unitario_al_momento_ordine`) VALUES
(1, 1, 1, 1, 4.00),
(2, 1, 10, 1, 2.50),
(3, 2, 4, 2, 2.50);
INSERT INTO `DettagliOrdine` (`id_dettaglio`, `id_ordine`, `tipo_panino_componibile`, `quantita`, `prezzo_unitario_al_momento_ordine`) VALUES
(4, 2, 'grande', 1, 5.50);

-- Popolamento Dettagli Panino Composto
INSERT INTO `DettagliPaninoComposto` (`id_dettaglio_ordine`, `id_ingrediente`) VALUES
(4, (SELECT id_ingrediente FROM Ingredienti WHERE nome = 'Pane Integrale')),
(4, (SELECT id_ingrediente FROM Ingredienti WHERE nome = 'Prosciutto Crudo')),
(4, (SELECT id_ingrediente FROM Ingredienti WHERE nome = 'Rucola')),
(4, (SELECT id_ingrediente FROM Ingredienti WHERE nome = 'Maionese'));

-- Popolamento Notifiche
INSERT INTO `Notifiche` (`id_utente_destinatario`, `messaggio`, `tipo_notifica`, `id_ordine_riferimento`) VALUES
(1, 'Il tuo ordine #00001 è stato ricevuto!', 'ordine_status', 1),
(3, 'Il tuo ordine #00002 è stato consegnato. Grazie!', 'ordine_status', 2),
(2, 'Hai ricevuto un nuovo ordine! #00001', 'nuovo_ordine_venditore', 1);
