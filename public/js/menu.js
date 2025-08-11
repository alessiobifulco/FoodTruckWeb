document.addEventListener('DOMContentLoaded', function () {
    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
    const cartItemsList = document.getElementById('cart-items-list');
    const emptyCartMessage = document.querySelector('.empty-cart-message');
    const summaryTotalPrice = document.getElementById('summary-total-price');
    const checkoutButton = document.querySelector('.btn-checkout');
    let cart = [];

    addToCartButtons.forEach(button => {
        button.addEventListener('click', () => {
            const product = {
                id: button.dataset.id,
                nome: button.dataset.nome,
                prezzo: parseFloat(button.dataset.prezzo)
            };
            cart.push(product);
            updateCart();
        });
    });

    function updateCart() {
        cartItemsList.innerHTML = '';
        if (cart.length === 0) {
            cartItemsList.innerHTML = '<li class="empty-cart-message">Il carrello è vuoto</li>';
            checkoutButton.disabled = true;
            summaryTotalPrice.textContent = '0,00 €';
        } else {
            let totalPrice = 0;
            cart.forEach((item, index) => {
                const listItem = document.createElement('li');
                listItem.classList.add('cart-item');
                listItem.innerHTML = `
                    <span class="cart-item-name">${item.nome}</span>
                    <span>${item.prezzo.toFixed(2).replace('.', ',')} €</span>
                    <button class="remove-item-btn" data-index="${index}">&times;</button>
                `;
                cartItemsList.appendChild(listItem);
                totalPrice += item.prezzo;
            });
            summaryTotalPrice.textContent = `${totalPrice.toFixed(2).replace('.', ',')} €`;
            checkoutButton.disabled = false;
        }
    }

    cartItemsList.addEventListener('click', function (event) {
        if (event.target.classList.contains('remove-item-btn')) {
            const indexToRemove = parseInt(event.target.dataset.index);
            cart.splice(indexToRemove, 1);
            updateCart();
        }
    });

    const overlay = document.getElementById('componi-panino-overlay');
    const openOverlayButtons = document.querySelectorAll('.open-overlay-btn');
    const closeOverlayButton = document.getElementById('close-overlay-btn');

    if (overlay) {
        openOverlayButtons.forEach(button => {
            button.addEventListener('click', () => {
                overlay.style.display = 'flex';
            });
        });

        closeOverlayButton.addEventListener('click', () => {
            overlay.style.display = 'none';
        });

        overlay.addEventListener('click', (event) => {
            if (event.target === overlay) {
                overlay.style.display = 'none';
            }
        });
    }
});