<?php include '../app/vistas/includes/header.php'; ?>

<?php
// Verifica si el usuario es administrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header('Location: denegado');
    exit();
}
?>

<section class="admin-section">
    <div class="container">
        <div class="admin-header">
            <h1>Bienvenido al Panel de Administrador,
                <?php echo htmlspecialchars($_SESSION['nombre']); ?>
            </h1>
        </div>

        <div class="admin-buttons-container">
            <div class="row">
                <div class="col-md-6 col-lg-4 mx-auto">
                    <div class="admin-button-group">
                        <a href="listarUsuarios" class="btn-admin">Gestionar Usuarios</a>
                        <a href="listarCategorias" class="btn-admin">Gestionar Categorías</a>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 mx-auto">
                    <div class="admin-button-group">
                        <a href="listarTallas" class="btn-admin">Gestionar Tallas</a>
                        <a href="listarProductos" class="btn-admin">Gestionar Productos</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../app/vistas/includes/footer.php'; ?>