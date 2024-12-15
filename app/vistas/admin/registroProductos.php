<?php include '../app/vistas/includes/header.php'; ?>

<?php
// Iniciar sesión para leer los mensajes

// Leer mensajes de la sesión y limpiarlos
$mensajeExito = isset($_SESSION['success']) ? $_SESSION['success'] : null;
$mensajeError = isset($_SESSION['error']) ? $_SESSION['error'] : null;
unset($_SESSION['success'], $_SESSION['error']); // Limpiar mensajes después de mostrarlos
?>

<div class="product-registration-container">
    <h1 class="product-form-title">Registrar Nuevo Producto</h1>

    <?php if ($mensajeExito): ?>
        <p class="product-alert-success"><?php echo htmlspecialchars($mensajeExito); ?></p>
    <?php endif; ?>

    <?php if ($mensajeError): ?>
        <p class="product-alert-error"><?php echo htmlspecialchars($mensajeError); ?></p>
    <?php endif; ?>

    <form id="product-form" action="procesarProducto" method="POST" enctype="multipart/form-data" class="product-form">
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" id="nombre" required>

        <label for="descripcion">Descripción:</label>
        <textarea name="descripcion" id="descripcion" required></textarea>

        <label for="descripcion_corta">Descripción Corta:</label>
        <input type="text" name="descripcion_corta" id="descripcion_corta" maxlength="255" required>

        <label for="precioPVP">Precio PVP:</label>
        <input type="number" name="precioPVP" id="precioPVP" step="0.01" required>

        <label for="stock">Stock:</label>
        <input type="number" name="stock" id="stock" required>

        <label for="id_categoria">Categoría:</label>
        <select name="id_categoria" id="id_categoria" required>
            <!-- Generar opciones dinámicamente -->
            <?php foreach ($categorias as $categoria): ?>
                <option value="<?php echo $categoria['id_categoria']; ?>">
                    <?php echo htmlspecialchars($categoria['nombre']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="imagen">Imagen:</label>
        <input type="file" name="imagen" id="imagen" accept="image/*">

        <button type="submit" class="product-btn-submit">Registrar</button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const productForm = document.getElementById('product-form');

        productForm.addEventListener('submit', async function (event) {
            event.preventDefault(); // Prevenir el envío tradicional del formulario

            // Confirmación personalizada
            const result = await Swal.fire({
                title: 'Confirmación',
                text: '¿Estás seguro de que deseas registrar este producto?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, registrar',
                cancelButtonText: 'Cancelar',
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            });

            if (result.isConfirmed) {
                // Enviar el formulario si el usuario confirma
                productForm.submit();
            }
        });
    });
</script>

<?php include '../app/vistas/includes/footer.php'; ?>
