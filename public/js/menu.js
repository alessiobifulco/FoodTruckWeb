document.addEventListener('DOMContentLoaded', function () {
    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
    const openOverlayButtons = document.querySelectorAll('.open-overlay-btn');
    const menuCartList = document.getElementById('cart-items-list');
    const summaryTotalPrice = document.getElementById('summary-total-price');
    const checkoutButton = document.querySelector('.btn-checkout');
    const cartForm = document.getElementById('cart-form');
    const overlay = document.getElementById('componi-panino-overlay');

    let cart = [];
    if (typeof serverCart !== 'undefined' && serverCart !== null) {
        cart = serverCart;
    } else {
        cart = JSON.parse(sessionStorage.getItem('foodTruckMenuCart')) || [];
    }

    let limitiSelezione = {};
    let paninoBase = {};

    function saveCartToSessionStorage() {
        sessionStorage.setItem('foodTruckMenuCart', JSON.stringify(cart));
        fetch('/FOODTRACKWEB/public/update_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(cart)
        });
    }

    function updateCartView() {
        if (!menuCartList) return;
        menuCartList.innerHTML = '';
        if (cart.length === 0) {
            menuCartList.innerHTML = '<li class="empty-cart-message">Il carrello è vuoto</li>';
            summaryTotalPrice.textContent = '0,00 €';
        } else {
            let totalPrice = 0;
            cart.forEach((item, index) => {
                const listItem = document.createElement('li');
                listItem.classList.add('cart-item');
                listItem.innerHTML = `<span class="cart-item-name">${item.nome}</span><div class="cart-item-actions"><span class="cart-item-price">${item.prezzo.toFixed(2).replace('.', ',')} €</span><button class="remove-item-btn" data-index="${index}">&times;</button></div>`;
                menuCartList.appendChild(listItem);
                totalPrice += item.prezzo;
            });
            summaryTotalPrice.textContent = `${totalPrice.toFixed(2).replace('.', ',')} €`;
        }
        checkoutButton.disabled = cart.length === 0;
    }

    function validateOverlaySelections() {
        const paneSelezionato = ingredientPicker.querySelector('input[name="pane"]:checked');
        const proteineSelezionate = ingredientPicker.querySelectorAll('input[name="proteina[]"]:checked').length;
        const proteineRichieste = limitiSelezione.proteina || 0;
        addCustomPaninoBtn.disabled = !(paneSelezionato && proteineSelezionate >= proteineRichieste);
    }

    const closeOverlay = () => {
        if (!overlay) return;
        overlay.style.display = 'none';
        document.body.classList.remove('overlay-open');
        ingredientPicker.querySelectorAll('input').forEach(input => {
            input.checked = false;
            input.disabled = false;
        });
    };

    addToCartButtons.forEach(button => {
        button.addEventListener('click', () => {
            const product = {
                id: button.dataset.id,
                nome: button.dataset.nome,
                prezzo: parseFloat(button.dataset.prezzo),
                immagine: button.closest('.product-item').querySelector('.product-item-image').getAttribute('src')
            };
            cart.push(product);
            saveCartToSessionStorage();
            updateCartView();
        });
    });

    if (menuCartList) {
        menuCartList.addEventListener('click', function (event) {
            if (event.target.classList.contains('remove-item-btn')) {
                const indexToRemove = parseInt(event.target.dataset.index);
                cart.splice(indexToRemove, 1);
                saveCartToSessionStorage();
                updateCartView();
            }
        });
    }

    if (cartForm) {
        cartForm.addEventListener('submit', function (event) {
            document.getElementById('cart_data_input').value = JSON.stringify(cart);
        });
    }

    if (overlay) {
        const closeOverlayButton = document.getElementById('close-overlay-btn');
        const addCustomPaninoBtn = document.getElementById('add-custom-panino-btn');
        const ingredientPicker = document.querySelector('.ingredient-picker');
        const overlayTitle = document.getElementById('overlay-title');
        const overlayDescription = document.getElementById('overlay-description');

        openOverlayButtons.forEach(button => {
            button.addEventListener('click', () => {
                paninoBase = { id: button.dataset.id, nome: button.dataset.nomePanino, prezzo: parseFloat(button.dataset.prezzo), immagine: button.dataset.immagine };
                limitiSelezione = { proteina: parseInt(button.dataset.limiteProteina), contorno: parseInt(button.dataset.limiteContorno), salsa: parseInt(button.dataset.limiteSalsa) };
                overlayTitle.textContent = paninoBase.nome;
                overlayDescription.textContent = button.closest('.product-item').querySelector('p').textContent;
                validateOverlaySelections();
                overlay.style.display = 'flex';
                document.body.classList.add('overlay-open');
            });
        });

        closeOverlayButton.addEventListener('click', closeOverlay);
        overlay.addEventListener('click', (event) => { if (event.target === overlay) closeOverlay(); });

        ingredientPicker.addEventListener('change', (event) => {
            if (event.target.type === 'checkbox') {
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
            }
            validateOverlaySelections();
        });

        addCustomPaninoBtn.addEventListener('click', () => {
            let ingredientiNomi = [];
            ingredientPicker.querySelectorAll('input:checked').forEach(input => {
                ingredientiNomi.push(input.dataset.nome);
            });
            let nomeFinale = paninoBase.nome;
            if (ingredientiNomi.length > 0) {
                nomeFinale += ` (${ingredientiNomi.join(', ')})`;
            }
            const paninoPersonalizzato = {
                id: 'custom_' + Date.now(),
                nome: nomeFinale,
                prezzo: paninoBase.prezzo,
                immagine: paninoBase.immagine
            };
            cart.push(paninoPersonalizzato);
            saveCartToSessionStorage();
            updateCartView();
            closeOverlay();
        });
    }

    updateCartView();
});