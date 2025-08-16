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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_data'])) {
    $_SESSION['cart'] = json_decode($_POST['cart_data'], true);
    header('Location: checkout.php');
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

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    header('Location: menu.php');
    exit;
}

$fasce_orarie_per_giorno = [];
$giorni_settimana_attivi = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
$giorni_italiano_map = ['monday' => 'lunedi', 'tuesday' => 'martedi', 'wednesday' => 'mercoledi', 'thursday' => 'giovedi', 'friday' => 'venerdi', 'saturday' => 'sabato', 'sunday' => 'domenica'];

foreach ($giorni_settimana_attivi as $giorno_en) {
    $giorno_db = $giorni_italiano_map[$giorno_en];
    $stmt = $conn->prepare("SELECT ora_inizio, ora_fine FROM FasceOrarie WHERE giorno_settimana = ? AND attiva = TRUE ORDER BY ora_inizio");
    $stmt->bind_param("s", $giorno_db);
    $stmt->execute();
    $result = $stmt->get_result();
    $fasce = [];
    while ($row = $result->fetch_assoc()) {
        $fasce[] = date('H:i', strtotime($row['ora_inizio'])) . ' - ' . date('H:i', strtotime($row['ora_fine']));
    }
    if (!empty($fasce)) {
        $fasce_orarie_per_giorno[$giorno_en] = $fasce;
    }
    $stmt->close();
}

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
        <form action="process_order.php" method="POST" id="final-checkout-form">
            <section class="checkout-section">
                <h2>1. Riepilogo Ordine</h2>
                <div class="summary-items-checkout">
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
                <a href="menu.php" class="btn-secondary" style="margin-top: 15px;">Aggiungi altri prodotti</a>
            </section>

            <section class="checkout-section">
                <h2>2. Scegli Giorno e Ora</h2>
                <div class="day-selector">
                    <button type="button" class="day-selector-btn" data-day="today">Oggi</button>
                    <button type="button" class="day-selector-btn" data-day="tomorrow">Domani</button>
                </div>
                <div class="form-group">
                    <label for="delivery_time">Seleziona una fascia oraria</label>
                    <select id="delivery_time" name="delivery_time" class="form-control" required disabled>
                        <option value="">Prima scegli un giorno</option>
                    </select>
                </div>
                <input type="hidden" id="delivery_day" name="delivery_day">
            </section>

            <section class="checkout-section">
                <h2>3. Dati di Consegna</h2>
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
            </section>

            <button type="submit" id="pay-button" class="btn-pay" disabled>Paga Ora (Simulato)</button>
        </form>
    </div>
    <aside class="checkout-summary">
        <h3>Totale Ordine</h3>
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
<script>
    const fasceOrarie = <?php echo json_encode($fasce_orarie_per_giorno); ?>;
</script>
<script src="js/checkout.js"></script>
<?php include_once __DIR__ . '/../templates/footer.php'; ?>