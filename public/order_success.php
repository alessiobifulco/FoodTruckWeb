<?php
require_once __DIR__ . '/../config/db.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$page_title = "Ordine Confermato!";
include_once __DIR__ . '/../templates/header.php';
?>
<link rel="stylesheet" href="css/order_success.css">

<main class="success-container">
    <div class="success-card">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <h1>Pagamento andato a buon termine!</h1>
        <p>Il tuo ordine è stato ricevuto e stiamo già iniziando a prepararlo. Riceverai una notifica per ogni aggiornamento.</p>
        <div class="success-actions">
            <a href="whoweare.php" class="btn-secondary">Scopri chi siamo</a>
            <a href="account.php" class="btn-primary">Vai ai miei ordini</a>
        </div>
    </div>
</main>

<?php include_once __DIR__ . '/../templates/footer.php'; ?>