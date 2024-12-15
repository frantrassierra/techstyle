<?php

require_once __DIR__ . '/../../config/database.php'; // Ruta a la configuración de la base de datos

class Resena {
    private $pdo;
    private $id_resena;
    private $id_usuario;
    private $id_producto;
    private $comentario;
    private $calificacion;
    private $fecha_resena;

    // Constructor con PDO para la base de datos
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Getters y setters
    public function getIdResena() {
        return $this->id_resena;
    }

    public function setIdResena($id_resena) {
        $this->id_resena = $id_resena;
    }

    public function getIdUsuario() {
        return $this->id_usuario;
    }

    public function setIdUsuario($id_usuario) {
        $this->id_usuario = $id_usuario;
    }

    public function getIdProducto() {
        return $this->id_producto;
    }

    public function setIdProducto($id_producto) {
        $this->id_producto = $id_producto;
    }

    public function getComentario() {
        return $this->comentario;
    }

    public function setComentario($comentario) {
        $this->comentario = $comentario;
    }

    public function getCalificacion() {
        return $this->calificacion;
    }

    public function setCalificacion($calificacion) {
        $this->calificacion = $calificacion;
    }

    public function getFechaResena() {
        return $this->fecha_resena;
    }

    public function setFechaResena($fecha_resena) {
        $this->fecha_resena = $fecha_resena;
    }

