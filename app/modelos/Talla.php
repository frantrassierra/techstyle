<?php

require_once __DIR__ . '/../../config/database.php'; // Ruta a la configuración de la base de datos

class Talla {
    private $pdo;
    private $id_talla;
    private $nombre_talla;
    private $descripcion;

    // Constructor con PDO para la base de datos
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Getters y setters
    public function getIdTalla() {
        return $this->id_talla;
    }

    public function setIdTalla($id_talla) {
        $this->id_talla = $id_talla;
    }

    public function getNombreTalla() {
        return $this->nombre_talla;
    }

    public function setNombreTalla($nombre_talla) {
        $this->nombre_talla = $nombre_talla;
    }

    public function getDescripcion() {
        return $this->descripcion;
    }

    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    // Método para obtener todas las tallas
    public function obtenerTallas() {
        try {
            // Realizar la consulta SQL para obtener todas las tallas
            $stmt = $this->pdo->query("SELECT * FROM Tallas");
            
            // Devolver las tallas como un array asociativo
            return $stmt->fetchAll(PDO::FETCH_ASSOC); 
        } catch (Exception $e) {
            throw new Exception('Error al obtener las tallas: ' . $e->getMessage());
        }
    }
    

    
    // Método para obtener una talla por su ID
    public function obtenerPorId($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM Tallas WHERE id_talla = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($data) {
            $talla = new Talla($this->pdo);
            $talla->setIdTalla($data['id_talla']);
            $talla->setNombreTalla($data['nombre_talla']);
            $talla->setDescripcion($data['descripcion']);
            return $talla; // Retorna el objeto Talla
        }
        return null; // Si no se encuentra la talla, retorna null
    }

  


// Obtener la lista de tallas para listar
public function obtenerTallasListar() {
    $stmt = $this->pdo->query("
        SELECT * FROM Tallas
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Obtener una talla específica por su ID
public function obtenerTallaPorIdCrud($id_talla) {
    $stmt = $this->pdo->prepare("SELECT * FROM Tallas WHERE id_talla = ?");
    $stmt->execute([$id_talla]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Actualizar una talla en la base de datos
public function actualizarTallaCrud($id_talla, $nombre_talla, $descripcion) {
    $stmt = $this->pdo->prepare("
        UPDATE Tallas
        SET nombre_talla = ?, descripcion = ?
        WHERE id_talla = ?
    ");
    $resultado = $stmt->execute([$nombre_talla, $descripcion, $id_talla]);
    
    return $resultado;
}

// Eliminar una talla
public function eliminarTalla($id_talla) {
    // Verificar si el administrador está logueado
    if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== "admin") {
        $_SESSION['error'] = "No tienes permisos para realizar esta acción. Por favor, inicia sesión como administrador.";
        header('Location: login');
        exit();
    }

    try {
        // Eliminar los registros en Carrito_Registro_Pedido que hagan referencia a la talla
        $stmt = $this->pdo->prepare("DELETE FROM Carrito_Registro_Pedido WHERE codigoTalla = ?");
        $stmt->execute([$id_talla]);

        // Eliminar la talla
        $stmt = $this->pdo->prepare("DELETE FROM Tallas WHERE id_talla = ?");
        $resultado = $stmt->execute([$id_talla]);

        if ($resultado) {
            $_SESSION['success'] = "Talla eliminada con éxito.";
        } else {
            $_SESSION['error'] = "La talla no existe o no se pudo eliminar.";
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Error al eliminar la talla: " . $e->getMessage();
    }

    // Redirigir a la lista de tallas
    header('Location: listarTallas');
    exit();
}

// Registrar una nueva talla
public function registrarTallaCrud($nombre_talla, $descripcion) {
    $stmt = $this->pdo->prepare("
        INSERT INTO Tallas (nombre_talla, descripcion)
        VALUES (?, ?)
    ");
    try {
        $stmt->execute([$nombre_talla, $descripcion]);
    } catch (PDOException $e) {
        throw new Exception('Error al registrar la talla: ' . $e->getMessage());
    }
}
}
?>
