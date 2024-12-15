<?php
require_once __DIR__ . '/../modelos/Producto.php';
require_once __DIR__ . '/../modelos/Categoria.php';
require_once __DIR__ . '/../modelos/Talla.php';
require_once __DIR__ . '/../modelos/Resena.php';
require_once __DIR__ . '../../middleware/autenticacion.php';


class ProductoControlador
{
    private $productoModel;
    private $categoriaModel;
    private $tallaModel;
    private $resenaModel; // Agregamos la propiedad de ResenaModel
    public function __construct($pdo)
    {
        $this->productoModel = new Producto($pdo);
        $this->categoriaModel = new Categoria($pdo);
        $this->tallaModel = new Talla($pdo);
        $this->resenaModel = new Resena($pdo); // Inicializamos el modelo de reseñas
    }


    public function listarProductosMas()
    {
        try {
            // Obtener los 3 productos con mayor calificación (limitados a 3)
            $productos = $this->productoModel->obtenerProductosMasValorados(3); // Solo los 3 más valorados

            // Obtener las categorías para el filtro de categoría
            $categorias = $this->categoriaModel->obtenerCategorias();

            // Obtener la calificación promedio de cada producto
            foreach ($productos as &$producto) {
                $producto->calificacion_promedio = $this->productoModel->obtenerCalificacionPromedio($producto->getIdProducto());
            }
            unset($producto); // Liberar la referencia para evitar efectos secundarios

            // Pasar los productos a la vista de inicio
            require_once __DIR__ . '/../vistas/inicio.php';
        } catch (Exception $e) {
            echo '<p>Error al cargar los productos más valorados: ' . $e->getMessage() . '</p>';
        }
    }


    public function listarProductos()
    {
        try {
            // Obtener el filtro de categoría (si existe)
            $categoria_id = isset($_GET['categoria_id']) ? (int) $_GET['categoria_id'] : null;

            // Obtener el filtro de popularidad (si existe)
            $popularidad = isset($_GET['popularidad']) ? $_GET['popularidad'] : null;

            // Obtener productos filtrados según la categoría y la popularidad
            $productos = $this->productoModel->obtenerProductos($categoria_id, $popularidad);

            // Obtener las categorías para el filtro de categoría
            $categorias = $this->categoriaModel->obtenerCategorias();

            // Obtener la calificación promedio de cada producto
            foreach ($productos as &$producto) {
                $producto->calificacion_promedio = $this->productoModel->obtenerCalificacionPromedio($producto->getIdProducto());
            }
            unset($producto); // Libera la referencia para evitar efectos secundarios.


            // Obtener el HTML de los productos
            $productosHtml = $this->productoModel->obtenerProductosHtml($productos);



            // Si se recibe una solicitud AJAX, devolver solo los productos
            if (isset($_GET['ajax']) && $_GET['ajax'] == 'true') {
                echo $productosHtml;
            } else {
                // Si no es una solicitud AJAX, cargar la vista completa

                require_once __DIR__ . '/../vistas/productos/catalogo.php';
            }

        } catch (Exception $e) {
            echo '<p>Error al cargar el catálogo: ' . $e->getMessage() . '</p>';
        }
    }


    public function detalleProducto()
    {
        try {
            // Validar si se pasó un ID de producto válido
            if (!isset($_GET['id_producto']) || !is_numeric($_GET['id_producto'])) {
                throw new Exception('ID de producto no válido.');
            }

            $idProducto = (int) $_GET['id_producto'];

            // Obtener los datos del producto
            $producto = $this->productoModel->obtenerProductoPorId($idProducto);
            if (!$producto) {
                throw new Exception('Producto no encontrado.');
            }

            // Obtener las reseñas del producto
            $resenas = $this->resenaModel->obtenerResenasPorProducto($idProducto);

            // Calificación promedio del producto
            $calificacionPromedio = $this->productoModel->obtenerCalificacionPromedio($idProducto);

            // Obtener las tallas disponibles para el producto
            $tallas = $this->tallaModel->obtenerTallas();

            // Identificar al usuario logueado
            $idUsuarioActual = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : null;

            // Variables a pasar a la vista
            $datos = [
                'producto' => $producto,
                'resenas' => $resenas,
                'calificacionPromedio' => $calificacionPromedio,
                'tallas' => $tallas,
                'idUsuarioActual' => $idUsuarioActual,
            ];

            // Cargar la vista de detalles con los datos
            require_once __DIR__ . '/../vistas/productos/detalle.php';

        } catch (Exception $e) {
            // Mostrar error en pantalla
            echo '<p style="color: red;">Error al cargar los detalles del producto: ' . $e->getMessage() . '</p>';
            // Registrar el error en los logs
            error_log("Error en detalleProducto: " . $e->getMessage());
        }
    }



