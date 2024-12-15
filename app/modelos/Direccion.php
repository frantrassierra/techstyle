<?php

require_once __DIR__ . '/../../config/database.php'; // Ruta a la configuración de la base de datos

class Direccion {
    private $pdo;
    private $id_direccion;
    private $id_usuario;
    private $ciudad;
    private $codigo_postal;
    private $pais;
    private $direccion_principal;

    // Constructor con PDO para la base de datos
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Getters y setters
    public function getIdDireccion() {
        return $this->id_direccion;
    }

    public function setIdDireccion($id_direccion) {
        $this->id_direccion = $id_direccion;
    }

    public function getIdUsuario() {
        return $this->id_usuario;
    }

    public function setIdUsuario($id_usuario) {
        $this->id_usuario = $id_usuario;
    }

    public function getCiudad() {
        return $this->ciudad;
    }

    public function setCiudad($ciudad) {
        $this->ciudad = $ciudad;
    }

    public function getCodigoPostal() {
        return $this->codigo_postal;
    }

    public function setCodigoPostal($codigo_postal) {
        $this->codigo_postal = $codigo_postal;
    }

    public function getPais() {
        return $this->pais;
    }

    public function setPais($pais) {
        $this->pais = $pais;
    }

    public function getDireccionPrincipal() {
        return $this->direccion_principal;
    }

    public function setDireccionPrincipal($direccion_principal) {
        $this->direccion_principal = $direccion_principal;
    }

    // Método para obtener todas las direcciones de un usuario
    public function obtenerDirecciones($id_usuario) {
        $stmt = $this->pdo->prepare("SELECT * FROM Direcciones WHERE id_usuario = ?");
        $stmt->execute([$id_usuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerDireccionPrincipal($idUsuario) {
        $query = "SELECT * FROM Direcciones WHERE id_usuario = :idUsuario AND direccion_principal = TRUE";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC); // Retorna la dirección principal o false si no hay ninguna
    }
    
    

// Método para eliminar una dirección
public function eliminarDireccion($id_direccion, $id_usuario) {
    // Verificar si la dirección existe y si pertenece al usuario
    $stmt = $this->pdo->prepare("SELECT * FROM Direcciones WHERE id_direccion = ? AND id_usuario = ?");
    $stmt->execute([$id_direccion, $id_usuario]);
    $direccion = $stmt->fetch(PDO::FETCH_ASSOC);

    // Si la dirección existe y pertenece al usuario, proceder con la eliminación
    if ($direccion) {
        $stmt = $this->pdo->prepare("DELETE FROM Direcciones WHERE id_direccion = ?");
        return $stmt->execute([$id_direccion]);
    } else {
        // Si no pertenece al usuario, devolver false o lanzar un error
        return false;
    }
}
    // Método para obtener una dirección específica
    public function obtenerDireccionPorId($id_direccion) {
        $stmt = $this->pdo->prepare("SELECT * FROM Direcciones WHERE id_direccion = ?");
        $stmt->execute([$id_direccion]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Método para obtener una dirección específica como objeto Direccion
public function obtenerObjetoDireccionPorId($id_direccion) {
    // Preparamos la consulta SQL
    $stmt = $this->pdo->prepare("SELECT * FROM Direcciones WHERE id_direccion = ?");
    $stmt->execute([$id_direccion]);

    // Obtenemos el resultado de la consulta
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    // Si la dirección existe, la devolvemos como un objeto Direccion
    if ($data) {
        $direccion = new Direccion($this->pdo);  // Creamos una nueva instancia de Direccion
        $direccion->setIdDireccion($data['id_direccion']);
        $direccion->setIdUsuario($data['id_usuario']);
        $direccion->setCiudad($data['ciudad']);
        $direccion->setCodigoPostal($data['codigo_postal']);
        $direccion->setPais($data['pais']);
        $direccion->setDireccionPrincipal($data['direccion_principal']);

        return $direccion;  // Devolvemos el objeto Direccion
    } else {
        return null;  // Si no se encuentra la dirección, devolvemos null
    }
}


  
    public function addDireccion($id_usuario, $direccion) {
        // Verificar si el usuario ya tiene una dirección principal
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM Direcciones WHERE id_usuario = ? AND direccion_principal = 1");
        $stmt->execute([$id_usuario]);
        $direccionPrincipalExistente = $stmt->fetchColumn();
    
        // Si ya tiene una dirección principal, mostrar un mensaje de error
        if ($direccionPrincipalExistente > 0 && isset($direccion['direccion_principal']) && $direccion['direccion_principal'] == 1) {
            // Si se intenta añadir una dirección principal, pero ya existe una, retornamos false
            $_SESSION['error'] = "Ya tienes una dirección principal. No puedes agregar otra dirección principal.";
            return false; // No se insertará la nueva dirección
        }
    
        // Si no hay dirección principal o no se intenta añadir una principal, insertamos la nueva dirección
        $stmt = $this->pdo->prepare("INSERT INTO Direcciones (id_usuario, ciudad, codigo_postal, pais, direccion_principal) 
                                     VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([
            $id_usuario,
            $direccion['ciudad'],
            $direccion['codigo_postal'],
            $direccion['pais'],
            isset($direccion['direccion_principal']) ? $direccion['direccion_principal'] : 0 // Default a 0 si no está marcado
        ]);
    }
    
    // Método para actualizar la dirección principal
    public function actualizarDireccionPrincipal($id_usuario, $id_direccion) {
        // Primero, desmarcar la dirección principal de todas las direcciones del usuario
        $stmt = $this->pdo->prepare("UPDATE Direcciones SET direccion_principal = FALSE WHERE id_usuario = ?");
        $stmt->execute([$id_usuario]);

        // Luego, marcar la nueva dirección como principal
        $stmt = $this->pdo->prepare("UPDATE Direcciones SET direccion_principal = TRUE WHERE id_direccion = ?");
        return $stmt->execute([$id_direccion]);
    }




    // Método para obtener la dirección asociada a un pedido por su ID
public function obtenerDireccionPorPedidoId($id_pedido) {
    // Preparamos la consulta para obtener la dirección basada en el id_direccion del pedido
    $stmt = $this->pdo->prepare("SELECT d.* FROM direcciones d
                                 JOIN pedidos p ON p.id_direccion = d.id_direccion
                                 WHERE p.id_pedido = :id_pedido");

    // Vinculamos el parámetro id_pedido
    $stmt->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);

    // Ejecutamos la consulta
    $stmt->execute();

    // Obtenemos el resultado
    $direccionData = $stmt->fetch(PDO::FETCH_ASSOC);

    // Si existe una dirección, la retornamos, sino retornamos null
    if ($direccionData) {
        return $direccionData; // Devuelve la dirección como un array asociativo
    } else {
        return null; // Si no se encuentra la dirección, retorna null
    }
}

}
?>
