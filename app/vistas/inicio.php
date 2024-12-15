<?php include '../app/vistas/includes/header.php';

?>

<!-- end header section -->
<!-- slider section -->
<section class="slider_section">



  <div class="slider_bg_box">
    <video autoplay loop muted playsinline class="background_video">
      <source src="/images/techstyle_fondo.mp4" type="video/mp4">
      Tu navegador no soporta la reproducción de videos.
    </video>
  </div>


  <div id="customCarousel1" class="carousel slide" data-ride="carousel">
    <div class="carousel-inner">
      <!-- Primer ítem del carrusel -->
      <div class="carousel-item active">
        <div class="container">
          <div class="row">
            <div class="col-md-7">
              <div class="detail-box">
             
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Segundo ítem del carrusel -->
      <div class="carousel-item">
        <div class="container">
          <div class="row">
            <div class="col-md-7">
              <div class="detail-box">
            

              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Tercer ítem del carrusel -->
      <div class="carousel-item">
        <div class="container">
          <div class="row">
            <div class="col-md-7">
              <div class="detail-box">
            

              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Cuarto ítem del carrusel -->
      <div class="carousel-item">
        <div class="container">
          <div class="row">
            <div class="col-md-7">
              <div class="detail-box">
              

              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quinto ítem del carrusel -->
      <div class="carousel-item">
        <div class="container">
          <div class="row">
            <div class="col-md-7">
              <div class="detail-box">
            
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>


  </div>

</section>
<!-- end slider section -->
</div>

<!-- sección de servicios -->

<section class="service_section">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-6 col-lg-3">
        <div class="box">
          <div class="img-box">
            <img src="images/feature-1.png" alt="">
          </div>
          <div class="detail-box">
            <h5>
              Entrega Rápida
            </h5>

          </div>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="box">
          <div class="img-box">
            <img src="images/feature-2.png" alt="">
          </div>
          <div class="detail-box">
            <h5>
              Envío Gratis
            </h5>

          </div>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="box">
          <div class="img-box">
            <img src="images/feature-3.png" alt="">
          </div>
          <div class="detail-box">
            <h5>
              Mejor Calidad
            </h5>

          </div>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="box">
          <div class="img-box">
            <img src="images/feature-4.png" alt="">
          </div>
          <div class="detail-box">
            <h5>
              Atención al Cliente 24x7
            </h5>

          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- fin de la sección de servicios -->



<!-- Sección Sobre Nosotros -->
<section class="about_section layout_padding">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-6">
        <div class="img_container">
          <div class="img-box b1">
            <img src="images/a-1.jpg" alt="Nuestra tienda en acción">
          </div>
          <div class="img-box b2">
            <img src="images/a-2.jpg" alt="Productos destacados de nuestra tienda">
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="detail-box">
          <h2>Conoce Nuestra Tienda</h2>
          <p>
            En nuestra tienda ofrecemos productos de la más alta calidad, seleccionados cuidadosamente para satisfacer
            las necesidades de nuestros clientes. Nos enorgullece brindar un servicio personalizado, adaptado a cada uno
            de nuestros compradores, con el objetivo de ofrecer una experiencia única de compra.
          </p>
          <a  class="leermassobre"  href="sobrenosotros">
            Leer más sobre nosotros
          </a>
        </div>
      </div>
    </div>
  </div>
</section>



<!-- product section -->
<section class="product_section">
  <div class="container">
    <div class="product_heading">
      <h2>Productos Más Valorados</h2>
      <a href="mostrarProductos" class="catalog_button">Ver Catálogo Completo</a>
    </div>

    <!-- Contenedor de productos -->
    <div class="product_container" id="productos">
      <?php


      if (empty($productos)): ?>
        <p>No se encontraron productos.</p>
      <?php else: ?>
        <?php foreach ($productos as $producto): ?>
          <div class="box" data-id="<?php echo htmlspecialchars($producto->getIdProducto(), ENT_QUOTES, 'UTF-8'); ?>">
            <div class="box-content">
              <div class="img-box">
                <img src="/productos/<?php echo htmlspecialchars($producto->getImagen(), ENT_QUOTES, 'UTF-8'); ?>"
                  alt="Imagen de <?php echo htmlspecialchars($producto->getNombre(), ENT_QUOTES, 'UTF-8'); ?>">
              </div>
              <div class="detail-box">
                <div class="text">
                  <h6>
                    <?php echo htmlspecialchars($producto->getNombre(), ENT_QUOTES, 'UTF-8'); ?>
                  </h6>
                  <h5><span>$</span> <?php echo htmlspecialchars($producto->getPrecioPVP(), ENT_QUOTES, 'UTF-8'); ?>
                  </h5>
                </div>
                <div class="like">
                  <h6>Calificación</h6>
                  <div class="star_container">
                    <?php for ($i = 0; $i < 5; $i++): ?>
                      <i class="fa fa-star<?php echo $i < round($producto->calificacion_promedio) ? '' : '-o'; ?>"
                        aria-hidden="true"></i>
                    <?php endfor; ?>
                  </div>
                </div>
              </div>
              <div class="btn-box">
                <a
                  href="detalleProducto?id_producto=<?php echo htmlspecialchars($producto->getIdProducto(), ENT_QUOTES, 'UTF-8'); ?>">Ver
                  detalles</a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>




<!-- end product section -->


