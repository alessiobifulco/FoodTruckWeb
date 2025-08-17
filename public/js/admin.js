document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('orderDetailsModal');
    if (modal) {
        const closeBtn = modal.querySelector('.close-btn');
        const closeModal = () => {
            modal.style.display = 'none';
        };

        closeBtn.addEventListener('click', closeModal);

        window.addEventListener('click', (event) => {
            if (event.target == modal) {
                closeModal();
            }
        });

        document.querySelectorAll('.view-details-btn').forEach(button => {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                const orderId = this.dataset.orderId;
                fetch(`admin.php?action=get_details&id=${orderId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.details) {
                            document.getElementById('modalOrderId').textContent = `#${data.details.id_ordine}`;
                            document.getElementById('modalOrderDetails').innerHTML = `<p><strong>Ricevuto il:</strong> ${new Date(data.details.data_ordine).toLocaleString('it-IT')}</p><p><strong>Ricevente:</strong> ${data.details.nome_ricevente} ${data.details.cognome_ricevente}</p><p><strong>Aula Consegna:</strong> ${data.details.aula_consegna || 'N/D'}</p><p><strong>Totale:</strong> ${parseFloat(data.details.totale).toFixed(2)} €</p><p><strong>Stato:</strong> ${data.details.stato}</p><p><strong>Note:</strong> ${data.details.note_utente || 'Nessuna nota'}</p>`;
                            const productList = document.getElementById('modalProductList');
                            productList.innerHTML = '';
                            if (data.products && Array.isArray(data.products)) {
                                data.products.forEach(product => {
                                    const li = document.createElement('li');
                                    let productText = `${product.quantita} x ${product.nome} - ${parseFloat(product.prezzo_unitario_al_momento_ordine).toFixed(2)} €`;
                                    if (product.ingredienti && product.ingredienti.length > 0) {
                                        const ingredients = product.ingredienti.map(ing => ing.nome).join(', ');
                                        productText += `<br><small class="product-ingredients">Ingredienti: ${ingredients}</small>`;
                                    }
                                    li.innerHTML = productText;
                                    productList.appendChild(li);
                                });
                            }
                            modal.style.display = 'flex';
                        } else {
                            alert('Dettagli dell\'ordine non trovati.');
                        }
                    })
                    .catch(error => console.error('Errore nel fetch:', error));
            });
        });
    }

    const orderList = document.getElementById('order-list');
    if (orderList) {
        orderList.addEventListener('click', function (event) {
            if (event.target && event.target.classList.contains('update-status-btn')) {
                const button = event.target;
                const orderId = button.dataset.orderId;
                const newStatus = button.dataset.newStatus;

                if (!confirm(`Sei sicuro di voler marcare l'ordine #${orderId} come "${newStatus.replace('_', ' ')}"?`)) return;

                const formData = new FormData();
                formData.append('action', 'update_status');
                formData.append('order_id', orderId);
                formData.append('new_status', newStatus);

                fetch('admin.php', { method: 'POST', body: formData })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const orderElement = document.getElementById(`order-${orderId}`);
                            const statusBadge = orderElement.querySelector('.status-badge');
                            if (statusBadge) {
                                statusBadge.textContent = data.new_status_display;
                                statusBadge.className = 'status-badge status-' + data.new_status_display.replace(' ', '_');
                            }
                            if (data.next_step) {
                                button.textContent = data.next_step.button_text;
                                button.dataset.newStatus = data.next_step.next_status_data;
                                button.className = `btn ${data.next_step.button_class} update-status-btn`;
                            } else {
                                button.remove();
                            }
                        } else {
                            alert('Errore: ' + (data.message || 'Si è verificato un problema.'));
                        }
                    })
                    .catch(error => console.error('Errore:', error));
            }
        });
    }
});