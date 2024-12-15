<?php

require_once __DIR__ . '/../../config/database.php'; // Ruta a la configuración de la base de datos

class RegistroPedido {
    private $pdo;
    private $idPedido;
    private $numeroCorrelativo;
    private $codigoProducto;
    private $codigoTalla;
    private $cantidad;
    private $precioTotal;

    // Constructor con PDO para la base de datos
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Getters y setters
    public function getIdPedido() {
        return $this->idPedido;
    }

    public function setIdPedido($idPedido) {
        $this->idPedido = $idPedido;
    }

    public function getNumeroCorrelativo() {
        return $this->numeroCorrelativo;
    }

    public function setNumeroCorrelativo($numeroCorrelativo) {
        $this->numeroCorrelativo = $numeroCorrelativo;
    }

    public function getCodigoProducto() {
        return $this->codigoProducto;
    }

    public function setCodigoProducto($codigoProducto) {
        $this->codigoProducto = $codigoProducto;
    }

    public function getCodigoTalla() {
        return $this->codigoTalla;
    }

    public function setCodigoTalla($codigoTalla) {
        $this->codigoTalla = $codigoTalla;
    }

    public function getCantidad() {
        return $this->cantidad;
    }

    public function setCantidad($cantidad) {
        $this->cantidad = $cantidad;
    }

    public function getPrecioTotal() {
        return $this->precioTotal;
    }

    public function setPrecioTotal($precioTotal) {
        $this->precioTotal = $precioTotal;
    }

    // Método para agregar un nuevo registro de pedido
    public function agregarRegistro($idPedido, $numeroCorrelativo, $codigoProducto, $codigoTalla, $cantidad, $precioUnitario) {
        $precioTotal = $cantidad * $precioUnitario; // Calcular el precio total
        $sql = "INSERT INTO Registro_Pedido (idPedido, NumeroCorrelativo, codigoProducto, codigoTalla, cantidad, precio_total) 
                VALUES (:idPedido, :numeroCorrelativo, :codigoProducto, :codigoTalla, :cantidad, :precioTotal)";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':idPedido' => $idPedido,
                ':numeroCorrelativo' => $numeroCorrelativo,
                ':codigoProducto' => $codigoProducto,
                ':codigoTalla' => $codigoTalla,
                ':cantidad' => $cantidad,
                ':precioTotal' => $precioTotal
            ]);
            return true; // Retorna true si la inserción fue exitosa
        } catch (PDOException $e) {
            // Manejo de errores
            error_log("Error al agregar registro: " . $e->getMessage()); // Log de errores
            return false;
        }
    }

    // Método para obtener registros de un pedido específico
    public function obtenerRegistroPorPedido($idPedido) {
        $sql = "SELECT * FROM Registro_Pedido WHERE idPedido = :idPedido"; // Asegúrate de que el nombre de la columna sea correcto
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':idPedido' => $idPedido]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve los resultados como un array asociativo

            // Convertir el array en objetos RegistroPedido
            $registros = [];
            foreach ($result as $data) {
                $registro = new RegistroPedido($this->pdo);
                $registro->setIdPedido($data['idPedido']);
                $registro->setNumeroCorrelativo($data['NumeroCorrelativo']);
                $registro->setCodigoProducto($data['codigoProducto']);
                $registro->setCodigoTalla($data['codigoTalla']);
                $registro->setCantidad($data['cantidad']);
                $registro->setPrecioTotal($data['precio_total']);
                $registros[] = $registro;
            }

            return $registros; // Retorna un array de objetos RegistroPedido
        } catch (PDOException $e) {
            error_log("Error al obtener registro por pedido: " . $e->getMessage()); // Log de errores
            return []; // Retorna un array vacío en caso de error
        }
    }

    // Método para obtener todos los registros asociados a un pedido específico
    public function obtenerRegistrosPorPedido($idPedido) {
        $stmt = $this->pdo->prepare("SELECT * FROM Registro_Pedido WHERE idPedido = :idPedido");
        $stmt->bindParam(':idPedido', $idPedido, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Convertir el array en objetos RegistroPedido
        $registros = [];
        foreach ($result as $data) {
            $registro = new RegistroPedido($this->pdo);
            $registro->setIdPedido($data['idPedido']);
            $registro->setNumeroCorrelativo($data['NumeroCorrelativo']);
            $registro->setCodigoProducto($data['codigoProducto']);
            $registro->setCodigoTalla($data['codigoTalla']);
            $registro->setCantidad($data['cantidad']);
            $registro->setPrecioTotal($data['precio_total']);
            $registros[] = $registro;
        }

        return $registros; // Retorna un array de objetos RegistroPedido
    }

    public function obtenerProductosCarrito($idUsuario) {
        $query = "SELECT * FROM Carrito_Registro_Pedido WHERE id_usuario = :idUsuario AND estado = 'en_carrito'";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function calcularTotal($productosCarrito) {
        $total = 0;
        foreach ($productosCarrito as $producto) {
            $total += $producto['precio_total']; // Supone que cada registro ya tiene el precio total calculado
        }
        return $total;
    }
    public function actualizarCarrito($idUsuario, $idPedido) {
        $query = "UPDATE Carrito_Registro_Pedido 
                  SET idPedido = :idPedido, estado = 'comprado' 
                  WHERE id_usuario = :idUsuario AND estado = 'en_carrito'";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':idPedido', $idPedido, PDO::PARAM_INT);
        $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
    }
    

    
    
}
?>
