<?php

require_once __DIR__ . '/../modelos/Contacto.php'; // Asegúrate de incluir el modelo Contacto


require_once __DIR__ . '/../../app/middleware/autenticacion.php';

class ContactoControlador {

    private $contactoModel;

    public function __construct($pdo) {
        // Crear una instancia del modelo Contacto
        $this->contactoModel = new Contacto($pdo);
    }

    

    // Procesar el formulario de contacto


    public function procesarFormularioContacto() {
        header('Content-Type: application/json'); // Indicamos que la respuesta será JSON
    
        try {
            // Comprobamos si el usuario está autenticado
            if (!isset($_SESSION['usuario_id'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Debes iniciar sesión para enviar un mensaje.'
                ]);
                return;
            }
    
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $usuario_id = $_SESSION['usuario_id']; // ID del usuario logueado
                $nombre = trim($_POST['nombre']);
                $telefono = trim($_POST['telefono']);
                $email = trim($_POST['email']);
                $mensaje = trim($_POST['mensaje']);
    
                // Validaciones
                if (empty($nombre) || empty($email) || empty($mensaje)) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Todos los campos obligatorios deben ser completados.'
                    ]);
                    return;
                }
    
                // Guardar mensaje en el modelo
                $this->contactoModel->setIdUsuario($usuario_id);
                $this->contactoModel->setNombre($nombre);
                $this->contactoModel->setTelefono($telefono);
                $this->contactoModel->setEmail($email);
                $this->contactoModel->setMensaje($mensaje);
    
                if ($this->contactoModel->guardarMensaje()) {
                    echo json_encode([
                        'success' => true,
                        'message' => '¡Gracias por contactarnos! Nos pondremos en contacto contigo pronto.'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Hubo un error al enviar tu mensaje. Intenta nuevamente.'
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Método no permitido.'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error en el servidor: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8')
            ]);
        }
    }
    
}
