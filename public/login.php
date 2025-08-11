<?php
// Avvio la sessione se non è già attiva
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../vendor/autoload.php';


// Il tuo file di configurazione che crea l'oggetto $conn
require_once __DIR__ . '/../config/db.php';

$page_title = "Accedi o Registrati";
$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Gestione del form di LOGIN
    if (isset($_POST['login_submit'])) {
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        if (empty($email) || empty($password)) {
            $error_message = 'Email e password sono obbligatorie.';
        } else {
            // Sintassi MySQLi con prepared statements
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
                    header('Location: adminPage/adminDashboard.php');
                } else {
                    header('Location: order.php');
                }
                exit;
            } else {
                $error_message = 'Credenziali non valide.';
            }
            $stmt->close();
        }
    }
    // Gestione del form di REGISTRAZIONE
    elseif (isset($_POST['register_submit'])) {
        $email_reg = trim($_POST['email_reg']);
        $password_reg = $_POST['password_reg'];
        $confirm_password_reg = $_POST['confirm_password_reg'];
        // Ho rimosso il campo 'name' perché non è presente nella tabella Utenti

        if (empty($email_reg) || empty($password_reg) || empty($confirm_password_reg)) {
            $error_message = 'Tutti i campi sono obbligatori per la registrazione.';
        } elseif ($password_reg !== $confirm_password_reg) {
            $error_message = 'Le password non corrispondono.';
        } elseif (!filter_var($email_reg, FILTER_VALIDATE_EMAIL)) {
            $error_message = 'Formato email non valido.';
        } else {
            // Controlla se l'email esiste già
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
                    $success_message = 'Registrazione effettuata con successo! Ora puoi accedere.';
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

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-tabs">
            <button id="show-login" class="auth-tab-btn active" type="button">Accedi</button>
            <button id="show-register" class="auth-tab-btn" type="button">Registrati</button>
        </div>

        <div class="auth-form-container">
            <?php if ($error_message): ?>
                <div class="message error-message"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <?php if ($success_message): ?>
                <div class="message success-message"><?php echo htmlspecialchars($success_message); ?></div>
            <?php endif; ?>

            <form id="login-form" action="login.php" method="POST" class="auth-form">
                <h2 class="form-title">Accedi al tuo account</h2>
                <div class="form-group">
                    <label for="login-email">Email</label>
                    <input type="email" id="login-email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="login-password">Password</label>
                    <input type="password" id="login-password" name="password" required>
                    <a href="for_pass.php" class="form-link">Password dimenticata?</a>
                </div>
                <button type="submit" name="login_submit" class="btn-submit">Accedi</button>
            </form>

            <form id="register-form" action="login.php" method="POST" class="auth-form" style="display: none;">
                <h2 class="form-title">Crea un nuovo account</h2>
                <div class="form-group">
                    <label for="register-email">Email</label>
                    <input type="email" id="register-email" name="email_reg" required>
                </div>
                <div class="form-group">
                    <label for="register-password">Password</label>
                    <input type="password" id="register-password" name="password_reg" required>
                </div>
                <div class="form-group">
                    <label for="register-confirm-password">Conferma Password</label>
                    <input type="password" id="register-confirm-password" name="confirm_password_reg" required>
                </div>
                <button type="submit" name="register_submit" class="btn-submit">Registrati</button>
            </form>
        </div>
    </div>
</div>

<?php
include_once __DIR__ . '/../templates/footer.php';
?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const showLoginBtn = document.getElementById('show-login');
        const showRegisterBtn = document.getElementById('show-register');
        const loginForm = document.getElementById('login-form');
        const registerForm = document.getElementById('register-form');

        showLoginBtn.addEventListener('click', () => {
            loginForm.style.display = 'block';
            registerForm.style.display = 'none';
            showLoginBtn.classList.add('active');
            showRegisterBtn.classList.remove('active');
        });

        showRegisterBtn.addEventListener('click', () => {
            loginForm.style.display = 'none';
            registerForm.style.display = 'block';
            showLoginBtn.classList.remove('active');
            showRegisterBtn.classList.add('active');
        });

        <?php if (isset($_POST['register_submit'])): ?>
            showRegisterBtn.click();
        <?php endif; ?>
    });
</script>