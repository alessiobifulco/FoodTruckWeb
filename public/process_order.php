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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Metodo non valido.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    echo json_encode(['success' => false, 'message' => 'Il carrello è vuoto.']);
    exit;
}

$conn->begin_transaction();

try {
    $stmt_user = $conn->prepare("SELECT primo_ordine_effettuato FROM Utenti WHERE id_utente = ?");
    $stmt_user->bind_param("i", $user_id);
    $stmt_user->execute();
    $primo_ordine_effettuato = $stmt_user->get_result()->fetch_assoc()['primo_ordine_effettuato'];
    $stmt_user->close();

    $costo_consegna = $primo_ordine_effettuato ? 2.00 : 0.00;
    $subtotal = 0;
    foreach ($cart as $item) {
        $subtotal += $item['prezzo'];
    }
    $total = $subtotal + $costo_consegna;

    $nome = $_POST['nome'];
    $cognome = $_POST['cognome'];
    $aula = $_POST['aula'] ?? null;
    $note = $_POST['note'] ?? null;
    $delivery_day = $_POST['delivery_day'];
    $delivery_time = $_POST['delivery_time'];
    $fascia_consegna = $delivery_day . " " . $delivery_time;

    $stmt_ordine = $conn->prepare("INSERT INTO Ordini (id_utente, totale, fascia_oraria_consegna, aula_consegna, nome_ricevente, cognome_ricevente, note_utente) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt_ordine->bind_param("idsssss", $user_id, $total, $fascia_consegna, $aula, $nome, $cognome, $note);
    $stmt_ordine->execute();
    $id_ordine = $stmt_ordine->insert_id;
    $stmt_ordine->close();

    $stmt_dettagli = $conn->prepare("INSERT INTO DettagliOrdine (id_ordine, id_prodotto, quantita, prezzo_unitario_al_momento_ordine) VALUES (?, ?, 1, ?)");
    foreach ($cart as $item) {
        $id_prodotto = isset($item['id']) ? $item['id'] : null;
        $stmt_dettagli->bind_param("iid", $id_ordine, $id_prodotto, $item['prezzo']);
        $stmt_dettagli->execute();
    }
    $stmt_dettagli->close();

    if (!$primo_ordine_effettuato) {
        $stmt_update_user = $conn->prepare("UPDATE Utenti SET primo_ordine_effettuato = TRUE WHERE id_utente = ?");
        $stmt_update_user->bind_param("i", $user_id);
        $stmt_update_user->execute();
        $stmt_update_user->close();
    }

    $conn->commit();

    unset($_SESSION['cart']);

    echo json_encode(['success' => true]);
    exit;
} catch (Exception $e) {
    $conn->rollback();
    error_log("Errore durante il processamento dell'ordine: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Errore del server durante il salvataggio dell\'ordine.']);
    exit;
}
