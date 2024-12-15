<?php include '../app/vistas/includes/header.php'; ?>


<div class="edit-profile-page">
    <div class="edit-profile-container">
        <h1 class="edit-profile-title">Editar Perfil</h1>

        <?php if (isset($error)) : ?>
            <p class="edit-profile-error-message"><?= htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form action="editarPerfil" method="POST" class="edit-profile-form" onsubmit="return customConfirm('¿Estás seguro de que deseas actualizar tu perfil?');">
            <div class="edit-profile-form-group">
                <label for="nombre" class="edit-profile-label">Nombre</label>
                <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($usuario['nombre']); ?>" required class="edit-profile-input">
            </div>
            <div class="edit-profile-form-group">
                <label for="email" class="edit-profile-label">Correo Electrónico</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($usuario['email']); ?>" required class="edit-profile-input">
            </div>
            <div class="edit-profile-form-group">
                <button type="submit" class="edit-profile-submit-btn">Actualizar Perfil</button>
            </div>
        </form>
    </div>
</div>



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

    document.querySelectorAll('form[onsubmit]').forEach(form => {
        form.onsubmit = async function(event) {
            event.preventDefault(); // Prevenir el envío del formulario
            const confirmed = await customConfirm(this.getAttribute('onsubmit').replace('return customConfirm(\'', '').replace('\');', ''));
            if (confirmed) this.submit();
        };
    });
</script>

<?php include '../app/vistas/includes/footer.php'; ?>

