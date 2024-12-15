<?php include '../app/vistas/includes/header.php'; ?>

<div class="size-registration-container">
    <h1 class="form-title">Registrar Nuevo Usuario</h1>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="message success-message"><?php echo htmlspecialchars($_SESSION['success']); ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="message error-message"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form action="procesarRegistroUsuario" method="POST" class="form-container" id="user-registration-form">
        <label for="nombre">Nombre del Usuario:</label>
        <input type="text" name="nombre" id="nombre" required>

        <label for="email">Correo Electrónico:</label>
        <input type="email" name="email" id="email" required>

        <label for="contrasena">Contraseña:</label>
        <input type="password" name="contrasena" id="contrasena" required>

        <label for="rol">Rol del Usuario:</label>
        <select name="rol" id="rol">
            <option value="usuario">Usuario</option>
            <option value="admin">Administrador</option>
        </select>

        <button type="submit" class="submit-btn" name="submit_usuario">Registrar</button>
    </form>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('user-registration-form');

        form.addEventListener('submit', function (event) {
            event.preventDefault(); // Prevenir el envío directo del formulario

            Swal.fire({
                title: 'Confirmación',
                text: '¿Estás seguro de que deseas registrar este usuario?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, registrar',
                cancelButtonText: 'Cancelar',
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit(); // Enviar el formulario si el usuario confirma
                }
            });
        });
    });
</script>

<?php include '../app/vistas/includes/footer.php'; ?>
