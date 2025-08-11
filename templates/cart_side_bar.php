<?php

?>
<div class="cart-sidebar">
    <div class="cart-header">
        <h4 class="cart-title">Carrello</h4>
    </div>
    <div class="cart-body">
        <h5 class="cart-subtitle">Il tuo ordine</h5>
        <ul class="cart-list">
            <li class="cart-item">
                <span class="item-name">Panino con cotolettetta x1</span>
                <span class="item-details">
                    €4.00 
                    <button class="btn-remove-item" title="Rimuovi articolo">X</button>
                </span>
            </li>
            <li class="cart-item">
                <span class="item-name">Coca Cola lattina x2</span>
                <span class="item-details">
                    €5.00 
                    <button class="btn-remove-item" title="Rimuovi articolo">X</button>
                </span>
            </li>
        </ul>
        <div class="cart-summary">
            <p class="cart-total">Totale: €9.00</p>
            <div class="cart-delivery-info">
                <p>Orario di consegna: 12:00 - 12:30</p>
                <p>Data di consegna: Oggi, <?php echo date('d/m/Y'); ?></p>
            </div>
        </div>
        <button class="btn btn-success btn-checkout">Procedi al Checkout</button>
    </div>
</div>