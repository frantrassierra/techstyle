<?php

require_once __DIR__ . '/../modelos/Usuario.php';
require_once __DIR__ . '/../modelos/Direccion.php';

class UsuarioControlador
{
    private $usuarioModel;
    private $direccionModel;
    public function __construct($pdo)
    {
        $this->usuarioModel = new Usuario($pdo);
        $this->direccionModel = new Direccion($pdo);
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Procesar registro de usuario
    public function procesarRegistro()
    {
        // Verificar si el formulario fue enviado con el método POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Recoger los datos del formulario
            $nombre = trim($_POST['nombre']);
            $email = trim($_POST['email']);
            $contrasena = trim($_POST['contrasena']);
            $confirmarContrasena = trim($_POST['confirmar_contrasena']);
    
            // Validaciones
            if (empty($nombre) || empty($email) || empty($contrasena) || empty($confirmarContrasena)) {
                $error = "Todos los campos son obligatorios.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "El formato de email es inválido.";
            } elseif ($contrasena !== $confirmarContrasena) {
                $error = "Las contraseñas no coinciden.";
            } else {
                // Verificar si el correo ya está registrado
                if ($this->usuarioModel->emailExiste($email)) {
                    $error = "El correo electrónico ya está registrado.";
                } else {
                    // Procesar el registro
                    $usuarioId = $this->usuarioModel->registrar($nombre, $email, $contrasena);
                    if ($usuarioId) {
                        // Registro exitoso, agregar mensaje de éxito a la sesión
                        $_SESSION['success_message'] = "Registro exitoso. Ahora puedes iniciar sesión.";
                        header('Location: login');
                        exit();
                    } else {
                        $error = "Error al registrar el usuario. Inténtalo de nuevo.";
                    }
                }
            }
        }
    
        // Si hubo un error o si es la primera vez que se carga la página, mostrar el formulario
        require_once __DIR__ . '/../vistas/usuarios/registro.php';
    }
    

