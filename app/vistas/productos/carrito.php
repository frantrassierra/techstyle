<?php include '../app/vistas/includes/header.php'; ?>





<section class="cart_section layout_padding">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="detail-box">
                    <!-- Mensajes de éxito y error -->
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

                    <!-- Verificación si el carrito está vacío -->
                    <?php if (empty($productos)): ?>
                        <div class="carritovacio">
                            <p class="cart-empty-message">No tienes productos en tu carrito.</p>

                        </div>
                    <?php else: ?>
                        <!-- Acciones del carrito -->
                        <div class="cart-actions text-center mb-4">
                            <h2>Productos en tu carrito</h2>
                            <div class="button-container">
                                <form method="POST" action="vaciarCarrito" style="display:inline;"
                                    onsubmit="return customConfirm('¿Estás seguro de que deseas vaciar el carrito?');">
                                    <button type="submit" class="btn btn-warning btn-lg">Vaciar Carrito</button>
                                </form>
                                <button class="btn btn-success btn-lg" data-toggle="modal"
                                    data-target="#paymentModal">Confirmar Pedido</button>
                            </div>

                        </div>

                        <!-- Tabla de productos en el carrito -->
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Imagen</th>
                                    <th>Nombre</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unitario</th>
                                    <th>Total</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($productos as $producto): ?>
                                    <tr>
                                        <td><img src="productos/<?= htmlspecialchars($producto->getImagen(), ENT_QUOTES, 'UTF-8') ?>"
                                                alt="<?= htmlspecialchars($producto->getNombre(), ENT_QUOTES, 'UTF-8') ?>"
                                                width="50"></td>
                                        <td>
                                            <?= htmlspecialchars($producto->getNombre(), ENT_QUOTES, 'UTF-8') ?>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($producto->getStock(), ENT_QUOTES, 'UTF-8') ?>
                                        </td>
                                        <td>
                                            <?= number_format($producto->getPrecioPVP(), 2, ',', '.') ?> €
                                        </td>
                                        <td>
                                            <?= number_format($producto->getPrecioPVP() * $producto->getStock(), 2, ',', '.') ?>
                                            €
                                        </td>
                                        <td>
                                            <form method="POST" action="eliminarUnoCarrito" style="display:inline;"
                                                onsubmit="return customConfirm('¿Estás seguro de que deseas eliminar este producto del carrito?');">
                                                <input type="hidden" name="idProducto"
                                                    value="<?= $producto->getIdProducto() ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <!-- Modal de confirmación y pago -->
                        <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog"
                            aria-labelledby="paymentModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="paymentModalLabel">Confirmación del Pedido</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <h4>Elija un método de pago</h4>

                                        <!-- Mensajes de error en el formulario -->
                                        <?php if (isset($errors['metodo_pago'])): ?>
                                            <div class="alert alert-danger">
                                                <?php echo $errors['metodo_pago']; ?>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Resumen del carrito -->
                                        <h5>Resumen de tu pedido:</h5>
                                        <ul>
                                            <?php
                                            $totalPedido = 0; // Variable para almacenar el total del precio
                                            $totalCantidad = 0; // Variable para almacenar la cantidad total de productos
                                            ?>
                                            <?php foreach ($productos as $producto): ?>
                                                <li>
                                                    <?= htmlspecialchars($producto->getNombre(), ENT_QUOTES, 'UTF-8') ?> x
                                                    <?= $producto->getStock() ?> -
                                                    <?= number_format($producto->getPrecioPVP() * $producto->getStock(), 2, ',', '.') ?>
                                                    €
                                                </li>
                                                <?php
                                                // Acumulamos el total de cada producto en el carrito
                                                $totalPedido += $producto->getPrecioPVP() * $producto->getStock();
                                                // Acumulamos la cantidad total de productos
                                                $totalCantidad += $producto->getStock();
                                                ?>
                                            <?php endforeach; ?>
                                        </ul>

                                        <!-- Mostrar la cantidad total de productos -->
                                        <h4>Cantidad total de productos:
                                            <?= $totalCantidad ?> productos
                                        </h4>

                                        <!-- Mostrar el total del pedido -->
                                        <h4>Total del pedido:
                                            <?= number_format($totalPedido, 2, ',', '.') ?> €
                                        </h4>



                                        <hr>

                                        <form action="confirmarPedido" method="POST" id="paymentForm">
                                            <div class="form-group_pago">
                                                <label for="metodo_pago">Método de Pago:</label>
                                                <select name="metodo_pago" id="metodo_pago" class="form-control" required>
                                                    <option value="Tarjeta de Crédito">Tarjeta de Crédito</option>
                                                    <option value="PayPal">PayPal</option>
                                                    <option value="Transferencia Bancaria">Transferencia Bancaria</option>
                                                </select>
                                            </div>

                                            <!-- Dirección de facturación -->
                                            <div class="form-group_pago">
                                                <label for="direccion_facturacion">Dirección de Facturación:</label>
                                                <input type="text" name="direccion_facturacion" id="direccion_facturacion"
                                                    class="form-control" required>
                                            </div>

                                            <!-- Detalles del pago -->
                                            <div class="form-group_pago">
                                                <label for="detalles_pago">Detalles del Pago:</label>
                                                <textarea name="detalles_pago" id="detalles_pago"
                                                    class="form-control"></textarea>
                                            </div>

                                            <input type="hidden" name="id_pedido" value="<?= $idPedido ?>">
                                            <input type="hidden" name="total" value="<?= $total ?>">

                                            <button type="submit" class="btn btn-success btn-lg">Confirmar Pedido</button>
                                        </form>

                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>





<script>
    // Escuchar el evento submit del formulario
    document.getElementById('paymentForm').addEventListener('submit', function (event) {
        event.preventDefault(); // Prevenir el envío del formulario

        // Mostrar el modal de confirmación con SweetAlert2
        Swal.fire({
            title: 'Confirmación',
            text: '¿Estás seguro de que deseas confirmar el pedido?',
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
            if (result.isConfirmed) {
                // Si el usuario confirma, enviar el formulario
                this.submit();
            }
        });
    });
</script>



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
        form.onsubmit = async function (event) {
            event.preventDefault(); // Prevenir el envío del formulario
            const confirmed = await customConfirm(this.getAttribute('onsubmit').replace('return customConfirm(\'', '').replace('\');', ''));
            if (confirmed) this.submit();
        };
    });
</script>


<?php include '../app/vistas/includes/footer.php'; ?>