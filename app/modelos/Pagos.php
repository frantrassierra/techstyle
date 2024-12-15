<?php
// app/models/Pago.php

require_once __DIR__ . '/../../config/database.php'; // Ruta a la configuración de la base de datos

class Pagos
{
    private $pdo;
    private $id_pago;
    private $id_pedido;
    private $metodo_pago;
    private $cantidad;
    private $fecha_pago;
    private $direccion_facturacion;
    private $detalles_pago;

    // Constructor
    public function __construct($pdo, $id_pago = null, $id_pedido = null, $metodo_pago = null, $cantidad = null, $direccion_facturacion = null, $detalles_pago = null)
    {
        $this->pdo = $pdo;
        if ($id_pago !== null) {
            $this->id_pago = $id_pago;
            $this->id_pedido = $id_pedido;
            $this->metodo_pago = $metodo_pago;
            $this->cantidad = $cantidad;
            $this->direccion_facturacion = $direccion_facturacion;
            $this->detalles_pago = $detalles_pago;
        }
    }

    // Métodos getter y setter
    public function getIdPago()
    {
        return $this->id_pago;
    }

    public function setIdPago($id_pago)
    {
        $this->id_pago = $id_pago;
    }

    public function getIdPedido()
    {
        return $this->id_pedido;
    }

    public function setIdPedido($id_pedido)
    {
        $this->id_pedido = $id_pedido;
    }

    public function getMetodoPago()
    {
        return $this->metodo_pago;
    }

    public function setMetodoPago($metodo_pago)
    {
        $this->metodo_pago = $metodo_pago;
    }

    public function getCantidad()
    {
        return $this->cantidad;
    }

    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;
    }

    public function getFechaPago()
    {
        return $this->fecha_pago;
    }

    public function setFechaPago($fecha_pago)
    {
        $this->fecha_pago = $fecha_pago;
    }




    public function getDireccionFacturacion()
    {
        return $this->direccion_facturacion;
    }

    public function setDireccionFacturacion($direccion_facturacion)
    {
        $this->direccion_facturacion = $direccion_facturacion;
    }

    public function getDetallesPago()
    {
        return $this->detalles_pago;
    }

    public function setDetallesPago($detalles_pago)
    {
        $this->detalles_pago = $detalles_pago;
    }

    // Registrar un pago
    // Registrar un pago
    
    public function registrarPago($idPedido, $cantidad, $metodoPago, $direccionFacturacion = null, $detallesPago = null) {
        try {
            // Asegurarse de que la cantidad y el método de pago no estén vacíos
            if (empty($idPedido) || empty($cantidad) || empty($metodoPago)) {
                throw new Exception('Faltan datos para procesar el pago.');
            }
    
            // Obtener la fecha y hora actual
            $fechaPago = date('Y-m-d H:i:s');
    
            // Preparar la consulta para insertar el pago, incluyendo la fecha del pago
            $sql = "INSERT INTO Pagos (id_pedido, metodo_pago, cantidad, fecha_pago, direccion_facturacion, detalles_pago) 
                    VALUES (:id_pedido, :metodo_pago, :cantidad, :fecha_pago, :direccion_facturacion, :detalles_pago)";
    
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id_pedido', $idPedido);
            $stmt->bindParam(':metodo_pago', $metodoPago);
            $stmt->bindParam(':cantidad', $cantidad);
            $stmt->bindParam(':fecha_pago', $fechaPago);
            $stmt->bindParam(':direccion_facturacion', $direccionFacturacion);
            $stmt->bindParam(':detalles_pago', $detallesPago);
    
            // Ejecutar la consulta y verificar si se ha ejecutado correctamente
            if (!$stmt->execute()) {
                throw new Exception('Error al registrar el pago en la base de datos.');
            }
    
            return true;
    
        } catch (Exception $e) {
            // Manejo de excepciones y registro del error
            error_log("Error al registrar el pago: " . $e->getMessage());
            return false;
        }
    }
    
    // Obtener detalles de un pago
    public function obtenerDetallePago($idPago)
    {
        $query = "
            SELECT p.id_pago, p.fecha_pago, p.cantidad,  p.direccion_facturacion, p.detalles_pago, 
                ped.id_pedido, ped.total, ped.estado AS estado_pedido
            FROM Pagos p
            JOIN Pedidos ped ON p.id_pedido = ped.id_pedido
            WHERE p.id_pago = :idPago
        ";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':idPago', $idPago, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener todos los pagos
    public function obtenerTodosPagos()
    {
        $query = "SELECT * FROM Pagos";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener pagos por ID de pedido
    public function obtenerPagosPorPedido($pedidoId)
    {
        $sql = "SELECT * FROM Pagos WHERE id_pedido = :id_pedido";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id_pedido', $pedidoId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>