<?php include '../app/vistas/includes/header.php'; ?>


<style>
  /* Estilos comunes para ambos formularios */
  /* Estilos comunes para ambos formularios */
  form.custom-form {
    background-color: #ffffff;
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    max-width: 600px;
    margin: 20px auto;
    font-family: 'Arial', sans-serif;
  }
/* Animación para hacer que el mensaje de error parpadee */
@keyframes blink {
  0% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
  100% {
    opacity: 1;
  }
}

/* Aplica la animación al mensaje de error */
#error-message {
  animation: blink 1s ease-in-out infinite;
}
#talla-error{
  animation: blink 1s ease-in-out infinite;
}

  form.custom-form label {
    display: block;
    font-size: 16px;
    margin-bottom: 10px;
    color: #333;
  }

  form.custom-form input,
  form.custom-form textarea,
  form.custom-form select,
  form.custom-form button {
    width: 100%;
    padding: 12px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 16px;
    box-sizing: border-box;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
  }

  /* Estilo para los campos de entrada */
  form.custom-form input:focus,
  form.custom-form textarea:focus,
  form.custom-form select:focus {
    border-color: #5cb85c;
    box-shadow: 0 0 8px rgba(92, 184, 92, 0.6);
  }

  form.custom-form textarea {
    height: 120px;
    resize: vertical;
  }

  /* Estilo de los botones */
  form.custom-form button {
    background-color: #5cb85c;
    color: white;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }

  form.custom-form button:hover {
    background-color: #460101;
  }

  form.custom-form button:focus {
    outline: none;
  }

  /* Estilo para las opciones de talla como botones cuadrados */
  .talla-selector {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    /* Permite que las tallas se ajusten cuando no hay espacio suficiente */
    margin-bottom: 15px;
  }

  .talla-option {
    background-color: #f0f0f0;
    padding: 10px 20px;
    border-radius: 5px;
    font-size: 16px;
    font-weight: bold;
    color: #333;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: center;
    display: inline-block;
  }

  .talla-option:hover {
    background-color: #460101;
    color: white;
    transform: translateY(-3px);
  }

  /* Animación de selección de talla */
  .talla-option.selected {
    background-color: #ff4949;
    color: white;
    transform: translateY(-3px);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  }

  /* Estilo de los radio buttons ocultos */
  .talla-option input[type="radio"] {
    display: none;
  }

  /* Estado seleccionado para el input de radio */
  .talla-option input[type="radio"]:checked+.talla-option {
    background-color: #ff4949;
    color: white;
    transform: translateY(-3px);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  }

  /* Estilo de placeholder */
  textarea::placeholder,
  select option {
    color: #888;
  }

  select option:hover {
    background-color: #460101;
  }

  /* Diseño responsivo */
  @media (max-width: 768px) {
    form.custom-form {
      padding: 15px;
      max-width: 100%;
    }

    form.custom-form button {
      padding: 12px;
    }
  }

  /* Estilo para las opciones de talla como botones cuadrados */
  .talla-selector {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    margin-bottom: 15px;
  }

  .talla-option {
    background-color: #f0f0f0;
    padding: 10px 20px;
    border-radius: 5px;
    font-size: 16px;
    font-weight: bold;
    color: #333;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: center;
    display: inline-block;
  }

  .talla-option:hover {
    background-color: #460101;
    color: white;
    transform: translateY(-3px);
  }

  /* Animación de selección de talla */
  .talla-option.selected {
    background-color: #ff4949;
    color: white;
    transform: translateY(-3px);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  }

  /* Estilo de los radio buttons ocultos */
  .talla-option input[type="radio"] {
    display: none;
  }

  /* Estado seleccionado para el input de radio */
  .talla-option input[type="radio"]:checked+.talla-option {
    background-color: #ff4949;
    color: white;
    transform: translateY(-3px);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  }
