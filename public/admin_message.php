<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'venditore') {
    header('Location: login.php');
    exit();
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
                    <div class="message-item <?php echo $msg['letto'] ? 'read' : ''; ?>">
                        <div class="message-header">
                            <span><strong>Da:</strong> <?php echo htmlspecialchars($msg['nome_mittente']); ?> (<?php echo htmlspecialchars($msg['email_mittente']); ?>)</span>
                            <span><strong>Data:</strong> <?php echo date('d/m/Y H:i', strtotime($msg['data_invio'])); ?></span>
                        </div>
                        <div class="message-body">
                            <p><?php echo nl2br(htmlspecialchars($msg['testo_messaggio'])); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php include_once __DIR__ . '/../templates/footer.php'; ?>