document.addEventListener('DOMContentLoaded', function () {
    const interactiveList = document.querySelector('#notifications-list, #messages-list');

    if (!interactiveList) {
        return;
    }

    interactiveList.addEventListener('click', function (e) {
        const target = e.target;
        if (target.matches('[data-action]')) {
            e.preventDefault();

            const action = target.dataset.action;
            const id = target.dataset.id;
            const itemElement = target.closest('.notification-item, .message-item');

            if (!action || !id || !itemElement) return;

            if (action.startsWith('delete')) {
                if (!confirm('Sei sicuro di voler eliminare questo elemento?')) {
                    return;
                }
            }

            fetch('admin_actions.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: action, id: id })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (action.startsWith('mark_')) {
                            itemElement.classList.remove('unread');
                            itemElement.classList.add('read');
                            const markReadButton = itemElement.querySelector('[data-action^="mark_"]');
                            if (markReadButton) {
                                markReadButton.remove();
                            }
                        } else if (action.startsWith('delete_')) {
                            itemElement.style.transition = 'opacity 0.3s ease';
                            itemElement.style.opacity = '0';
                            setTimeout(() => itemElement.remove(), 300);
                        }
                    } else {
                        alert('Errore: ' + (data.message || 'Azione fallita. Riprova.'));
                    }
                })
                .catch(error => {
                    console.error('Errore:', error);
                    alert('Si è verificato un errore di comunicazione con il server.');
                });
        }
    });
});