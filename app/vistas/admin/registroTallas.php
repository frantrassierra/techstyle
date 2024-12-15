<?php include '../app/vistas/includes/header.php'; ?>

<div class="size-registration-container">
    <h1 class="form-title">Registrar Nueva Talla</h1>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="message success-message"><?php echo htmlspecialchars($_SESSION['success']); ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="message error-message"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form action="procesarRegistroTalla" method="POST" class="form-container" id="size-registration-form">
        <label for="nombre_talla">Nombre de la Talla:</label>
        <input type="text" name="nombre_talla" id="nombre_talla" required>

        <label for="descripcion">Descripción:</label>
        <textarea name="descripcion" id="descripcion"></textarea>

        <button type="submit" class="submit-btn" name="submit_talla">Registrar</button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('size-registration-form');

        form.addEventListener('submit', function (event) {
            event.preventDefault(); // Prevenir el envío directo del formulario

            Swal.fire({
                title: 'Confirmación',
                text: '¿Estás seguro de que deseas registrar esta talla?',
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
