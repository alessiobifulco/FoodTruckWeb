document.addEventListener('DOMContentLoaded', function () {
    const dayButtons = document.querySelectorAll('.day-selector-btn');
    const timeSelect = document.getElementById('delivery_time');
    const dayInput = document.getElementById('delivery_day');
    const payButton = document.getElementById('pay-button');

    const today = new Date();
    const tomorrow = new Date();
    tomorrow.setDate(today.getDate() + 1);

    const getDayName = (date) => {
        const days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        return days[date.getDay()];
    };

    const dayNameToLabel = {
        'today': 'Oggi',
        'tomorrow': 'Domani'
    };

    let selectedDayName = null;

    dayButtons.forEach(button => {
        button.addEventListener('click', function () {
            dayButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            const dayType = this.dataset.day;
            selectedDayName = (dayType === 'today') ? getDayName(today) : getDayName(tomorrow);

            dayInput.value = dayNameToLabel[dayType];

            timeSelect.innerHTML = '<option value="">Scegli un orario...</option>';

            if (fasceOrarie[selectedDayName] && fasceOrarie[selectedDayName].length > 0) {
                fasceOrarie[selectedDayName].forEach(fascia => {
                    const option = document.createElement('option');
                    option.value = fascia;
                    option.textContent = fascia;
                    timeSelect.appendChild(option);
                });
                timeSelect.disabled = false;
            } else {
                timeSelect.innerHTML = '<option value="">Nessun orario disponibile</option>';
                timeSelect.disabled = true;
            }
            checkFormValidity();
        });
    });

    timeSelect.addEventListener('change', function () {
        checkFormValidity();
    });

    function checkFormValidity() {
        const isDaySelected = !!dayInput.value;
        const isTimeSelected = timeSelect.value !== '';
        payButton.disabled = !(isDaySelected && isTimeSelected);
    }
});