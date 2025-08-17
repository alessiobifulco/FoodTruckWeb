document.addEventListener('DOMContentLoaded', function () {

    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'flex';
        }
    }

    function closeModal(modalElement) {
        modalElement.style.display = 'none';
    }

    document.body.addEventListener('click', function (event) {
        if (event.target.classList.contains('btn-edit')) {
            const modalId = event.target.dataset.modalId;
            openModal(modalId);
        }

        if (event.target.classList.contains('close-btn')) {
            const modal = event.target.closest('.modal');
            closeModal(modal);
        }

        if (event.target.classList.contains('modal')) {
            closeModal(event.target);
        }
    });

});