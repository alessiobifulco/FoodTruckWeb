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

$user_id = $_SESSION['user_id'];
$stmt_user = $conn->prepare("SELECT primo_ordine_effettuato FROM Utenti WHERE id_utente = ?");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user_result = $stmt_user->get_result()->fetch_assoc();
$primo_ordine_effettuato = $user_result['primo_ordine_effettuato'];
$stmt_user->close();

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

$costo_consegna = $primo_ordine_effettuato ? 2.00 : 0.00;
$messaggio_sconto = !$primo_ordine_effettuato ? "Consegna gratuita applicata per il tuo primo ordine!" : "";

$subtotal = 0;
foreach ($cart as $item) {
    $subtotal += $item['prezzo'] ?? 0;
}
$total = $subtotal + $costo_consegna;

$page_title = "Checkout";
include_once __DIR__ . '/../templates/header.php';
?>
<link rel="stylesheet" href="css/checkout.css">
<link rel="stylesheet" href="css/order_success.css">

<main class="checkout-container">
    <div class="checkout-details">
        <form action="process_order.php" method="POST" id="final-checkout-form">
            <section class="checkout-section">
                <h2>1. Riepilogo Ordine</h2>
                <div class="summary-items-checkout">
                    <?php foreach ($cart as $index => $item):
                        $nome_prodotto = $item['nome'] ?? 'Prodotto non definito';
                        $prezzo_prodotto = $item['prezzo'] ?? 0;
                        $immagine_prodotto = $item['immagine'] ?? 'img/paninocomponibile.png';

                        if (str_starts_with($nome_prodotto, 'Panino Normale') || str_starts_with($nome_prodotto, 'Panino Grande') || str_starts_with($nome_prodotto, 'Panino Maxi')) {
                            if (str_starts_with($nome_prodotto, 'Panino Normale')) $nome_prodotto = 'Panino Normale Composto';
                            if (str_starts_with($nome_prodotto, 'Panino Grande')) $nome_prodotto = 'Panino Grande Composto';
                            if (str_starts_with($nome_prodotto, 'Panino Maxi')) $nome_prodotto = 'Panino Maxi Composto';
                            $immagine_prodotto = 'img/paninocomponibile.png';
                        }
                    ?>
                        <div class="summary-item">
                            <img src="<?php echo htmlspecialchars($immagine_prodotto); ?>" alt="" class="item-image">
                            <div class="item-details">
                                <span class="item-name"><?php echo htmlspecialchars($nome_prodotto); ?></span>
                            </div>
                            <span class="item-price"><?php echo number_format($prezzo_prodotto, 2, ',', ''); ?> €</span>
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
                    <label for="email">Contatto</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?>" autocomplete="email" disabled>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="nome">Nome</label>
                        <input type="text" id="nome" name="nome" autocomplete="given-name" required>
                    </div>
                    <div class="form-group">
                        <label for="cognome">Cognome</label>
                        <input type="text" id="cognome" name="cognome" autocomplete="family-name" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="aula">Aula (Opzionale)</label>
                    <input type="text" id="aula" name="aula">
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
        <?php if ($messaggio_sconto): ?>
            <div class="recap-line success-message" style="display: block; text-align: center; background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 0.9rem;"><?php echo $messaggio_sconto; ?></div>
        <?php endif; ?>
        <div class="summary-recap">
            <div class="recap-line">
                <span>Subtotale</span>
                <span><?php echo number_format($subtotal, 2, ',', ''); ?> €</span>
            </div>
            <div class="recap-line">
                <span>Costo Consegna</span>
                <span><?php echo number_format($costo_consegna, 2, ',', ''); ?> €</span>
            </div>
            <div class="recap-line total">
                <span>Totale</span>
                <span><?php echo number_format($total, 2, ',', ''); ?> €</span>
            </div>
        </div>
    </aside>
</main>

<div id="success-message" class="success-container" style="display: none;">
    <div class="success-card">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <h1>Pagamento andato a buon fine!</h1>
        <p>Il tuo ordine è stato ricevuto e stiamo già iniziando a prepararlo. Riceverai una notifica per ogni aggiornamento.</p>
        <div class="success-actions">
            <a href="whoweare.php" class="btn-secondary">Scopri chi siamo</a>
            <a href="account.php" class="btn-primary">Vai ai miei ordini</a>
        </div>
    </div>
</div>

<script>
    const fasceOrarie = <?php echo json_encode($fasce_orarie_per_giorno); ?>;
</script>
<script src="js/checkout.js"></script>
<?php include_once __DIR__ . '/../templates/footer.php'; ?>