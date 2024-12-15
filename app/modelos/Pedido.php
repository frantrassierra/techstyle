<?php
// app/models/Pedido.php

require_once __DIR__ . '/../../config/database.php'; // Ruta a la configuración de la base de datos

class Pedido {
    private $pdo;
    private $id_pedido;
    private $id_usuario;
    private $id_direccion;
    private $fecha_pedido;
    private $total;
    private $estado;

    // Constructor
    public function __construct($pdo, $id_pedido = null, $id_usuario = null, $id_direccion = null, $fecha_pedido = null, $total = null, $estado = null) {
        $this->pdo = $pdo;
        if ($id_pedido !== null) {
            $this->id_pedido = $id_pedido;
            $this->id_usuario = $id_usuario;
            $this->id_direccion = $id_direccion;
            $this->fecha_pedido = $fecha_pedido;
            $this->total = $total;
            $this->estado = $estado;
        }
    }
    // Obtener el total de los productos de un pedido
    public function obtenerTotalPedido() {
        return $this->total;
    }

    // Métodos getter y setter
    public function getIdPedido() {
        return $this->id_pedido;
    }

    public function setIdPedido($id_pedido) {
        $this->id_pedido = $id_pedido;
    }

    public function getIdUsuario() {
        return $this->id_usuario;
    }

    public function setIdUsuario($id_usuario) {
        $this->id_usuario = $id_usuario;
    }

    public function getIdDireccion() {
        return $this->id_direccion;
    }

    public function setIdDireccion($id_direccion) {
        $this->id_direccion = $id_direccion;
    }

    public function getFechaPedido() {
        return $this->fecha_pedido;
    }

    public function setFechaPedido($fecha_pedido) {
        $this->fecha_pedido = $fecha_pedido;
    }

    public function getTotal() {
        return $this->total;
    }

    public function setTotal($total) {
        $this->total = $total;
    }

    public function getEstado() {
        return $this->estado;
    }

    public function setEstado($estado) {
        $this->estado = $estado;
    }

    
    public function registrarPedido($idUsuario, $idDireccion, $total) {
        $query = "INSERT INTO Pedidos (id_usuario, id_direccion, total, estado) VALUES (:idUsuario, :idDireccion, :total, 'pendiente')";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->bindParam(':idDireccion', $idDireccion, PDO::PARAM_INT);
        $stmt->bindParam(':total', $total, PDO::PARAM_STR);
        $stmt->execute();
        return $this->pdo->lastInsertId(); // Retorna el ID del pedido recién insertado
    }


