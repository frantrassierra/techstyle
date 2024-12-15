<?php include '../app/vistas/includes/header.php'; ?>

<div class="size-edit-container">
    <h1 class="form-title">Editar Categoría</h1>

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

    <form action="procesarActualizacionCategoria?id=<?php echo $categoria['id_categoria']; ?>" method="POST"
        class="form-container" id="category-edit-form">
        <label for="nombre_categoria">Nombre de la Categoría:</label>
        <input type="text" name="nombre_categoria" id="nombre_categoria"
            value="<?php echo htmlspecialchars($categoria['nombre']); ?>" required>

        <label for="descripcion">Descripción:</label>
        <textarea name="descripcion"
            id="descripcion"><?php echo htmlspecialchars($categoria['descripcion']); ?></textarea>

        <button type="submit" class="submit-btn">Actualizar</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('category-edit-form');

        form.addEventListener('submit', function (event) {
            event.preventDefault(); // Prevenir el envío directo del formulario

            Swal.fire({
                title: 'Confirmación',
                text: '¿Estás seguro de que deseas actualizar esta categoría?',
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
