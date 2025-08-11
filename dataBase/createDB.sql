CREATE DATABASE IF NOT EXISTS `FoodTruckDB`;
USE `FoodTruckDB`;

SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS `Utenti` (
    `id_utente` INT AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `ruolo` ENUM('cliente', 'venditore') NOT NULL DEFAULT 'cliente',
    `attivo` BOOLEAN NOT NULL DEFAULT TRUE
);

CREATE TABLE IF NOT EXISTS `Prodotti` (
    `id_prodotto` INT AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(255) NOT NULL UNIQUE,
    `descrizione` TEXT,
    `prezzo` DECIMAL(5,2) NOT NULL CHECK (prezzo >= 0),
    `categoria` ENUM('panino_predefinito', 'pizzetta', 'bevanda', 'panino_componibile') NOT NULL,
    `path_immagine` VARCHAR(255) DEFAULT 'img/default.jpg', 
    `disponibile` BOOLEAN NOT NULL DEFAULT TRUE
);

CREATE TABLE IF NOT EXISTS `Ingredienti` (
    `id_ingrediente` INT AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(100) NOT NULL UNIQUE,
    `categoria_ingrediente` ENUM('pane', 'proteina', 'contorno', 'salsa') NOT NULL,
    `disponibile` BOOLEAN NOT NULL DEFAULT TRUE
);

CREATE TABLE IF NOT EXISTS `FasceOrarie` (
    `id_fascia` INT AUTO_INCREMENT PRIMARY KEY,
    `giorno_settimana` ENUM('lunedi', 'martedi', 'mercoledi', 'giovedi', 'venerdi') NOT NULL,
    `ora_inizio` TIME NOT NULL,
    `ora_fine` TIME NOT NULL,
    `attiva` BOOLEAN NOT NULL DEFAULT TRUE,
    `capacita_massima` INT NOT NULL DEFAULT 10
);

CREATE TABLE IF NOT EXISTS `StatoFasceGiornaliere` (
    `id_stato_fascia_giorno` INT AUTO_INCREMENT PRIMARY KEY,
    `id_fascia` INT NOT NULL,
    `data_riferimento` DATE NOT NULL,
    `stato_giornaliero` ENUM('disponibile', 'piena', 'chiusa') NOT NULL DEFAULT 'disponibile',
    `numero_ordini_correnti` INT NOT NULL DEFAULT 0,
    UNIQUE (`id_fascia`, `data_riferimento`),
    FOREIGN KEY (`id_fascia`) REFERENCES `FasceOrarie`(`id_fascia`)
);

CREATE TABLE IF NOT EXISTS `Ordini` (
    `id_ordine` INT AUTO_INCREMENT PRIMARY KEY,
    `id_utente` INT NOT NULL,
    `data_ordine` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `totale` DECIMAL(6,2) NOT NULL CHECK (totale >= 0),
    `stato` ENUM('ricevuto', 'in_preparazione', 'in_consegna', 'consegnato', 'annullato') NOT NULL DEFAULT 'ricevuto',
    `fascia_oraria_consegna` VARCHAR(50) NOT NULL,
    `aula_consegna` VARCHAR(50), -- NULLABLE
    `nome_ricevente` VARCHAR(100) NOT NULL,
    `cognome_ricevente` VARCHAR(100) NOT NULL,
    `note_utente` TEXT, -- NULLABLE
    FOREIGN KEY (`id_utente`) REFERENCES `Utenti`(`id_utente`)
);

CREATE TABLE IF NOT EXISTS `DettagliOrdine` (
    `id_dettaglio` INT AUTO_INCREMENT PRIMARY KEY,
    `id_ordine` INT NOT NULL,
    `id_prodotto` INT, 
    `tipo_panino_componibile` ENUM('normale', 'grande', 'maxi'), 
    `quantita` INT NOT NULL DEFAULT 1 CHECK (quantita >= 1),
    `prezzo_unitario_al_momento_ordine` DECIMAL(5,2) NOT NULL CHECK (prezzo_unitario_al_momento_ordine >= 0),
    FOREIGN KEY (`id_ordine`) REFERENCES `Ordini`(`id_ordine`),
    FOREIGN KEY (`id_prodotto`) REFERENCES `Prodotti`(`id_prodotto`)
);

CREATE TABLE IF NOT EXISTS `DettagliPaninoComposto` (
    `id_composizione_panino` INT AUTO_INCREMENT PRIMARY KEY,
    `id_dettaglio_ordine` INT NOT NULL,
    `id_ingrediente` INT NOT NULL,
    FOREIGN KEY (`id_dettaglio_ordine`) REFERENCES `DettagliOrdine`(`id_dettaglio`),
    FOREIGN KEY (`id_ingrediente`) REFERENCES `Ingredienti`(`id_ingrediente`)
);

CREATE TABLE IF NOT EXISTS `Notifiche` (
    `id_notifica` INT AUTO_INCREMENT PRIMARY KEY,
    `id_utente_destinatario` INT NOT NULL,
    `messaggio` TEXT NOT NULL,
    `data_creazione` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `letta` BOOLEAN NOT NULL DEFAULT FALSE,
    `tipo_notifica` ENUM('ordine_status', 'disponibilita_prodotto', 'messaggio_generico', 'sconto_benvenuto', 'fascia_piena_venditore', 'nuovo_ordine_venditore') NOT NULL DEFAULT 'messaggio_generico',
    `id_ordine_riferimento` INT, -- NULLABLE
    FOREIGN KEY (`id_utente_destinatario`) REFERENCES `Utenti`(`id_utente`),
    FOREIGN KEY (`id_ordine_riferimento`) REFERENCES `Ordini`(`id_ordine`)
);

SET FOREIGN_KEY_CHECKS = 1;