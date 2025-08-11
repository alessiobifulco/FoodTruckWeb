<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../vendor/autoload.php';



require_once __DIR__ . '/../config/db.php';

$page_title = "Recupera Password";
$feedback_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $feedback_message = 'Per favore, inserisci un indirizzo email valido.';
        $is_error = true;
    } else {
        $stmt = $conn->prepare("SELECT id_utente FROM Utenti WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($user = $result->fetch_assoc()) {
            $id_utente = $user['id_utente'];
            $messaggio = "Abbiamo ricevuto una richiesta di reset password. Controlla la tua casella di posta per le istruzioni (simulazione).";
            $tipo_notifica = 'messaggio_generico'; 

            $insert_stmt = $conn->prepare("INSERT INTO Notifiche (id_utente_destinatario, messaggio, tipo_notifica) VALUES (?, ?, ?)");
            $insert_stmt->bind_param("iss", $id_utente, $messaggio, $tipo_notifica);
            $insert_stmt->execute();
            $insert_stmt->close();
        }
        $stmt->close();

        $feedback_message = 'Se un account con questa email esiste, abbiamo inviato le istruzioni per il reset tramite una notifica sul sito.';
    }
}

include_once __DIR__ . '/../templates/header.php';
?>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-form-container">
            
            <h2 class="form-title">Password Dimenticata</h2>
            <p style="text-align: center; margin-top: -15px; margin-bottom: 25px; color: #666;">Inserisci la tua email per ricevere le istruzioni di recupero.</p>

            <?php if ($feedback_message): ?>
                <div class="message <?php echo isset($is_error) ? 'error-message' : 'success-message'; ?>">
                    <?php echo htmlspecialchars($feedback_message); ?>
                </div>
            <?php endif; ?>

            <?php if (empty($feedback_message) || isset($is_error)): // Mostra il form solo se non è stato inviato con successo ?>
            <form action="forgot_password.php" method="POST" class="auth-form">
                <div class="form-group">
                    <label for="email">La tua Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <button type="submit" class="btn-submit">Invia Istruzioni</button>
            </form>
            <?php endif; ?>

            <div style="text-align: center; margin-top: 20px;">
                <a href="login.php" class="form-link">Torna al Login</a>
            </div>
        </div>
    </div>
</div>

<?php
include_once __DIR__ . '/../templates/footer.php';
?>