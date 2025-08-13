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

// Logica per rimuovere un articolo dal carrello
if (isset($_GET['remove'])) {
    $index_to_remove = (int)$_GET['remove'];
    if (isset($_SESSION['cart'][$index_to_remove])) {
        unset($_SESSION['cart'][$index_to_remove]);
        // Riordina gli indici dell'array per evitare buchi
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
    // Ricarica la pagina senza il parametro GET per evitare rimozioni multiple con un refresh
    header('Location: checkout.php');
    exit;
}

// Se l'utente arriva qui via POST dal menu, salviamo il carrello in sessione
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_data'])) {
    $_SESSION['cart'] = json_decode($_POST['cart_data'], true);
    $_SESSION['delivery_day'] = htmlspecialchars($_POST['delivery_day']);
    $_SESSION['delivery_time'] = htmlspecialchars($_POST['delivery_time']);
    // Facciamo un redirect per evitare il reinvio del form con un refresh
    header('Location: checkout.php');
    exit;
}

// Se il carrello è vuoto (o si arriva qui senza dati), rimanda al menu
if (empty($_SESSION['cart'])) {
    header('Location: menu.php');
    exit;
}

$cart = $_SESSION['cart'];
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

<main class="checkout-container">
    <div class="checkout-details">
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

<?php include_once __DIR__ . '/../templates/footer.php'; ?>