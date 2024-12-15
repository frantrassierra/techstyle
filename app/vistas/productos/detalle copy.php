
<?php include '../app/vistas/includes/header.php'; ?>



<section class="product-section layout_padding">
  <div class="container-fluid">
    <div class="row">
      <!-- Columna de imágenes del producto -->
      <div class="col-md-6">
       
            <img src="http://localhost/techStyle/public/productos/<?php echo htmlspecialchars($producto->getImagen(), ENT_QUOTES, 'UTF-8'); ?>" alt="Imagen del producto" class="img-fluid">
     
        </div>
      </div>
      
      <!-- Columna con detalles del producto -->
      <div class="col-md-6">
        <div class="detail-box">
          <h2><?php echo htmlspecialchars($producto->getNombre(), ENT_QUOTES, 'UTF-8'); ?></h2>
          <p><strong>Descripción Completa:</strong> <?php echo nl2br(htmlspecialchars($producto->getDescripcion(), ENT_QUOTES, 'UTF-8')); ?></p>
          <p><strong>Descripción Corta:</strong> <?php echo htmlspecialchars($producto->getDescripcionCorta(), ENT_QUOTES, 'UTF-8'); ?></p>
          <p><strong>Precio:</strong> $<?php echo number_format($producto->getPrecioPVP(), 2); ?></p>
          <p><strong>Stock Disponible:</strong> <?php echo htmlspecialchars($producto->getStock(), ENT_QUOTES, 'UTF-8'); ?></p>
          <p><strong>Calificación Promedio:</strong> <?php echo number_format($calificacionPromedio, 1); ?> estrellas</p>
          
          <!-- Mostrar mensajes de error o éxito para agregar al carrito -->
          <?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success">
        <?php echo htmlspecialchars($_SESSION['success_message'], ENT_QUOTES, 'UTF-8'); ?>
    </div>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger">
        <?php echo htmlspecialchars($_SESSION['error_message'], ENT_QUOTES, 'UTF-8'); ?>
    </div>
    <?php unset($_SESSION['error_message']); ?>
<?php endif; ?>

          <div id="messages" style="display: none;"></div>

<!-- Formulario para añadir al carrito -->
<form id="add-to-cart-form">
  <label for="talla">Selecciona una talla:</label>
  <select id="talla" name="id_talla" required>
    <?php 
      if (isset($tallas) && is_array($tallas)) {
        foreach ($tallas as $talla) {
          echo "<option value='" . $talla['id_talla'] . "'>" . htmlspecialchars($talla['nombre_talla'], ENT_QUOTES, 'UTF-8') . "</option>";
        }
      } else {
        echo "<option disabled>No hay tallas disponibles</option>";
      }
    ?>
  </select>

  <br>

  <label for="cantidad">Cantidad:</label>
  <input type="number" id="cantidad" name="cantidad" value="1" min="1" max="<?php echo htmlspecialchars($producto->getStock(), ENT_QUOTES, 'UTF-8'); ?>" required>
  <input type="hidden" name="redirect_url" value="<?php echo htmlspecialchars($_SERVER['HTTP_REFERER'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
  <input type="hidden" name="id_producto" value="<?php echo htmlspecialchars($producto->getIdProducto(), ENT_QUOTES, 'UTF-8'); ?>">
  <input type="hidden" name="precio_unitario" value="<?php echo htmlspecialchars($producto->getPrecioPVP(), ENT_QUOTES, 'UTF-8'); ?>">

  <br><br>

  <button type="submit" class="btn btn-success">Añadir al carrito</button>
</form>
          
        </div>
      </div>
    </div>
  </div>
</section>