<!-- Sección de testimonios -->
<section class="client_section layout_padding-bottom">
  <div class="container">
    <div class="heading_container heading_center">
      <h2>Testimonios de nuestros clientes</h2>
    </div>
  </div>
  <div id="customCarousel2" class="carousel slide" data-ride="carousel">
    <div class="carousel-inner">
      <div class="carousel-item active">
        <div class="container">
          <div class="row">
            <div class="col-md-10 mx-auto">
              <div class="box">
                <div class="img-box">
                  <img src="/images/client.jpg" alt="Testimonio de cliente">
                </div>
                <div class="detail-box">
                  <div class="client_info">
                    <div class="client_name">
                      <h5>Juan Pérez</h5>
                      <h6>Cliente Satisfecho</h6>
                    </div>
                    <i class="fa fa-quote-left" aria-hidden="true"></i>
                  </div>
                  <p>
                    "Gracias al servicio recibido, pude resolver mis necesidades de manera rápida y eficiente. El equipo
                    fue increíblemente profesional y atento, lo cual me dio mucha confianza para continuar con mi
                    proyecto. Sin duda volveré a elegirlos en el futuro."
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="carousel-item">
        <div class="container">
          <div class="row">
            <div class="col-md-10 mx-auto">
              <div class="box">
                <div class="img-box">
                  <img src="images/client.jpg" alt="Testimonio de cliente">
                </div>
                <div class="detail-box">
                  <div class="client_info">
                    <div class="client_name">
                      <h5>Ana García</h5>
                      <h6>Cliente Contento</h6>
                    </div>
                    <i class="fa fa-quote-left" aria-hidden="true"></i>
                  </div>
                  <p>
                    "El trato que recibí fue excepcional. Desde el primer contacto, me sentí escuchada y comprendida.
                    Los resultados superaron mis expectativas y no dudaré en recomendar sus servicios a mis conocidos."
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="carousel-item">
        <div class="container">
          <div class="row">
            <div class="col-md-10 mx-auto">
              <div class="box">
                <div class="img-box">
                  <img src="images/client.jpg" alt="Testimonio de cliente">
                </div>
                <div class="detail-box">
                  <div class="client_info">
                    <div class="client_name">
                      <h5>Carlos Méndez</h5>
                      <h6>Cliente Satisfecho</h6>
                    </div>
                    <i class="fa fa-quote-left" aria-hidden="true"></i>
                  </div>
                  <p>
                    "El servicio fue increíble, superaron todas mis expectativas. Todo el equipo fue muy amable,
                    eficiente y profesional. Recomiendo totalmente sus servicios y no tengo duda de que volveré a
                    trabajar con ellos en el futuro."
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <ol class="carousel-indicators">
      <li data-target="#customCarousel2" data-slide-to="0" class="active"></li>
      <li data-target="#customCarousel2" data-slide-to="1"></li>
      <li data-target="#customCarousel2" data-slide-to="2"></li>
    </ol>
  </div>
</section>

<!-- end client section -->

<section class="contact_section layout_padding">
  <div class="container">
    <div class="heading_container">
      <h2>Contacta con Techstyle</h2>
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="form_container">
          <!-- Formulario de contacto -->
          <form id="contact-form">
            <div>
              <input type="text" name="nombre" placeholder="Introduce nombre" required />
            </div>
            <div>
              <input type="number" name="telefono" placeholder="Introduce número de teléfono" required />
            </div>
            <div>
              <input type="email" name="email" placeholder="Introduce correo" required />
            </div>
            <div>
              <textarea name="mensaje" class="message-box" placeholder="Introduce mensaje" required></textarea>
            </div>
            <div class="btn_box">
              <button type="submit">Enviar</button>
            </div>
          </form>
          <!-- Contenedor de mensajes -->
          <div id="contact-message" style="display: none;"></div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="map_container">
          <div class="map">
            <img src="productos/1732902452_a-1.jpg" alt="Imagen del producto" class="img-fluid">
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

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

  document.addEventListener('DOMContentLoaded', function () {
    const contactForm = document.getElementById('contact-form');
    const contactMessage = document.getElementById('contact-message');

    contactForm.addEventListener('submit', async function (event) {
      event.preventDefault(); // Prevenir el envío tradicional del formulario

      // Confirmación personalizada
      const confirmed = await customConfirm('¿Estás seguro de que deseas enviar este mensaje?');
      if (!confirmed) {
        return; // Detener si el usuario cancela
      }

      // Crear un objeto FormData con los datos del formulario
      const formData = new FormData(contactForm);

      try {
        // Realizar la solicitud AJAX
        const response = await fetch('procesarFormularioContacta', {
          method: 'POST',
          body: formData
        });

        // Procesar la respuesta del servidor
        const result = await response.json();

        // Mostrar el mensaje en el contenedor de mensajes
        contactMessage.style.display = 'block';
        contactMessage.textContent = result.message;
        contactMessage.className = result.success ? 'alert alert-success' : 'alert alert-danger';

        // Limpiar el formulario si el mensaje fue enviado con éxito
        if (result.success) {
          contactForm.reset();
        }
      } catch (error) {
        // Manejar errores de red o del servidor
        contactMessage.style.display = 'block';
        contactMessage.textContent = 'Ocurrió un error al procesar tu mensaje. Por favor, intenta nuevamente.';
        contactMessage.className = 'alert alert-danger';
      }
    });
  });
</script>




<?php include '../app/vistas/includes/footer.php'; ?>