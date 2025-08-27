# Project Setup: FoodTruckWeb

Questa guida fornisce le istruzioni passo-passo per configurare e avviare il progetto FoodTruckWeb in un ambiente di sviluppo locale su Windows.

## 1. Prerequisiti

Assicurati di avere il seguente software installato e configurato sul tuo sistema.

### a) Ambiente Server XAMPP
Il progetto è testato su **XAMPP**, che include Apache (web server) e MySQL (database).
- **Download:** Scarica XAMPP.
- **Installazione:** Segui la procedura guidata. L'installazione di default in `C:\xampp` è consigliata.

### b) Composer (per Windows)
**Composer** è essenziale per installare le dipendenze del progetto e creare la cartella `vendor/`.
1.  **Download:** Vai sul sito ufficiale di Composer: [getcomposer.org](https://getcomposer.org/download/).
2.  **Installer:** Scarica l'eseguibile `Composer-Setup.exe`.
3.  **Verifica:** Una volta completata l'installazione, apri un **nuovo** terminale (CMD, PowerShell o Git Bash) e digita il comando `composer --version`. Se l'installazione è andata a buon fine, vedrai la versione di Composer installata.

## 2. Configurazione del Progetto

Segui questi passaggi nell'ordine indicato.

### Step 1: Scarica il Progetto
Clona il repository da GitHub nella cartella `htdocs` di XAMPP.

```bash
# Apri un terminale in C:\xampp\htdocs
cd C:\xampp\htdocs

# Clona il repository (sostituisci con il tuo URL)
git clone [https://github.com/tuo-username/FoodTruckWeb.git](https://github.com/tuo-username/FoodTruckWeb.git)

# Entra nella cartella del progetto
cd FoodTruckWeb
```

### Step 2: Installa le Dipendenze PHP

```bash
composer install
```
Questo comando leggerà il file `composer.json` e creerà la cartella `vendor/` con tutte le librerie necessarie.

### Step 3: Crea e Popola il Database
1.  Avvia i moduli **Apache** e **MySQL** dal pannello di controllo di XAMPP.
2.  Apri il browser e vai a `http://localhost/phpmyadmin/`.
3.  Crea un nuovo database:
    - Clicca su "Nuovo" nel menu a sinistra.
    - Inserisci il nome del database: `FoodTruckDB`.
    - Clicca su "Crea".
4.  Importa la struttura e i dati:
    - Clicca sul database `FoodTruckDB` appena creato nel menu a sinistra.
    - Vai alla scheda "**Importa**" in alto.
    - Clicca su "Scegli file" e seleziona lo script SQL fornito con il progetto (`database/createtable.sql e database/populatetable.sql`).
    - Clicca sul pulsante "Esegui" in fondo alla pagina.

### Step 4: Configura la Connessione al Database
1.  Apri il file `config/db.php` con un editor di codice.
2.  Verifica che le credenziali corrispondano al tuo ambiente XAMPP. La configurazione di default di XAMPP di solito ha `root` come utente e nessuna password.

    ```php
    define('DB_SERVER', 'localhost');
    define('DB_USERNAME', 'root');
    define('DB_PASSWORD', ''); 
    define('DB_NAME', 'FoodTruckDB');
    ```

## 3. Avvio

Se tutti i passaggi sono stati completati, puoi avviare l'applicazione.
Naviga con il browser all'URL della cartella `public`:

**`http://localhost/FoodTruckWeb/public/`**

Se visualizzi la homepage del sito, l'installazione è stata completata con successo.
