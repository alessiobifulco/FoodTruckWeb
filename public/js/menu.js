document.addEventListener('DOMContentLoaded', function () {
    // --- ELEMENTI DELLA PAGINA ---
    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
    const openOverlayButtons = document.querySelectorAll('.open-overlay-btn');
    const menuCartList = document.getElementById('cart-items-list');
    const summaryTotalPrice = document.getElementById('summary-total-price');
    const checkoutButton = document.querySelector('.btn-checkout');
    const summaryDay = document.getElementById('summary-day');
    const summaryTime = document.getElementById('summary-time');
    const dayButtons = document.querySelectorAll('.day-selector-btn');
    const timeSlotButtons = document.querySelectorAll('.time-slot-btn:not(.disabled)');
    const searchInput = document.querySelector('.search-bar-container input');
    const cartForm = document.getElementById('cart-form');

    // --- ELEMENTI DELL'OVERLAY ---
    const overlay = document.getElementById('componi-panino-overlay');
    const closeOverlayButton = document.getElementById('close-overlay-btn');
    const addCustomPaninoBtn = document.getElementById('add-custom-panino-btn');
    const ingredientPicker = document.querySelector('.ingredient-picker');
    const overlayTitle = document.getElementById('overlay-title');
    const overlayDescription = document.getElementById('overlay-description');

    // --- STATO LOCALE DELLA PAGINA ---
    let cart = JSON.parse(sessionStorage.getItem('foodTruckCart')) || [];
    let isTimeSlotSelected = document.querySelector('.time-slot-btn.active') !== null;
    let limitiSelezione = {};
    let paninoBase = {};

    // --- FUNZIONI ---
    function saveCartState() {
        sessionStorage.setItem('foodTruckCart', JSON.stringify(cart));
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
                listItem.innerHTML = `<span class="cart-item-name">${item.nome}</span><span>${item.prezzo.toFixed(2).replace('.', ',')} €</span><button class="remove-item-btn" data-index="${index}">&times;</button>`;
                menuCartList.appendChild(listItem);
                totalPrice += item.prezzo;
            });
            summaryTotalPrice.textContent = `${totalPrice.toFixed(2).replace('.', ',')} €`;
        }
        checkoutButton.disabled = !(cart.length > 0 && isTimeSlotSelected);
    }

    function validateOverlaySelections() {
        const paneSelezionato = ingredientPicker.querySelector('input[name="pane"]:checked');
        const proteineSelezionate = ingredientPicker.querySelectorAll('input[name="proteina[]"]:checked').length;
        const proteineRichieste = limitiSelezione.proteina || 0;
        addCustomPaninoBtn.disabled = !(paneSelezionato && proteineSelezionate === proteineRichieste);
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

    // --- EVENT LISTENERS ---
    addToCartButtons.forEach(button => {
        button.addEventListener('click', () => {
            const product = {
                id: button.dataset.id,
                nome: button.dataset.nome,
                prezzo: parseFloat(button.dataset.prezzo),
                immagine: button.closest('.product-item').querySelector('.product-item-image').getAttribute('src')
            };
            cart.push(product);
            saveCartState();
            updateCartView();
        });
    });

    if (menuCartList) {
        menuCartList.addEventListener('click', function (event) {
            if (event.target.classList.contains('remove-item-btn')) {
                const indexToRemove = parseInt(event.target.dataset.index);
                cart.splice(indexToRemove, 1);
                saveCartState();
                updateCartView();
            }
        });
    }

    timeSlotButtons.forEach(button => {
        button.addEventListener('click', function () {
            timeSlotButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            isTimeSlotSelected = true;
            if (summaryTime) {
                summaryTime.textContent = this.textContent.trim();
            }
            updateCartView();
        });
    });

    if (cartForm) {
        cartForm.addEventListener('submit', function (event) {
            document.getElementById('cart_data_input').value = JSON.stringify(cart);
            document.getElementById('delivery_day_input').value = summaryDay.textContent;
            document.getElementById('delivery_time_input').value = summaryTime.textContent;
        });
    }

    if (overlay) {
        openOverlayButtons.forEach(button => {
            button.addEventListener('click', () => {
                paninoBase = { id: button.dataset.id, nome: button.dataset.nomePanino, prezzo: parseFloat(button.dataset.prezzo), immagine: button.dataset.immagine };
                limitiSelezione = { proteina: parseInt(button.dataset.limiteProteina), contorno: parseInt(button.dataset.limiteContorno), salsa: parseInt(button.dataset.limiteSalsa) };
                overlayTitle.textContent = paninoBase.nome;
                overlayDescription.textContent = button.closest('.product-item').querySelector('p').textContent;
                document.getElementById('proteina-title').innerHTML = `Scegli la Proteina <span class="required-badge">${limitiSelezione.proteina} Obbligatori</span>`;
                document.getElementById('contorno-title').innerHTML = `Scegli il Contorno <span class="required-badge">${limitiSelezione.contorno} Opzionali</span>`;
                document.getElementById('salsa-title').innerHTML = `Scegli la Salsa <span class="required-badge">${limitiSelezione.salsa} Opzionali</span>`;
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
                if (checkedInputs.length >= limite) { uncheckedInputs.forEach(input => input.disabled = true); } else { uncheckedInputs.forEach(input => input.disabled = false); }
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
            saveCartState();
            updateCartView();
            closeOverlay();
        });
    }

    updateCartView();
});