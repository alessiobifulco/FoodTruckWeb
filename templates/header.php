<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$is_logged_in = isset($_SESSION['user_id']);
$page_title = $page_title ?? 'Food Truck App';
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <header class="header-fixed-top">
        <nav class="header-nav">
            <div class="header-left">
                <a href="index.php" aria-label="Torna alla Home"><span class="fas fa-truck-moving" aria-hidden="true"></span></a>
            </div>
            <div class="header-center">
                <a href="index.php">Food Truck</a>
            </div>
            <div class="header-right">
                <?php if ($is_logged_in): ?>
                    <div class="user-menu-container">
                        <a href="account.php" aria-label="Profilo Utente"><span class="fas fa-user-circle" aria-hidden="true"></span></a>
                        <div class="user-dropdown">
                            <a href="account.php">Il Mio Account</a>
                            <a href="logout.php">Disconnetti</a>
                        </div>
                    </div>
                    <a href="cart.php" class="header-icon-link" aria-label="Carrello"><span class="fas fa-shopping-cart" aria-hidden="true"></span></a>
                <?php else: ?>
                    <a href="login.php" aria-label="Accedi o Registrati"><span class="fas fa-user-circle" aria-hidden="true"></span></a>
                    <a href="login.php" aria-label="Carrello"><span class="fas fa-shopping-cart" aria-hidden="true"></span></a>
                <?php endif; ?>
            </div>
        </nav>
    </header>
    <main>