<?php include '../app/vistas/includes/header.php'; ?>
<div class="size-list-container">
    <h1 class="form-title">Lista de Usuarios</h1>
    <a href="mostrarFormUsuarios" class="btn-register">Registrar Nuevo Usuario</a>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="message success-message"><?php echo htmlspecialchars($_SESSION['success']); ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="message error-message"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <table class="list-table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['rol']); ?></td>
                    <td>
                        <a href="editarUsuario?id=<?php echo $usuario['id_usuario']; ?>" class="btn-edit">Editar</a>
                        <a href="javascript:void(0);" 
                           class="btn-delete" 
                           data-id="<?php echo $usuario['id_usuario']; ?>"
                           data-name="<?php echo htmlspecialchars($usuario['nombre']); ?>"
                           >Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
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

    // Agregar evento de clic en los enlaces de eliminación
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', async function(event) {
            event.preventDefault();  // Prevenir la acción predeterminada (enlace)
            const userId = this.getAttribute('data-id');
            const userName = this.getAttribute('data-name');
            const confirmed = await customConfirm(`¿Estás seguro de que deseas eliminar el usuario "${userName}"?`);

            if (confirmed) {
                // Redirigir al enlace de eliminación si el usuario confirma
                window.location.href = `eliminarUsuario?id=${userId}`;
            }
        });
    });
</script>


<?php include '../app/vistas/includes/footer.php'; ?>