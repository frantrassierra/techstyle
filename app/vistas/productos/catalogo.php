<?php include '../app/vistas/includes/header.php'; ?>

<section class="product_section">
    <div class="container">
       
        
        <!-- Formulario de Filtro -->
 <!-- Formulario de Filtro -->
<!-- Formulario de Filtro Mejorado -->
<form id="filtroFormulario" action="" method="GET">
    <div class="form-group">
        <label for="categoria_id">Categoría</label>
        <select name="categoria_id" id="categoria_id">
            <option value="">-- Todas las categorías --</option>
            <?php foreach ($categorias as $categoria): ?>
                <option value="<?php echo htmlspecialchars($categoria['id_categoria']); ?>"
                    <?php echo (isset($_GET['categoria_id']) && $_GET['categoria_id'] == $categoria['id_categoria']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($categoria['nombre']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
    <label for="popularidad">Popularidad</label>
    <select name="popularidad" id="popularidad">
        <option value="">-- Todos los productos --</option>
        <option value="1" <?php echo (isset($_GET['popularidad']) && $_GET['popularidad'] == '0') ? 'selected' : ''; ?>>
            Más populares
        </option>
        <option value="0" <?php echo (isset($_GET['popularidad']) && $_GET['popularidad'] == '1') ? 'selected' : ''; ?>>
            Menos populares
        </option>
    </select>
</div>


    <button type="submit" class="btn-filtrar">Filtrar</button>
</form>



        <!-- Contenedor de productos -->
        <div class="product_container" id="productos">
            <?php if (empty($productos)): ?>
                <p>No se encontraron productos.</p>
            <?php else: ?>
                <?php foreach ($productos as $producto): ?>
                    <div class="box" data-id="<?php echo htmlspecialchars($producto->getIdProducto(), ENT_QUOTES, 'UTF-8'); ?>">
                        <div class="box-content">
                            <div class="img-box">
                                <img src="productos/<?php echo htmlspecialchars($producto->getImagen(), ENT_QUOTES, 'UTF-8'); ?>"
                                     alt="Imagen de <?php echo htmlspecialchars($producto->getNombre(), ENT_QUOTES, 'UTF-8'); ?>">
                            </div>
                            <div class="detail-box">
                                <div class="text">
                                    <h6><?php echo htmlspecialchars($producto->getNombre(), ENT_QUOTES, 'UTF-8'); ?></h6>
                                    <h5> <?php echo htmlspecialchars($producto->getPrecioPVP(), ENT_QUOTES, 'UTF-8'); ?> <span>€</span></h5>
                                </div>
                                <div class="like">
                                    <div class="star_container">
                                        <?php for ($i = 0; $i < 5; $i++): ?>
                                            <i class="fa fa-star<?php echo $i < round($producto->calificacion_promedio) ? '' : '-o'; ?>" aria-hidden="true"></i>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="btn-box">
                                <a href="detalleProducto?id_producto=<?php echo htmlspecialchars($producto->getIdProducto(), ENT_QUOTES, 'UTF-8'); ?>">Ver detalles</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

 





    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        $('#filtroFormulario').on('submit', function (event) {
            event.preventDefault(); // Evitar recarga de la página

            var categoria_id = $('#categoria_id').val();
            var popularidad = $('#popularidad').val();

            $.ajax({
                url: 'mostrarProductos?ajax=true',  // Apunta a la ruta correcta con ajax=true
                method: 'GET',
                data: {
                    categoria_id: categoria_id,
                    popularidad: popularidad
                },
                success: function (response) {
                    // Actualiza el contenedor de productos con la respuesta HTML
                    $('#productos').html(response);
                },
                error: function () {
                    alert('Hubo un error al filtrar los productos.');
                }
            });
        });
    });
</script>



<?php include '../app/vistas/includes/footer.php'; ?>

