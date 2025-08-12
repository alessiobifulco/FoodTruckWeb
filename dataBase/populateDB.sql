USE `FoodTruckDB`;


INSERT INTO `Utenti` (`email`, `password`, `ruolo`, `attivo`) VALUES
('cliente1@campus.it', '$2y$10$hashedpassword1', 'cliente', TRUE), -- Password: password123
('cliente2@campus.it', '$2y$10$hashedpassword2', 'cliente', TRUE), -- Password: password123
('venditore@campus.it', '$2y$10$hashedpassword3', 'venditore', TRUE), -- Password: password123
('prova@prova.it', '$2y$10$wI4p.h8v/2xG8h.Lg2f7a.Xg.S3h.9j.Zg.Yg.S3h.9j.Zg.Yg.S', 'cliente', TRUE);


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

INSERT INTO `FasceOrarie` (`giorno_settimana`, `ora_inizio`, `ora_fine`, `attiva`, `capacita_massima`) VALUES
('lunedi', '11:00:00', '11:30:00', TRUE, 10),
('lunedi', '11:30:00', '12:00:00', TRUE, 10),
('lunedi', '12:00:00', '12:30:00', TRUE, 10),
('lunedi', '12:30:00', '13:00:00', TRUE, 10),
('lunedi', '13:00:00', '13:30:00', TRUE, 10),
('lunedi', '13:30:00', '14:00:00', TRUE, 10),
('lunedi', '14:00:00', '14:30:00', TRUE, 10),

('martedi', '11:00:00', '11:30:00', TRUE, 10),
('martedi', '11:30:00', '12:00:00', TRUE, 10),
('martedi', '12:00:00', '12:30:00', TRUE, 10),
('martedi', '12:30:00', '13:00:00', TRUE, 10); 


INSERT INTO `StatoFasceGiornaliere` (`id_fascia`, `data_riferimento`, `stato_giornaliero`, `numero_ordini_correnti`) VALUES
((SELECT id_fascia FROM FasceOrarie WHERE giorno_settimana='lunedi' AND ora_inizio='11:00:00' LIMIT 1), '2025-08-01', 'disponibile', 3),
((SELECT id_fascia FROM FasceOrarie WHERE giorno_settimana='martedi' AND ora_inizio='11:30:00' LIMIT 1), '2025-08-01', 'piena', 10),
((SELECT id_fascia FROM FasceOrarie WHERE giorno_settimana='lunedi' AND ora_inizio='12:00:00' LIMIT 1), '2025-08-01', 'disponibile', 5);


INSERT INTO `Ordini` (`id_utente`, `data_ordine`, `totale`, `stato`, `fascia_oraria_consegna`, `aula_consegna`, `nome_ricevente`, `cognome_ricevente`, `note_utente`) VALUES
(1, NOW(), 7.00, 'ricevuto', '12:30 - 13:00', 'Aula G3', 'Anna', 'Rossi', NULL),
(2, NOW() - INTERVAL 1 DAY, 10.50, 'consegnato', '13:00 - 13:30', NULL, 'Marco', 'Bianchi', 'Senza salse extra'),
(1, NOW() - INTERVAL 2 HOUR, 5.00, 'in_preparazione', '11:00 - 11:30', 'Aula A10', 'Anna', 'Rossi', NULL);


INSERT INTO `DettagliOrdine` (`id_ordine`, `id_prodotto`, `tipo_panino_componibile`, `quantita`, `prezzo_unitario_al_momento_ordine`) VALUES
(1, (SELECT id_prodotto FROM Prodotti WHERE nome = 'Panino con cotolettetta' LIMIT 1), NULL, 1, 4.00),
(1, (SELECT id_prodotto FROM Prodotti WHERE nome = 'Coca Cola lattina' LIMIT 1), NULL, 1, 2.50);

INSERT INTO `DettagliOrdine` (`id_ordine`, `id_prodotto`, `tipo_panino_componibile`, `quantita`, `prezzo_unitario_al_momento_ordine`) VALUES
(2, NULL, 'grande', 1, 5.50), 
(2, (SELECT id_prodotto FROM Prodotti WHERE nome = 'Pizzetta Margherita' LIMIT 1), NULL, 2, 2.50);

INSERT INTO `DettagliOrdine` (`id_ordine`, `id_prodotto`, `tipo_panino_componibile`, `quantita`, `prezzo_unitario_al_momento_ordine`) VALUES
(3, (SELECT id_prodotto FROM Prodotti WHERE nome = 'Panino con cotto' LIMIT 1), NULL, 1, 3.50);


INSERT INTO `DettagliPaninoComposto` (`id_dettaglio_ordine`, `id_ingrediente`) VALUES
((SELECT id_dettaglio FROM DettagliOrdine WHERE id_ordine = 2 AND tipo_panino_componibile = 'grande' LIMIT 1), (SELECT id_ingrediente FROM Ingredienti WHERE nome = 'Pane Integrale' LIMIT 1)),
((SELECT id_dettaglio FROM DettagliOrdine WHERE id_ordine = 2 AND tipo_panino_componibile = 'grande' LIMIT 1), (SELECT id_ingrediente FROM Ingredienti WHERE nome = 'Prosciutto Crudo' LIMIT 1)),
((SELECT id_dettaglio FROM DettagliOrdine WHERE id_ordine = 2 AND tipo_panino_componibile = 'grande' LIMIT 1), (SELECT id_ingrediente FROM Ingredienti WHERE nome = 'Rucola' LIMIT 1)),
((SELECT id_dettaglio FROM DettagliOrdine WHERE id_ordine = 2 AND tipo_panino_componibile = 'grande' LIMIT 1), (SELECT id_ingrediente FROM Ingredienti WHERE nome = 'Maionese' LIMIT 1));


INSERT INTO `Notifiche` (`id_utente_destinatario`, `messaggio`, `data_creazione`, `letta`, `tipo_notifica`, `id_ordine_riferimento`) VALUES
(1, 'Ordine #1 appena ricevuto! Grazie!', NOW(), FALSE, 'ordine_status', 1), 
(1, 'Il tuo ordine #2 è in consegna verso Aula G3!', NOW() - INTERVAL 1 HOUR, FALSE, 'ordine_status', 2), 
(2, 'Benvenuto! Hai il 5% di sconto sul tuo primo ordine!', NOW() - INTERVAL 2 DAY, TRUE, 'sconto_benvenuto', NULL), 
(3, 'Hai ricevuto un nuovo ordine! #00125', NOW(), FALSE, 'nuovo_ordine_venditore', 1), 
(3, 'Fascia 11:30-12:00 (Venerdì) ha raggiunto la capacità massima.', NOW(), FALSE, 'fascia_piena_venditore', NULL); 