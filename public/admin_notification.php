<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'venditore') {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_notification'])) {
    if (isset($_POST['id_notifica'])) {
        $id_notifica = intval($_POST['id_notifica']);
        $user_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("DELETE FROM Notifiche WHERE id_notifica = ? AND id_utente_destinatario = ?");
        $stmt->bind_param("ii", $id_notifica, $user_id);
        $stmt->execute();
        $stmt->close();
        header("Location: admin_notification.php");
        exit();
    }
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM Notifiche WHERE id_utente_destinatario = ? ORDER BY data_creazione DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$notifiche = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

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
                    <div class="notification-item <?php echo !$notifica['letta'] ? 'unread' : ''; ?>">
                        <div class="notification-text">
                            <i class="fas fa-bell"></i>
                            <p><?php echo htmlspecialchars($notifica['messaggio']); ?></p>
                        </div>
                        <div class="notification-actions">
                            <span><?php echo date('d/m/Y H:i', strtotime($notifica['data_creazione'])); ?></span>
                            <?php if (!$notifica['letta']): ?>
                                <button class="btn-link" data-action="mark_notification_read" data-id="<?php echo $notifica['id_notifica']; ?>">Segna come letta</button>
                            <?php endif; ?>
                            <button class="btn-link-danger" data-action="delete_notification" data-id="<?php echo $notifica['id_notifica']; ?>">Elimina</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</div>

<script src="js/admin_actions.js" defer></script>
<?php include_once __DIR__ . '/../templates/footer.php'; ?>