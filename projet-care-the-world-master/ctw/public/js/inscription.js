const app = {
    init: function() {
        const birthMonth = document.getElementById('user_form_birth_month');
        const birthDay = document.getElementById('user_form_birth_day');
        const birthYear = document.getElementById('user_form_birth_year');

        birthMonth.classList.add('custom-select');
        birthDay.classList.add('custom-select');
        birthYear.classList.add('custom-select');
    }
}

document.addEventListener('DOMContentLoaded', app.init);