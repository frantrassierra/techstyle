<?php include '../app/vistas/includes/header.php'; ?>

<section class="contact_section layout_padding">
    <div class="container">
        <div class="heading_container">
            <h2>Contacta con nosotros</h2>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form_container">
                    <!-- Formulario de contacto -->
                    <form id="contact-form" onsubmit="return customConfirm('¿Estás seguro de que deseas enviar este mensaje?');">
                        <div>
                            <input type="text" name="nombre" placeholder="Introduce tu nombre" required />
                        </div>
                        <div>
                            <input type="number" name="telefono" placeholder="Introducet tu número de teléfono" required />
                        </div>
                        <div>
                            <input type="email" name="email" placeholder="Introduce correo" required />
                        </div>
                        <div>
                            <textarea name="mensaje" class="message-box" placeholder="Introduce mensaje" required></textarea>
                        </div>
                        <div class="btn_box">
                            <button type="submit">SEND</button>
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


  <!-- end contact section -->


  

  
  <?php include '../app/vistas/includes/footer.php'; ?>
