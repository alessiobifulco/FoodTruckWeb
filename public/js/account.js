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

                fetch('/FOODTRACKWEB/public/manage_notification.php', {
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

            fetch('/FOODTRACKWEB/public//manage_account.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
                .then(res => res.json())
                .then(result => {
                    passwordMessage.textContent = result.message;
                    passwordMessage.className = result.success ? 'message success-message' : 'message error-message';

                    if (result.success) {
                        changePasswordOverlay.style.display = 'none';
                        passwordForm.reset();

                        const mainPageMessage = document.getElementById('account-page-message');
                        if (mainPageMessage) {
                            mainPageMessage.textContent = result.message;
                            mainPageMessage.className = 'message success-message';
                            mainPageMessage.style.display = 'block';

                            setTimeout(() => {
                                mainPageMessage.textContent = '';
                                mainPageMessage.style.display = 'none';
                            }, 3000);
                        }
                    }
                });
        });
    }

    if (deleteAccountBtn) {
        deleteAccountBtn.addEventListener('click', () => {
            if (confirm("Sei sicuro di voler eliminare il tuo account? L'azione è irreversibile.")) {
                fetch('/FOODTRACKWEB/public/manage_account.php', {
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
});