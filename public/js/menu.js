document.addEventListener('DOMContentLoaded', function () {
    // --- LOGICA CARRELLO ---
    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
    const cartItemsList = document.getElementById('cart-items-list');
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

    // --- LOGICA RICERCA ---
    const searchInput = document.querySelector('.search-bar-container input');
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            const searchTerm = this.value.toLowerCase();
            document.querySelectorAll('.product-category').forEach(category => {
                let categoryHasVisibleItems = false;
                category.querySelectorAll('.product-item').forEach(item => {
                    const name = item.querySelector('h3').textContent.toLowerCase();
                    if (name.includes(searchTerm)) {
                        item.style.display = 'flex';
                        categoryHasVisibleItems = true;
                    } else {
                        item.style.display = 'none';
                    }
                });
                category.querySelector('h2').style.display = categoryHasVisibleItems ? 'block' : 'none';
            });
        });
    }

    // --- LOGICA SELEZIONE GIORNO E ORA ---
    const summaryDay = document.getElementById('summary-day');
    const summaryTime = document.getElementById('summary-time');
    const dayButtons = document.querySelectorAll('.day-selector-btn');
    const timeSlotButtons = document.querySelectorAll('.time-slot-btn:not(.disabled)');

    dayButtons.forEach(button => {
        if (button.classList.contains('active')) {
            summaryDay.textContent = button.textContent.trim();
        }
    });

    timeSlotButtons.forEach(button => {
        button.addEventListener('click', function () {
            timeSlotButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            summaryTime.textContent = this.textContent.trim();
        });
    });

    // --- LOGICA OVERLAY ---
    const overlay = document.getElementById('componi-panino-overlay');
    const openOverlayButtons = document.querySelectorAll('.open-overlay-btn');
    const closeOverlayButton = document.getElementById('close-overlay-btn');
    const overlayTitle = document.getElementById('overlay-title');
    const ingredientPicker = document.querySelector('.ingredient-picker');

    let limitiSelezione = {};

    if (overlay) {
        openOverlayButtons.forEach(button => {
            button.addEventListener('click', () => {
                limitiSelezione = {
                    pane: parseInt(button.dataset.limitePane),
                    proteina: parseInt(button.dataset.limiteProteina),
                    contorno: parseInt(button.dataset.limiteContorno),
                    salsa: parseInt(button.dataset.limiteSalsa),
                };

                overlayTitle.textContent = button.dataset.nomePanino;
                document.getElementById('proteina-title').innerHTML = `Scegli la Proteina <span class="required-badge">${limitiSelezione.proteina} Obbligatori</span>`;
                document.getElementById('contorno-title').innerHTML = `Scegli il Contorno <span class="required-badge">${limitiSelezione.contorno} Opzionali</span>`;
                document.getElementById('salsa-title').innerHTML = `Scegli la Salsa <span class="required-badge">${limitiSelezione.salsa} Opzionali</span>`;

                overlay.style.display = 'flex';
                document.body.classList.add('overlay-open');
            });
        });

        const closeOverlay = () => {
            overlay.style.display = 'none';
            document.body.classList.remove('overlay-open');
            ingredientPicker.querySelectorAll('input').forEach(input => input.checked = false);
            ingredientPicker.querySelectorAll('input[type="checkbox"]').forEach(input => input.disabled = false);
        };

        closeOverlayButton.addEventListener('click', closeOverlay);
        overlay.addEventListener('click', (event) => {
            if (event.target === overlay) {
                closeOverlay();
            }
        });

        ingredientPicker.addEventListener('change', (event) => {
            if (event.target.type !== 'checkbox') return;

            const categoriaDiv = event.target.closest('.ingredient-category');
            const categoria = categoriaDiv.dataset.categoria;
            const limite = limitiSelezione[categoria];

            const checkedInputs = categoriaDiv.querySelectorAll('input[type="checkbox"]:checked');
            const uncheckedInputs = categoriaDiv.querySelectorAll('input[type="checkbox"]:not(:checked)');

            if (checkedInputs.length >= limite) {
                uncheckedInputs.forEach(input => input.disabled = true);
            } else {
                uncheckedInputs.forEach(input => input.disabled = false);
            }
        });
    }
});