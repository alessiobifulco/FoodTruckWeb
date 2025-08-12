document.addEventListener('DOMContentLoaded', function () {
    const showLoginBtn = document.getElementById('show-login');
    const showRegisterBtn = document.getElementById('show-register');
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');

    if (showLoginBtn && showRegisterBtn && loginForm && registerForm) {
        showLoginBtn.addEventListener('click', () => {
            loginForm.style.display = 'block';
            registerForm.style.display = 'none';
            showLoginBtn.classList.add('active');
            showRegisterBtn.classList.remove('active');
        });
        showRegisterBtn.addEventListener('click', () => {
            loginForm.style.display = 'none';
            registerForm.style.display = 'block';
            showLoginBtn.classList.remove('active');
            showRegisterBtn.classList.add('active');
        });
    }

    const passwordInput = document.getElementById('register-password');
    const strengthBar = document.getElementById('password-strength-bar');
    const strengthText = document.getElementById('password-strength-text');

    if (passwordInput) {
        passwordInput.addEventListener('keyup', function () {
            const password = this.value;
            let strength = 0;
            if (password.length >= 8) strength += 1;
            if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength += 1;
            if (password.match(/[0-9]/)) strength += 1;
            if (password.match(/[^a-zA-Z0-9]/)) strength += 1;

            strengthBar.className = '';
            let text = 'Debole';
            let barClass = 'weak';

            switch (strength) {
                case 2:
                    text = 'Media';
                    barClass = 'medium';
                    break;
                case 3:
                case 4:
                    text = 'Forte';
                    barClass = 'strong';
                    break;
            }

            if (password.length > 0) {
                strengthBar.classList.add(barClass);
                strengthText.textContent = text;
            } else {
                strengthText.textContent = '';
            }
        });
    }
});