  // Método para obtener reseñas por producto
  public function obtenerResenasPorProducto($idProducto) {
    $sql = "SELECT 
                r.id_resena, 
                r.comentario, 
                r.calificacion, 
                r.fecha, 
                r.id_usuario, 
                u.nombre
            FROM Resenas r
            JOIN Usuarios u ON r.id_usuario = u.id_usuario
            WHERE r.id_producto = :id_producto";
    try {
        // Preparar la consulta
        $stmt = $this->pdo->prepare($sql);

        // Vincular el parámetro
        $stmt->bindParam(':id_producto', $idProducto, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();

        // Obtener los resultados
        $resenas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Comprobar si hay resultados
        if (empty($resenas)) {
            error_log("No se encontraron reseñas para el producto con ID: $idProducto");
        }

        // Retornar las reseñas
        return $resenas;

    } catch (PDOException $e) {
        // Registrar detalles del error
        error_log("Error al obtener reseñas: " . $e->getMessage());
        error_log("Consulta SQL: " . $sql);
        error_log("Parámetro proporcionado: id_producto = $idProducto");
        return [];
    }
}



    



/*     public function agregarResena($idUsuario, $idProducto, $comentario, $calificacion) {
        ECHO"QUEPASAAAAAA";
        try {
            // Comprobamos que los valores son válidos
            echo "ID Usuario: " . htmlspecialchars($idUsuario) . "<br>";
            echo "ID Producto: " . htmlspecialchars($idProducto) . "<br>";
            echo "Comentario: " . htmlspecialchars($comentario) . "<br>";
            echo "Calificación: " . htmlspecialchars($calificacion) . "<br>";
    
            // Preparamos la consulta SQL para insertar la reseña
            $sql = "INSERT INTO Resenas (id_usuario, id_producto, comentario, calificacion, fecha_resena)
                    VALUES (:id_usuario, :id_producto, :comentario, :calificacion, NOW())";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id_usuario', $idUsuario, PDO::PARAM_INT);
            $stmt->bindParam(':id_producto', $idProducto, PDO::PARAM_INT);
            $stmt->bindParam(':comentario', $comentario, PDO::PARAM_STR);
            $stmt->bindParam(':calificacion', $calificacion, PDO::PARAM_INT);
            
            // Ejecutamos la consulta
            if ($stmt->execute()) {
                echo "Reseña agregada exitosamente.<br>";
                return true;
            } else {
                echo "Error al ejecutar la consulta: " . implode(", ", $stmt->errorInfo()) . "<br>";
                return false;
            }
    
        } catch (Exception $e) {
            // Captura el error y lo imprime
            echo "Error al agregar la reseña: " . $e->getMessage() . "<br>";
            return false;
        }
    } */

    public function agregarResena($idUsuario, $idProducto, $comentario, $calificacion) {
        try {
            echo "=== INICIO DEL PROCESO DE AGREGAR RESEÑA ===<br>";
    
            // Validar parámetros
            if (empty($idUsuario) || empty($idProducto) || empty($comentario) || empty($calificacion)) {
                echo "Error: Uno o más parámetros están vacíos.<br>";
                return false;
            }
    
            // Verificar existencia de claves foráneas
            $usuarioExiste = $this->pdo->prepare("SELECT COUNT(*) FROM Usuarios WHERE id_usuario = :id_usuario");
            $usuarioExiste->execute([':id_usuario' => $idUsuario]);
            if ($usuarioExiste->fetchColumn() == 0) {
                echo "Error: El usuario con id $idUsuario no existe.<br>";
                return false;
            }
    
            $productoExiste = $this->pdo->prepare("SELECT COUNT(*) FROM Productos WHERE id_producto = :id_producto");
            $productoExiste->execute([':id_producto' => $idProducto]);
            if ($productoExiste->fetchColumn() == 0) {
                echo "Error: El producto con id $idProducto no existe.<br>";
                return false;
            }
    
            $sql = "INSERT INTO Resenas (id_usuario, id_producto, comentario, calificacion, fecha)
                    VALUES (:id_usuario, :id_producto, :comentario, :calificacion, NOW())";
            $stmt = $this->pdo->prepare($sql);
    
            // Vincular parámetros
            $stmt->bindParam(':id_usuario', $idUsuario, PDO::PARAM_INT);
            $stmt->bindParam(':id_producto', $idProducto, PDO::PARAM_INT);
            $stmt->bindParam(':comentario', $comentario, PDO::PARAM_STR);
            $stmt->bindParam(':calificacion', $calificacion, PDO::PARAM_INT);
    
            // Ejecutar consulta
            if ($stmt->execute()) {
                echo "Reseña agregada exitosamente.<br>";
                echo "=== FIN DEL PROCESO DE AGREGAR RESEÑA ===<br>";
                return true;
            } else {
                // Capturar información del error SQL
                $errorInfo = $stmt->errorInfo();
                echo "Error en la ejecución de la consulta SQL:<br>";
                echo "Código de error: " . $errorInfo[0] . "<br>";
                echo "Detalles del error: " . $errorInfo[2] . "<br>";
    
                // Registrar error en logs
                error_log("Error SQL al agregar reseña: " . implode(", ", $errorInfo));
                return false;
            }
        } catch (Exception $e) {
            // Registrar detalles de la excepción
            echo "Excepción capturada: " . $e->getMessage() . "<br>";
            echo "Traza del error: <pre>" . $e->getTraceAsString() . "</pre><br>";
            error_log("Excepción en agregarResena: " . $e->getMessage());
            error_log("Traza completa: " . $e->getTraceAsString());
            return false;
        }
    }
    

    
    // Método para comprobar si el usuo ya ha dejado una reseña para un producto
public function verificarResenaExistente($idUsuario, $idProducto) {
    try {
        // Consultamos si ya existe una reseña para ese producto y usuario
        $sql = "SELECT COUNT(*) FROM Resenas WHERE id_usuario = :id_usuario AND id_producto = :id_producto";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id_usuario', $idUsuario, PDO::PARAM_INT);
        $stmt->bindParam(':id_producto', $idProducto, PDO::PARAM_INT);
        $stmt->execute();
        
        // Si el resultado es mayor que 0, significa que ya existe una reseña
        $resultado = $stmt->fetchColumn();
        
        return $resultado > 0;
    } catch (Exception $e) {
        error_log("Error al verificar reseña existente: " . $e->getMessage());
        return false; // En caso de error, retornamos false
    }
}



    // Método para eliminar una reseña
    public function eliminarResena($idResena){
        $sql = "DELETE FROM Resenas WHERE id_resena = :id_resena";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id_resena', $idResena);

        $stmt->execute();

        // Luego de eliminar la reseña, actualizar el promedio de calificación del producto
    }

    // Método para actualizar el promedio de calificación del producto
   

    


public function verificarResenaPorUsuario($idResena, $idUsuario) {
    $sql = "SELECT 1 FROM Resenas WHERE id_resena = :id_resena AND id_usuario = :id_usuario";
    try {
        // Preparar la consulta
        $stmt = $this->pdo->prepare($sql);

        // Ejecutar con los parámetros
        $stmt->execute([
            ':id_resena' => $idResena,
            ':id_usuario' => $idUsuario,
        ]);

        // Verificar si existe algún resultado
        return $stmt->fetchColumn() !== false;
    } catch (PDOException $e) {
        // Manejo de errores
        error_log("Error al verificar reseña: " . $e->getMessage());
        return false;
    }
}









}
?>
