<?php

define('DEBUG_MODE', true);

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'BLS007ab&');
define('DB_NAME', 'FoodTruckDB');

error_reporting(E_ALL);
ini_set('display_errors', DEBUG_MODE ? '1' : '0');

if (DEBUG_MODE) {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
}

try {
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    error_log("Database connection failed: " . $e->getMessage());

    if (DEBUG_MODE) {
        die("<h1>Errore di Connessione al Database</h1><p>Dettagli: " . $e->getMessage() . "</p><p>Controlla le credenziali definite nel file `config/db.php` e assicurati che il server MySQL (XAMPP) sia attivo.</p>");
    } else {
        die("Errore di connessione al database. Riprova più tardi.");
    }
}

function closeConnection()
{
    global $conn;
    if ($conn instanceof mysqli && !$conn->connect_error) {
        $conn->close();
    }
}
register_shutdown_function('closeConnection');
