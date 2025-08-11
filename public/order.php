<?php
require_once __DIR__ . '/../config/db.php';
$page_title = "Come Ordinare";
include_once __DIR__ . '/../templates/header.php';

// Esempio per i più venduti (da rendere dinamico in futuro)
$piu_venduti = [
    [
        'nome' => 'Panino con Cotoletta',
        'descrizione' => 'Pane arabo, cotoletta, insalata, maionese',
        'path_immagine' => 'img/paninocotoletta.png'
    ],
    [
        'nome' => 'Pizzetta Margherita',
        'descrizione' => 'Pomodoro, mozzarella',
        'path_immagine' => 'img/pizzettamargherita.png'
    ]
];
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <link rel="stylesheet" href="css/order.css">
</head>

<body>

    <div class="order-page-vertical-container">

        <section class="selection-section">
            <h2>1. Scegli Giorno e Orario</h2>
            <p class="section-subtitle">Una volta nel menu, potrai selezionare il giorno (oggi o domani) e la fascia oraria disponibile per la consegna.</p>
        </section>

        <section class="selection-section">
            <h2>2. Scegli Cosa Ordinare</h2>
            <p class="section-subtitle">Scegli tra i nostri panini, le pizzette oppure componi il tuo panino da zero!</p>
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
            <h2>I più venduti</h2>
            <div class="products-scroller">
                <?php foreach ($piu_venduti as $prodotto): ?>
                    <div class="product-card">
                        <img src="<?php echo htmlspecialchars($prodotto['path_immagine']); ?>" alt="<?php echo htmlspecialchars($prodotto['nome']); ?>">
                        <div class="product-info">
                            <h3 class="product-name"><?php echo htmlspecialchars($prodotto['nome']); ?></h3>
                            <p class="product-description-small"><?php echo htmlspecialchars($prodotto['descrizione']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
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
            <h2>3. Conferma l'Ordine e Paga</h2>
            <div class="summary-card text-center">
                <i class="fas fa-shopping-cart summary-icon"></i>
                <p>
                    Dopo aver scelto i prodotti, nel menu troverai il riepilogo finale. <br>
                    Ricorda che **puoi anche inserire il numero dell'aula** per la consegna!
                </p>
            </div>
        </section>

    </div>

    <?php
    include_once __DIR__ . '/../templates/footer.php';
    ?>