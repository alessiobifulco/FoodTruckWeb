<?php
require_once __DIR__ . '/../config/db.php';
$page_title = "Privacy Policy";
include_once __DIR__ . '/../templates/header.php';
?>
<link rel="stylesheet" href="css/pages.css">

<main class="static-page-container">
    <h1>Informativa sulla Privacy</h1>
    <h2>1. Introduzione</h2>
    <p>Noi di <strong>Food Truck</strong> teniamo alla tua privacy. Questa informativa, redatta ai sensi dell'art. 13 del Regolamento (UE) 2016/679 (GDPR), spiega come raccogliamo, utilizziamo e proteggiamo i tuoi dati personali quando utilizzi il nostro sito o effettui un ordine.</p>
    <h2>2. Titolare del trattamento</h2>
    <p>Il titolare del trattamento è <strong>Food Truck S.r.l.</strong>, con sede in Via Esempio 123, 00100 Roma (Italia). Email: <a href="mailto:privacy@foodtruck.it">privacy@foodtruck.it</a></p>
    <h2>3. Dati che raccogliamo</h2>
    <ul>
        <li>Dati identificativi (nome, cognome, indirizzo email, numero di telefono)</li>
        <li>Dati di consegna (indirizzo, orari preferiti)</li>
        <li>Dati di pagamento (gestiti in forma criptata dal provider, non da noi)</li>
        <li>Dati di utilizzo del sito (indirizzo IP, pagine visitate, interazioni)</li>
        <li>Preferenze di acquisto e storico ordini</li>
    </ul>
    <h2>4. Finalità del trattamento</h2>
    <ul>
        <li>Gestione degli ordini e consegna dei prodotti</li>
        <li>Miglioramento del servizio e dell’esperienza utente</li>
        <li>Comunicazioni promozionali (previo consenso)</li>
        <li>Adempimenti legali e fiscali</li>
    </ul>
    <h2>5. Diritti dell’utente</h2>
    <p>Ai sensi del GDPR, puoi esercitare i diritti di accesso, rettifica, cancellazione, limitazione, opposizione e portabilità dei dati. Per farlo, contattaci a <a href="mailto:privacy@foodtruck.it">privacy@foodtruck.it</a>.</p>
</main>

<?php include_once __DIR__ . '/../templates/footer.php'; ?>