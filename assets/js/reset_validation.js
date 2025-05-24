document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('.recovery-form');
    const pass = form.querySelector('input[name="contrasena"]');
    const confirmPass = form.querySelector('input[name="confirmar_contrasena"]');


    const iconSpan = document.createElement('span');
    iconSpan.style.marginLeft = '8px';
    iconSpan.style.fontWeight = 'bold';
    iconSpan.style.fontSize = '18px';
    confirmPass.parentNode.insertBefore(iconSpan, confirmPass.nextSibling);

    function checkPasswords() {
        if (confirmPass.value === '') {
            iconSpan.textContent = '';
            confirmPass.style.borderColor = '';
            return;
        }
        if (pass.value === confirmPass.value) {
            iconSpan.textContent = '✅';
            iconSpan.style.color = 'green';
            confirmPass.style.borderColor = 'green';
        } else {
            iconSpan.textContent = '❌';
            iconSpan.style.color = 'red';
            confirmPass.style.borderColor = 'red';
        }
    }

    confirmPass.addEventListener('input', checkPasswords);
    pass.addEventListener('input', checkPasswords);

    form.addEventListener('submit', e => {
        if (pass.value !== confirmPass.value) {
            e.preventDefault();
            alert('Las contraseñas no coinciden. Por favor, verifica.');
            confirmPass.focus();
        }
    });
});
