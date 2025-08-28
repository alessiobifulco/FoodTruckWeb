<?php
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');
$provenienza = $_POST['provenienza'] ?? 'contacts.php';

if (empty($name) || empty($email) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ' . $provenienza . '?status=error#contact-form-section');
    exit;
}

try {
    $stmt = $conn->prepare("INSERT INTO Messaggi (nome_mittente, email_mittente, testo_messaggio, letto) VALUES (?, ?, ?, ?)");
    
    $letto = false;
    
    $stmt->bind_param("sssi", $name, $email, $message, $letto);
    
    $stmt->execute();
    $stmt->close();

    header('Location: confirmation.php');
    exit;
} catch (Exception $e) {
    error_log("Errore salvataggio messaggio: " . $e->getMessage());
    header('Location: ' . $provenienza . '?status=dberror#contact-form-section');
    exit;
}