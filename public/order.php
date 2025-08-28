<?php
require_once __DIR__ . '/../config/db.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$page_title = "Come Ordinare";
include_once __DIR__ . '/../templates/header.php';

$piu_venduti = [];
$sql_venduti = "SELECT nome, descrizione, path_immagine FROM Prodotti WHERE id_prodotto IN (1, 4) LIMIT 2";
$result_venduti = $conn->query($sql_venduti);
if ($result_venduti) {
    while ($row = $result_venduti->fetch_assoc()) {
        $piu_venduti[] = $row;
    }
}
?>
<link rel="stylesheet" href="css/order.css">
<div class="order-page-vertical-container">
    <section class="selection-section">
        <h2>1. Scegli i tuoi Prodotti</h2>
        <p class="section-subtitle">Naviga nel nostro menu e aggiungi al carrello tutto quello che desideri, dai panini classici alle bevande, oppure componi il tuo panino da zero!</p>
        <div class="order-options-grid">
            <a href="menu.php#panini" class="option-card"><img src="img/paninocotoletta.png" alt="I nostri Panini">
                <div class="card-text">
                    <h3>I nostri Panini</h3>
                    <p>Sfoglia i nostri classici</p>
                </div>
            </a>
            <a href="menu.php#pizzette" class="option-card"><img src="img/pizzettamargherita.png" alt="Le nostre Pizzette">
                <div class="card-text">
                    <h3>Le nostre Pizzette</h3>
                    <p>Scopri le nostre pizzette</p>
                </div>
            </a>
            <a href="menu.php#componi" class="option-card dark-card"><img src="img/paninocomponibile.png" alt="Componi il tuo Panino">
                <div class="card-text">
                    <h3>Componi il tuo Panino!</h3>
                    <p>Crea il tuo panino perfetto</p>
                </div>
            </a>
        </div>
    </section>

    <section class="selection-section">
        <h2>2. Scegli Data e Orario</h2>
        <p class="section-subtitle">Una volta scelti i prodotti, vai al checkout. Lì potrai selezionare il giorno e la fascia oraria che preferisci per la consegna.</p>
    </section>

    <section class="selection-section">
        <h2>3. Conferma e Paga</h2>
        <p class="section-subtitle">Nella pagina di checkout, dopo aver scelto l'orario, potrai inserire i tuoi dati, aggiungere note e completare l'ordine con il pagamento.</p>
    </section>

    <section class="selection-section">
        <h2>4. Attendi la Conferma</h2>
        <p class="section-subtitle">Fatto! Riceverai una notifica sul sito per ogni cambio di stato del tuo ordine, dalla preparazione fino alla consegna.</p>
    </section>

    <section class="selection-section">
        <div class="cta-banner">
            <h2>Pronto per ordinare?</h2>
            <p>Tranquillo, sfoglia il nostro menu completo!</p>
            <a href="menu.php" class="btn-hero">Sfoglia il Menu</a>
        </div>
    </section>
</div>

<?php include_once __DIR__ . '/../templates/footer.php'; ?>