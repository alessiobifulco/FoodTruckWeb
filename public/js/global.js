document.addEventListener('DOMContentLoaded', function () {
    const cartContainer = document.querySelector('.header-cart-container');
    const previewList = document.querySelector('.header-cart-preview .preview-item-list');

    if (!cartContainer || !previewList) {
        return;
    }

    cartContainer.addEventListener('mouseover', function () {
        const cart = JSON.parse(sessionStorage.getItem('foodTruckMenuCart')) || [];

        previewList.innerHTML = ''; // Svuota la lista precedente

        if (cart.length === 0) {
            previewList.innerHTML = '<li class="preview-empty-message">Il carrello è vuoto.</li>';
        } else {
            cart.forEach(item => {
                const listItem = document.createElement('li');
                listItem.innerHTML = `<span>${item.nome}</span><strong>${item.prezzo.toFixed(2).replace('.', ',')} €</strong>`;
                previewList.appendChild(listItem);
            });
        }
    });
});