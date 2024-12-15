<?php include '../app/vistas/includes/header.php'; ?>

<div class="direcciones-container">
    <h1 class="direcciones-title">Direcciones de Usuario</h1>

    <!-- Mostrar mensajes de error o éxito -->
    <?php if (isset($_SESSION['error'])) : ?>
        <div class="direcciones-alert direcciones-alert-error">
            <?= htmlspecialchars($_SESSION['error']); ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])) : ?>
        <div class="direcciones-alert direcciones-alert-success">
            <?= htmlspecialchars($_SESSION['success']); ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <!-- Mostrar las direcciones -->
    <?php foreach ($direcciones as $direccion) : ?>
        <div class="direccion-item <?= $direccion['direccion_principal'] == 1 ? 'direccion-principal' : ''; ?>">
            <?php if ($direccion['direccion_principal'] == 1): ?>
                <h3 class="direccion-principal-title"><strong>Esta es tu dirección principal.</strong></h3>
            <?php endif; ?>
            <p><strong>Ciudad:</strong> <?= htmlspecialchars($direccion['ciudad']); ?></p>
            <p><strong>Código Postal:</strong> <?= htmlspecialchars($direccion['codigo_postal']); ?></p>
            <p><strong>País:</strong> <?= htmlspecialchars($direccion['pais']); ?></p>

            <div class="direccion-actions">
                <a href="javascript:void(0);" 
                   class="direccion-action-link btn-delete-direccion" 
                   data-id="<?= $direccion['id_direccion']; ?>"
                   data-ciudad="<?= htmlspecialchars($direccion['ciudad']); ?>">
                   Eliminar
                </a>
            </div>
        </div>
    <?php endforeach; ?>

    <div class="direcciones-links">
        <a href="mostrarFormularioAddDireccion" class="add-direccion-link">Añadir Nueva Dirección</a>
    </div>
</div>

<!-- Modal de confirmación (SweetAlert2) -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script>
    // Función para mostrar el modal de confirmación
    function customConfirm(message) {
        return new Promise((resolve) => {
            Swal.fire({
                title: 'Confirmación',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
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

    // Agregar evento de clic en los enlaces de eliminación
    document.querySelectorAll('.btn-delete-direccion').forEach(btn => {
        btn.addEventListener('click', async function(event) {
            event.preventDefault();  // Prevenir la acción predeterminada (enlace)
            const direccionId = this.getAttribute('data-id');
            const direccionCiudad = this.getAttribute('data-ciudad');
            const confirmed = await customConfirm(`¿Estás seguro de que deseas eliminar la dirección en "${direccionCiudad}"?`);

            if (confirmed) {
                // Redirigir al enlace de eliminación si el usuario confirma
                window.location.href = `eliminarDireccion?id=${direccionId}`;
            }
        });
    });
</script>
<?php include '../app/vistas/includes/footer.php'; ?>

