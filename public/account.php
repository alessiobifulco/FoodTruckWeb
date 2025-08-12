<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/db.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Protezione della pagina: se l'utente non è loggato, lo rimanda al login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$page_title = "Il Mio Account";
include_once __DIR__ . '/../templates/header.php';

$user_id = $_SESSION['user_id'];
$user_email = $_SESSION['user_email'];

// Recupero ordini dell'utente
$ordini = [];
$stmt_ordini = $conn->prepare("SELECT id_ordine, data_ordine, totale, stato FROM Ordini WHERE id_utente = ? ORDER BY data_ordine DESC");
$stmt_ordini->bind_param("i", $user_id);
$stmt_ordini->execute();
$result_ordini = $stmt_ordini->get_result();
while ($row = $result_ordini->fetch_assoc()) {
    $ordini[] = $row;
}
$stmt_ordini->close();

// Recupero notifiche dell'utente
$notifiche = [];
$stmt_notifiche = $conn->prepare("SELECT id_notifica, messaggio, data_creazione, letta FROM Notifiche WHERE id_utente_destinatario = ? ORDER BY data_creazione DESC");
$stmt_notifiche->bind_param("i", $user_id);
$stmt_notifiche->execute();
$result_notifiche = $stmt_notifiche->get_result();
while ($row = $result_notifiche->fetch_assoc()) {
    $notifiche[] = $row;
}
$stmt_notifiche->close();
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <link rel="stylesheet" href="css/account.css">
</head>

<body>
    <div class="account-container">
        <h1>Il Mio Account</h1>

        <section class="account-section">
            <h2>Dettagli Account</h2>
            <div class="details-card">
                <span>Sei loggato come: <strong><?php echo htmlspecialchars($user_email); ?></strong></span>
                <button class="btn-secondary">Cambia Password</button>
            </div>
            <div class="details-card">
                <span>Vuoi cancellare il tuo account?</span>
                <button class="btn-danger">Elimina Account</button>
            </div>
        </section>

        <section class="account-section">
            <h2>Storico Ordini</h2>
            <div class="order-history-container">
                <?php if (empty($ordini)): ?>
                    <p>Non hai ancora effettuato nessun ordine.</p>
                <?php else: ?>
                    <?php foreach ($ordini as $ordine): ?>
                        <div class="order-card">
                            <div class="order-info">
                                <span><strong>Order ID:</strong> #<?php echo str_pad($ordine['id_ordine'], 5, '0', STR_PAD_LEFT); ?></span>
                                <span><strong>Data:</strong> <?php echo date('d/m/Y', strtotime($ordine['data_ordine'])); ?></span>
                                <span><strong>Totale:</strong> <?php echo number_format($ordine['totale'], 2, ',', ''); ?> €</span>
                            </div>
                            <div class="order-status">
                                <span class="status-badge status-<?php echo strtolower(htmlspecialchars($ordine['stato'])); ?>">
                                    <?php echo htmlspecialchars(str_replace('_', ' ', $ordine['stato'])); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <section class="account-section">
            <h2>Centro Notifiche</h2>
            <div class="notifications-container">
                <?php if (empty($notifiche)): ?>
                    <p>Nessuna notifica presente.</p>
                <?php else: ?>
                    <?php foreach ($notifiche as $notifica): ?>
                        <div class="notification-item <?php echo !$notifica['letta'] ? 'unread' : ''; ?>">
                            <div class="notification-text">
                                <i class="fas fa-bell"></i>
                                <p><?php echo htmlspecialchars($notifica['messaggio']); ?></p>
                            </div>
                            <div class="notification-actions">
                                <span><?php echo date('d/m/Y H:i', strtotime($notifica['data_creazione'])); ?></span>
                                <?php if (!$notifica['letta']): ?>
                                    <button class="btn-link">Segna come letta</button>
                                <?php endif; ?>
                                <button class="btn-link-danger">Elimina</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </div>
    <?php include_once __DIR__ . '/../templates/footer.php'; ?>