<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

$is_ajax = isset($_POST['is_ajax']);
$provenienza = $_POST['provenienza'] ?? 'contacts.php';
$anchor = '#contact-form-section';

if (empty($name) || empty($email) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Per favore, compila tutti i campi correttamente.']);
    } else {
        header('Location: ' . $provenienza . '?status=error' . $anchor);
    }
    exit;
}

try {
    $stmt = $conn->prepare("INSERT INTO Messaggi (nome_mittente, email_mittente, testo_messaggio) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $message);
    $stmt->execute();
    $stmt->close();

    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Messaggio inviato con successo!']);
    } else {
        header('Location: ' . $provenienza . '?status=success' . $anchor);
    }
    exit;
} catch (Exception $e) {
    error_log("Errore salvataggio messaggio: " . $e->getMessage());
    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Errore del server. Riprova più tardi.']);
    } else {
        header('Location: ' . $provenienza . '?status=dberror' . $anchor);
    }
    exit;
}
