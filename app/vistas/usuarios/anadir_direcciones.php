<?php include '../app/vistas/includes/header.php'; ?>


<?php
// Asegurarse de que el usuario esté autenticado antes de mostrar el formulario
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login');
    exit();
}
?>

<div class="custom-form-container">
    <h2 class="custom-form-title">Añadir Dirección</h2>

    <!-- Comprobar si hay un mensaje de error -->
    <?php if (isset($error)): ?>
        <div class="custom-error-message">
            <p><?php echo $error; ?></p>
        </div>
    <?php endif; ?>

    <!-- Formulario para añadir una dirección -->
    <form action="procesarAddDirecciones" method="POST" class="custom-address-form" onsubmit="return customConfirm('¿Estás seguro de que deseas añadir esta dirección?');">
        <div class="custom-form-group">
            <label for="ciudad" class="custom-label">Ciudad:</label>
            <input type="text" id="ciudad" name="ciudad" required class="custom-input">
        </div>

        <div class="custom-form-group">
            <label for="codigo_postal" class="custom-label">Código Postal:</label>
            <input type="text" id="codigo_postal" name="codigo_postal" required class="custom-input">
        </div>

        <div class="custom-form-group">
            <label for="pais" class="custom-label">País:</label>
            <input type="text" id="pais" name="pais" required class="custom-input">
        </div>

        <div class="custom-form-group">
            <label for="direccion_principal" class="custom-label">¿Es esta tu dirección principal?</label>
            <input type="checkbox" id="direccion_principal" name="direccion_principal" value="1" class="custom-checkbox">
        </div>

        <button type="submit" class="custom-submit-btn">Añadir Dirección</button>
    </form>

    <!-- Enlace para volver a las direcciones -->
    <a href="direcciones" class="custom-back-link">Volver a las direcciones</a>
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
