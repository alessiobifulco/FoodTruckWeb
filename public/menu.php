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

$page_title = "Il Nostro Menu";
include_once __DIR__ . '/../templates/header.php';

$selected_day = $_GET['day'] ?? ($_SESSION['delivery_day'] ?? 'today');
$selected_time = $_GET['time'] ?? ($_SESSION['delivery_time'] ?? null);

if ($selected_day === 'tomorrow') {
    $selected_date_obj = new DateTime('tomorrow');
} else {
    $selected_date_obj = new DateTime('today');
}
$selected_date_str = $selected_date_obj->format('Y-m-d');
$day_of_week_english = strtolower($selected_date_obj->format('l'));
$giorni_italiano = ['monday' => 'lunedi', 'tuesday' => 'martedi', 'wednesday' => 'mercoledi', 'thursday' => 'giovedi', 'friday' => 'venerdi'];
$giorno_settimana_db = $giorni_italiano[$day_of_week_english] ?? '';

$fasce_orarie = [];
if ($giorno_settimana_db) {
    $stmt = $conn->prepare("SELECT fo.ora_inizio, fo.ora_fine, fo.capacita_massima, sfg.stato_giornaliero, sfg.numero_ordini_correnti FROM FasceOrarie fo LEFT JOIN StatoFasceGiornaliere sfg ON fo.id_fascia = sfg.id_fascia AND sfg.data_riferimento = ? WHERE fo.giorno_settimana = ? AND fo.attiva = TRUE ORDER BY fo.ora_inizio");
    $stmt->bind_param("ss", $selected_date_str, $giorno_settimana_db);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $fasce_orarie[] = $row;
    }
    $stmt->close();
}

$prodotti_per_categoria = [];
$sql_prodotti = "SELECT * FROM Prodotti WHERE disponibile = TRUE ORDER BY FIELD(categoria, 'panino_predefinito', 'pizzetta', 'panino_componibile', 'bevanda')";
$result_prodotti = $conn->query($sql_prodotti);
if ($result_prodotti) {
    while ($row = $result_prodotti->fetch_assoc()) {
        $prodotti_per_categoria[$row['categoria']][] = $row;
    }
}

$ingredienti_per_categoria = [];
$sql_ingredienti = "SELECT nome, categoria_ingrediente FROM Ingredienti WHERE disponibile = TRUE ORDER BY FIELD(categoria_ingrediente, 'pane', 'proteina', 'contorno', 'salsa')";
$result_ingredienti = $conn->query($sql_ingredienti);
if ($result_ingredienti) {
    while ($row = $result_ingredienti->fetch_assoc()) {
        $ingredienti_per_categoria[$row['categoria_ingrediente']][] = $row;
    }
}

