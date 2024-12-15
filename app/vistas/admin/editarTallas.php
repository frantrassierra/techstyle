<?php include '../app/vistas/includes/header.php'; ?>

<div class="size-edit-container">
    <h1 class="form-title">Editar Talla</h1>

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

    <form action="procesarActualizacionTalla?id=<?php echo $talla['id_talla']; ?>" method="POST" class="form-container" id="edit-size-form">
        <label for="nombre_talla">Nombre de la Talla:</label>
        <input type="text" name="nombre_talla" id="nombre_talla"
            value="<?php echo htmlspecialchars($talla['nombre_talla']); ?>" required>

        <label for="descripcion">Descripción:</label>
        <textarea name="descripcion" id="descripcion"><?php echo htmlspecialchars($talla['descripcion']); ?></textarea>

        <button type="submit" class="submit-btn">Actualizar</button>
    </form>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('edit-size-form');

        form.addEventListener('submit', function (event) {
            event.preventDefault(); // Prevenir el envío directo del formulario

            Swal.fire({
                title: 'Confirmación',
                text: '¿Estás seguro de que deseas actualizar esta talla?',
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