    private function redirectToPreviousPage()
    {
        $prevPage = $_SERVER['HTTP_REFERER'] ?? '/defaultPageURL'; // URL por defecto en caso de no haber referencia
        header("Location: $prevPage");
        exit;
    }
    /*     public function agregarResenaControl() {
            try {
                if (isset($_POST['id_producto'], $_POST['comentario'], $_POST['calificacion'])) {
                    $idProducto = intval($_POST['id_producto']);
                    $comentario = $_POST['comentario'];
                    $calificacion = intval($_POST['calificacion']);
        
                    if (!isset($_SESSION['usuario_id'])) {
                        $_SESSION['error_message'] = 'Debes iniciar sesión para agregar una reseña.';
                        $this->redirectToPreviousPage();
                        return;
                    }
        
                    $idUsuario = $_SESSION['usuario_id'];
        
                    if ($calificacion < 1 || $calificacion > 5) {
                        $_SESSION['error_message'] = 'La calificación debe ser un valor entre 1 y 5.';
                        $this->redirectToPreviousPage();
                        return;
                    }
        
                    if ($this->resenaModel->verificarResenaExistente($idUsuario, $idProducto)) {
                        $_SESSION['error_message'] = "Ya has dejado una reseña para este producto.";
                        $this->redirectToPreviousPage();
                        return;
                    }
        
                    // Llamar al modelo para agregar la reseña
                    $resenaAgregada = $this->resenaModel->agregarResena($idUsuario, $idProducto, $comentario, $calificacion);
                    if ($resenaAgregada) {
                        $nuevoPromedio = $this->productoModel->calcularYActualizarPromedioCalificacion($idProducto);
                        $_SESSION['success_message'] = "Tu reseña ha sido agregada con éxito. Promedio actualizado: $nuevoPromedio estrellas.";
                    } else {
                        $_SESSION['error_message'] = "Error al agregar la reseña en la base de datos.";
                        $_SESSION['error_details'] = "No se pudo insertar la reseña en la base de datos. Por favor, revisa los logs.";
                    }
        
                    $this->redirectToPreviousPage();
                } else {
                    $_SESSION['error_message'] = "Faltan parámetros necesarios para agregar la reseña.";
                    $this->redirectToPreviousPage();
                }
            } catch (Exception $e) {
                $_SESSION['error_message'] = 'Error en el servidor: ' . $e->getMessage();
                error_log("Error en agregarResenaControl: " . $e->getMessage()); // Registrar en logs
                error_log("Traza completa: " . $e->getTraceAsString());         // Más detalles del error
                $this->redirectToPreviousPage();
            }
        }
         */

