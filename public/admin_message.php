<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'venditore') {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_message'])) {
    if (isset($_POST['id_messaggio'])) {
        $id_messaggio = intval($_POST['id_messaggio']);
        $stmt = $conn->prepare("DELETE FROM Messaggi WHERE id_messaggio = ?");
        $stmt->bind_param("i", $id_messaggio);
        $stmt->execute();
        $stmt->close();
        header("Location: admin_message.php");
        exit();
    }
}

$messaggi = $conn->query("SELECT * FROM Messaggi ORDER BY data_invio DESC")->fetch_all(MYSQLI_ASSOC);

$page_title = "Messaggi Ricevuti";
include_once __DIR__ . '/../templates/header.php';
?>
<link rel="stylesheet" href="css/admin.css">

<div class="dashboard-container">
    <h1>Messaggi Ricevuti</h1>
    <a href="admin.php" class="btn-back-dashboard">⬅ Torna alla Dashboard</a>
    <section class="card">
        <h2>Elenco Messaggi</h2>
        <div id="messages-list">
            <?php if (empty($messaggi)): ?>
                <p>Non è stato ricevuto nessun messaggio.</p>
            <?php else: ?>
                <?php foreach ($messaggi as $msg): ?>
                    <?php
                        $is_read = isset($msg['letta']) && $msg['letta'];
                    ?>
                    <div class="message-item <?php echo !$is_read ? 'unread' : 'read'; ?>">
                        <div class="message-header">
                            <span><strong>Da:</strong> <?php echo htmlspecialchars($msg['nome_mittente']); ?> (<?php echo htmlspecialchars($msg['email_mittente']); ?>)</span>
                            <span><strong>Data:</strong> <?php echo date('d/m/Y H:i', strtotime($msg['data_invio'])); ?></span>
                        </div>
                        <div class="message-body">
                            <p><?php echo nl2br(htmlspecialchars($msg['testo_messaggio'])); ?></p>
                        </div>
                        <div class="message-footer">
                            <?php if (!$is_read): ?>
                                <button class="btn-link" data-action="mark_message_read" data-id="<?php echo $msg['id_messaggio']; ?>">Segna come letto</button>
                            <?php endif; ?>
                            <button class="btn-link-danger" data-action="delete_message" data-id="<?php echo $msg['id_messaggio']; ?>">Elimina</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</div>

<script src="js/admin_actions.js" defer></script>
<?php include_once __DIR__ . '/../templates/footer.php'; ?>