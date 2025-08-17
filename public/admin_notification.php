<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'venditore') {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$notifiche = $conn->query("SELECT * FROM Notifiche WHERE id_utente_destinatario = $user_id ORDER BY data_creazione DESC")->fetch_all(MYSQLI_ASSOC);

$page_title = "Notifiche Venditore";
include_once __DIR__ . '/../templates/header.php';
?>
<link rel="stylesheet" href="css/admin.css">

<div class="dashboard-container">
    <h1>Centro Notifiche</h1>
    <a href="admin.php" class="btn-back-dashboard">⬅ Torna alla Dashboard</a>
    <section class="card">
        <h2>Elenco Notifiche</h2>
        <div id="notifications-list">
            <?php if (empty($notifiche)): ?>
                <p>Non ci sono notifiche.</p>
            <?php else: ?>
                <?php foreach ($notifiche as $notifica): ?>
                    <div class="notification-item <?php echo $notifica['letta'] ? '' : 'unread'; ?>">
                        <div class="notification-text">
                            <i class="fas fa-bell"></i>
                            <p><?php echo htmlspecialchars($notifica['messaggio']); ?></p>
                        </div>
                        <div class="notification-actions">
                            <span><?php echo date('d/m/Y H:i', strtotime($notifica['data_creazione'])); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php include_once __DIR__ . '/../templates/footer.php'; ?>