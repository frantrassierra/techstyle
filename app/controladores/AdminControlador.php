<?php
// app/controllers/AdminController.php
require_once __DIR__ . '/../modelos/Producto.php';
require_once __DIR__ . '/../modelos/Categoria.php';
require_once __DIR__ . '/../modelos/RegistroPedido.php';
require_once __DIR__ . '/../modelos/Talla.php';
require_once __DIR__ . '/../modelos/Pedido.php';
require_once __DIR__ . '/../modelos/Usuario.php';


require_once __DIR__ . '/../../app/middleware/autenticacion.php';


class AdminControlador
{
    private $productoModel;
    private $categoriaModel;
    private $pedidoModel;
    private $tallaModel;
    private $registroPedidoModel;

    private $usuarioModel;

    public function __construct($pdo)
    {
        $this->productoModel = new Producto($pdo);
        $this->categoriaModel = new Categoria($pdo);
        $this->pedidoModel = new Pedido($pdo);
        $this->tallaModel = new Talla($pdo);
        $this->usuarioModel = new Usuario($pdo);

        $this->registroPedidoModel = new RegistroPedido($pdo);
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }




    // ----- Productos CRUD -----



    public function mostrarFormularioRegistroProductos()
    {
        try {
            $categorias = $this->categoriaModel->obtenerCategorias(); // Obtienes el array de categorías
            require_once __DIR__ . '/../vistas/admin/registroProductos.php'; // Incluir la vista para mostrar el formulario
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error al cargar los productos: ' . $e->getMessage();
            echo $_SESSION['error'];
            exit();
        }

    }


    // Registrar un nuevo producto
    public function registrarProducto()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Recoger datos del formulario
                $nombre = trim($_POST['nombre']);
                $descripcion = trim($_POST['descripcion']);
                $descripcion_corta = trim($_POST['descripcion_corta']);
                $precioPVP = trim($_POST['precioPVP']);
                $stock = trim($_POST['stock']);
                $id_categoria = trim($_POST['id_categoria']);

                // Validar campos obligatorios
                if (empty($nombre) || empty($descripcion) || empty($precioPVP) || empty($stock) || empty($id_categoria)) {
                    throw new InvalidArgumentException('Todos los campos son obligatorios.');
                }

                // Validar precio
                if (!is_numeric($precioPVP) || $precioPVP <= 0) {
                    throw new InvalidArgumentException('El precio debe ser un número positivo.');
                }

                // Validar stock
                if (!is_numeric($stock) || $stock < 0) {
                    throw new InvalidArgumentException('El stock debe ser un número no negativo.');
                }

                // Subir la imagen
                $imagen = $this->subirImagen($_FILES['imagen']);

                // Registrar producto en la base de datos
                $this->productoModel->registrarProducto($nombre, $descripcion, $descripcion_corta, $precioPVP, $stock, $id_categoria, $imagen);

