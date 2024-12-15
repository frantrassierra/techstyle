<?php include '../app/vistas/includes/header.php'; ?>
<div class="pedido-details-container">
    <h1 class="pedido-details-title">Detalles del Pedido #<?= $pedido[0]['id_pedido'] ?></h1>

    <!-- Información General del Pedido -->
    <div class="pedido-info">
        <h2 class="pedido-info-title">Información del Pedido</h2>
        <p><strong>Fecha del Pedido:</strong> <?= $pedido[0]['fecha_pedido'] ?></p>
        <p><strong>Estado:</strong> <?= $pedido[0]['estado'] ?></p>
        <p><strong>Usuario:</strong> <?= $pedido[0]['usuario'] ?></p>
        <p><strong>Total:</strong> $<?= number_format($pedido[0]['total'], 2) ?></p>
        <p><strong>Dirección:</strong> <?= $pedido[0]['ciudad'] ?>, <?= $pedido[0]['codigo_postal'] ?>, <?= $pedido[0]['pais'] ?></p>
    </div>

    <!-- Productos del Pedido -->
    <div class="pedido-products">
        <h2 class="pedido-products-title">Productos del Pedido</h2>
        <table class="pedido-products-table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Talla</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Precio Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pedido as $detalle): ?>
                <tr>
                    <td><?= htmlspecialchars($detalle['producto']) ?></td>
                    <td><?= htmlspecialchars($detalle['talla']) ?></td>
                    <td><?= htmlspecialchars($detalle['cantidad']) ?></td>
                    <td>$<?= number_format($detalle['precio_unitario'], 2) ?></td>
                    <td>$<?= number_format($detalle['precio_total'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Botón para volver a "Mis Pedidos" -->
    <div class="back-button">
        <a href="misPedidos" class="back-button-link">Volver a Mis Pedidos</a>
    </div>
</div>


<?php include '../app/vistas/includes/footer.php'; ?>
