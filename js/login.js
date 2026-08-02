// Mostrar / ocultar contraseña
const togglePassword = document.getElementById('togglePassword');
const passwordInput  = document.getElementById('password');
const eyeIcon        = document.getElementById('eyeIcon');

togglePassword.addEventListener('click', function () {
const isPassword = passwordInput.type === 'password';
passwordInput.type = isPassword ? 'text' : 'password';
eyeIcon.className  = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
});

// Toggle mostrar/ocultar contraseña de panel de administrador
const togglePassword = document.getElementById('togglePassword');
const passwordInput  = document.getElementById('password');
const eyeIcon        = document.getElementById('eyeIcon');

togglePassword.addEventListener('click', function () {
const isPassword = passwordInput.type === 'password';
passwordInput.type = isPassword ? 'text' : 'password';
eyeIcon.classList.toggle('bi-eye', !isPassword);
eyeIcon.classList.toggle('bi-eye-slash', isPassword);
});