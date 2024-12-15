<?php include '../app/vistas/includes/header.php'; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="message error-message">
        <?php echo $_SESSION['error']; ?>
        <?php unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="message success-message">
        <?php echo $_SESSION['success']; ?>
        <?php unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<div class="size-edit-container">
    <h1 class="form-title">Editar Producto</h1>

    <form action="procesarActualizacionProducto?id=<?php echo $producto['id_producto']; ?>" method="POST" enctype="multipart/form-data" class="edit-form" id="edit-product-form">
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>

        <label for="descripcion">Descripción:</label>
        <textarea name="descripcion" id="descripcion" required><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>

        <label for="descripcion_corta">Descripción Corta:</label>
        <input type="text" name="descripcion_corta" id="descripcion_corta" maxlength="255" value="<?php echo htmlspecialchars($producto['descripcion_corta']); ?>" required>

        <label for="precioPVP">Precio PVP:</label>
        <input type="number" name="precioPVP" id="precioPVP" step="0.01" value="<?php echo htmlspecialchars($producto['precioPVP']); ?>" required>

        <label for="stock">Stock:</label>
        <input type="number" name="stock" id="stock" value="<?php echo htmlspecialchars($producto['stock']); ?>" required>

        <label for="id_categoria">Categoría:</label>
        <select name="id_categoria" id="id_categoria" required>
            <?php foreach ($categorias as $categoria): ?>
                <option value="<?php echo $categoria['id_categoria']; ?>" <?php echo $categoria['id_categoria'] == $producto['id_categoria'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($categoria['nombre']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="imagen">Imagen:</label>
        <input type="file" name="imagen" id="imagen" required accept="image/*">
        <p>Imagen actual: <img src="http://localhost/techStyle/public/productos/<?php echo $producto['imagen']; ?>" alt="Imagen del producto" width="50"></p>

        <button type="submit" class="submit-btn">Actualizar</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('edit-product-form');

        form.addEventListener('submit', function (event) {
            event.preventDefault(); // Prevenir el envío directo del formulario

            Swal.fire({
                title: 'Confirmación',
                text: '¿Estás seguro de que deseas actualizar este producto?',
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
