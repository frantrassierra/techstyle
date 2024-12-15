<?php
// app/controllers/CarritoControlador.php
require_once __DIR__ . '/../modelos/Producto.php';
require_once __DIR__ . '/../modelos/Categoria.php';
require_once __DIR__ . '/../modelos/RegistroPedido.php';
require_once __DIR__ . '/../modelos/Talla.php';
require_once __DIR__ . '/../modelos/Pedido.php';
require_once __DIR__ . '/../modelos/Direccion.php';
require_once __DIR__ . '/../modelos/Pagos.php';

require_once __DIR__ . '/../../app/middleware/autenticacion.php';

class CarritoControlador {
    private $productoModel;
    private $categoriaModel;
    private $pedidoModel;
    private $tallaModel;
    private $registroPedidoModel;
    private $direccionModel;

    private $pagosModel;  // Añadir la propiedad

    public function __construct($pdo) {
        $this->productoModel = new Producto($pdo);
        $this->categoriaModel = new Categoria($pdo);
        $this->pedidoModel = new Pedido($pdo);
        $this->tallaModel = new Talla($pdo);
        $this->registroPedidoModel = new RegistroPedido($pdo);
        $this->direccionModel = new Direccion($pdo);
        $this->pagosModel = new Pagos($pdo);  // Inicializa el modelo de pagos


        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Método para agregar un producto al carrito
    public function agregarProductoAlCarrito() {
        header('Content-Type: application/json');
    
        try {
            // Verificar si los datos fueron enviados por POST
            if (isset($_POST['id_producto'], $_POST['id_talla'], $_POST['cantidad'], $_POST['precio_unitario'])) {
                $idProducto = intval($_POST['id_producto']);
                $idTalla = intval($_POST['id_talla']);
                $cantidad = intval($_POST['cantidad']);
                $precioUnitario = floatval($_POST['precio_unitario']);
    
                // Verificar autenticación del usuario
                if (!isset($_SESSION['usuario_id'])) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Debe iniciar sesión para agregar productos al carrito.',
                    ]);
                    return;
                }
                
    
                $idUsuario = $_SESSION['usuario_id'];
    
                // Validar los valores
                if ($cantidad <= 0 || $precioUnitario <= 0) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Cantidad o precio no válidos.',
                    ]);
                    return;
                }
    
                // Llamar al modelo para agregar el producto al carrito
                $respuesta = $this->productoModel->agregarProducto($idProducto, $idTalla, $cantidad, $precioUnitario, $idUsuario);
    
                // Responder según el resultado del modelo
                if ($respuesta['success']) {
                    echo json_encode([
                        'success' => true,
                        'message' => $respuesta['message'],
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => $respuesta['message'],
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Faltan parámetros necesarios.',
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error en el servidor: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'),
            ]);
        }
    }
    
    private function redirectToPreviousPage() {
        $prevPage = $_SERVER['HTTP_REFERER'] ?? '/defaultPageURL'; // URL por defecto en caso de no haber referencia
        header("Location: $prevPage");
        exit;
    }
    

    // Método para ver el contenido del carrito
    public function verCarrito($idUsuario) {
        try {
            $productos = $this->productoModel->obtenerProductosCarrito($idUsuario);
            require_once __DIR__ . '/../vistas/productos/carrito.php'; // Vista del carrito
        } catch (Exception $e) {
            echo 'Error al cargar el carrito: ' . $e->getMessage();
        }
    }

    // Método para vaciar el carrito
    public function vaciarCarrito() {
        try {
            if (!isset($_SESSION['usuario_id'])) {
                $_SESSION['error_message'] = 'Usuario no autenticado';
                header('Location: carrito');  // Redirige al carrito si no está autenticado
                exit();
            }
    
            $idUsuario = $_SESSION['usuario_id'];
            $respuesta = $this->productoModel->vaciarCarrito($idUsuario);
    
            // Mensaje de éxito
            $_SESSION['success_message'] = 'Carrito vacío con éxito.';
            
            // Redirigir al carrito
            header('Location: carrito');
            exit();
    
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Error al vaciar el carrito: ' . $e->getMessage();
            header('Location: carrito');  // Redirigir al carrito si hay error
            exit();
        }
    }
    

    // Método para eliminar un producto del carrito
    public function eliminarProductoDelCarrito() {
        try {
            if (isset($_POST['idProducto'])) {
                $idProducto = $_POST['idProducto'];

                if (!isset($_SESSION['usuario_id'])) {
                    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
                    return;
                }

                $idUsuario = $_SESSION['usuario_id'];
                $respuesta = $this->productoModel->eliminarProductoDelCarrito($idUsuario, $idProducto);

                if ($respuesta['success']) {
                    header('Location: carrito');
                    exit;
                } else {
                    echo json_encode(['success' => false, 'message' => $respuesta['message']]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'No se recibió el idProducto']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar el producto: ' . $e->getMessage()]);
        }
    }


/*      public function confirmarPedido($idUsuario) {
        try {
            // Obtener los productos del carrito
            $productosCarrito = $this->registroPedidoModel->obtenerProductosCarrito($idUsuario);
    
            if (empty($productosCarrito)) {
                header('Location: carrito');
                exit;
            }
    
            // Calcular el total del pedido
            $total = $this->registroPedidoModel->calcularTotal($productosCarrito);
    
            // Obtener la dirección principal del usuario
            $direccion = $this->direccionModel->obtenerDireccionPrincipal($idUsuario);
    
            if (!$direccion) {
                header('Location: direcciones');
                exit;
            }
    
            // Registrar el pedido en la base de datos
            $idPedido = $this->pedidoModel->registrarPedido($idUsuario, $direccion['id_direccion'], $total);
    
            // Actualizar los productos del carrito para asociarlos con el pedido
            $this->registroPedidoModel->actualizarCarrito($idUsuario, $idPedido);
            echo "bien";
            // Redirigir a la página de confirmación con el ID del pedido
            header('Location: confirmacionPedido?id=' . $idPedido);
            exit; // Asegúrate de hacer un exit después de la redirección
    
        } catch (Exception $e) {
            echo "Error al confirmar el pedido: " . $e->getMessage();
        }
    } */
    public function confirmarPedido($idUsuario) {
        try {
            // Obtener los productos del carrito
            $productosCarrito = $this->registroPedidoModel->obtenerProductosCarrito($idUsuario);
    
            if (empty($productosCarrito)) {
                $_SESSION['error_message'] = 'Tu carrito está vacío. Añade productos antes de continuar.';
                header('Location: carrito');
                exit;
            }
    
            // Calcular el total del pedido
            $total = $this->registroPedidoModel->calcularTotal($productosCarrito);
    
            // Obtener la dirección principal del usuario
            $direccion = $this->direccionModel->obtenerDireccionPrincipal($idUsuario);
    
            if (!$direccion) {
                $_SESSION['error_message'] = 'No tienes una dirección principal registrada. Registra una dirección para continuar.';
                header('Location: carrito');
                exit;
            }
    
            // Registrar el pedido en la base de datos
            $idPedido = $this->pedidoModel->registrarPedido($idUsuario, $direccion['id_direccion'], $total);
    
           
            // Verificar si se ha recibido un pago (por ejemplo, con el formulario de pago)
            if (isset($_POST['metodo_pago']) && isset($_POST['total'])) {
                $metodoPago = $_POST['metodo_pago'];
    
                // Registrar el pago
                if (!$this->pagosModel->registrarPago($idPedido, $_POST['total'], $metodoPago, $_POST['direccion_facturacion'], $_POST['detalles_pago'])) {
                    throw new Exception("Error al procesar el pago.");
                }
            }
             // Actualizar los productos del carrito para asociarlos con el pedido
             $this->registroPedidoModel->actualizarCarrito($idUsuario, $idPedido);
    
    
            // Redirigir a la página de confirmación con el ID del pedido
            header('Location: confirmacionPedido?id=' . $idPedido);
            exit;
    
        } catch (Exception $e) {
            // Manejo de excepciones
            $_SESSION['error_message'] = "Error al confirmar el pedido: " . $e->getMessage();
            header('Location: carrito');
            exit;
        }
    }
    
    
    
    public function verConfirmacionPedido($idPedido) {
        try {
            // Obtener los detalles del pedido usando el ID del pedido
            $pedido = $this->pedidoModel->obtenerDetallePedido($idPedido);
    
            if (!$pedido) {
                // Si no se encuentra el pedido, redirigir al usuario a la lista de pedidos
                header('Location: misPedidos');
                exit;
            }
    
            // Incluir la vista de confirmación del pedido
            require_once __DIR__ . '/../vistas/pedidos/confirmacionPedido.php'; // Vista del pedido confirmado
    
        } catch (Exception $e) {
            echo "Error al cargar la confirmación del pedido: " . $e->getMessage();
        }
    }
    
    
    public function verMisPedidos($idUsuario) {
        try {
            $pedidos = $this->pedidoModel->obtenerPedidosUsuario($idUsuario);
            require_once __DIR__ . '/../vistas/pedidos/misPedidos.php'; // Vista del pedido confirmado


        } catch (Exception $e) {
            echo "Error al cargar los pedidos: " . $e->getMessage();
        }
    }

    public function verDetallePedido($idPedido) {
        try {
            // Obtener el detalle del pedido desde la base de datos
            $pedido = $this->pedidoModel->obtenerDetallePedido($idPedido);
    
            if (!$pedido) {
                // Si no se encuentra el pedido, redirigir a la lista de pedidos
                header('Location: misPedidos');
                exit;
            }
    
            // Obtener los productos del pedido
            $productos = $this->pedidoModel->obtenerProductosPorPedido($idPedido);
    
            // Incluir la vista de detalles del pedido
            require_once __DIR__ . '/../vistas/pedidos/detallePedido.php'; // Vista para mostrar los detalles del pedido
    
        } catch (Exception $e) {
            echo "Error al cargar los detalles del pedido: " . $e->getMessage();
        }
    }
    
    
}
?>
