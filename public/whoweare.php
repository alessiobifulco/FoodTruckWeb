<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/db.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$is_logged_in = isset($_SESSION['user_id']);

$page_title = "Chi Siamo - Food Truck";
include_once __DIR__ . '/../templates/header.php';
?>
<link rel="stylesheet" href="css/pages.css">

<main>
    <section class="about-hero">
        <img src="img/chisiamo.jpg" alt="Il nostro food truck" class="about-hero-image">
        <div class="about-hero-title">
            <h1>Chi Siamo</h1>
            <p>Un servizio di consegna veloce, conveniente e gustoso, pensato per studenti e lavoratori sempre in movimento.</p>
        </div>
    </section>

    <div class="about-content-wrapper" style="background-image: url('img/sfondo3.png');">
        <div class="about-inner-content">
            <section class="about-section">
                <div class="about-text">
                    <h2>La nostra Mission</h2>
                    <p>La nostra missione è semplice: portare cibo di qualità direttamente a te, nel minor tempo possibile, mantenendo prezzi accessibili e un servizio amichevole. Crediamo che una pausa pranzo gustosa e comoda possa davvero migliorare la tua giornata.</p>
                </div>
                <div class="about-image">
                    <img src="img/chisiamo2.jpg" alt="La nostra missione">
                </div>
            </section>
            <section class="about-section reverse">
                <div class="about-image">
                    <img src="img/etica.jpg" alt="La nostra etica">
                </div>
                <div class="about-text">
                    <h2>La nostra Etica</h2>
                    <p>Operiamo con trasparenza e rispetto per l'ambiente. Utilizziamo esclusivamente imballaggi sostenibili e biodegradabili e collaboriamo con fornitori locali che condividono la nostra filosofia, privilegiando prodotti freschi, di stagione e a chilometro zero.</p>
                </div>
            </section>
            <section class="about-section">
                <div class="about-text">
                    <h2>Perché scegliere Food Truck?</h2>
                    <ul>
                        <li>Consegna rapida, anche in aula</li>
                        <li>Cibi freschi e gustosi ogni giorno</li>
                        <li>Impegno per la sostenibilità</li>
                        <li>Servizio vicino alle esigenze della comunità</li>
                    </ul>
                </div>
                <div class="about-image">
                    <img src="img/chisiamo3.jpg" alt="Il nostro team">
                </div>
            </section>
            <section class="community-section">
                <h2>Unisciti alla nostra Community</h2>
                <p>
                    Segui le nostre novità, scopri le promozioni e diventa parte della famiglia Food Truck.
                    Per noi non sei solo un cliente, ma parte di una grande tavolata!
                </p>
                <a href="login.php" class="btn-hero">Unisciti!</a>
            </section>
        </div>
    </div>
</main>

<?php include_once __DIR__ . '/../templates/footer.php'; ?>