</style>
<section class="product-section layout_padding">
  <div class="container-fluid">
    <div class="row">
      <!-- Columna de imágenes del producto -->
      <div class="col-md-6">


        <img src="productos/<?php echo htmlspecialchars($producto->getImagen(), ENT_QUOTES, 'UTF-8'); ?>"
          alt="Imagen del producto" class="img-fluid" style=" width: 100%;">


      </div>

      <!-- Columna con detalles del producto -->
      <div class="col-md-6">
        <div class="detail-box">
          <div class="centered-container">
            <h2>
              <?php echo htmlspecialchars($producto->getNombre(), ENT_QUOTES, 'UTF-8'); ?>
            </h2>
            <p><strong>Descripción Completa:</strong>
              <?php echo nl2br(htmlspecialchars($producto->getDescripcion(), ENT_QUOTES, 'UTF-8')); ?>
            </p>
            <p><strong>Descripción Corta:</strong>
              <?php echo htmlspecialchars($producto->getDescripcionCorta(), ENT_QUOTES, 'UTF-8'); ?>
            </p>
            <p><strong>Precio:</strong> 
              <?php echo number_format($producto->getPrecioPVP(), 2); ?>
              €
            </p>
            <p><strong>Stock Disponible:</strong>
              <?php echo htmlspecialchars($producto->getStock(), ENT_QUOTES, 'UTF-8'); ?>
            </p>
            <p><strong>Calificación Promedio:</strong> <?php echo number_format($calificacionPromedio, 1); ?> estrellas
            </p>
          </div>

          <!-- Mostrar mensajes de error o éxito para agregar al carrito -->
          <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
              <?php echo htmlspecialchars($_SESSION['success_message'], ENT_QUOTES, 'UTF-8'); ?>
            </div>
            <?php unset($_SESSION['success_message']); ?>
          <?php endif; ?>

          <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger">
              <strong>Error:</strong> <?php echo htmlspecialchars($_SESSION['error_message'], ENT_QUOTES, 'UTF-8'); ?>
            </div>
            <?php if (isset($_SESSION['error_details'])): ?>
              <div class="alert alert-warning">
                <strong>Detalles del error:</strong>
                <pre><?php echo htmlspecialchars($_SESSION['error_details'], ENT_QUOTES, 'UTF-8'); ?></pre>
              </div>
              <?php unset($_SESSION['error_details']); ?>
            <?php endif; ?>
            <?php unset($_SESSION['error_message']); ?>
          <?php endif; ?>

          <div id="messages" style="display: none;"></div>


          <form id="add-to-cart-form" class="custom-form" onsubmit="return validateForm();">
  <label for="talla">Selecciona una talla:</label>
  <div class="talla-selector">
    <?php
    if (isset($tallas) && is_array($tallas)) {
      foreach ($tallas as $talla) {
        echo "<label class='talla-option'>
        <input type='radio' name='id_talla' value='" . $talla['id_talla'] . "'>
        " . htmlspecialchars($talla['nombre_talla'], ENT_QUOTES, 'UTF-8') . "
      </label>";
      }
    } else {
      echo "<label>No hay tallas disponibles</label>";
    }
    ?>
  </div>
  <span id="talla-error" style="color: red; display: none;">¡Debes seleccionar una talla!</span>

  <br>

  <label for="cantidad">Cantidad:</label>
  <input type="number" id="cantidad" name="cantidad" value="1" min="1"
    max="<?php echo htmlspecialchars($producto->getStock(), ENT_QUOTES, 'UTF-8'); ?>" required>

  <input type="hidden" name="redirect_url"
    value="<?php echo htmlspecialchars($_SERVER['HTTP_REFERER'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
  <input type="hidden" name="id_producto"
    value="<?php echo htmlspecialchars($producto->getIdProducto(), ENT_QUOTES, 'UTF-8'); ?>">
  <input type="hidden" name="precio_unitario"
    value="<?php echo htmlspecialchars($producto->getPrecioPVP(), ENT_QUOTES, 'UTF-8'); ?>">

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
            <strong>
              <?php echo htmlspecialchars($resena['nombre'], ENT_QUOTES, 'UTF-8'); ?>
            </strong>
            <span>(
              <?php echo $resena['calificacion']; ?> estrellas)
            </span>
            <p><strong>Comentario:</strong>
              <?php echo nl2br(htmlspecialchars($resena['comentario'], ENT_QUOTES, 'UTF-8')); ?>
            </p>
            <p><strong>Fecha:</strong> <?php echo date("d/m/Y", strtotime($resena['fecha'])); ?>
            </p>

            <?php if (isset($idUsuarioActual) && $idUsuarioActual == $resena['id_usuario']): ?>
              <form method="POST" action="eliminarResena" style="display:inline;"
                onsubmit="return customConfirm('¿Estás seguro de que deseas eliminar esta reseña?');">
                <input type="hidden" name="id_resena"
                  value="<?php echo htmlspecialchars($resena['id_resena'], ENT_QUOTES, 'UTF-8'); ?>" />
                <input type="hidden" name="id_producto"
                  value="<?php echo htmlspecialchars($producto->getIdProducto(), ENT_QUOTES, 'UTF-8'); ?>" />
                <button type="submit" class="btn btn-danger">Eliminar Reseña</button>
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

    <form action="agregarResena" method="POST"
      onsubmit="return customConfirm('¿Estás seguro de que deseas confirmar?');">
      <input type="hidden" name="id_producto"
        value="<?php echo htmlspecialchars($producto->getIdProducto(), ENT_QUOTES, 'UTF-8'); ?>" />
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


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

