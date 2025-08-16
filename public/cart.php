<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/db.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    $page_title = "Carrello Vuoto";
    include_once __DIR__ . '/../templates/header.php';
?>
    <link rel="stylesheet" href="css/cart.css">
    <main class="cart-empty-container">
        <div class="empty-cart-card">
            <i class="fas fa-shopping-cart empty-cart-icon"></i>
            <h2>Il tuo carrello è vuoto</h2>
            <p>Non hai ancora aggiunto nessun prodotto.</p>
            <a href="order.php" class="btn-primary">Inizia a ordinare</a>
        </div>
    </main>
<?php
    include_once __DIR__ . '/../templates/footer.php';
} else {
    header('Location: checkout.php');
    exit;
}
