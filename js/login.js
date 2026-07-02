document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('loginForm');
    const btnSubmit = document.getElementById('btnSubmit');

    // Expresión regular para validar la estructura del correo electrónico
    const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    // Agrega validación en los eventos blur e input para correo y contraseña
    ['email', 'password'].forEach(id => {
        const inputElement = document.getElementById(id);
        if (inputElement) {
            inputElement.addEventListener('blur', () => validarCampo(id));
            inputElement.addEventListener('input', () => limpiarError(id));
        }
    });

    // Función encargada de validar cada campo de forma independiente
    const validarCampo = (id) => {
        const input = document.getElementById(id);
        const valor = input.value.trim();
        let esValido = true;

        switch(id) {
            case 'email':
                if (valor === '') {
                    mostrarError('email', 'El correo electrónico es obligatorio.');
                    esValido = false;
                } else if (!regexEmail.test(valor)) {
                    mostrarError('email', 'Introduce un correo electrónico válido (ejemplo@empresa.com).');
                    esValido = false;
                }
                break;
            case 'password':
                if (valor === '') {
                    mostrarError('password', 'La contraseña no puede estar vacía.');
                    esValido = false;
                } else if (valor.length < 8) { // Cambiado a un mínimo de 8 caracteres
                    mostrarError('password', 'La contraseña debe tener al menos 8 caracteres.');
                    esValido = false;
                }
                break;
        }
        return esValido;
    };

    // Muestra el mensaje de error y resalta TODO el contenedor input-group
    const mostrarError = (inputId, mensaje) => {
        const input = document.getElementById(inputId);
        const inputGroup = input.closest('.input-group'); // Buscamos el contenedor padre
        const errorDiv = document.getElementById(`error-${inputId}`);
        
        // Forzamos a Bootstrap a mostrar el feedback block de forma correcta
        if (errorDiv) {
            errorDiv.textContent = mensaje;
            errorDiv.style.display = 'block'; 
        }

        if (inputGroup) {
            inputGroup.classList.add('is-invalid-group');
        }
    };

    // Elimina los estilos de error en todo el contenedor al escribir de nuevo
    const limpiarError = (inputId) => {
        const input = document.getElementById(inputId);
        const inputGroup = input.closest('.input-group');
        const errorDiv = document.getElementById(`error-${inputId}`);
        
        if (errorDiv) {
            errorDiv.textContent = '';
            errorDiv.style.display = 'none';
        }

        if (inputGroup) {
            inputGroup.classList.remove('is-invalid-group');
        }
    };

    // Manejo del evento submit del formulario
    form.addEventListener('submit', (e) => {
        const emailValido = validarCampo('email');
        const passwordValido = validarCampo('password');

        // Si alguna validación falla, se detiene el envío de datos
        if (!emailValido || !passwordValido) {
            e.preventDefault();
            return;
        }

        // Bloqueo de seguridad para evitar múltiples clics
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = 'Verificando... <i class="fa-solid fa-spinner fa-spin ms-2"></i>';
    });
});