<?php include '../app/vistas/includes/header.php'; ?>

<div class="size-registration-container">
    <h1 class="form-title">Registrar Nueva Categoría</h1>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="message success-message"><?php echo htmlspecialchars($_SESSION['success']); ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="message error-message"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Formulario con confirmación antes de enviar -->
    <form action="procesarRegistroCategoria" method="POST" class="form-container" onsubmit="return customConfirm('¿Estás seguro de que deseas registrar esta nueva categoría?');">
        <label for="nombre_categoria">Nombre de la Categoría:</label>
        <input type="text" name="nombre_categoria" id="nombre_categoria" required>

        <label for="descripcion">Descripción:</label>
        <textarea name="descripcion" id="descripcion"></textarea>

        <button type="submit" class="submit-btn" name="submit_categoria">Registrar</button>
    </form>
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
