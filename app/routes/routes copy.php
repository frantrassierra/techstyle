<?php
require_once __DIR__ . '/../../app/middleware/autenticacion.php';
// routes.php
function redirectToPreviousPage()
{
    $prevPage = $_SERVER['HTTP_REFERER'] ?? '/defaultPageURL'; // URL por defecto en caso de no haber referencia
    header("Location: $prevPage");
    exit;
}

function route($uri, $usuarioController, $productoController, $adminController, $carritoController, $contactoController)
{

    switch ($uri) {
        case 'inicio':
            $productoController->listarProductosMas();

            // Si la URI es 'inicio', mostrar la página de inicio
            require_once BASE_PATH . '/app/vistas/inicio.php';
            break;
        case 'sobrenosotros':
            // Si la URI es 'inicio', mostrar la página de inicio
            require_once BASE_PATH . '/app/vistas/sobre-nosotros.php';
            break;
        case 'contacta':
            // Si la URI es 'inicio', mostrar la página de inicio
            require_once BASE_PATH . '/app/vistas/contacta.php';
            break;

        case 'procesarFormularioContacta':
            // Procesar el formulario de contacto
            $contactoController->procesarFormularioContacto();
            break;
        // Rutas para usuarios
        case 'registro':
            // Mostrar el formulario de registro
            require_once BASE_PATH . '/app/vistas/usuarios/registro.php';
            break;

        case 'registrar':
            // Procesar el formulario de registro
            $usuarioController->procesarRegistro();
            break;

        case 'login':
            // Mostrar el formulario de login
            require_once BASE_PATH . '/app/vistas/usuarios/login.php';
            break;
        case 'adminPanel':
            require_once BASE_PATH . '/app/vistas/admin/panel.php';

            verificarAutenticacion();  // Verificar si el usuario está autenticado
            verificarRol();  // Verificar si el usuario tiene el rol de admin

            break;


        case 'registroProducto':
            verificarRol();  // Verificar si el usuario tiene el rol de admin
            $adminController->mostrarFormularioRegistroProductos();

            break;
        case 'procesarProducto':
            $adminController->registrarProducto();
            header('Location: login');

            break;
        case 'listarProductos':
            verificarRol();  // Verificar si el usuario tiene el rol de admin

            $adminController->listarProductos();

            break;
        case 'editarProducto':
            verificarRol();  // Verificar si el usuario tiene el rol de admin

            if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                try {
                    // Obtener el producto y las categorías
                    $producto = $adminController->editarProducto($_GET['id']);
                    require_once __DIR__ . '/../vistas/admin/editarProductos.php'; // Incluye la vista para editar el producto
                } catch (Exception $e) {
                    $_SESSION['error'] = "Error al obtener el producto: " . $e->getMessage();
                    header('Location: listarProductos');
                    exit();
                }
            } else {
                $_SESSION['error'] = "ID de producto no especificado o inválido.";
                header('Location: listarProductos');
                exit();
            }
            break;


        case 'procesarActualizacionProducto':
            if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                // Llamar al controlador para manejar la actualización
                $adminController->actualizarProductoCrudo($_GET['id']);
            } else {
                $_SESSION['error'] = "ID de producto no especificado o inválido.";
                header('Location: listarProductos');
                exit();
            }
            break;



        case 'eliminarProducto':
            if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                $adminController->eliminarProducto($_GET['id']);
            } else {
                $_SESSION['error'] = "ID de producto no especificado o inválido.";
                header('Location: listarProductos');
                exit();
            }
            break;


        case 'listarTallas':
            verificarRol();  // Verificar si el usuario tiene el rol de admin

            $adminController->listarTallas();
            break;
        case 'mostrarFormTallas':
            verificarRol();  // Verificar si el usuario tiene el rol de admin

            $adminController->mostrarFormularioRegistroTalla();
            break;

        case 'procesarRegistroTalla':
            $adminController->registrarTalla();
            header('Location: login');

            break;

        case 'editarTalla':
            verificarRol();  // Verificar si el usuario tiene el rol de admin

            if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                $adminController->editarTalla($_GET['id']);
            } else {
                $_SESSION['error'] = "ID de talla no especificado o inválido.";
                header('Location: listarTallas');
            }
            break;

        case 'procesarActualizacionTalla':
            if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                $adminController->actualizarTalla($_GET['id']);
            } else {
                $_SESSION['error'] = "ID de talla no especificado o inválido.";
                header('Location: listarTallas');
            }
            break;

        case 'eliminarTalla':
            if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                $adminController->eliminarTalla($_GET['id']);
            } else {
                $_SESSION['error'] = "ID de talla no especificado o inválido.";
                header('Location: listarTallas');
            }
            break;









        case 'listarCategorias':
            verificarRol();  // Verificar si el usuario tiene el rol de admin

            $adminController->listarCategorias();
            break;
        case 'mostrarFormCategorias':
            verificarRol();  // Verificar si el usuario tiene el rol de admin

            $adminController->mostrarFormularioRegistroCategoria();
            break;
        case 'procesarRegistroCategoria':
            $adminController->registrarCategoria();
            break;
        case 'editarCategoria':
            verificarRol();  // Verificar si el usuario tiene el rol de admin

            if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                $adminController->editarCategoria($_GET['id']);
            } else {
                $_SESSION['error'] = "ID de categoría no especificado o inválido.";
                header('Location: listarCategorias');
            }
            break;
        case 'procesarActualizacionCategoria':
            if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                $adminController->actualizarCategoria($_GET['id']);
            } else {
                $_SESSION['error'] = "ID de categoría no especificado o inválido.";
                header('Location: listarCategorias');
            }
            break;
        case 'eliminarCategoria':
            if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                $adminController->eliminarCategoria($_GET['id']);
            } else {
                $_SESSION['error'] = "ID de categoría no especificado o inválido.";
                header('Location: listarCategorias');
            }
            break;









        case 'listarUsuarios':
            verificarRol();  // Verificar si el usuario tiene el rol de admin

            $adminController->listarUsuarios();
            break;
        case 'mostrarFormUsuarios':
            verificarRol();  // Verificar si el usuario tiene el rol de admin

            $adminController->mostrarFormularioRegistroUsuario();
            break;
        case 'procesarRegistroUsuario':
            $adminController->registrarUsuario();
            break;
        case 'editarUsuario':
            verificarRol();  // Verificar si el usuario tiene el rol de admin

            if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                $adminController->editarUsuario($_GET['id']);
            } else {
                $_SESSION['error'] = "ID de usuario no especificado o inválido.";
                header('Location: listarUsuarios');
            }
            break;
        case 'procesarActualizacionUsuario':
            if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                $adminController->actualizarUsuario($_GET['id']);
            } else {
                $_SESSION['error'] = "ID de usuario no especificado o inválido.";
                header('Location: listarUsuarios');
            }
            break;
        case 'eliminarUsuario':
            if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                $adminController->eliminarUsuario($_GET['id']);
            } else {
                $_SESSION['error'] = "ID de usuario no especificado o inválido.";
                header('Location: listarUsuarios');
            }
            break;


        case 'procesarLogin':
            // Procesar el login
            $usuarioController->procesarLogin();
            break;

        case 'logout':
            // Cerrar sesión
            $usuarioController->logout();
            break;

        case 'perfil':
            // Ver perfil de usuario (aquí pasamos el ID del usuario)
            if (isset($_SESSION['usuario_id'])) {
                $usuarioController->verPerfil($_SESSION['usuario_id']);
            } else {
                header('Location: login');
            }
            break;

        case 'editarPerfil':
            if (isset($_SESSION['usuario_id'])) {
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    // Si se está enviando el formulario, procesar la edición
                    $usuarioController->procesarEditarPerfil($_SESSION['usuario_id']);
                } else {
                    // Si no es un POST, mostrar el perfil para editar
                    $usuarioController->mostrarPerfil($_SESSION['usuario_id']);
                }
            } else {
                header('Location: login');
            }
            break;
        case 'direcciones':
            if (isset($_SESSION['usuario_id'])) {
                $usuarioController->gestionarDirecciones($_SESSION['usuario_id']);
            } else {
                header('Location: login');
            }
            break;

        case 'mostrarFormularioAddDireccion':
            if (isset($_SESSION['usuario_id'])) {
                $usuarioController->mostrarFormularioAñadirDireccion($_SESSION['usuario_id']);
            } else {
                header('Location: login');
            }
            break;
        case 'procesarAddDirecciones':
            if (isset($_SESSION['usuario_id'])) {
                $usuarioController->procesarAddDireccion($_SESSION['usuario_id']);
            } else {
                header('Location: login');
            }
            break;
        // En tu archivo de rutas (donde gestionas las acciones)
        case 'eliminarDireccion':
            if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                $usuarioController->eliminarDireccion($_GET['id']);
            } else {
                header('Location: direcciones');
            }
            break;





        // Ruta para mostrar productos (AJAX)


        case 'mostrarProductos':
            // Verifica si se recibe un parámetro AJAX

            $productoController->listarProductos();

            break;


        case 'detalleProducto':
            if (isset($_GET['id_producto']) && is_numeric($_GET['id_producto'])) {
                $productoController->detalleProducto(); // Llamada al controlador con ID
            } else {
                echo "Error: ID de producto no válido.";
            }
            break;





        case 'agregarCarrito':
            if (isset($_POST['id_producto'], $_POST['id_talla'], $_POST['cantidad'], $_POST['precio_unitario'])) {
                $carritoController->agregarProductoAlCarrito();
            } else {
                echo "Error: Faltan parámetros para agregar al carrito.";
            }

            break;

        case 'carrito':
            // Ver carrito de usuario (pasamos el ID del usuario desde la sesión)
            if (isset($_SESSION['usuario_id'])) {
                $carritoController->verCarrito($_SESSION['usuario_id']);
            } else {
                // Guardar un mensaje de error en la sesión
                $_SESSION['error_message'] = "Debes iniciar sesión para acceder al carrito.";

                // Redirigir al login
                header('Location: login');
                exit();
            }

            break;

        // index.php

        // Ruta para vaciar el carrito
        // index.php

        // Ruta para vaciar el carrito
        case 'vaciarCarrito':
            if (isset($_SESSION['usuario_id'])) {
                // Llamamos al método para vaciar el carrito
                $carritoController->vaciarCarrito();
            } else {
                // Redirigir al carrito si el usuario no está autenticado
                $_SESSION['error_message'] = "Debes iniciar sesión para vaciar el carrito.";
                header('Location: carrito');
                exit();
            }
            break;


        // Ruta para eliminar un producto del carrito
        // Archivo de rutas (ej. index.php)
        // Archivo de rutas (ej. index.php)

        // Archivo de rutas (ej. index.php)

        case 'eliminarUnoCarrito':
            // Obtener el ID del producto desde la solicitud POST
            $idProducto = $_POST['idProducto'];

            // Llamar al método del controlador para eliminar el producto del carrito
            $carritoController->eliminarProductoDelCarrito();

            // Redirigir a la página del carrito para actualizar la vista
            header('Location: carrito');
            break;

        case 'confirmarPedido':
            if (isset($_SESSION['usuario_id'])) {
                $carritoController->confirmarPedido($_SESSION['usuario_id']);
            } else {
                header('Location: login');
            }
            break;


        case 'confirmacionPedido':
            if (isset($_GET['id'])) {
                $carritoController->verConfirmacionPedido($_GET['id']);
            } else {
                echo "Error: ID de pedido no válido.";
            }
            break;

        // Rutas para pedidos

        // Ruta para mostrar la confirmación de un pedido específico


        // Ruta para mostrar todos los pedidos del usuario actual
        case 'misPedidos':
            if (isset($_SESSION['usuario_id'])) {
                // Llama al método para mostrar la lista de pedidos del usuario
                $carritoController->verMisPedidos($_SESSION['usuario_id']);
            } else {
                // Si no está autenticado, redirigir al login
                header('Location: login');
            }
            break;

        case 'detallePedido':
            if (isset($_GET['idPedido'])) {
                $carritoController->verDetallePedido($_GET['idPedido']);
            } else {
                echo "Error: ID de pedido no válido.";
            }
            break;


        case 'agregarResena':
            // Asegúrate de que el usuario esté autenticado
            if (!isset($_SESSION['usuario_id'])) {
                $_SESSION['error_message'] = 'Debes iniciar sesión para agregar una reseña.';
                redirectToPreviousPage();
                return;
            }

            if (isset($_POST['id_producto'], $_POST['comentario'], $_POST['calificacion'])) {
                $idProducto = $_POST['id_producto'];
                $comentario = $_POST['comentario'];
                $calificacion = $_POST['calificacion'];

                // Validar que el id_producto sea numérico y calificación esté entre 1 y 5
                if (is_numeric($idProducto) && $calificacion >= 1 && $calificacion <= 5) {
                    // Llamamos al método del controlador para agregar la reseña
                    $productoController->agregarResenaControl();
                } else {
                    $_SESSION['error_message'] = 'El ID del producto o la calificación no es válido.';
                    redirectToPreviousPage();
                }
            } else {
                $_SESSION['error_message'] = 'Faltan parámetros para agregar la reseña.';
                redirectToPreviousPage();
            }
            break;


        case 'eliminarResena':
            echo "MI GENTE";

            // Verificar que el usuario esté autenticado y los parámetros sean válidos
            if (isset($_SESSION['usuario_id']) && isset($_POST['id_resena']) && is_numeric($_POST['id_resena']) && isset($_POST['id_producto']) && is_numeric($_POST['id_producto'])) {
                echo "MI GENTE ESTAMOS DENTRO";
                $productoController->eliminarResenaControl();
            } else {
                echo "Error: No estás autorizado o la reseña no es válida.";
            }
            break;


        // Ruta para agregar una reseña



        case 'denegado':
            require_once BASE_PATH . '/app/vistas/acceso_denegado.php';
            break;


        default:
            require_once BASE_PATH . '/app/vistas/404.php'; // Mostrar página 404
            break;
    }


}




// Enrutamiento
