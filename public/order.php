<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/db.php';

$page_title = "Crea il tuo Ordine";
include_once __DIR__ . '/../templates/header.php';

$selected_day = $_GET['day'] ?? 'today';
if ($selected_day === 'tomorrow') {
    $selected_date_obj = new DateTime('tomorrow');
} else {
    $selected_date_obj = new DateTime('today');
}
$selected_date_str = $selected_date_obj->format('Y-m-d');
$day_of_week = strtolower($selected_date_obj->format('l'));
$giorni_italiano = ['monday' => 'lunedi', 'tuesday' => 'martedi', 'wednesday' => 'mercoledi', 'thursday' => 'giovedi', 'friday' => 'venerdi'];
$giorno_settimana_db = $giorni_italiano[$day_of_week] ?? '';

$fasce_orarie = [];
if ($giorno_settimana_db) {
    $stmt = $conn->prepare("
        SELECT fo.ora_inizio, fo.ora_fine, fo.capacita_massima, sfg.stato_giornaliero, sfg.numero_ordini_correnti
        FROM FasceOrarie fo
        LEFT JOIN StatoFasceGiornaliere sfg ON fo.id_fascia = sfg.id_fascia AND sfg.data_riferimento = ?
        WHERE fo.giorno_settimana = ? AND fo.attiva = TRUE
        ORDER BY fo.ora_inizio
    ");
    $stmt->bind_param("ss", $selected_date_str, $giorno_settimana_db);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $fasce_orarie[] = $row;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <link rel="stylesheet" href="css/order.css">
</head>

<body>

    <div class="order-page-vertical-container">

        <section class="selection-section">
            <h2>1. Seleziona Giorno e Ora</h2>
            <div class="day-selector">
                <a href="order.php?day=today" class="day-selector-btn <?php echo ($selected_day === 'today') ? 'active' : ''; ?>">Oggi</a>
                <a href="order.php?day=tomorrow" class="day-selector-btn <?php echo ($selected_day === 'tomorrow') ? 'active' : ''; ?>">Domani</a>
            </div>
            <div class="time-slots">
                <?php if (empty($fasce_orarie)): ?>
                    <p>Nessuna fascia oraria disponibile per il giorno selezionato.</p>
                <?php else: ?>
                    <?php foreach ($fasce_orarie as $fascia):
                        $is_full = ($fascia['stato_giornaliero'] === 'piena' || ($fascia['numero_ordini_correnti'] ?? 0) >= $fascia['capacita_massima']);
                        $label = date('H:i', strtotime($fascia['ora_inizio'])) . ' - ' . date('H:i', strtotime($fascia['ora_fine']));
                    ?>
                        <button class="time-slot-btn <?php echo $is_full ? 'disabled' : ''; ?>" data-timeslot="<?php echo $label; ?>" <?php echo $is_full ? 'disabled' : ''; ?>>
                            <?php echo $label; ?>
                        </button>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <section class="selection-section">
            <h2>2. Scegli cosa ordinare</h2>
            <div class="order-options-grid">
                <a href="menu.php#panini" class="option-card"><img src="img/paninocotoletta.png" alt="I nostri Panini">
                    <div class="card-text">
                        <h3>I nostri Panini</h3>
                        <p>Scegli un panino</p>
                    </div>
                </a>
                <a href="menu.php#pizzette" class="option-card"><img src="img/pizzettamargherita.png" alt="Le nostre Pizzette">
                    <div class="card-text">
                        <h3>Le nostre Pizzette</h3>
                        <p>Scegli una pizzetta</p>
                    </div>
                </a>
                <a href="menu.php#componi" class="option-card dark-card"><img src="img/paninocomponibile.png" alt="Componi il tuo Panino">
                    <div class="card-text">
                        <h3>Componi il tuo Panino!</h3>
                        <p>Scegli gli ingredienti</p>
                    </div>
                </a>
            </div>
        </section>

        <section class="selection-section">
            <h2>I più venduti</h2>
            <div class="products-scroller">
                <div class="product-card"><img src="img/paninocotoletta.png" alt="Panino con Cotoletta">
                    <div class="product-info">
                        <h3 class="product-name">Panino Cotoletta</h3><span class="product-price">€4.00</span>
                    </div>
                </div>
                <div class="product-card"><img src="img/pizzettamargherita.png" alt="Pizzetta Margherita">
                    <div class="product-info">
                        <h3 class="product-name">Pizzetta Margherita</h3><span class="product-price">€2.50</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="selection-section">
            <div class="cta-banner">
                <h2>Non sai cosa prendere?</h2>
                <p>Tranquillo, sfoglia il nostro menu completo!</p>
                <a href="menu.php" class="btn-hero">Sfoglia il Menu</a>
            </div>
        </section>

        <section id="riepilogo-ordine" class="selection-section">
            <h2>Riepilogo Ordine</h2>
            <div class="summary-card">
                <ul class="order-items-list">
                    <li class="empty-cart-message">Il tuo carrello è vuoto.</li>
                </ul>
                <div class="summary-footer">
                    <div class="summary-line"><span>Fascia oraria:</span><span id="summary-time-slot">Nessuna</span></div>
                    <div class="total-price"><span>Totale:</span><span>€0,00</span></div>
                    <button class="btn-checkout" disabled>Procedi al pagamento</button>
                </div>
            </div>
        </section>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const timeSlotButtons = document.querySelectorAll('.time-slot-btn:not(.disabled)');
            const summaryTimeSlot = document.getElementById('summary-time-slot');
            timeSlotButtons.forEach(button => {
                button.addEventListener('click', function() {
                    timeSlotButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    const selectedTime = this.getAttribute('data-timeslot');
                    if (summaryTimeSlot) {
                        summaryTimeSlot.textContent = selectedTime;
                    }
                });
            });
        });
    </script>

    <?php
    include_once __DIR__ . '/../templates/footer.php';
    ?>