$_SESSION['delivery_day'] = $selected_day === 'today' ? 'Oggi' : 'Domani';
$_SESSION['delivery_time'] = $selected_time ?? 'Nessuna';
?>
<link rel="stylesheet" href="css/menu.css">
<main class="menu-page-container">
    <div class="product-list-container">
        <section class="time-selection-menu">
            <h3>Scegli Giorno e Ora di Consegna</h3>
            <div class="day-selector">
                <a href="menu.php?day=today" class="day-selector-btn <?php echo ($selected_day === 'today') ? 'active' : ''; ?>">Oggi</a>
                <a href="menu.php?day=tomorrow" class="day-selector-btn <?php echo ($selected_day === 'tomorrow') ? 'active' : ''; ?>">Domani</a>
            </div>
            <div class="time-slots">
                <?php if (empty($fasce_orarie)): ?>
                    <p class="no-slots-message">Nessuna fascia oraria disponibile.</p>
                <?php else: ?>
                    <?php foreach ($fasce_orarie as $fascia):
                        $is_full = ($fascia['stato_giornaliero'] === 'piena' || ($fascia['numero_ordini_correnti'] ?? 0) >= $fascia['capacita_massima']);
                        $label = date('H:i', strtotime($fascia['ora_inizio'])) . ' - ' . date('H:i', strtotime($fascia['ora_fine']));
                        $is_active = ($label === $selected_time);
                    ?>
                        <button class="time-slot-btn <?php if ($is_full) echo 'disabled';
                                                        if ($is_active) echo ' active'; ?>"
                            data-timeslot="<?php echo $label; ?>" <?php if ($is_full) echo 'disabled'; ?>><?php echo $label; ?></button>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
        <div class="search-bar-container"><i class="fas fa-search"></i><input type="search" placeholder="Cerca un prodotto..."></div>
        <nav class="category-nav">
            <a href="#panini" class="category-link active">Panini</a>
            <a href="#pizzette" class="category-link">Pizzette</a>
            <a href="#componi" class="category-link">Componi</a>
            <a href="#bevande" class="category-link">Bevande</a>
        </nav>
        <section id="panini" class="product-category">
            <h2>Panini</h2>
            <?php foreach ($prodotti_per_categoria['panino_predefinito'] ?? [] as $prodotto): ?>
                <div class="product-item">
                    <img src="<?php echo htmlspecialchars($prodotto['path_immagine']); ?>" alt="<?php echo htmlspecialchars($prodotto['nome']); ?>" class="product-item-image">
                    <div class="product-details">
                        <h3><?php echo htmlspecialchars($prodotto['nome']); ?></h3>
                        <p><?php echo htmlspecialchars($prodotto['descrizione']); ?></p>
                        <span class="product-price"><?php echo number_format($prodotto['prezzo'], 2, ',', ''); ?> €</span>
                    </div>
                    <div class="product-action"><button class="add-to-cart-btn" data-id="<?php echo $prodotto['id_prodotto']; ?>" data-nome="<?php echo htmlspecialchars($prodotto['nome']); ?>" data-prezzo="<?php echo $prodotto['prezzo']; ?>" data-immagine="<?php echo htmlspecialchars($prodotto['path_immagine']); ?>">AGGIUNGI</button></div>
                </div>
            <?php endforeach; ?>
        </section>
        <section id="pizzette" class="product-category">
            <h2>Pizzette</h2>
            <?php foreach ($prodotti_per_categoria['pizzetta'] ?? [] as $prodotto): ?>
                <div class="product-item">
                    <img src="<?php echo htmlspecialchars($prodotto['path_immagine']); ?>" alt="<?php echo htmlspecialchars($prodotto['nome']); ?>" class="product-item-image">
                    <div class="product-details">
                        <h3><?php echo htmlspecialchars($prodotto['nome']); ?></h3>
                        <p><?php echo htmlspecialchars($prodotto['descrizione'] ?? ''); ?></p>
                        <span class="product-price"><?php echo number_format($prodotto['prezzo'], 2, ',', ''); ?> €</span>
                    </div>
                    <div class="product-action"><button class="add-to-cart-btn" data-id="<?php echo $prodotto['id_prodotto']; ?>" data-nome="<?php echo htmlspecialchars($prodotto['nome']); ?>" data-prezzo="<?php echo $prodotto['prezzo']; ?>" data-immagine="<?php echo htmlspecialchars($prodotto['path_immagine']); ?>">AGGIUNGI</button></div>
                </div>
            <?php endforeach; ?>
        </section>
        <section id="componi" class="product-category">
            <h2>Componi il tuo Panino</h2>
            <?php foreach ($prodotti_per_categoria['panino_componibile'] ?? [] as $prodotto):
                $limiti = ['pane' => 1, 'proteina' => 1, 'contorno' => 1, 'salsa' => 1];
                if (strpos($prodotto['nome'], 'Grande') !== false) {
                    $limiti = ['pane' => 1, 'proteina' => 1, 'contorno' => 2, 'salsa' => 2];
                } elseif (strpos($prodotto['nome'], 'Maxi') !== false) {
                    $limiti = ['pane' => 1, 'proteina' => 2, 'contorno' => 3, 'salsa' => 2];
                }
            ?>
                <div class="product-item">
                    <img src="<?php echo htmlspecialchars($prodotto['path_immagine']); ?>" alt="Componi il tuo panino" class="product-item-image">
                    <div class="product-details">
                        <h3><?php echo htmlspecialchars(str_replace(' (base)', '', $prodotto['nome'])); ?></h3>
                        <p><?php echo htmlspecialchars($prodotto['descrizione']); ?></p>
                        <span class="product-price"><?php echo number_format($prodotto['prezzo'], 2, ',', ''); ?> €</span>
                    </div>
                    <div class="product-action">
                        <button class="open-overlay-btn" data-id="<?php echo $prodotto['id_prodotto']; ?>" data-prezzo="<?php echo $prodotto['prezzo']; ?>" data-limite-pane="<?php echo $limiti['pane']; ?>" data-limite-proteina="<?php echo $limiti['proteina']; ?>" data-limite-contorno="<?php echo $limiti['contorno']; ?>" data-limite-salsa="<?php echo $limiti['salsa']; ?>" data-nome-panino="<?php echo htmlspecialchars(str_replace(' (base)', '', $prodotto['nome'])); ?>" data-immagine="<?php echo htmlspecialchars($prodotto['path_immagine']); ?>">SCEGLI</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </section>
        <section id="bevande" class="product-category">
            <h2>Bevande</h2>
            <?php foreach ($prodotti_per_categoria['bevanda'] ?? [] as $prodotto): ?>
                <div class="product-item">
                    <img src="<?php echo htmlspecialchars($prodotto['path_immagine']); ?>" alt="<?php echo htmlspecialchars($prodotto['nome']); ?>" class="product-item-image">
                    <div class="product-details">
                        <h3><?php echo htmlspecialchars($prodotto['nome']); ?></h3>
                        <p><?php echo htmlspecialchars($prodotto['descrizione'] ?? ''); ?></p>
                        <span class="product-price"><?php echo number_format($prodotto['prezzo'], 2, ',', ''); ?> €</span>
                    </div>
                    <div class="product-action"><button class="add-to-cart-btn" data-id="<?php echo $prodotto['id_prodotto']; ?>" data-nome="<?php echo htmlspecialchars($prodotto['nome']); ?>" data-prezzo="<?php echo $prodotto['prezzo']; ?>" data-immagine="<?php echo htmlspecialchars($prodotto['path_immagine']); ?>">AGGIUNGI</button></div>
                </div>
            <?php endforeach; ?>
        </section>
    </div>
    <aside class="cart-container">
        <form action="checkout.php" method="POST" id="cart-form">
            <div class="cart-card">
                <h3>Riepilogo Ordine</h3>
                <ul id="cart-items-list" class="cart-items-list">
                    <li class="empty-cart-message">Il carrello è vuoto</li>
                </ul>
                <div class="cart-summary">
                    <div class="summary-delivery">
                        <p><strong>Giorno:</strong> <span id="summary-day"><?php echo $_SESSION['delivery_day']; ?></span></p>
                        <p><strong>Orario:</strong> <span id="summary-time"><?php echo $_SESSION['delivery_time']; ?></span></p>
                    </div>
                    <div class="summary-total"><span>Totale</span><span id="summary-total-price">0,00 €</span></div>
                    <button type="submit" class="btn-checkout" disabled>Vai al Checkout</button>
                </div>
            </div>
            <input type="hidden" name="cart_data" id="cart_data_input">
            <input type="hidden" name="delivery_day" id="delivery_day_input">
            <input type="hidden" name="delivery_time" id="delivery_time_input">
        </form>
    </aside>
