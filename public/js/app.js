//ver contarsena
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.toggle-password').forEach(button => {
        const input = document.getElementById(button.getAttribute('data-target'));

        // Alternar visibilidad y cambiar fondo del ícono
        button.addEventListener('click', function () {
            if (input.type === 'password') {
                input.type = 'text';
                button.classList.add('bg-gray-200'); // Fondo activo
            } else {
                input.type = 'password';
                button.classList.remove('bg-gray-200'); // Fondo inactivo
            }
        });

        // Ocultar la contraseña al salir del campo y restablecer fondo
        input.addEventListener('blur', function () {
            input.type = 'password';
            button.classList.remove('bg-gray-200');
        });

        // También ocultar si el usuario hace clic fuera
        document.addEventListener('click', function (event) {
            if (!input.contains(event.target) && !button.contains(event.target)) {
                input.type = 'password';
                button.classList.remove('bg-gray-200');
            }
        });
    });
});


