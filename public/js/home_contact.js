document.addEventListener('DOMContentLoaded', function () {
    const contactForm = document.getElementById('homepage-contact-form');
    const notificationDiv = document.getElementById('form-notification');

    if (contactForm) {
        contactForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(contactForm);
            const submitButton = contactForm.querySelector('button[type="submit"]');

            notificationDiv.innerHTML = '';
            submitButton.disabled = true;
            submitButton.textContent = 'Invio in corso...';

            fetch('process_contact_ajax.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        notificationDiv.className = 'message success-message';
                        notificationDiv.textContent = data.message;
                        contactForm.reset();
                    } else {
                        notificationDiv.className = 'message error-message';
                        notificationDiv.textContent = data.message;
                    }
                })
                .catch(error => {
                    notificationDiv.className = 'message error-message';
                    notificationDiv.textContent = 'Si è verificato un errore di rete. Riprova.';
                })
                .finally(() => {
                    submitButton.disabled = false;
                    submitButton.textContent = 'Invia';
                });
        });
    }
});