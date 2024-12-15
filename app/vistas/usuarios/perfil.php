<?php include '../app/vistas/includes/header.php'; ?>

<div class="profile-page">
    <div class="profile-container">
        <h1>Perfil de Usuario</h1>

        <!-- Detalles del perfil -->
        <div class="profile-details">
            <p><span class="label">Nombre:</span> <?= htmlspecialchars($usuario['nombre']); ?></p>
            <p><span class="label">Correo Electrónico:</span> <?= htmlspecialchars($usuario['email']); ?></p>
        </div>

        <!-- Acciones de perfil -->
        <div class="actions">
            <a href="editarPerfil" class="btn btn-edit">Editar perfil</a>
            <a href="direcciones" class="btn btn-address">Ver  direcciones</a>
            <a href="misPedidos" class="btn btn-info">Ver pedidos</a>

        </div>
    </div>
</div>

<?php include '../app/vistas/includes/footer.php'; ?>
