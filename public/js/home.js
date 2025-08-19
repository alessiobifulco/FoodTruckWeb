document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('homepage-contact-form');
    const notification = document.getElementById('form-notification');

    form.addEventListener('submit', function (event) {
        event.preventDefault();

        notification.textContent = '';
        notification.className = '';

        notification.textContent = 'Invio in corso...';
        notification.style.display = 'block';

        const formData = new FormData(form);

        fetch(form.action, {
            method: form.method,
            body: formData
        })
            .then(response => {
                const contentType = response.headers.get("content-type");
                if (contentType && contentType.indexOf("application/json") !== -1) {
                    return response.json();
                } else {
                    throw new Error('Risposta non valida dal server.');
                }
            })
            .then(data => {
                notification.textContent = data.message;
                if (data.success) {
                    notification.className = 'success-message';
                    form.reset();
                } else {
                    notification.className = 'error-message';
                }
            })
            .catch(error => {
                console.error('Errore:', error);
                notification.textContent = 'Si è verificato un errore durante l\'invio. Riprova più tardi.';
                notification.className = 'error-message';
            });
    });
});