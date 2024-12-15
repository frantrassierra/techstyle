<?php include '../app/vistas/includes/header.php'; ?>

<?php
// Mostrar mensajes de éxito si existen
if (isset($_SESSION['success_message'])) {
    echo "<p class='success-message'>" . htmlspecialchars($_SESSION['success_message'], ENT_QUOTES, 'UTF-8') . "</p>";
    unset($_SESSION['success_message']); // Limpiar el mensaje después de mostrarlo
}

// Mostrar mensajes de error si existen
if (isset($error)) {
    echo "<p class='error-message'>$error</p>";
}
if (isset($_SESSION['error_message'])) {
    echo "<p class='error-message'>" . htmlspecialchars($_SESSION['error_message'], ENT_QUOTES, 'UTF-8') . "</p>";
    unset($_SESSION['error_message']); // Limpiar el mensaje después de mostrarlo
}
?>

<div class="login-page">
    <div class="login-box">
        <h2>Iniciar sesión</h2>

        <!-- Formulario de inicio de sesión -->
        <form action="procesarLogin" method="POST">
            <div class="input-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required placeholder="Introduce tu email">
            </div>

            <div class="input-group">
                <label for="contrasena">Contraseña:</label>
                <input type="password" name="contrasena" id="contrasena" required placeholder="Introduce tu contraseña">
            </div>

            <button type="submit" class="btn">Iniciar sesión</button>

            <p class="register-link">
                ¿No tienes cuenta? <a href="registro">Regístrate aquí</a>
            </p>
        </form>
    </div>
</div>


<?php include '../app/vistas/includes/footer.php'; ?>