</main>
<div id="componi-panino-overlay" class="overlay-container">
    <div class="overlay-content">
        <button id="close-overlay-btn" class="close-btn">&times;</button>
        <h3 id="overlay-title">Componi il tuo Panino</h3>
        <p id="overlay-description" class="overlay-subtitle"></p>
        <div class="ingredient-picker">
            <div class="ingredient-category" data-categoria="pane">
                <h4>Scegli il Pane <span class="required-badge">1 Obbligatorio</span></h4>
                <?php foreach ($ingredienti_per_categoria['pane'] ?? [] as $ingrediente): ?>
                    <label class="ingredient-option"><input type="radio" name="pane" data-nome="<?php echo htmlspecialchars($ingrediente['nome']); ?>"> <span><?php echo htmlspecialchars($ingrediente['nome']); ?></span></label>
                <?php endforeach; ?>
            </div>
            <div class="ingredient-category" data-categoria="proteina">
                <h4 id="proteina-title">Scegli la Proteina</h4>
                <?php foreach ($ingredienti_per_categoria['proteina'] ?? [] as $ingrediente): ?>
                    <label class="ingredient-option"><input type="checkbox" name="proteina[]" data-nome="<?php echo htmlspecialchars($ingrediente['nome']); ?>"> <span><?php echo htmlspecialchars($ingrediente['nome']); ?></span></label>
                <?php endforeach; ?>
            </div>
            <div class="ingredient-category" data-categoria="contorno">
                <h4 id="contorno-title">Scegli il Contorno</h4>
                <?php foreach ($ingredienti_per_categoria['contorno'] ?? [] as $ingrediente): ?>
                    <label class="ingredient-option"><input type="checkbox" name="contorno[]" data-nome="<?php echo htmlspecialchars($ingrediente['nome']); ?>"> <span><?php echo htmlspecialchars($ingrediente['nome']); ?></span></label>
                <?php endforeach; ?>
            </div>
            <div class="ingredient-category" data-categoria="salsa">
                <h4 id="salsa-title">Scegli la Salsa</h4>
                <?php foreach ($ingredienti_per_categoria['salsa'] ?? [] as $ingrediente): ?>
                    <label class="ingredient-option"><input type="checkbox" name="salsa[]" data-nome="<?php echo htmlspecialchars($ingrediente['nome']); ?>"> <span><?php echo htmlspecialchars($ingrediente['nome']); ?></span></label>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="overlay-footer">
            <div class="quantity-selector"><button type="button">-</button><span>1</span><button type="button">+</button></div>
            <button id="add-custom-panino-btn" class="btn-submit" disabled>Aggiungi al Carrello</button>
        </div>
    </div>
</div>
<script>
    const serverCart = <?php echo isset($_SESSION['cart']) ? json_encode(array_values($_SESSION['cart'])) : 'null'; ?>;
</script>
<script src="js/menu.js" defer></script>
<?php include_once __DIR__ . '/../templates/footer.php'; ?>