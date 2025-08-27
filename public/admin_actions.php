<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'venditore') {
    http_response_code(403); 
    echo json_encode(['success' => false, 'message' => 'Accesso non autorizzato.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'] ?? null;
$id = $data['id'] ?? null;
$user_id = $_SESSION['user_id'];

if (!$action || !$id || !is_numeric($id)) {
    http_response_code(400); 
    echo json_encode(['success' => false, 'message' => 'Dati mancanti o non validi.']);
    exit;
}

try {
    $stmt = null;
    switch ($action) {
        case 'mark_notification_read':
            $stmt = $conn->prepare("UPDATE Notifiche SET letta = TRUE WHERE id_notifica = ? AND id_utente_destinatario = ?");
            $stmt->bind_param("ii", $id, $user_id);
            break;
        case 'delete_notification':
            $stmt = $conn->prepare("DELETE FROM Notifiche WHERE id_notifica = ? AND id_utente_destinatario = ?");
            $stmt->bind_param("ii", $id, $user_id);
            break;
        case 'mark_message_read':
            $stmt = $conn->prepare("UPDATE Messaggi SET letto = TRUE WHERE id_messaggio = ?");
            $stmt->bind_param("i", $id);
            break;
        case 'delete_message':
            $stmt = $conn->prepare("DELETE FROM Messaggi WHERE id_messaggio = ?");
            $stmt->bind_param("i", $id);
            break;
        default:
            throw new Exception("Azione non valida.");
    }

    if ($stmt) {
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Nessun elemento trovato o azione non necessaria.']);
        }
        $stmt->close();
    }
} catch (Exception $e) {
    http_response_code(500); 
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Errore del server.']);
}