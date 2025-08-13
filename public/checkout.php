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

if (isset($_GET['remove'])) {
    $index_to_remove = (int)$_GET['remove'];
    if (isset($_SESSION['cart'][$index_to_remove])) {
        unset($_SESSION['cart'][$index_to_remove]);
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
    header('Location: checkout.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_data'])) {
    $_SESSION['cart'] = json_decode($_POST['cart_data'], true);
    $_SESSION['delivery_day'] = htmlspecialchars($_POST['delivery_day']);
    $_SESSION['delivery_time'] = htmlspecialchars($_POST['delivery_time']);
    header('Location: checkout.php');
    exit;
}

// Ora il carrello può essere vuoto, quindi non reindirizziamo più da qui
$cart = $_SESSION['cart'] ?? [];
define('COSTO_CONSEGNA', 2.00);
$subtotal = 0;
foreach ($cart as $item) {
    $subtotal += $item['prezzo'];
}
$total = $subtotal + COSTO_CONSEGNA;

$page_title = "Checkout";
include_once __DIR__ . '/../templates/header.php';
?>
<link rel="stylesheet" href="css/checkout.css">

<?php if (!empty($cart)): ?>
    <main class="checkout-container">
        <div class="checkout-details">
            <div class="continue-shopping-banner">
                <p>Vuoi ordinare altro? Torna pure al nostro fantastico menu!</p>
                <a href="menu.php" class="btn-secondary">Torna al Menu</a>
            </div>

            <h2>Dettagli Consegna</h2>
            <form action="process_order.php" method="POST">
                <div class="form-group">
                    <label>Contatto</label>
                    <input type="text" value="<?php echo htmlspecialchars($_SESSION['user_email']); ?>" disabled>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="nome">Nome</label>
                        <input type="text" id="nome" name="nome" required>
                    </div>
                    <div class="form-group">
                        <label for="cognome">Cognome</label>
                        <input type="text" id="cognome" name="cognome" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="aula">Aula (Opzionale)</label>
                    <input type="text" id="aula" name="aula" placeholder="Es: 2.12">
                </div>
                <div class="form-group">
                    <label for="note">Note per l'ordine (Opzionale)</label>
                    <textarea id="note" name="note" rows="3" placeholder="Intolleranze, richieste particolari, ecc."></textarea>
                </div>
                <button type="submit" class="btn-pay">Paga Ora (Simulato)</button>
            </form>
        </div>

        <aside class="checkout-summary">
            <div class="summary-delivery-info">
                Consegna per <strong><?php echo htmlspecialchars($_SESSION['delivery_day']); ?></strong>,
                orario <strong><?php echo htmlspecialchars($_SESSION['delivery_time']); ?></strong>
            </div>
            <div class="summary-items">
                <?php foreach ($cart as $index => $item): ?>
                    <div class="summary-item">
                        <img src="<?php echo htmlspecialchars($item['immagine']); ?>" alt="" class="item-image">
                        <div class="item-details">
                            <span class="item-name"><?php echo htmlspecialchars($item['nome']); ?></span>
                        </div>
                        <span class="item-price"><?php echo number_format($item['prezzo'], 2, ',', ''); ?> €</span>
                        <a href="checkout.php?remove=<?php echo $index; ?>" class="remove-item-btn" title="Rimuovi">&times;</a>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="summary-recap">
                <div class="recap-line">
                    <span>Subtotale</span>
                    <span><?php echo number_format($subtotal, 2, ',', ''); ?> €</span>
                </div>
                <div class="recap-line">
                    <span>Costo Consegna</span>
                    <span><?php echo number_format(COSTO_CONSEGNA, 2, ',', ''); ?> €</span>
                </div>
                <div class="recap-line total">
                    <span>Totale</span>
                    <span><?php echo number_format($total, 2, ',', ''); ?> €</span>
                </div>
            </div>
        </aside>
    </main>
<?php else: ?>
    <main class="checkout-container-empty">
        <div class="empty-cart-card">
            <i class="fas fa-shopping-cart empty-cart-icon"></i>
            <h2>Il tuo carrello è vuoto</h2>
            <p>Non hai ancora aggiunto nessun prodotto. Torna al menu per iniziare il tuo ordine.</p>
            <a href="menu.php" class="btn-primary">Torna al Menu</a>
        </div>
    </main>
<?php endif; ?>

<?php include_once __DIR__ . '/../templates/footer.php'; ?>