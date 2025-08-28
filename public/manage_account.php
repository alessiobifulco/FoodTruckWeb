<?php
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
$action = $data['action'] ?? null;
$user_id = $_SESSION['user_id'];

if (!$action) {
    echo json_encode(['success' => false, 'message' => 'Azione non specificata.']);
    exit;
}

try {
    if ($action === 'change_password') {
        $current_password = $data['current_password'];
        $new_password = $data['new_password'];
        $confirm_password = $data['confirm_password'];

        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            throw new Exception('Tutti i campi sono obbligatori.');
        }
        if ($new_password !== $confirm_password) {
            throw new Exception('Le nuove password non corrispondono.');
        }

        $stmt = $conn->prepare("SELECT password FROM Utenti WHERE id_utente = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$user || !password_verify($current_password, $user['password'])) {
            throw new Exception('La password attuale non è corretta.');
        }

        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        $stmt_update = $conn->prepare("UPDATE Utenti SET password = ? WHERE id_utente = ?");
        $stmt_update->bind_param("si", $hashed_password, $user_id);
        $stmt_update->execute();
        $stmt_update->close();

        echo json_encode(['success' => true, 'message' => 'Password modificata con successo.']);
    } elseif ($action === 'delete_account') {
        $stmt = $conn->prepare("DELETE FROM Utenti WHERE id_utente = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            session_unset();
            session_destroy();
            echo json_encode(['success' => true, 'message' => 'Account eliminato con successo.']);
        } else {
            throw new Exception('Impossibile eliminare l\'account.');
        }
        $stmt->close();
    } else {
        throw new Exception('Azione non valida.');
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
