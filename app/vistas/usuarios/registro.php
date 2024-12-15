<?php include '../app/vistas/includes/header.php'; ?>

<div class="register-page">
    <?php
    // Mostrar mensajes de error si existen
    if (isset($error)) {
        echo "<p class='error-message'>$error</p>";
    }
    ?>

    <div class="register-box">
        <h2>Formulario de Registro</h2>

        <!-- Formulario de Registro con confirmación -->
        <form action="registrar" method="POST" onsubmit="return customConfirm('¿Estás seguro de que deseas registrar esta cuenta?');">
            <div class="input-group">
                <label for="nombre">Nombre:</label>
                <input type="text" name="nombre" id="nombre" value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>" required placeholder="Introduce tu nombre">
            </div>

            <div class="input-group">
                <label for="email">Correo Electrónico:</label>
                <input type="email" name="email" id="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required placeholder="Introduce tu correo">
            </div>

            <div class="input-group">
                <label for="contrasena">Contraseña:</label>
                <input type="password" name="contrasena" id="contrasena" required placeholder="Crea una contraseña">
            </div>

            <div class="input-group">
                <label for="confirmar_contrasena">Confirmar Contraseña:</label>
                <input type="password" name="confirmar_contrasena" id="confirmar_contrasena" required placeholder="Confirma tu contraseña">
            </div>

            <button type="submit" class="btn">Registrar</button>

            <p class="login-link">
                ¿Ya tienes una cuenta? <a href="login">Inicia sesión aquí</a>
            </p>
        </form>
    </div>
</div>

<script>
    function customConfirm(message) {
        return new Promise((resolve) => {
            Swal.fire({
                title: 'Confirmación',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, continuar',
                cancelButtonText: 'Cancelar',
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            }).then((result) => {
                resolve(result.isConfirmed);
            });
        });
    }

    document.querySelectorAll('form[onsubmit]').forEach(form => {
        form.onsubmit = async function(event) {
            event.preventDefault(); // Prevenir el envío del formulario
            const confirmed = await customConfirm(this.getAttribute('onsubmit').replace('return customConfirm(\'', '').replace('\');', ''));
            if (confirmed) this.submit();
        };
    });
</script>


<?php include '../app/vistas/includes/footer.php'; ?>
