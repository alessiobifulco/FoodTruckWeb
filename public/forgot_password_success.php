<?php
$page_title = "Istruzioni Inviate";
include_once __DIR__ . '/../templates/header.php';
?>
<link rel="stylesheet" href="css/login.css">
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-form-container" style="text-align: center;">
            <h2 class="form-title">Istruzioni Inviate</h2>
            <p style="margin-bottom: 25px; color: #666;">Se un account con l'email fornita esiste, riceverai una notifica sul sito con le istruzioni.</p>
            <div class="message success-message" style="display: block;">Operazione completata!</div>

            <a href="login.php" class="btn-submit">Torna al Login</a>
        </div>
    </div>
</div>
<?php include_once __DIR__ . '/../templates/footer.php'; ?>