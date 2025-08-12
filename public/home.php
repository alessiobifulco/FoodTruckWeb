<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/db.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$is_logged_in = isset($_SESSION['user_id']);

// NUOVA LOGICA: Carica tutti i prodotti per il carosello
$tutti_i_prodotti = [];
$sql = "SELECT nome, descrizione, path_immagine FROM Prodotti WHERE disponibile = TRUE";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $tutti_i_prodotti[] = $row;
    }
}

$page_title = "Benvenuto su Food Truck";
include_once __DIR__ . '/../templates/header.php';
?>
<link rel="stylesheet" href="css/home.css">

<section class="hero-container">
    <h1 class="hero-main-title">Food Truck</h1>
    <div class="hero-panel">
        <ul class="promo-list">
            <li><i class="fas fa-percent"></i><span>Sconto del 5% per il tuo primo ordine!</span></li>
            <li><i class="fas fa-tag"></i><span>Panino con cotoletta a soli 3.50 euro!</span></li>
            <li><i class="fas fa-motorcycle"></i><span>Ordina e ricevi direttamente in aula!</span></li>
            <li><i class="far fa-clock"></i><span>Ordina il giorno prima per non pensarci troppo!</span></li>
        </ul>
        <div class="hero-cta">
            <p>Registrati o accedi per iniziare l'ordine</p>
            <a href="<?php echo $is_logged_in ? 'order.php' : 'login.php'; ?>" class="btn-hero">Inizia a ordinare!</a>
        </div>
    </div>
</section>

<main class="page-container">
    <section class="menu-section">
        <div class="menu-dark-container">
            <div class="content-wrapper">
                <h2 class="menu-title">Scopri il nostro fantastico menu</h2>

                <div class="products-scroller" data-animated="true">
                    <div class="scroller-inner">
                        <?php foreach (array_merge($tutti_i_prodotti, $tutti_i_prodotti) as $prodotto): ?>
                            <div class="product-card">
                                <img src="<?php echo htmlspecialchars($prodotto['path_immagine']); ?>" alt="<?php echo htmlspecialchars($prodotto['nome']); ?>">
                                <div class="product-info">
                                    <h3 class="product-name"><?php echo htmlspecialchars($prodotto['nome']); ?></h3>
                                    <p class="product-description-home"><?php echo htmlspecialchars($prodotto['descrizione']); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="curved-separator"></div>
        <div class="menu-light-container">
            <div class="content-wrapper">
                <div class="custom-order-card">
                    <h3>Componi il tuo Panino!</h3>
                    <p>Scegli i tuoi ingredienti preferiti</p>
                    <a href="<?php echo $is_logged_in ? 'order.php' : 'login.php'; ?>" class="btn-hero">Inizia a ordinare</a>
                </div>
                <div class="time-slots-card">
                    <h4>Le nostre fasce orarie</h4>
                    <ul>
                        <li>11:00 - 11:30</li>
                        <li>11:30 - 12:00</li>
                        <li>12:00 - 12:30</li>
                        <li>12:30 - 13:00</li>
                        <li>13:00 - 14:00</li>
                        <li>14:00 - 14:30</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <section class="contact-section">
        <div class="content-wrapper">
            <h2 class="contact-title">Se hai qualche domanda contattaci!</h2>
            <form action="#" method="POST" class="contact-form">
                <input type="text" name="name" placeholder="Nome"><input type="email" name="email" placeholder="Email">
                <textarea name="message" rows="4" placeholder="Messaggio"></textarea>
                <button type="submit" class="btn-submit">Invia</button>
            </form>
            <p class="footer-summary-text">Di fianco all'università • Dal lunedì al venerdì • Disponibile solo a pranzo</p>
        </div>
    </section>
</main>

<?php include_once __DIR__ . '/../templates/footer.php'; ?>