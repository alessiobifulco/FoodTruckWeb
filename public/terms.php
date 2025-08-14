<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/db.php';
$page_title = "Termini e Condizioni";
include_once __DIR__ . '/../templates/header.php';
?>
<link rel="stylesheet" href="css/pages.css">

<main class="static-page-container">
    <h1>Termini e Condizioni</h1>
    <h2>1. Accettazione dei Termini</h2>
    <p>Utilizzando il servizio <strong>Food Truck</strong>, accetti di rispettare e di essere vincolato dai presenti Termini e Condizioni. Se non sei d'accordo con qualsiasi parte di questi termini, ti invitiamo a non utilizzare il nostro servizio.</p>
    <h2>2. Servizio offerto</h2>
    <p>Food Truck fornisce un servizio di consegna di cibo preparato e confezionato, principalmente rivolto a studenti e lavoratori in aree limitate. La consegna può avvenire direttamente in aula o presso altri punti concordati.</p>
    <h2>3. Ordini e pagamenti</h2>
    <ul>
        <li>Gli ordini devono essere effettuati tramite il nostro sito entro i termini indicati.</li>
        <li>I prezzi dei prodotti sono quelli indicati al momento dell’ordine.</li>
        <li>I pagamenti vengono gestiti tramite provider terzi sicuri.</li>
    </ul>
    <h2>4. Privacy e dati personali</h2>
    <p>Il trattamento dei dati personali avviene secondo quanto descritto nella nostra <a href="privacy.php">Informativa sulla Privacy</a>. Utilizzando il servizio, acconsenti alla raccolta e al trattamento dei tuoi dati secondo tali termini.</p>
    <h2>5. Contatti</h2>
    <p>Per domande relative a questi Termini e Condizioni, contattaci a <a href="mailto:info@foodtruck.it">info@foodtruck.it</a>.</p>
</main>

<?php include_once __DIR__ . '/../templates/footer.php'; ?>