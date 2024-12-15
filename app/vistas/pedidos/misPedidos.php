<?php include '../app/vistas/includes/header.php'; ?>

<div class="my-orders-container">
    <h1 class="my-orders-title">Mis Pedidos</h1>

    <div class="table-responsive">
        <table class="my-orders-table">
            <thead>
                <tr>
                    <th>Pedido</th>
                    <th>Total</th>
                    <th>Fecha de pedido</th>
                    <th>Ciudad</th>
                    <th>Detalles</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pedidos as $pedido): ?>
                    <tr>
                        <td><?= $pedido['id_pedido'] ?></td>
                        <td>$<?= number_format($pedido['total'], 2) ?></td>
                        <td><?= $pedido['fecha_pedido'] ?></td>
                        <td><?= $pedido['ciudad'] ?></td>
                        <td class="action-links">
                            <a href="detallePedido?idPedido=<?= $pedido['id_pedido'] ?>" class="view-details-link">Ver</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>


<?php include '../app/vistas/includes/footer.php'; ?>
