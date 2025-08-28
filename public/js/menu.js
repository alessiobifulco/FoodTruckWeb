document.addEventListener('DOMContentLoaded', function () {
    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
    const openOverlayButtons = document.querySelectorAll('.open-overlay-btn');
    const menuCartList = document.getElementById('cart-items-list');
    const summaryTotalPrice = document.getElementById('summary-total-price');
    const goToCheckoutBtn = document.getElementById('go-to-checkout-btn');
    const cartForm = document.getElementById('cart-form');
    const overlay = document.getElementById('componi-panino-overlay');

    const searchInput = document.getElementById('search-input');
    const productItems = document.querySelectorAll('.product-item');

    let cart = serverCart || JSON.parse(sessionStorage.getItem('foodTruckCart')) || [];

    function saveCartAndSync() {
        sessionStorage.setItem('foodTruckCart', JSON.stringify(cart));
        fetch('/FOODTRUCKWEB/public/update_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(cart)
        });
        updateCartView();
    }

    function updateCartView() {
        menuCartList.innerHTML = '';
        if (cart.length === 0) {
            menuCartList.innerHTML = '<li class="empty-cart-message">Il carrello è vuoto</li>';
            summaryTotalPrice.textContent = '0,00 €'; 
        } else {
            let totalPrice = 0;
            cart.forEach((item, index) => {
                const listItem = document.createElement('li');
                listItem.classList.add('cart-item');
                listItem.innerHTML = `<span class="cart-item-name">${item.nome}</span><div class="cart-item-actions"><span class="cart-item-price">${item.prezzo.toFixed(2).replace('.', ',')} €</span><button type="button" class="remove-item-btn" data-index="${index}">&times;</button></div>`;
                menuCartList.appendChild(listItem);
                totalPrice += item.prezzo;
            });
            summaryTotalPrice.textContent = `${totalPrice.toFixed(2).replace('.', ',')} €`;
        }
        goToCheckoutBtn.disabled = cart.length === 0;
    }

    function filterProducts(searchTerm) {
        const searchText = searchTerm.toLowerCase();

        productItems.forEach(item => {
            const productName = item.querySelector('.product-details h3').textContent.toLowerCase();
            const productDescription = item.querySelector('.product-details p') ? item.querySelector('.product-details p').textContent.toLowerCase() : '';

            if (productName.includes(searchText) || productDescription.includes(searchText)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    }

    if (searchInput) {
        searchInput.addEventListener('keyup', (e) => {
            filterProducts(e.target.value);
        });
    }

    document.querySelector('.product-list-container').addEventListener('click', function (e) {
        if (e.target.classList.contains('add-to-cart-btn')) {
            const productItem = e.target.closest('.product-item');
            const product = {
                id: e.target.dataset.id,
                nome: e.target.dataset.nome,
                prezzo: parseFloat(e.target.dataset.prezzo),
                immagine: productItem.querySelector('.product-item-image').getAttribute('src')
            };
            cart.push(product);
            saveCartAndSync();
        }
    });

    menuCartList.addEventListener('click', function (event) {
        if (event.target.classList.contains('remove-item-btn')) {
            const indexToRemove = parseInt(event.target.dataset.index);
            cart.splice(indexToRemove, 1);
            saveCartAndSync();
        }
    });

    cartForm.addEventListener('submit', function (e) {
        if (cart.length === 0) {
            e.preventDefault();
            alert('Il carrello è vuoto!');
        }
        document.getElementById('cart_data_input').value = JSON.stringify(cart);
    });

    if (overlay) {
        const closeOverlayButton = document.getElementById('close-overlay-btn');
        const addCustomPaninoBtn = document.getElementById('add-custom-panino-btn');
        const ingredientPicker = document.querySelector('.ingredient-picker');
        let limitiSelezione = {};
        let paninoBase = {};

        const closeOverlay = () => {
            overlay.style.display = 'none';
            ingredientPicker.querySelectorAll('input').forEach(input => {
                input.checked = false;
                input.disabled = false;
            });
        };
        const validateOverlaySelections = () => {
            const paneSelezionato = ingredientPicker.querySelector('input[name="pane"]:checked');
            const proteineSelezionate = ingredientPicker.querySelectorAll('input[name="proteina[]"]:checked').length;
            const proteineRichieste = limitiSelezione.proteina || 0;
            addCustomPaninoBtn.disabled = !(paneSelezionato && proteineSelezionate >= proteineRichieste);
        };
        openOverlayButtons.forEach(button => {
            button.addEventListener('click', () => {
                paninoBase = {
                    id: button.dataset.id,
                    nome: button.dataset.nomePanino,
                    prezzo: parseFloat(button.dataset.prezzo),
                    immagine: button.closest('.product-item').querySelector('.product-item-image').getAttribute('src')
                };
                limitiSelezione = {
                    proteina: parseInt(button.dataset.limiteProteina) || 0,
                    contorno: parseInt(button.dataset.limiteContorno) || 0,
                    salsa: parseInt(button.dataset.limiteSalsa) || 0
                };
                document.getElementById('overlay-title').textContent = paninoBase.nome;
                document.getElementById('overlay-description').textContent = button.closest('.product-item').querySelector('p').textContent;
                document.getElementById('proteina-title').innerHTML = `Scegli la Proteina <span class="required-badge">${limitiSelezione.proteina} Obbligatori</span>`;
                document.getElementById('contorno-title').innerHTML = `Scegli il Contorno <span class="badge">${limitiSelezione.contorno} max</span>`;
                document.getElementById('salsa-title').innerHTML = `Scegli la Salsa <span class="badge">${limitiSelezione.salsa} max</span>`;
                overlay.style.display = 'flex';
                validateOverlaySelections();
            });
        });
        closeOverlayButton.addEventListener('click', closeOverlay);
        addCustomPaninoBtn.addEventListener('click', () => {
            let ingredientiNomi = [];
            ingredientPicker.querySelectorAll('input:checked').forEach(input => {
                ingredientiNomi.push(input.dataset.nome);
            });
            let nomeFinale = paninoBase.nome + ` (${ingredientiNomi.join(', ')})`;
            const paninoPersonalizzato = {
                id: 'custom_' + Date.now(),
                nome: nomeFinale,
                prezzo: paninoBase.prezzo,
                immagine: paninoBase.immagine
            };
            cart.push(paninoPersonalizzato);
            saveCartAndSync();
            closeOverlay();
        });
        ingredientPicker.addEventListener('change', (event) => {
            if (event.target.type === 'checkbox') {
                const categoria = event.target.name.replace('[]', '');
                const limite = limitiSelezione[categoria];
                if (limite) {
                    const checkedInputs = ingredientPicker.querySelectorAll(`input[name="${event.target.name}"]:checked`);
                    const uncheckedInputs = ingredientPicker.querySelectorAll(`input[name="${event.target.name}"]:not(:checked)`);

                    if (checkedInputs.length >= limite) {
                        uncheckedInputs.forEach(input => input.disabled = true);
                    } else {
                        uncheckedInputs.forEach(input => input.disabled = false);
                    }
                }
            }
            validateOverlaySelections();
        });
    }

    updateCartView();
});