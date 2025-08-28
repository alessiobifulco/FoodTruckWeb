<?php
require_once __DIR__ . '/../config/db.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$is_logged_in = isset($_SESSION['user_id']);

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
    <article class="hero-panel">
        <ul class="promo-list">
            <li><span class="fas fa-shipping-fast" aria-hidden="true"></span><span>Consegna gratuita sul tuo primo ordine!</span></li>
            <li><span class="fas fa-tag" aria-hidden="true"></span><span>Panino con cotoletta a soli 3.50 euro!</span></li>
            <li><span class="fas fa-motorcycle" aria-hidden="true"></span><span>Ordina e ricevi direttamente in aula!</span></li>
            <li><span class="far fa-clock" aria-hidden="true"></span><span>Ordina il giorno prima per non pensarci troppo!</span></li>
        </ul>
        <div class="hero-cta">
            <p>Registrati o accedi per iniziare l'ordine</p>
            <a href="<?php echo $is_logged_in ? 'order.php' : 'login.php'; ?>" class="btn-hero">Inizia a ordinare!</a>
        </div>
    </article>
</section>

<main class="page-container">
    <section class="menu-section">
        <section class="menu-dark-container">
            <header class="content-wrapper">
                <h2 class="menu-title">Scopri il nostro fantastico menu</h2>
                <div class="products-scroller" data-animated="true">
                    <ul class="scroller-inner">
                        <?php foreach (array_merge($tutti_i_prodotti, $tutti_i_prodotti) as $prodotto): ?>
                            <li class="product-card">
                                <img src="<?php echo htmlspecialchars($prodotto['path_immagine']); ?>" alt="<?php echo htmlspecialchars($prodotto['nome']); ?>">
                                <div class="product-info">
                                    <h3 class="product-name"><?php echo htmlspecialchars($prodotto['nome']); ?></h3>
                                    <p class="product-description-home"><?php echo htmlspecialchars($prodotto['descrizione']); ?></p>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </header>
        </section>
        <div class="curved-separator"></div>
        <section class="menu-light-container">
            <div class="content-wrapper">
                <article class="custom-order-card">
                    <h3>Componi il tuo Panino!</h3>
                    <p>Scegli i tuoi ingredienti preferiti</p>
                    <a href="<?php echo $is_logged_in ? 'menu.php' : 'login.php'; ?>" class="btn-hero">Inizia a ordinare</a>
                </article>
                <article class="time-slots-card">
                    <h4>Le nostre fasce orarie</h4>
                    <ul>
                        <li>11:00 - 11:30</li>
                        <li>11:30 - 12:00</li>
                        <li>12:00 - 12:30</li>
                        <li>12:30 - 13:00</li>
                    </ul>
                </article>
            </div>
        </section>
    </section>
    <section class="contact-section">
        <div class="content-wrapper">
            <h2 class="contact-title">Se hai qualche domanda contattaci!</h2>
            <?php
            if (isset($_GET['status']) && $_GET['status'] == 'error') {
                echo '<p class="error-message">Per favore, compila tutti i campi correttamente.</p>';
            }
            if (isset($_GET['status']) && $_GET['status'] == 'dberror') {
                echo '<p class="error-message">Errore del server. Riprova più tardi.</p>';
            }
            ?>
            <form action="process_contact.php" method="POST" class="contact-form">
                <label for="name-input" class="visually-hidden">Il tuo Nome</label>
                <input type="text" name="name" id="name-input" placeholder="Il tuo Nome" required>
                <label for="email-input" class="visually-hidden">La tua Email</label>
                <input type="email" name="email" id="email-input" placeholder="La tua Email" required>
                <label for="message-textarea" class="visually-hidden">Il tuo Messaggio</label>
                <textarea name="message" id="message-textarea" rows="4" placeholder="Il tuo Messaggio" required></textarea>
                <button type="submit" class="btn-submit">Invia</button>
            </form>
            <p class="footer-summary-text">Di fianco all'università • Dal lunedì al venerdì • Disponibile solo a pranzo</p>
        </div>
    </section>
</main>

<?php include_once __DIR__ . '/../templates/footer.php'; ?>