<!-- Sección de reseñas -->
<section class="reviews-section layout_padding">
  <div class="container">
    <h3>Reseñas de los usuarios:</h3>
    <?php if (!empty($resenas)): ?>
      <ul class="reviews-list">
        <?php foreach ($resenas as $resena): ?>
          <li class="review-item">
            <strong><?php echo htmlspecialchars($resena['nombre'], ENT_QUOTES, 'UTF-8'); ?></strong>
            <span>(<?php echo $resena['calificacion']; ?> estrellas)</span>
            <p><strong>Comentario:</strong> <?php echo nl2br(htmlspecialchars($resena['comentario'], ENT_QUOTES, 'UTF-8')); ?></p>
            <p><strong>Fecha:</strong> <?php echo date("d/m/Y", strtotime($resena['fecha_resena'])); ?></p>

            <!-- Verificar si la reseña pertenece al usuario autenticado -->
            <?php if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $resena['id_usuario']): ?>
              <form action="eliminarResena" method="POST" style="display:inline;">
                <input type="hidden" name="id_resena" value="<?php echo htmlspecialchars($resena['id_resena'], ENT_QUOTES, 'UTF-8'); ?>" />
                <input type="hidden" name="id_producto" value="<?php echo htmlspecialchars($producto->getIdProducto(), ENT_QUOTES, 'UTF-8'); ?>" />
                <button type="submit" class="btn btn-danger" onclick="return confirm('¿Estás seguro de que deseas eliminar esta reseña?');">Eliminar Reseña</button>
              </form>
            <?php endif; ?>
            <hr>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <p>No hay reseñas para este producto.</p>
    <?php endif; ?>
  </div>
</section>

<!-- Formulario para añadir reseña -->
<section class="add-review-form layout_padding">
  <div class="container">
    <h3>Añadir una Reseña:</h3>

    <!-- Mostrar mensajes de error o éxito para agregar reseña -->
    <?php
    if (isset($_SESSION['error_message_resena'])) {
        echo "<div class='alert alert-error'>" . htmlspecialchars($_SESSION['error_message_resena'], ENT_QUOTES, 'UTF-8') . "</div>";
        unset($_SESSION['error_message_resena']);
    }

    if (isset($_SESSION['success_message_resena'])) {
        echo "<div class='alert alert-success'>" . htmlspecialchars($_SESSION['success_message_resena'], ENT_QUOTES, 'UTF-8') . "</div>";
        unset($_SESSION['success_message_resena']);
    }
    ?>

    <form action="agregarResena" method="POST">
      <input type="hidden" name="id_producto" value="<?php echo htmlspecialchars($producto->getIdProducto(), ENT_QUOTES, 'UTF-8'); ?>" />
      <textarea name="comentario" placeholder="Escribe tu comentario" required></textarea>
      <select name="calificacion" required>
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
      </select>
      <button type="submit" class="btn btn-primary">Agregar Reseña</button>
    </form>
  </div>
</section>




<?php include '../app/vistas/includes/footer.php'; ?>



<script>
document.addEventListener('DOMContentLoaded', function () {


  const form = document.getElementById('add-to-cart-form');
  const messagesDiv = document.getElementById('messages');

  form.addEventListener('submit', async function (event) {
    event.preventDefault(); // Prevenir el envío tradicional del formulario

    // Crear un objeto FormData con los datos del formulario
    const formData = new FormData(form);

    try {
      // Realizar la solicitud AJAX
      const response = await fetch('agregarCarrito', {
        method: 'POST',
        body: formData,
      });

      // Manejar la respuesta del servidor
      const result = await response.json();

      // Mostrar mensajes de éxito o error
      messagesDiv.style.display = 'block';
      messagesDiv.textContent = result.message;
      messagesDiv.className = result.success ? 'alert alert-success' : 'alert alert-danger';

      if (result.success) {
        // Limpiar el formulario o realizar otras acciones si es necesario
        form.reset();
      }
    } catch (error) {
      // Manejar errores de la solicitud
      messagesDiv.style.display = 'block';
      messagesDiv.textContent = 'Ocurrió un error al añadir el producto al carrito. Por favor, intenta nuevamente.';
      messagesDiv.className = 'alert alert-danger';
    }
  });
});
</script>

