<?php include '../app/vistas/includes/header.php'; ?>

<div class="size-edit-container">
    <h1 class="form-title">Editar Usuario</h1>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="message success-message">
            <?php echo htmlspecialchars($_SESSION['success']); ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="message error-message">
            <?php echo htmlspecialchars($_SESSION['error']); ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form action="procesarActualizacionUsuario?id=<?php echo $usuario['id_usuario']; ?>" method="POST" class="form-container" id="edit-user-form">
        <label for="nombre">Nombre del Usuario:</label>
        <input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>

        <label for="email">Correo Electrónico:</label>
        <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>

        <label for="contrasena">Contraseña (dejar en blanco si no desea cambiarla):</label>
        <input type="password" name="contrasena" id="contrasena">

        <label for="rol">Rol del Usuario:</label>
        <select name="rol" id="rol">
            <option value="usuario" <?php echo $usuario['rol'] == 'usuario' ? 'selected' : ''; ?>>Usuario</option>
            <option value="admin" <?php echo $usuario['rol'] == 'admin' ? 'selected' : ''; ?>>Administrador</option>
        </select>

        <button type="submit" class="submit-btn">Actualizar</button>
    </form>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('edit-user-form');

        form.addEventListener('submit', function (event) {
            event.preventDefault(); // Prevenir el envío directo del formulario

            Swal.fire({
                title: 'Confirmación',
                text: '¿Estás seguro de que deseas actualizar este usuario?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, actualizar',
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
