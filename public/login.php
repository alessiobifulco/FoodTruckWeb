<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/db.php';

$page_title = "Accedi o Registrati";
$error_message = '';
$success_message = '';
$form_to_display = 'login';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login_submit'])) {
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        if (empty($email) || empty($password)) {
            $error_message = 'Email e password sono obbligatorie.';
        } else {
            $stmt = $conn->prepare("SELECT id_utente, email, password, ruolo FROM Utenti WHERE email = ? AND attivo = TRUE");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id_utente'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['ruolo'];
                if ($user['ruolo'] === 'venditore') {
                    header('Location: admin/dashboard.php');
                } else {
                    header('Location: order.php');
                }
                exit;
            } else {
                $error_message = 'Credenziali non valide.';
            }
            $stmt->close();
        }
    } elseif (isset($_POST['register_submit'])) {
        $form_to_display = 'register';
        $email_reg = trim($_POST['email_reg']);
        $password_reg = $_POST['password_reg'];
        $confirm_password_reg = $_POST['confirm_password_reg'];
        if (empty($email_reg) || empty($password_reg) || empty($confirm_password_reg)) {
            $error_message = 'Tutti i campi sono obbligatori.';
        } elseif ($password_reg !== $confirm_password_reg) {
            $error_message = 'Le password non corrispondono.';
        } else {
            $stmt = $conn->prepare("SELECT id_utente FROM Utenti WHERE email = ?");
            $stmt->bind_param("s", $email_reg);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $error_message = 'Questa email è già registrata.';
            } else {
                $hashed_password = password_hash($password_reg, PASSWORD_BCRYPT);
                $insert_stmt = $conn->prepare("INSERT INTO Utenti (email, password, ruolo) VALUES (?, ?, 'cliente')");
                $insert_stmt->bind_param("ss", $email_reg, $hashed_password);
                if ($insert_stmt->execute()) {
                    $new_user_id = $insert_stmt->insert_id;

                    $id_venditore = 2;
                    $messaggio_venditore = "Nuovo utente registrato: " . htmlspecialchars($email_reg);
                    $tipo_notifica_venditore = 'nuovo_utente';
                    $stmt_notifica = $conn->prepare("INSERT INTO Notifiche (id_utente_destinatario, messaggio, tipo_notifica) VALUES (?, ?, ?)");
                    $stmt_notifica->bind_param("iss", $id_venditore, $messaggio_venditore, $tipo_notifica_venditore);
                    $stmt_notifica->execute();

                    $messaggio_cliente = "Benvenuto! Hai diritto alla consegna gratuita sul tuo primo ordine.";
                    $tipo_notifica_cliente = 'sconto_benvenuto';
                    $stmt_notifica->bind_param("iss", $new_user_id, $messaggio_cliente, $tipo_notifica_cliente);
                    $stmt_notifica->execute();
                    $stmt_notifica->close();

                    $_SESSION['user_id'] = $new_user_id;
                    $_SESSION['user_email'] = $email_reg;
                    $_SESSION['user_role'] = 'cliente';
                    header('Location: order.php');
                    exit;
                } else {
                    $error_message = 'Errore durante la registrazione. Riprova.';
                }
                $insert_stmt->close();
            }
            $stmt->close();
        }
    }
}

include_once __DIR__ . '/../templates/header.php';
?>
<link rel="stylesheet" href="css/login.css">

<main class="auth-container">
    <div class="auth-card">
        <div class="auth-tabs">
            <button id="show-login" class="auth-tab-btn active" type="button">Accedi</button>
            <button id="show-register" class="auth-tab-btn" type="button">Registrati</button>
        </div>
        <div class="auth-form-container">
            <?php if ($error_message): ?><div class="message error-message"><?php echo htmlspecialchars($error_message); ?></div><?php endif; ?>
            <?php if ($success_message): ?><div class="message success-message"><?php echo htmlspecialchars($success_message); ?></div><?php endif; ?>

            <form id="login-form" action="login.php" method="POST" class="auth-form">
                <h2 class="form-title">Accedi al tuo account</h2>
                <div class="form-group">
                    <label for="login-email">Email</label>
                    <input type="email" id="login-email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="login-password">Password</label>
                    <input type="password" id="login-password" name="password" required>
                    <a href="forgot_password.php" class="form-link">Password dimenticata?</a>
                </div>
                <button type="submit" name="login_submit" class="btn-submit">Accedi</button>
            </form>

            <form id="register-form" action="login.php" method="POST" class="auth-form" style="display: none;">
                <h2 class="form-title">Crea un nuovo account</h2>
                <div class="form-group">
                    <label for="register-email">Email</label>
                    <input type="email" id="register-email" name="email_reg" required value="<?php echo isset($email_reg) ? htmlspecialchars($email_reg) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="register-password">Password</label>
                    <input type="password" id="register-password" name="password_reg" required>
                    <div id="password-strength-meter">
                        <div id="password-strength-bar"></div>
                        <span id="password-strength-text"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="register-confirm-password">Conferma Password</label>
                    <input type="password" id="register-confirm-password" name="confirm_password_reg" required>
                </div>
                <button type="submit" name="register_submit" class="btn-submit">Registrati</button>
            </form>
        </div>
    </div>
</main>

<?php include_once __DIR__ . '/../templates/footer.php'; ?>
<script>
    const formToDisplay = '<?php echo $form_to_display; ?>';
</script>
<script src="js/login.js"></script>