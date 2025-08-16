<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Utente non autenticato.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$notification_id = $data['notification_id'] ?? null;
$action = $data['action'] ?? null;
$user_id = $_SESSION['user_id'];

if (!$notification_id || !$action) {
    echo json_encode(['success' => false, 'message' => 'Dati mancanti.']);
    exit;
}

try {
    if ($action === 'mark_read') {
        $stmt = $conn->prepare("UPDATE Notifiche SET letta = TRUE WHERE id_notifica = ? AND id_utente_destinatario = ?");
        $stmt->bind_param("ii", $notification_id, $user_id);
    } elseif ($action === 'delete') {
        $stmt = $conn->prepare("DELETE FROM Notifiche WHERE id_notifica = ? AND id_utente_destinatario = ?");
        $stmt->bind_param("ii", $notification_id, $user_id);
    } else {
        throw new Exception("Azione non valida.");
    }

    $stmt->execute();
    $affected_rows = $stmt->affected_rows;
    $stmt->close();

    if ($affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Nessuna notifica trovata o non autorizzato.']);
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Errore del server.']);
}
