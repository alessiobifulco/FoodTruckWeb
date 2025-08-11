<?php
require_once __DIR__ . '/../vendor/autoload.php';



require_once __DIR__ . '/../config/db.php';

$page_title = "Benvenuto su Food Truck";

include_once __DIR__ . '/../templates/header.php';
?>

<section class="hero-container">

    <h1 class="hero-main-title">Food Truck</h1>

    <div class="hero-panel">
        
        <ul class="promo-list">
            <li>
                <i class="fas fa-percent"></i>
                <span>Sconto del 5% per il tuo primo ordine!</span>
            </li>
            <li>
                <i class="fas fa-tag"></i>
                <span>Panino con cotoletta a soli 3.50 euro!</span>
            </li>
            <li>
                <i class="fas fa-motorcycle"></i>
                <span>Ordina e ricevi direttamente in aula!</span>
            </li>
            <li>
                <i class="far fa-clock"></i>
                <span>Ordina il giorno prima per non pensarci troppo!</span>
            </li>
        </ul>

        <div class="hero-cta">
            <p>Registrati o accedi per iniziare l'ordine</p>
            <a href="login.php" class="btn-hero">Inizia a ordinare!</a>
        </div>

    </div>
</section>
<section class="menu-section">

    <div class="menu-dark-container">
        <h2 class="menu-title">Scopri il nostro fantastico menu</h2>
        
        <div class="products-scroller">
            <div class="product-card">
                <img src="img/paninocotoletta.png" alt="Panino con Cotoletta">
                <div class="product-info">
                    <h3 class="product-name">Panino Cotoletta</h3>
                    <span class="product-price">€3.50</span>
                </div>
            </div>

            <div class="product-card">
                <img src="img/pizzettamargherita.png" alt="Pizzetta Margherita">
                <div class="product-info">
                    <h3 class="product-name">Pizzetta Margherita</h3>
                    <span class="product-price">€2.50</span>
                </div>
            </div>

            </div>
    </div>

    <div class="curved-separator"></div>

    <div class="menu-light-container">
        <div class="custom-order-card">
            <h3>Componi il tuo Panino!</h3>
            <p>Scegli i tuoi ingredienti preferiti</p>
            <a href="menu.php" class="btn-hero">Inizia a ordinare</a>
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

</section>
<section class="contact-section">
    <h2 class="contact-title">Se hai qualche domanda contattaci!</h2>

    <form action="#" method="POST" class="contact-form">
        <input type="text" name="name" placeholder="Nome">
        <input type="email" name="email" placeholder="Email">
        <textarea name="message" rows="4" placeholder="Messaggio"></textarea>
        <button type="submit" class="btn-submit">Invia</button>
    </form>

    <p class="footer-summary-text">
        Di fianco all'università • Dal lunedì al venerdì • Disponibile solo a pranzo
    </p>
</section>

<?php
include_once __DIR__ . '/../templates/footer.php';
?>