<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/db.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$page_title = "Il Nostro Menu";
include_once __DIR__ . '/../templates/header.php';

// --- LOGICA PER RECUPERARE I PRODOTTI DAL DATABASE ---
$prodotti_per_categoria = [];
$sql = "SELECT id_prodotto, nome, descrizione, prezzo, categoria, path_immagine FROM Prodotti WHERE disponibile = TRUE ORDER BY FIELD(categoria, 'panino_predefinito', 'pizzetta', 'panino_componibile', 'bevanda')";
$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $prodotti_per_categoria[$row['categoria']][] = $row;
    }
}

// Simuliamo di aver ricevuto giorno e ora dalla pagina precedente (da salvare in sessione)
$_SESSION['giorno_consegna'] = $_SESSION['giorno_consegna'] ?? 'Oggi';
$_SESSION['fascia_oraria'] = $_SESSION['fascia_oraria'] ?? 'Nessuna';
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <link rel="stylesheet" href="css/menu.css">
</head>

<body>

    <div class="menu-page-container">
        <main class="product-list-container">
            <a href="order.php#riepilogo-ordine" class="btn-back-summary">Riepilogo Ordine</a>

            <div class="search-bar-container">
                <i class="fas fa-search"></i>
                <input type="search" placeholder="Cerca un prodotto...">
            </div>

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
                        <div class="product-action"><button class="add-to-cart-btn" data-id="<?php echo $prodotto['id_prodotto']; ?>" data-nome="<?php echo htmlspecialchars($prodotto['nome']); ?>" data-prezzo="<?php echo $prodotto['prezzo']; ?>">AGGIUNGI</button></div>
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
                        <div class="product-action"><button class="add-to-cart-btn" data-id="<?php echo $prodotto['id_prodotto']; ?>" data-nome="<?php echo htmlspecialchars($prodotto['nome']); ?>" data-prezzo="<?php echo $prodotto['prezzo']; ?>">AGGIUNGI</button></div>
                    </div>
                <?php endforeach; ?>
            </section>

            <section id="componi" class="product-category">
                <h2>Componi il tuo Panino</h2>
                <?php foreach ($prodotti_per_categoria['panino_componibile'] ?? [] as $prodotto): ?>
                    <div class="product-item">
                        <img src="img/paninocomponibile.png" alt="Componi il tuo panino" class="product-item-image">
                        <div class="product-details">
                            <h3><?php echo htmlspecialchars($prodotto['nome']); ?></h3>
                            <p><?php echo htmlspecialchars($prodotto['descrizione']); ?></p>
                            <span class="product-price"><?php echo number_format($prodotto['prezzo'], 2, ',', ''); ?> €</span>
                        </div>
                        <div class="product-action"><button class="add-to-cart-btn open-overlay-btn">SCEGLI</button></div>
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
                        <div class="product-action"><button class="add-to-cart-btn" data-id="<?php echo $prodotto['id_prodotto']; ?>" data-nome="<?php echo htmlspecialchars($prodotto['nome']); ?>" data-prezzo="<?php echo $prodotto['prezzo']; ?>">AGGIUNGI</button></div>
                    </div>
                <?php endforeach; ?>
            </section>
        </main>

        <aside class="cart-container">
            <div class="cart-card">
                <h3>Riepilogo Ordine</h3>
                <ul id="cart-items-list" class="cart-items-list">
                    <li class="empty-cart-message">Il carrello è vuoto</li>
                </ul>
                <div class="cart-summary">
                    <div class="summary-delivery">
                        <p><strong>Giorno:</strong> <span id="summary-day"><?php echo $_SESSION['giorno_consegna']; ?></span></p>
                        <p><strong>Orario:</strong> <span id="summary-time"><?php echo $_SESSION['fascia_oraria']; ?></span></p>
                    </div>
                    <div class="summary-total">
                        <span>Totale</span>
                        <span id="summary-total-price">0,00 €</span>
                    </div>
                    <button class="btn-checkout" disabled>Vai al Checkout</button>
                </div>
            </div>
        </aside>
    </div>

    <script src="js/menu.js"></script>
    <?php include_once __DIR__ . '/../templates/footer.php'; ?>