    public function procesarLogin()
{
    // Verificar si el formulario fue enviado con el método POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email']);
        $contrasena = trim($_POST['contrasena']);

        // Validar si los campos no están vacíos
        if (empty($email) || empty($contrasena)) {
            $error = "Todos los campos son obligatorios.";
        } else {
            // Intentar obtener el usuario desde el modelo
            $usuario = $this->usuarioModel->login($email, $contrasena);

            if ($usuario) {
                // Si las credenciales son correctas, iniciar sesión
                $_SESSION['usuario_id'] = $usuario->getIdUsuario(); // Usar el getter para acceder al ID
                $_SESSION['nombre'] = $usuario->getNombre(); // Usar el getter para acceder al nombre
                $_SESSION['email'] = $usuario->getEmail(); // Usar el getter para acceder al email
                $_SESSION['rol'] = $usuario->getRol(); // Usar el getter para acceder al rol

                // Redirigir según el rol del usuario
                if ($usuario->getRol() === 'admin') {
                    header('Location: adminPanel'); // Redirige al panel de administración
                } else {
                    header('Location: inicio'); // Redirige al inicio del usuario
                }
                exit(); // Asegurarse de salir después de la redirección
            } else {
                // Si el login falla
                $error = "Email o contraseña incorrectos.";
            }
        }

        // Incluir la vista de login con el mensaje de error si es necesario
        require_once __DIR__ . '/../vistas/usuarios/login.php';
    }
}


    // Logout y destrucción de sesión
    public function logout()
    {
        session_start(); // Asegúrate de iniciar la sesión antes de manipularla
        session_unset(); // Eliminar todas las variables de sesión
        session_destroy(); // Destruir la sesión
    
        // Inicia una nueva sesión para el mensaje de confirmación
        session_start();
        $_SESSION['success_message'] = "Has cerrado sesión correctamente.";
    
        header('Location: login'); // Redirigir a la página de login
        exit();
    }
    

    // Ver perfil de usuario
    // Ver perfil de usuario
    public function verPerfil($idUsuario)
    {
        // Asegurarse de que el usuario está autenticado
        if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_id'] != $idUsuario) {
            header('Location: login');
            exit();
        }

        $usuario = $this->usuarioModel->obtenerUsuarioPorId($idUsuario);
        require_once __DIR__ . '/../vistas/usuarios/perfil.php';

    }

    public function procesarEditarPerfil($idUsuario) {
        if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_id'] != $idUsuario) {
            header('Location: login');
            exit();
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->usuarioModel->actualizarPerfilBaseDatos($idUsuario, $_POST)) {
                header('Location: perfil');
                exit();
            } else {
                $error = "Error al actualizar el perfil.";
            }
        }
    }
    

    public function mostrarPerfil($idUsuario) {
        if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_id'] != $idUsuario) {
            header('Location: login');
            exit();
        }
    
        $usuario = $this->usuarioModel->obtenerUsuarioPorId($idUsuario);
         // Obtener las direcciones asociadas al usuario
        $direcciones = $this->direccionModel->obtenerDirecciones($idUsuario);
        require_once __DIR__ . '/../vistas/usuarios/editar_perfil.php';
    }
    


    public function gestionarDirecciones($idUsuario)
    {
        if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_id'] != $idUsuario) {
            header('Location: login');
            exit();
        }
    
        // Obtener direcciones asociadas al usuario
        $direcciones = $this->direccionModel->obtenerDirecciones($idUsuario);
        require_once __DIR__ . '/../vistas/usuarios/direcciones.php';
    }
    
    public function procesarAddDireccion($idUsuario)
    {
        if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_id'] != $idUsuario) {
            header('Location: login');
            exit();
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->direccionModel->addDireccion($idUsuario, $_POST)) {
                // Redirigir a la página de direcciones del usuario si la adición es exitosa
                header('Location: direcciones'); // Asegúrate de usar la ruta completa o relativa correcta
                exit();
            } else {
                $error = $_SESSION['error'] ?? "Error al añadir la dirección."; // Usar mensaje de error de la sesión
                unset($_SESSION['error']); // Limpiar el mensaje de error
                // Si ocurre un error, mostrar el formulario con el mensaje de error
                $this->mostrarFormularioAñadirDireccion($idUsuario, $error);
            }
        }
    }
    
    
    public function mostrarFormularioAñadirDireccion($idUsuario, $error = null)
    {
        // Llamar a la vista y pasar los datos del formulario y posibles errores
        require_once __DIR__ . '/../vistas/usuarios/anadir_direcciones.php';
    }
    

    // Eliminar dirección
 // Método en el controlador
// Método del controlador para eliminar una dirección
// Método del controlador para eliminar una dirección
// En tu controlador

public function eliminarDireccion($id_direccion) {
    // Verificar si el usuario está logueado
    if (!isset($_SESSION['usuario_id'])) {
        $_SESSION['error'] = "No estás logueado. Por favor, inicia sesión.";
        header('Location: login');
        exit();
    }

    // Obtener el ID del usuario logueado
    $id_usuario = $_SESSION['usuario_id'];

    // Llamar al método eliminarDireccion del modelo
    $resultado = $this->direccionModel->eliminarDireccion($id_direccion, $id_usuario);

    if ($resultado) {
        // Si la dirección fue eliminada, redirigir con un mensaje de éxito
        $_SESSION['success'] = "Dirección eliminada con éxito.";
    } else {
        // Si la dirección no pertenece al usuario o no se encuentra, redirigir con un mensaje de error
        $_SESSION['error'] = "No puedes eliminar esta dirección.";
    }

    // Redirigir a la página de direcciones
    header('Location: direcciones');
    exit();
}





    // Establecer una dirección como principal
    public function establecerDireccionPrincipal($idUsuario, $idDireccion)
    {
        if ($this->direccionModel->actualizarDireccionPrincipal($idUsuario, $idDireccion)) {
            header('Location: /direcciones.php?id=' . $idUsuario);
            exit();
        }
    }

}
