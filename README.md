# Project Setup: FoodTruckWeb

Questa guida fornisce le istruzioni passo-passo per configurare e avviare il progetto FoodTruckWeb in un ambiente di sviluppo locale.

## 1. Requisiti

Prima di iniziare, assicurati di avere installato sul tuo sistema:

* Un ambiente di sviluppo web locale come **XAMPP**, che include Apache (web server), PHP e MariaDB (database).
    * **Download:** [apachefriends.org](https://www.apachefriends.org/index.html)

## 2. Installazione e Configurazione

Segui questi passaggi nell'ordine indicato.

### Step 1: Scarica il Progetto
Clona o scarica il repository del progetto e posizionalo all'interno della cartella `htdocs` della tua installazione di XAMPP (solitamente `C:\xampp\htdocs`).

Se usi Git, apri un terminale in `C:\xampp\htdocs` e lancia:
```bash
# Sostituisci con l'URL del tuo repository
git clone [https://github.com/tuo-username/FoodTruckWeb.git](https://github.com/tuo-username/FoodTruckWeb.git)
```

### Step 2: Imposta il Database
1.  Avvia i moduli **Apache** e **MySQL** dal pannello di controllo di XAMPP.
2.  Apri il browser e vai a `http://localhost/phpmyadmin/`.
3.  Crea un nuovo database vuoto chiamato `FoodTruckDB`.
4.  Seleziona il database `FoodTruckDB` appena creato nel menu a sinistra.
5.  Vai alla scheda "**Importa**" in alto.
6.  Importa gli script SQL forniti nella cartella `database/`:
    * Per prima cosa, importa il file `createtable.sql` per creare la struttura di tutte le tabelle.
    * Subito dopo, importa il file `populatetable.sql` per inserire i dati iniziali (prodotti, utenti, ecc.).

### Step 3: Configura la Connessione al Database
1.  Apri il file `config/db.php` con un editor di codice.
2.  Verifica che le credenziali corrispondano al tuo ambiente XAMPP. La configurazione di default di solito ha `root` come utente e nessuna password.

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
