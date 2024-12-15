<?php include '../app/vistas/includes/header.php'; ?>

<div class="order-confirmation-container">
    <h1 class="order-confirmation-title">¡Tu pedido ha sido confirmado con éxito!</h1>

    <h2 class="order-details-title">Detalles del Pedido</h2>

    <p><strong>ID del Pedido:</strong> <?= $pedido[0]['id_pedido'] ?></p>
    <p><strong>Fecha:</strong> <?= $pedido[0]['fecha_pedido'] ?></p>
    <p><strong>Total:</strong> $<?= number_format($pedido[0]['total'], 2) ?></p>
    <p><strong>Estado:</strong> <?= $pedido[0]['estado'] ?></p>

    <h3 class="shipping-info-title">Enviado a:</h3>
    <p><strong>Ciudad:</strong> <?= $pedido[0]['ciudad'] ?></p>
    <p><strong>Código Postal:</strong> <?= $pedido[0]['codigo_postal'] ?></p>
    <p><strong>País:</strong> <?= $pedido[0]['pais'] ?></p>

    <h3 class="products-title">Productos:</h3>
    <table class="order-products-table">
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
            <?php foreach ($pedido as $producto): ?>
                <tr>
                    <td><?= $producto['producto'] ?></td>
                    <td><?= $producto['talla'] ?></td>
                    <td><?= $producto['cantidad'] ?></td>
                    <td>$<?= number_format($producto['precio_unitario'], 2) ?></td>
                    <td>$<?= number_format($producto['precio_total'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../app/vistas/includes/footer.php'; ?>