    public function obtenerDetallePedido($idPedido) {
        $query = "
            SELECT 
                p.id_pedido, p.fecha_pedido, p.total, p.estado, 
                u.nombre AS usuario, d.ciudad, d.codigo_postal, d.pais, 
                crp.cantidad, crp.precio_unitario, crp.precio_total, 
                pr.nombre AS producto, t.nombre_talla AS talla
            FROM Pedidos p
            JOIN Usuarios u ON p.id_usuario = u.id_usuario
            JOIN Direcciones d ON p.id_direccion = d.id_direccion
            JOIN Carrito_Registro_Pedido crp ON crp.idPedido = p.id_pedido
            JOIN Productos pr ON crp.codigoProducto = pr.id_producto
            JOIN Tallas t ON crp.codigoTalla = t.id_talla
            WHERE p.id_pedido = :idPedido
        ";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':idPedido', $idPedido, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    


// Método para obtener los productos del pedido
public function obtenerProductosPorPedido($idPedido) {
    $sql = "
        SELECT 
            pr.nombre AS nombre_producto, 
            crp.cantidad, 
            crp.precio_unitario AS precio, 
            (crp.cantidad * crp.precio_unitario) AS total, 
            t.nombre_talla AS talla,
            crp.precio_total
        FROM Carrito_Registro_Pedido crp
        JOIN Productos pr ON crp.codigoProducto = pr.id_producto
        JOIN Tallas t ON crp.codigoTalla = t.id_talla
        WHERE crp.idPedido = :idPedido
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->bindParam(':idPedido', $idPedido, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}




    public function obtenerPedidosUsuario($idUsuario) {
        $query = "
            SELECT 
                p.id_pedido, p.fecha_pedido, p.total, p.estado,
                d.ciudad, d.pais
            FROM Pedidos p
            JOIN Direcciones d ON p.id_direccion = d.id_direccion
            WHERE p.id_usuario = :idUsuario
            ORDER BY p.fecha_pedido DESC
        ";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    // Crear un nuevo pedido
    public function crear($id_usuario, $id_direccion, $total, $estado = 'pendiente') {
        $stmt = $this->pdo->prepare("INSERT INTO pedidos (id_usuario, id_direccion, total, estado) VALUES (:id_usuario, :id_direccion, :total, :estado)");
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->bindParam(':id_direccion', $id_direccion);
        $stmt->bindParam(':total', $total);
        $stmt->bindParam(':estado', $estado);
        if ($stmt->execute()) {
            $this->id_pedido = $this->pdo->lastInsertId();
            return $this;
        }
        return false;
    }

    // Obtener todos los pedidos
    public function obtenerTodos() {
        $stmt = $this->pdo->prepare("SELECT * FROM pedidos");
        $stmt->execute();
        $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(function($pedidoData) {
            return new Pedido($this->pdo, $pedidoData['id_pedido'], $pedidoData['id_usuario'], $pedidoData['id_direccion'], $pedidoData['fecha_pedido'], $pedidoData['total'], $pedidoData['estado']);
        }, $pedidos);
    }

    // Obtener un pedido por ID
    public function obtenerPorId($id_pedido) {
        $stmt = $this->pdo->prepare("SELECT * FROM pedidos WHERE id_pedido = :id_pedido");
        $stmt->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
        $stmt->execute();
        $pedidoData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($pedidoData) {
            return new Pedido($this->pdo, $pedidoData['id_pedido'], $pedidoData['id_usuario'], $pedidoData['id_direccion'], $pedidoData['fecha_pedido'], $pedidoData['total'], $pedidoData['estado']);
        }
        return null;
    }

  

   
    public function obtenerProductosCarrito($idUsuario) {
        $query = "SELECT * FROM Carrito_Registro_Pedido WHERE id_usuario = :idUsuario AND estado = 'en_carrito'";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    public function crearPedido($idUsuario, $idDireccion, $total) {
        try {
            // Iniciar la transacción
            $this->pdo->beginTransaction();
    
            // Crear el pedido en la tabla Pedidos
            $stmt = $this->pdo->prepare("INSERT INTO Pedidos (id_usuario, id_direccion, total, estado)
                                         VALUES (:idUsuario, :idDireccion, :total, 'pendiente')");
            $stmt->execute([
                ':idUsuario' => $idUsuario,
                ':idDireccion' => $idDireccion,
                ':total' => $total
            ]);
    
            // Obtener el ID del pedido recién creado
            $idPedido = $this->pdo->lastInsertId();
    
            // Actualizar los productos en el carrito y asignarles el idPedido
            $stmt = $this->pdo->prepare("UPDATE Carrito_Registro_Pedido
                                         SET idPedido = :idPedido, estado = 'comprado'
                                         WHERE idPedido IS NULL");  // Actualiza solo los productos sin pedido asignado
            $stmt->execute([
                ':idPedido' => $idPedido,  // Asignamos el ID del pedido
            ]);
    
            // Confirmar la transacción
            $this->pdo->commit();
    
            return ['success' => true, 'message' => 'Pedido creado y carrito actualizado'];
    
        } catch (Exception $e) {
            // En caso de error, hacer rollback y devolver el mensaje de error
            $this->pdo->rollBack();
            return ['success' => false, 'message' => 'Error al crear el pedido y actualizar el carrito: ' . $e->getMessage()];
        }
    }
    

    public function verPedidosPorEstado($idUsuario, $estado) {
        $stmt = $this->pdo->prepare("SELECT * FROM pedidos WHERE id_usuario = :idUsuario AND estado = :estado");
        $stmt->execute([':idUsuario' => $idUsuario, ':estado' => $estado]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para ver el detalle de un pedido específico
    public function verDetallePedido($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM registro_pedido WHERE pedido_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Devolver como array asociativo
    }

    // Método para actualizar el estado de un pedido
    public function actualizarEstadoPedido($id, $nuevoEstado) {
        $stmt = $this->pdo->prepare("UPDATE pedidos SET estado = ? WHERE id = ?");
        return $stmt->execute([$nuevoEstado, $id]);
    }

    // Método para obtener todos los pedidos
    public function obtenerTodosPedidos() {
        $stmt = $this->pdo->prepare("SELECT * FROM pedidos");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Devolver como array asociativo
    }

   // Método para obtener un pedido por ID
public function obtenerPedidoPorId($idPedido) {
    $sql = "SELECT * FROM pedidos WHERE id_pedido = :idPedido"; // Asegúrate de que el nombre de la columna sea correcto
    
    try {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':idPedido' => $idPedido]);
        $pedidoData = $stmt->fetch(PDO::FETCH_ASSOC); // Devuelve un solo resultado como un array asociativo
        
        // Si el pedido existe, lo retornamos como un objeto Pedido
        if ($pedidoData) {
            return new Pedido(
                $this->pdo,
                $pedidoData['id_pedido'],
                $pedidoData['id_usuario'],
                $pedidoData['id_direccion'],
                $pedidoData['fecha_pedido'],
                $pedidoData['total'],
                $pedidoData['estado']
            );
        }
        
        return null; // Si no se encuentra el pedido, devolvemos null
    } catch (PDOException $e) {
        error_log("Error al obtener pedido por ID: " . $e->getMessage());
        return null; // En caso de error, retornamos null
    }
}

    
    // Método para obtener un informe de pedidos
    public function obtenerInformePedidos() {
        $stmt = $this->pdo->prepare("
            SELECT p.id, p.fecha, u.nombre AS usuario, SUM(rp.cantidad) AS total_items
            FROM pedidos p
            JOIN usuarios u ON p.usuario_id = u.id
            JOIN registro_pedido rp ON p.id = rp.pedido_id
            GROUP BY p.id
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Devolver como array asociativo
    }
    
      // Actualizar el estado de un pedido
      public function actualizarEstado($idPedido, $nuevoEstado) {
        $stmt = $this->pdo->prepare("UPDATE pedidos SET estado = :estado WHERE id = :idPedido");
        $stmt->bindParam(':estado', $nuevoEstado);
        $stmt->bindParam(':idPedido', $idPedido, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