    public function agregarResenaControl()
    {
        try {
            // Verificar que la sesión esté activa
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // Validar que los parámetros necesarios están presentes
            if (!isset($_POST['id_producto'], $_POST['comentario'], $_POST['calificacion'])) {
                $_SESSION['error_message'] = "Faltan parámetros necesarios para agregar la reseña.";
                $this->redirectToPreviousPage();
                return;
            }

            $idProducto = intval($_POST['id_producto']);
            $comentario = htmlspecialchars(trim($_POST['comentario']), ENT_QUOTES, 'UTF-8');
            $calificacion = intval($_POST['calificacion']);

            // Validar que el usuario esté autenticado
            if (!isset($_SESSION['usuario_id'])) {
                $_SESSION['error_message'] = 'Debes iniciar sesión para agregar una reseña.';
                $this->redirectToPreviousPage();
                return;
            }

            $idUsuario = $_SESSION['usuario_id'];

            // Validar que la calificación esté en el rango permitido
            if ($calificacion < 1 || $calificacion > 5) {
                $_SESSION['error_message'] = 'La calificación debe ser un valor entre 1 y 5.';
                $this->redirectToPreviousPage();
                return;
            }

            // Verificar si ya existe una reseña para este usuario y producto
            if ($this->resenaModel->verificarResenaExistente($idUsuario, $idProducto)) {
                $_SESSION['error_message'] = "Ya has dejado una reseña para este producto.";
                $this->redirectToPreviousPage();
                return;
            }

            // Agregar reseña en la base de datos
            try {
                $resenaAgregada = $this->resenaModel->agregarResena($idUsuario, $idProducto, $comentario, $calificacion);
                if ($resenaAgregada) {
                    // Calcular y actualizar el promedio de calificaciones
                    try {
                        $nuevoPromedio = $this->productoModel->calcularYActualizarPromedioCalificacion($idProducto);
                        $_SESSION['success_message'] = "Tu reseña ha sido agregada con éxito. Promedio actualizado: $nuevoPromedio estrellas.";
                    } catch (Exception $e) {
                        $_SESSION['success_message'] = "Tu reseña ha sido agregada con éxito, pero ocurrió un problema al actualizar el promedio.";
                        error_log("Error al actualizar el promedio de calificación: " . $e->getMessage());
                    }
                } else {
                    $_SESSION['error_message'] = "Error al agregar la reseña en la base de datos.";
                    error_log("No se pudo insertar la reseña en la base de datos. Usuario: $idUsuario, Producto: $idProducto.");
                }
            } catch (Exception $e) {
                $_SESSION['error_message'] = "Error al interactuar con la base de datos: " . $e->getMessage();
                error_log("Error en modelo al agregar reseña: " . $e->getMessage());
            }

            $this->redirectToPreviousPage();
            return;
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Error en el servidor: ' . $e->getMessage();
            error_log("Error en agregarResenaControl: " . $e->getMessage());
            error_log("Traza completa: " . $e->getTraceAsString());
            $this->redirectToPreviousPage();
            return;
        }
    }

    public function eliminarResenaControl()
    {
        // Verificar si la solicitud es POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Verificar que el usuario esté autenticado y los datos sean correctos
            if (isset($_SESSION['usuario_id'], $_POST['id_resena'], $_POST['id_producto'])) {

                $idUsuario = $_SESSION['usuario_id'];
                $idResena = $_POST['id_resena'];
                $idProducto = $_POST['id_producto'];

                // Comprobar si la reseña pertenece al usuario
                if ($this->resenaModel->verificarResenaPorUsuario($idResena, $idUsuario)) {

                    // Eliminar la reseña
                    echo "ILOOOOOOOOOOOOOOOOOOOOOOOOOOOO";
                    $this->resenaModel->eliminarResena($idResena);

                    // Verificar si la reseña fue eliminada correctamente


                }

                // Redirigir al detalle del producto
                header("Location: detalleProducto?id_producto=" . $idProducto);

                exit();
            }

        } else {
            // Si el método de solicitud no es POST
            $_SESSION['error_message'] = "Método de solicitud no permitido.";
            header("Location: detalleProducto?id_producto=" . $_POST['id_producto']);
            exit();
        }
    }


    /* 
      public function detalleProducto() {
          try {
              // Validar si se pasó un ID de producto válido
              if (!isset($_GET['id_producto']) || !is_numeric($_GET['id_producto'])) {
                  throw new Exception('ID de producto no válido.');
              }
      
              $idProducto = (int)$_GET['id_producto'];
      
              // Obtener el producto por su ID
              $producto = $this->productoModel->obtenerProductoPorId($idProducto);
      
              if (!$producto) {
                  throw new Exception('Producto no encontrado.');
              }
      
              // Obtener las tallas disponibles para el producto
              $tallas = $this->tallaModel->obtenerTallas(); // Esto devuelve todas las tallas disponibles
      
              // Cargar la vista de detalles con los datos del producto y las tallas
              require_once __DIR__ . '/../vistas/productos/detalle.php';
      
          } catch (Exception $e) {
              // Manejar errores y mostrar mensaje en la vista o redirigir a una página de error
              echo '<p style="color: red;">Error al cargar los detalles del producto: ' . $e->getMessage() . '</p>';
          }
      }
      
       */




}
