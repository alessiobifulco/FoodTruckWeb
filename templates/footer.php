</main>

<footer class="site-footer">
    <div class="footer-top">
        <a href="whoweare.php">Chi Siamo</a>
        <a href="contacts.php">Contatti</a>
        <a href="privacy.php">Privacy Policy</a>
        <a href="termini.php">Termini e Condizioni</a>
    </div>
    <div class="footer-bottom">
        <span>Copyright &copy; <?php echo date('Y'); ?> Food Truck. All Rights Reserved.</span>
        <div class="footer-social">
            <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
            <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            <a href="#" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
            <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
        </div>
    </div>
</footer>

<div id="cart-overlay" class="cart-overlay-container">
    <div class="cart-overlay-content">
        <button id="close-cart-overlay" class="cart-overlay-close-btn">&times;</button>
        <h3>Il Tuo Carrello</h3>
        <div class="cart-overlay-empty">
            <p>Il tuo carrello è vuoto.</p>
            <a href="order.php" class="btn-hero">Inizia a ordinare</a>
        </div>
        <div class="cart-overlay-filled" style="display: none;">
            <ul class="cart-overlay-items"></ul>
            <div class="cart-overlay-delivery">
                <p><strong>Giorno:</strong> <span id="overlay-delivery-day"></span></p>
                <p><strong>Orario:</strong> <span id="overlay-delivery-time"></span></p>
            </div>
        </div>
    </div>
</div>

<script src="js/global.js" defer></script>
</body>

</html>