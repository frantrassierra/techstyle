<?php include '../app/vistas/includes/header.php'; ?>

<section class="payment_section layout_padding">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="detail-box">
                    <?php if (isset($_SESSION['success_message'])): ?>
                        <div class="alert alert-success">
                            <?php echo $_SESSION['success_message']; ?>
                        </div>
                        <?php unset($_SESSION['success_message']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error_message'])): ?>
                        <div class="alert alert-danger">
                            <?php echo $_SESSION['error_message']; ?>
                        </div>
                        <?php unset($_SESSION['error_message']); ?>
                    <?php endif; ?>

                    <h2>Confirmación del Pedido</h2>

                    <p><strong>Dirección de Envío:</strong> <?= htmlspecialchars($direccion['ciudad'] . ', ' . $direccion['pais'], ENT_QUOTES, 'UTF-8') ?></p>

                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio Unitario</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($productosCarrito as $producto): ?>
                                <tr>
                                    <td><?= htmlspecialchars($producto['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($producto['cantidad'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= number_format($producto['precio_unitario'], 2, ',', '.') ?> €</td>
                                    <td><?= number_format($producto['total'], 2, ',', '.') ?> €</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <h3>Total del Pedido: <?= number_format($total, 2, ',', '.') ?> €</h3>

                    <h4>Elija un método de pago</h4>

                    <form action="confirmarPedido" method="POST">
    <div class="form-group" style="width: 100% !important;">
        <label for="metodo_pago">Método de Pago:</label>
        <select  style="width: 100% !important;" name="metodo_pago" id="metodo_pago" class="form-control" required>
            <option value="Tarjeta de Crédito">Tarjeta de Crédito</option>
            <option value="PayPal">PayPal</option>
            <option value="Transferencia Bancaria">Transferencia Bancaria</option>
        </select>
    </div>

    <input type="hidden" name="id_pedido" value="<?= $idPedido ?>">
    <input type="hidden" name="total" value="<?= $total ?>">

    <button type="submit" class="btn btn-success btn-lg" onclick="return customConfirm('¿Estás seguro de que deseas confirmar el pedido?');">Confirmar Pedido</button>
</form>
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

    document.querySelectorAll('form[onsubmit]').forEach(form => {
        form.onsubmit = async function(event) {
            event.preventDefault(); // Prevenir el envío del formulario
            const confirmed = await customConfirm(this.getAttribute('onsubmit').replace('return customConfirm(\'', '').replace('\');', ''));
            if (confirmed) this.submit();
        };
    });
</script>

<?php include '../app/vistas/includes/footer.php'; ?>