                // Mensaje de éxito
                $_SESSION['success'] = 'Producto registrado con éxito. ';
                header('Location: listarProductos'); // Redirige al formulario
                exit();
            } catch (Exception $e) {
                // Manejar errores
                $_SESSION['error'] = $e->getMessage();
                header('Location: listarProductos'); // Redirige al formulario
                exit();
            }
        }
    }


    // Subir imagen
    private function subirImagen($imagen)
    {
        if (empty($imagen["name"])) {
            throw new Exception('No se ha seleccionado ninguna imagen.');
        }

        // Validar el tamaño de la imagen (máximo 5MB)
        if ($imagen["size"] > 5 * 1024 * 1024) {
            throw new Exception('La imagen excede el tamaño máximo permitido (5MB).');
        }

        // Validar el tipo de archivo (solo imágenes jpg, jpeg, png, gif)
        $fileType = strtolower(pathinfo($imagen["name"], PATHINFO_EXTENSION));
        if (!in_array($fileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            throw new Exception('Solo se permiten imágenes en formato JPG, JPEG, PNG o GIF.');
        }

        // Ruta de destino correcta
        $directorio = __DIR__ . '/../../public/productos/';
        ; // Uso de DOCUMENT_ROOT
        if (!is_dir($directorio)) {
            throw new Exception('El directorio de destino no existe.');
        }

        $nombreImagen = time() . "_" . basename($imagen["name"]);
        $rutaDestino = $directorio . $nombreImagen;

        // Mover el archivo subido al directorio de destino
        if (move_uploaded_file($imagen["tmp_name"], $rutaDestino)) {
            return $nombreImagen;
        } else {
            throw new Exception('Error al subir la imagen.');
        }
    }




    public function listarProductos()
    {
        try {
            $productos = $this->productoModel->obtenerProductosListar(); // Obtiene los productos desde el modelo
            require_once __DIR__ . '/../vistas/admin/listarProductos.php'; // Incluye la vista para listar productos
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error al cargar los productos: ' . $e->getMessage();
            echo $_SESSION['error'];
            exit();
        }
    }


    public function editarProducto($id_producto)
    {
        // Verificar si el administrador está logueado
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== "admin") {
            $_SESSION['error'] = "No tienes permisos para realizar esta acción. Por favor, inicia sesión como administrador.";
            header('Location: login');
            exit();
        }

        try {
            // Obtener los detalles del producto y las categorías disponibles
            $producto = $this->productoModel->obtenerProductoPorIdCrud($id_producto);
            $categorias = $this->categoriaModel->obtenerCategorias();

            // Cargar la vista de edición de productos
            require_once __DIR__ . '/../vistas/admin/editarProductos.php';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error al cargar el producto: ' . $e->getMessage();
            header('Location: listarProductos');
            exit();
        }
    }

    public function actualizarProductoCrudo($id_producto)
    {
        // Verificar si el formulario fue enviado con POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Recoger datos del formulario
                $nombre = trim($_POST['nombre']);
                $descripcion = trim($_POST['descripcion']);
                $descripcion_corta = trim($_POST['descripcion_corta']);
                $precioPVP = trim($_POST['precioPVP']);
                $stock = trim($_POST['stock']);
                $id_categoria = trim($_POST['id_categoria']);

                // Validar campos obligatorios
                if (empty($nombre) || empty($descripcion) || empty($precioPVP) || empty($stock) || empty($id_categoria)) {
                    throw new InvalidArgumentException('Todos los campos son obligatorios.');
                }

                // Validar precio
                if (!is_numeric($precioPVP) || $precioPVP <= 0) {
                    throw new InvalidArgumentException('El precio debe ser un número positivo.');
                }

                // Validar stock
                if (!is_numeric($stock) || $stock < 0) {
                    throw new InvalidArgumentException('El stock debe ser un número no negativo.');
                }

                // Subir la imagen si es nueva
                if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
                    $imagen = $this->subirImagen($_FILES['imagen']);  // Llamamos a la misma función que en el registro
                } else {
                    // Si no se sube una nueva imagen, usamos la imagen actual
                    $imagen = $_POST['imagen_actual'];
                }

                // Actualizar producto en la base de datos
                $this->productoModel->actualizarProductoCrud($id_producto, $nombre, $descripcion, $descripcion_corta, $precioPVP, $stock, $id_categoria, $imagen);

                // Mensaje de éxito
                $_SESSION['success'] = 'Producto actualizado con éxito.';
                header('Location: listarProductos'); // Redirige al listado de productos
                exit();
            } catch (Exception $e) {
                // Manejar errores
                $_SESSION['error'] = $e->getMessage();
                header('Location: editarProducto/' . $id_producto); // Redirige de nuevo al formulario
                exit();
            }
        } else {
            // Si no es un POST, mostrar el formulario de edición

            header('Location: editarProducto/' . $id_producto); // Redirige de nuevo al formulario

        }
    }
    public function eliminarProducto($id_producto)
    {
        // Verificar si el administrador está logueado
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== "admin") {
            $_SESSION['error'] = "No tienes permisos para realizar esta acción. Por favor, inicia sesión como administrador.";
            header('Location: login');
            exit();
        }

        try {
            // Intentar eliminar el producto
            $resultado = $this->productoModel->eliminarProducto($id_producto);

            if ($resultado) {
                $_SESSION['success'] = "Producto eliminado con éxito.";
            } else {
                $_SESSION['error'] = "El producto no existe o no se pudo eliminar.";
            }
        } catch (Exception $e) {
            // Manejo de excepciones
            $_SESSION['error'] = "Error al eliminar el producto: " . $e->getMessage();
        }

        // Redirigir a la lista de productos
        header('Location: listarProductos');
        exit();
    }




    // ----- Usuarios CRUD -----


    // Mostrar la lista de usuarios
    public function listarUsuarios()
    {
        $usuarios = $this->usuarioModel->obtenerUsuarios();
        require_once __DIR__ . '/../vistas/admin/listarUsuarios.php';
    }

    // Mostrar el formulario de registro de usuario
    public function mostrarFormularioRegistroUsuario()
    {
        require_once __DIR__ . '/../vistas/admin/registroUsuarios.php';
    }

    // Registrar un nuevo usuario
    public function registrarUsuario()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $nombre = trim($_POST['nombre']);
                $email = trim($_POST['email']);
                $contrasena = trim($_POST['contrasena']);
                $rol = isset($_POST['rol']) ? $_POST['rol'] : 'usuario';

                if (empty($nombre) || empty($email) || empty($contrasena)) {
                    throw new InvalidArgumentException('Todos los campos son obligatorios.');
                }

                $this->usuarioModel->registrarUsuario($nombre, $email, $contrasena, $rol);
                $_SESSION['success'] = 'Usuario registrado con éxito.';
                header('Location: listarUsuarios');
                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = 'Error al registrar el usuario: ' . $e->getMessage();
                header('Location: mostrarFormUsuarios');
                exit();
            }
        }
    }

    // Editar un usuario
    public function editarUsuario($id_usuario)
    {
        try {
            $usuario = $this->usuarioModel->obtenerUsuarioPorId($id_usuario);

            if (!$usuario) {
                throw new Exception("El usuario solicitado no existe.");
            }

            require_once __DIR__ . '/../vistas/admin/editarUsuarios.php';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error al cargar el usuario: ' . $e->getMessage();
            header('Location: listarUsuarios');
            exit();
        }
    }

    // Actualizar un usuario
    public function actualizarUsuario($id_usuario)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $nombre = trim($_POST['nombre']);
                $email = trim($_POST['email']);
                $contrasena = trim($_POST['contrasena']);
                $rol = $_POST['rol'];

                // Validar que los campos obligatorios estén completos
                if (empty($nombre) || empty($email)) {
                    throw new InvalidArgumentException('El nombre y el correo son obligatorios.');
                }

                // Obtener la contraseña actual desde la base de datos si no se ha proporcionado una nueva
                if (empty($contrasena)) {
                    // Si la contraseña está vacía, dejamos la contraseña actual sin cambios
                    // Aquí deberías obtener la contraseña actual del usuario desde la base de datos
                    $contrasena = $this->usuarioModel->obtenerContrasenaActual($id_usuario);
                } else {
                    // Si la contraseña se proporciona, la encriptamos
                    $contrasena = password_hash($contrasena, PASSWORD_DEFAULT);
                }

                // Llamar al método para actualizar los datos del usuario
                $this->usuarioModel->actualizarUsuario($id_usuario, $nombre, $email, $contrasena, $rol);

                $_SESSION['success'] = 'Usuario actualizado con éxito.';
                header('Location: listarUsuarios');
                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = 'Error al actualizar el usuario: ' . $e->getMessage();
                header('Location: editarUsuario/' . $id_usuario);
                exit();
            }
        }
    }


    // Eliminar un usuario
    public function eliminarUsuario($id_usuario)
    {
        try {
            $this->usuarioModel->eliminarUsuario($id_usuario);
            $_SESSION['success'] = 'Usuario eliminado con éxito.';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error al eliminar el usuario: ' . $e->getMessage();
        }

        header('Location: listarUsuarios');
        exit();
    }




    // ----- Categorías CRUD -----

    // Listar todas las categorías
    public function listarCategorias()
    {
        try {
            $categorias = $this->categoriaModel->obtenerCategoriasCRUD();  // Llama al modelo para obtener todas las categorías
            require_once __DIR__ . '/../vistas/admin/listarCategorias.php';  // Vista para mostrar las categorías
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error al cargar las categorías: ' . $e->getMessage();
            header('Location: dashboard');
            exit();
        }
    }

    // Mostrar el formulario para registrar una nueva categoría
    public function mostrarFormularioRegistroCategoria()
    {
        try {
            require_once __DIR__ . '/../vistas/admin/registroCategorias.php';  // Vista para el formulario
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error al cargar el formulario de registro: ' . $e->getMessage();
            echo $_SESSION['error'];
            exit();
        }
    }

    // Registrar una nueva categoría
    public function registrarCategoria()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $nombre_categoria = trim($_POST['nombre_categoria']);
                $descripcion = trim($_POST['descripcion']);

                // Validar campos obligatorios
                if (empty($nombre_categoria) || empty($descripcion)) {
                    throw new InvalidArgumentException('Todos los campos son obligatorios.');
                }

                // Registrar la categoría en la base de datos
                $this->categoriaModel->registrarCategoriaCrud($nombre_categoria, $descripcion);

                $_SESSION['success'] = 'Categoría registrada con éxito.';
                header('Location: listarCategorias');
                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = 'Error al registrar la categoría: ' . $e->getMessage();
                header('Location: listarCategorias');
                exit();
            }
        }
    }

    // Mostrar el formulario para editar una categoría
    public function editarCategoria($id_categoria)
    {
        try {
            // Obtener los detalles de la categoría
            $categoria = $this->categoriaModel->obtenerCategoriaPorIdCrud($id_categoria);

            if (!$categoria) {
                throw new Exception("La categoría solicitada no existe.");
            }

            // Cargar la vista de edición de categorías
            require_once __DIR__ . '/../vistas/admin/editarCategorias.php';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error al cargar la categoría: ' . $e->getMessage();
            header('Location: listarCategorias');
            exit();
        }
    }


    // Actualizar una categoría
    public function actualizarCategoria($id_categoria)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $nombre_categoria = trim($_POST['nombre_categoria']);
                $descripcion = trim($_POST['descripcion']);

                // Validar campos obligatorios
                if (empty($nombre_categoria) || empty($descripcion)) {
                    throw new InvalidArgumentException('Todos los campos son obligatorios.');
                }

                // Actualizar la categoría en la base de datos
                $this->categoriaModel->actualizarCategoriaCrud($id_categoria, $nombre_categoria, $descripcion);

                $_SESSION['success'] = 'Categoría actualizada con éxito.';
                header('Location: listarCategorias');
                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = 'Error al actualizar la categoría: ' . $e->getMessage();
                header('Location: editarCategoria/' . $id_categoria);
                exit();
            }
        } else {
            try {
                $categoria = $this->categoriaModel->obtenerCategoriaPorIdCrud($id_categoria);
                require_once __DIR__ . '/../vistas/admin/editarCategoria.php';
            } catch (Exception $e) {
                $_SESSION['error'] = 'Error al cargar la categoría: ' . $e->getMessage();
                header('Location: listarCategorias');
                exit();
            }
        }
    }

    // Eliminar una categoría
    public function eliminarCategoria($id_categoria)
    {
        try {
            $resultado = $this->categoriaModel->eliminarCategoria($id_categoria);

            if ($resultado) {
                $_SESSION['success'] = 'Categoría eliminada con éxito.';
                header('Location: listarCategorias');
            } else {
                $_SESSION['error'] = 'La categoría no existe o no se pudo eliminar.';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error al eliminar la categoría: ' . $e->getMessage();
        }

        header('Location: listarCategorias');
        exit();
    }










    // ----- Tallas CRUD -----


    /**
     * Listar todas las tallas
     */
    public function listarTallas()
    {
        try {
            $tallas = $this->tallaModel->obtenerTallas();
            require_once __DIR__ . '/../vistas/admin/listarTallas.php';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error al cargar las tallas: ' . $e->getMessage();
            header('Location: dashboard'); // Redirige al panel de administrador
            exit();
        }
    }
    public function mostrarFormularioRegistroTalla()
    {
        try {
            require_once __DIR__ . '/../vistas/admin/registroTallas.php'; // Incluir la vista para mostrar el formulario
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error al cargar los productos: ' . $e->getMessage();
            echo $_SESSION['error'];
            exit();
        }

    }
    /**
     * Registrar una nueva talla
     */
    public function registrarTalla()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $nombre_talla = trim($_POST['nombre_talla']);
                $descripcion = trim($_POST['descripcion']);

                // Validar campos obligatorios
                if (empty($nombre_talla) || empty($descripcion)) {
                    throw new InvalidArgumentException('TodODDDDDDDDDDDs los campos son obligatorios.');
                }

                // Registrar talla en la base de datos
                $this->tallaModel->registrarTallaCrud($nombre_talla, $descripcion);

                $_SESSION['success'] = 'Talla registrada con éxito.';
                header('Location: listarTallas');
                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = 'Error al registrar la talla: ' . $e->getMessage();
                header('Location: listarTallas');
                exit();
            }
        }
    }

    public function editarTalla($id_talla)
    {
        // Verificar si el administrador está logueado
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== "admin") {
            $_SESSION['error'] = "No tienes permisos para realizar esta acción. Por favor, inicia sesión como administrador.";
            header('Location: login');
            exit();
        }

        try {
            // Obtener los detalles de la talla
            $talla = $this->tallaModel->obtenerTallaPorIdCrud($id_talla);

            if (!$talla) {
                throw new Exception("La talla solicitada no existe.");
            }

            // Cargar la vista de edición de tallas
            require_once __DIR__ . '/../vistas/admin/editarTallas.php';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error al cargar la talla: ' . $e->getMessage();
            header('Location: gestionarTallas');
            exit();
        }
    }


    /**
     * Mostrar y actualizar una talla
     */
    public function actualizarTalla($id_talla)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $nombre_talla = trim($_POST['nombre_talla']);
                $descripcion = trim($_POST['descripcion']);

                // Validar campos obligatorios
                if (empty($nombre_talla) || empty($descripcion)) {
                    throw new InvalidArgumentException('Todos los camposssssssss son obligatorios.');
                }

                // Actualizar talla en la base de datos
                $this->tallaModel->actualizarTallaCrud($id_talla, $nombre_talla, $descripcion);

                $_SESSION['success'] = 'Talla actualizada con éxito.';
                header('Location: listarTallas');
                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = 'Error al actualizar la talla: ' . $e->getMessage();
                header('Location: editarTalla/' . $id_talla);
                exit();
            }
        } else {
            try {
                $talla = $this->tallaModel->obtenerTallaPorIdCrud($id_talla);
                require_once __DIR__ . '/../vistas/admin/editarTalla.php';
            } catch (Exception $e) {
                $_SESSION['error'] = 'Error al cargar la talla: ' . $e->getMessage();
                header('Location: listarTallas');
                exit();
            }
        }
    }

    /**
     * Eliminar una talla
     */
    public function eliminarTalla($id_talla)
    {
        try {
            $resultado = $this->tallaModel->eliminarTalla($id_talla);

            if ($resultado) {
                $_SESSION['success'] = 'Talla eliminada con éxito.';
                header('Location: listarTallas');

            } else {
                $_SESSION['error'] = 'La talla no existe o no se pudo eliminar.';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error al eliminar la talla: ' . $e->getMessage();
        }

        header('Location: listarTallas');
        exit();
    }







}