document.addEventListener('DOMContentLoaded', function () {
  const messagesDiv = document.getElementById('messages');

  // Función de confirmación personalizada
  async function customConfirm(message) {
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

  // Manejar formularios con `onsubmit`
  document.querySelectorAll('form[onsubmit]').forEach(form => {
    form.onsubmit = async function (event) {
      event.preventDefault(); // Prevenir el envío del formulario

      // Mensaje de confirmación específico
      const message = "¿Estás seguro?";
      const confirmed = await customConfirm(message); // Usar la función de confirmación con el mensaje personalizado
      if (!confirmed) return;

      // Si es el formulario con AJAX, manejarlo aparte
      if (this.id === 'add-to-cart-form') {
        const formData = new FormData(this);

        try {
          const response = await fetch('agregarCarrito', {
            method: 'POST',
            body: formData,
          });

          const result = await response.json();

          messagesDiv.style.display = 'block';
          messagesDiv.textContent = result.message;
          messagesDiv.className = result.success ? 'alert alert-success' : 'alert alert-danger';

          if (result.success) {
            // Aquí eliminamos la clase 'selected' después de confirmar
            const tallaOptions = document.querySelectorAll('.talla-option');
            tallaOptions.forEach(option => option.classList.remove('selected'));

            // También reseteamos el formulario si es exitoso
            this.reset();
          }
        } catch (error) {
          messagesDiv.style.display = 'block';
          messagesDiv.textContent = 'Ocurrió un error al añadir el producto al carrito. Por favor, intenta nuevamente.';
          messagesDiv.className = 'alert alert-danger';
        }
      } else {
        // Envío tradicional del formulario
        this.submit();
      }
    };
});


  const tallaOptions = document.querySelectorAll('.talla-option');
  const tallaError = document.getElementById('talla-error');

  // Establecer el color al seleccionar una talla
  tallaOptions.forEach(option => {
    option.addEventListener('click', function () {
      tallaOptions.forEach(opt => opt.classList.remove('selected'));  // Elimina la clase 'selected' de otras opciones
      this.classList.add('selected');  // Añade la clase 'selected' a la opción clickeada
    });
  });

  // Función para la validación de la talla seleccionada
  document.getElementById('add-to-cart-form').addEventListener('submit', function (event) {
    const selectedTalla = document.querySelector('input[name="id_talla"]:checked');
    if (!selectedTalla) {
      tallaError.style.display = 'inline';  // Muestra el mensaje de error si no hay talla seleccionada
      event.preventDefault();  // Previene el envío del formulario
    } else {
      tallaError.style.display = 'none';  // Oculta el mensaje si se selecciona una talla
    }
  });
});

// Función para ocultar el mensaje
function hideMessage(elementId) {
    const messageElement = document.getElementById(elementId);
    if (messageElement) {
      messageElement.style.display = 'none';
    }
  }

  // Ocultar el mensaje después de 20 segundos
  setTimeout(() => {
    hideMessage('success-message');
    hideMessage('error-message');
  }, 20000); // 20000 ms = 20 segundos

  // Si el usuario hace clic en cualquier botón o envía un formulario, ocultar el mensaje
  document.querySelectorAll('button, form').forEach(element => {
    element.addEventListener('click', () => {
      hideMessage('success-message');
      hideMessage('error-message');
    });
  });
</script>
