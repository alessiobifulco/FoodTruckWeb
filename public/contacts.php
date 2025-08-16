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
<main>
    <div id="contact-form-section" class="contact-page-wrapper">
        <div class="contact-form-container">

            <h1 class="contact-title">Contattaci</h1>
            <p class="contact-subtitle">
                Hai domande, suggerimenti o hai bisogno di assistenza con un ordine? Compila il modulo qui sotto e ti risponderemo il prima possibile.
            </p>

            <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
                <div class="message success-message">Messaggio inviato con successo! Ti risponderemo il prima possibile.</div>
            <?php elseif (isset($_GET['status']) && $_GET['status'] == 'error'): ?>
                <div class="message error-message">Per favore, compila tutti i campi e inserisci un'email valida.</div>
            <?php endif; ?>

            <form action="process_contact.php" method="POST" class="contact-form">
                <input type="hidden" name="provenienza" value="contacts.php">
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