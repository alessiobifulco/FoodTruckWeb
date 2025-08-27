document.addEventListener('DOMContentLoaded', function () {
    const notificationsContainer = document.getElementById('notifications-list');

    if (notificationsContainer) {
        notificationsContainer.addEventListener('click', function (e) {
            const target = e.target;
            if (target.matches('.btn-link, .btn-link-danger')) {
                const action = target.dataset.action;
                const notificationId = target.dataset.id;
                const notificationItem = target.closest('.notification-item');

                if (!action || !notificationId) return;

                fetch('/FOODTRUCKWEB/public/manage_notification.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: action, notification_id: notificationId })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            if (action === 'mark_read') {
                                notificationItem.classList.remove('unread');
                                target.remove();
                            } else if (action === 'delete') {
                                notificationItem.style.opacity = '0';
                                setTimeout(() => notificationItem.remove(), 300);
                            }
                        } else {
                            alert('Errore: ' + (data.message || 'Riprova.'));
                        }
                    })
                    .catch(error => console.error('Errore:', error));
            }
        });
    }

    const changePasswordBtn = document.getElementById('change-password-btn');
    const deleteAccountBtn = document.getElementById('delete-account-btn');
    const changePasswordOverlay = document.getElementById('change-password-overlay');

    if (changePasswordOverlay) {
        const closePasswordBtn = changePasswordOverlay.querySelector('.close-btn');
        const passwordForm = document.getElementById('change-password-form');
        const passwordMessage = document.getElementById('password-message');

        changePasswordBtn.addEventListener('click', () => {
            changePasswordOverlay.style.display = 'flex';
        });

        closePasswordBtn.addEventListener('click', () => {
            changePasswordOverlay.style.display = 'none';
            passwordMessage.textContent = '';
            passwordMessage.className = '';
        });

        passwordForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(passwordForm);
            const data = Object.fromEntries(formData.entries());
            data.action = 'change_password';

            fetch('/FOODTRUCKWEB/public/manage_account.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
                .then(res => res.json())
                .then(result => {
                    passwordMessage.textContent = result.message;
                    passwordMessage.className = result.success ? 'message success-message' : 'message error-message';

                    if (result.success) {
                        setTimeout(() => {
                            changePasswordOverlay.style.display = 'none';
                            passwordForm.reset();
                        }, 2000);
                    }
                });
        });
    }

    if (deleteAccountBtn) {
        deleteAccountBtn.addEventListener('click', () => {
            if (confirm("Sei sicuro di voler eliminare il tuo account? L'azione è irreversibile.")) {
                fetch('/FOODTRUCKWEB/public/manage_account.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'delete_account' })
                })
                    .then(res => res.json())
                    .then(result => {
                        if (result.success) {
                            alert(result.message);
                            window.location.href = 'index.php';
                        } else {
                            alert('Errore: ' + (result.message || 'Riprova.'));
                        }
                    });
            }
        });
    }

    const modal = document.getElementById('orderDetailsModalClient');
    if (modal) {
        const closeBtn = modal.querySelector('.close-btn');

        const openModal = () => modal.style.display = 'flex';
        const closeModal = () => modal.style.display = 'none';

        closeBtn.addEventListener('click', closeModal);
        window.addEventListener('click', (event) => {
            if (event.target == modal) {
                closeModal();
            }
        });

        document.querySelectorAll('.view-details-btn').forEach(button => {
            button.addEventListener('click', function () {
                const orderId = this.dataset.orderId;
                fetch(`account.php?action=get_details&id=${orderId}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data && data.details) {
                            document.getElementById('modalOrderIdClient').textContent = `#${String(data.details.id_ordine).padStart(5, '0')}`;

                            document.getElementById('modalOrderDetailsClient').innerHTML = `
                                <p><strong>Data Ordine:</strong> ${new Date(data.details.data_ordine).toLocaleString('it-IT')}</p>
                                <p><strong>Fascia Consegna:</strong> ${data.details.fascia_oraria_consegna || 'N/D'}</p>
                                <p><strong>Stato:</strong> ${data.details.stato.replace('_', ' ')}</p>
                                <p><strong>Note:</strong> ${data.details.note_utente || 'Nessuna nota'}</p>
                            `;

                            const productList = document.getElementById('modalProductListClient');
                            productList.innerHTML = '';
                            data.products.forEach(product => {
                                const li = document.createElement('li');
                                li.textContent = `${product.quantita} x ${product.nome} - ${parseFloat(product.prezzo_unitario_al_momento_ordine).toFixed(2)} €`;
                                productList.appendChild(li);
                            });

                            openModal();
                        } else {
                            alert('Dettagli dell\'ordine non trovati.');
                        }
                    })
                    .catch(error => {
                        console.error('Errore nel recuperare i dettagli dell\'ordine:', error);
                        alert('Si è verificato un errore. Riprova.');
                    });
            });
        });
    }
});