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

$success = false;

switch ($action) {
    case 'mark_notification_read':
        $stmt = $conn->prepare("UPDATE Notifiche SET letta = TRUE WHERE id_notifica = ? AND id_utente_destinatario = ?");
        $stmt->bind_param("ii", $id, $user_id);
        if ($stmt->execute()) $success = true;
        $stmt->close();
        break;

    case 'delete_notification':
        $stmt = $conn->prepare("DELETE FROM Notifiche WHERE id_notifica = ? AND id_utente_destinatario = ?");
        $stmt->bind_param("ii", $id, $user_id);
        if ($stmt->execute()) $success = true;
        $stmt->close();
        break;

    case 'mark_message_read':
        $stmt = $conn->prepare("UPDATE Messaggi SET letto = TRUE WHERE id_messaggio = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) $success = true;
        $stmt->close();
        break;

    case 'delete_message':
        $stmt = $conn->prepare("DELETE FROM Messaggi WHERE id_messaggio = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) $success = true;
        $stmt->close();
        break;
        
    default:
        $success = false;
        break;
}

if ($success) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'L\'operazione sul database è fallita.']);
}