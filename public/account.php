<?php
require_once __DIR__ . '/../config/db.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'get_details' && isset($_GET['id'])) {
    header('Content-Type: application/json');
    $order_id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];

    $stmt_order = $conn->prepare("SELECT * FROM Ordini WHERE id_ordine = ? AND id_utente = ?");
    $stmt_order->bind_param("ii", $order_id, $user_id);
    $stmt_order->execute();
    $main_details = $stmt_order->get_result()->fetch_assoc();
    $stmt_order->close();

    if (!$main_details) {
        http_response_code(404);
        echo json_encode(['error' => 'Ordine non trovato o non autorizzato.']);
        exit;
    }

    $stmt_products = $conn->prepare("SELECT COALESCE(do.nome_personalizzato, p.nome, 'Prodotto eliminato') AS nome, do.quantita, do.prezzo_unitario_al_momento_ordine FROM DettagliOrdine do LEFT JOIN Prodotti p ON do.id_prodotto = p.id_prodotto WHERE do.id_ordine = ?");
    $stmt_products->bind_param("i", $order_id);
    $stmt_products->execute();
    $products = $stmt_products->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt_products->close();

    echo json_encode(['details' => $main_details, 'products' => $products]);
    exit;
}


$page_title = "Il Mio Account";
include_once __DIR__ . '/../templates/header.php';

$user_id = $_SESSION['user_id'];
$user_email = $_SESSION['user_email'];

$ordini = [];
$stmt_ordini = $conn->prepare("SELECT id_ordine, data_ordine, totale, stato FROM Ordini WHERE id_utente = ? ORDER BY data_ordine DESC");
$stmt_ordini->bind_param("i", $user_id);
$stmt_ordini->execute();
$result_ordini = $stmt_ordini->get_result();
while ($row = $result_ordini->fetch_assoc()) {
    $ordini[] = $row;
}
$stmt_ordini->close();

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

<link rel="stylesheet" href="css/account.css">

<div class="account-container">
    <h1>Il Mio Account</h1>

    <div id="account-page-message"></div>

    <section class="account-section">
        <h2>Dettagli Account</h2>
        <div class="details-card">
            <span>Sei loggato come: <strong><?php echo htmlspecialchars($user_email); ?></strong></span>
            <button id="change-password-btn" class="btn-secondary">Cambia Password</button>
        </div>
        <div class="details-card">
            <span>Vuoi cancellare il tuo account?</span>
            <button id="delete-account-btn" class="btn-danger">Elimina Account</button>
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
                        <div class="order-actions">
                            <button class="btn btn-secondary btn-small view-details-btn" data-order-id="<?php echo $ordine['id_ordine']; ?>">Dettagli</button>
                        </div>
                        </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <section class="account-section">
        <h2>Centro Notifiche</h2>
        <div id="notifications-list" class="notifications-container">
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
                                <button class="btn-link" data-action="mark_read" data-id="<?php echo $notifica['id_notifica']; ?>">Segna come letta</button>
                            <?php endif; ?>
                            <button class="btn-link-danger" data-action="delete" data-id="<?php echo $notifica['id_notifica']; ?>">Elimina</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</div>

<div id="change-password-overlay" class="account-overlay-container">
    <div class="account-overlay-content">
        <button class="close-btn">&times;</button>
        <h3>Cambia la tua Password</h3>
        <div id="password-message" style="margin-bottom: 15px;"></div>
        <form id="change-password-form">
            <div class="form-group">
                <label for="current_password">Password Attuale</label>
                <input type="password" id="current_password" name="current_password" required>
            </div>
            <div class="form-group">
                <label for="new_password">Nuova Password</label>
                <input type="password" id="new_password" name="new_password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Conferma Nuova Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn-submit">Salva Modifiche</button>
        </form>
    </div>
</div>

<div id="orderDetailsModalClient" class="modal">
    <div class="modal-content">
        <span class="close-btn">&times;</span>
        <h2>Dettagli Ordine <span id="modalOrderIdClient"></span></h2>
        <div id="modalOrderDetailsClient"></div>
        <h3>Prodotti Ordinati</h3>
        <ul id="modalProductListClient"></ul>
    </div>
</div>
<script src="js/account.js" defer></script>
<?php include_once __DIR__ . '/../templates/footer.php'; ?>