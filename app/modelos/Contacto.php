<?php

require_once __DIR__ . '/../../config/database.php'; // Ruta a la configuración de la base de datos

class Contacto {
    private $pdo;
    private $id_contacto;
    private $id_usuario;
    private $nombre;
    private $telefono;
    private $email;
    private $mensaje;
    private $fecha_envio;

    // Constructor con PDO para la base de datos
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Getters y setters
    public function getIdContacto() {
        return $this->id_contacto;
    }

    public function setIdContacto($id_contacto) {
        $this->id_contacto = $id_contacto;
    }

    public function getIdUsuario() {
        return $this->id_usuario;
    }

    public function setIdUsuario($id_usuario) {
        $this->id_usuario = $id_usuario;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    public function getTelefono() {
        return $this->telefono;
    }

    public function setTelefono($telefono) {
        $this->telefono = $telefono;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getMensaje() {
        return $this->mensaje;
    }

    public function setMensaje($mensaje) {
        $this->mensaje = $mensaje;
    }

    public function getFechaEnvio() {
        return $this->fecha_envio;
    }

    public function setFechaEnvio($fecha_envio) {
        $this->fecha_envio = $fecha_envio;
    }

    // Método para insertar un nuevo mensaje de contacto
    public function guardarMensaje() {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO Contactos (id_usuario, nombre, telefono, email, mensaje) 
                                         VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $this->id_usuario, 
                $this->nombre, 
                $this->telefono, 
                $this->email, 
                $this->mensaje
            ]);

            return true; // Si se guardó correctamente
        } catch (PDOException $e) {
            // Si ocurre un error, se captura y se registra en el log
            error_log($e->getMessage(), 3, 'errors.log');
            return false; // Si ocurre un error, devuelve false
        }
    }
    // Método para obtener todos los mensajes de contacto (opcional)
    public function obtenerTodosLosMensajes() {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM Contactos ORDER BY fecha_envio DESC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Retorna todos los mensajes como un array
        } catch (PDOException $e) {
            // Si hay un error, se maneja aquí
            error_log($e->getMessage(), 3, 'errors.log');
            return false;
        }
    }
}
