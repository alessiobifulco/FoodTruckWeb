<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db.php';

$page_title = "Recupera Password";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: forgot_password_success.php');
        exit;
    } else {
        $error_message = 'Per favore, inserisci un indirizzo email valido.';
    }
}

include_once __DIR__ . '/../templates/header.php';
?>
<link rel="stylesheet" href="css/login.css">
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-form-container">
            <h2 class="form-title">Password Dimenticata</h2>
            <p style="text-align: center; margin-top: -15px; margin-bottom: 25px; color: #666;">Inserisci la tua email per ricevere le istruzioni di recupero.</p>

            <?php if (isset($error_message)): ?>
                <div class="message error-message" style="display:block;"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <form action="forgot_password.php" method="POST" class="auth-form">
                <div class="form-group">
                    <label for="email">La tua Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <button type="submit" class="btn-submit">Invia Istruzioni</button>
            </form>
            <div style="text-align: center; margin-top: 20px;">
                <a href="login.php" class="form-link">Torna al Login</a>
            </div>
        </div>
    </div>
</div>
<?php include_once __DIR__ . '/../templates/footer.php'; ?>