<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/db.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$page_title = "Contatti - Food Truck";
include_once __DIR__ . '/../templates/header.php';
?>
<link rel="stylesheet" href="css/contacts.css">
<link rel="stylesheet" href="css/home.css">
<main>
    <div class="contact-page-wrapper">
        <div class="contact-form-container">

            <h1 class="contact-title">Contattaci</h1>
            <p class="contact-subtitle">
                Hai domande, suggerimenti o hai bisogno di assistenza con un ordine? Compila il modulo qui sotto e ti risponderemo il prima possibile.
            </p>

            <form action="#" method="POST" class="contact-form">
                <input type="text" name="name" placeholder="Il tuo Nome" required>
                <input type="email" name="email" placeholder="La tua Email" required>
                <textarea name="message" rows="5" placeholder="Il tuo Messaggio" required></textarea>
                <button type="submit" class="btn-submit">Invia Messaggio</button>
            </form>

            <div class="contact-alternative">
                <i class="fas fa-phone-alt"></i>
                <h2>Parla con un Nostro Operatore</h2>
                <p>Preferisci parlare direttamente con noi? Chiamaci al numero:</p>
                <a href="tel:+390547123456" class="phone-number">+39 0547 123456</a>
            </div>

            <div class="contact-location-info">
                <h2 class="contact-title">Dove Trovarci</h2>
                <p class="footer-summary-text">
                    Di fianco all'università • Dal lunedì al venerdì • Disponibile solo a pranzo
                </p>
            </div>

        </div>
    </div>
</main>

<?php include_once __DIR__ . '/../templates/footer.php'; ?>