<?php

require_once __DIR__ . '/../../config/database.php'; // Ruta a la configuración de la base de datos

class Producto {
    private $pdo;
    private $id_producto;
    private $nombre;
    private $descripcion;
    private $descripcion_corta;
    private $precioPVP;
    private $stock;
    private $id_categoria;
    private $imagen;
    private $calificacion_promedio;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }


    public function setCalificacionPromedio($calificacion) {
        $this->calificacion_promedio = $calificacion;
    }

    public function getCalificacionPromedio() {
        return $this->calificacion_promedio;
    }

    // Getters y setters
    public function getIdProducto() {
        return $this->id_producto;
    }

    public function setIdProducto($id_producto) {
        $this->id_producto = $id_producto;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    public function getDescripcion() {
        return $this->descripcion;
    }

    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    public function getDescripcionCorta() {
        return $this->descripcion_corta;
    }

    public function setDescripcionCorta($descripcion_corta) {
        $this->descripcion_corta = $descripcion_corta;
    }

    public function getPrecioPVP() {
        return $this->precioPVP;
    }

    public function setPrecioPVP($precioPVP) {
        $this->precioPVP = $precioPVP;
    }

    public function getStock() {
        return $this->stock;
    }

    public function setStock($stock) {
        $this->stock = $stock;
    }

    public function getIdCategoria() {
        return $this->id_categoria;
    }

    public function setIdCategoria($id_categoria) {
        $this->id_categoria = $id_categoria;
    }

    public function getImagen() {
        return $this->imagen;
    }

    public function setImagen($imagen) {
        $this->imagen = $imagen;
    }

    public function obtenerProductosCarrito($idUsuario) {
        try {
            // Consulta para obtener los productos del carrito del usuario
            $stmt = $this->pdo->prepare("
                SELECT crp.codigoProducto, crp.codigoTalla, crp.cantidad, crp.precio_unitario, crp.precio_total, p.nombre, p.descripcion, p.imagen
                FROM Carrito_Registro_Pedido crp
                JOIN Productos p ON crp.codigoProducto = p.id_producto
                WHERE crp.id_usuario = :idUsuario AND crp.idPedido IS NULL
            ");
            $stmt->execute([':idUsuario' => $idUsuario]);
            
            // Obtener los resultados de la consulta
            $productosData = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Crear un array de objetos Producto
            $productos = [];
            foreach ($productosData as $data) {
                $producto = new Producto($this->pdo);
                $producto->setIdProducto($data['codigoProducto']);
                $producto->setNombre($data['nombre']);
                $producto->setDescripcion($data['descripcion']);
                $producto->setImagen($data['imagen']);
                $producto->setPrecioPVP($data['precio_unitario']);
                $producto->setStock($data['cantidad']);
                // Añadir el objeto Producto al array de productos
                $productos[] = $producto;
            }
            
            // Retornar el array de productos
            return $productos;
        } catch (Exception $e) {
            throw new Exception('Error al obtener los productos del carrito: ' . $e->getMessage());
        }
    }
    
    
   // Producto.php

public function vaciarCarrito($idUsuario) {
    try {
        $query = "DELETE FROM Carrito_Registro_Pedido WHERE id_usuario = :id_usuario";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id_usuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();

        return ['success' => true, 'message' => 'Carrito vacío correctamente'];
    } catch (PDOException $e) {
        throw new Exception("Error al vaciar el carrito: " . $e->getMessage());
    }
}



    
    public function agregarProducto($idProducto, $idTalla, $cantidad, $precioUnitario, $idUsuario) {
        try {
            // Calcular el precio total
            $precioTotal = $cantidad * $precioUnitario;
    
            // Insertar el producto en el carrito
            // El idPedido será NULL porque aún no se ha confirmado la compra
            $stmt = $this->pdo->prepare("INSERT INTO Carrito_Registro_Pedido (idPedido, codigoProducto, codigoTalla, cantidad, precio_unitario, precio_total, id_usuario)
                                         VALUES (NULL, :codigoProducto, :codigoTalla, :cantidad, :precioUnitario, :precioTotal, :idUsuario)");
            $stmt->execute([
                ':codigoProducto' => $idProducto,     // ID del producto
                ':codigoTalla' => $idTalla,           // ID de la talla seleccionada
                ':cantidad' => $cantidad,             // Cantidad del producto
                ':precioUnitario' => $precioUnitario, // Precio unitario del producto
                ':precioTotal' => $precioTotal,       // Precio total (cantidad * precioUnitario)
                ':idUsuario' => $idUsuario           // ID del usuario
            ]);
    
            return ['success' => true, 'message' => 'Producto agregado al carrito'];
        } catch (Exception $e) {
            // Manejar errores y devolver mensaje
            return ['success' => false, 'message' => 'Error al agregar el producto al carrito: ' . $e->getMessage()];
        }
    }
    
    
    public function actualizarStock($idProducto, $nuevoStock) {
        $sql = "UPDATE productos SET stock = :nuevoStock WHERE id_producto = :idProducto";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':nuevoStock' => $nuevoStock,
            ':idProducto' => $idProducto
        ]);
    
        // Obtener el producto actualizado y devolverlo
        return $this->obtenerProductoPorId($idProducto); 
    }
     public function obtenerProductos($categoria = null) {
        try {
            // Si se pasa una categoría, obtener solo los productos de esa categoría
            if ($categoria) {
                // Preparar la consulta para obtener productos filtrados por categoría
                $stmt = $this->pdo->prepare("SELECT * FROM Productos WHERE id_categoria = ?");
                $stmt->execute([$categoria]);
            } else {
                // Si no se pasa categoría, obtener todos los productos
                $stmt = $this->pdo->query("SELECT * FROM Productos");
            }
    
            // Obtener los resultados de la consulta como un array asociativo
            $productosData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Crear un array de objetos Producto
            $productos = [];
            foreach ($productosData as $data) {
                // Crear una nueva instancia de Producto
                $producto = new Producto($this->pdo);
                
                // Asignar los valores a los atributos del objeto Producto
                $producto->setIdProducto($data['id_producto']);
                $producto->setNombre($data['nombre']);
                $producto->setDescripcion($data['descripcion']);
                $producto->setDescripcionCorta($data['descripcion_corta']);
                $producto->setPrecioPVP($data['precioPVP']);
                $producto->setStock($data['stock']);
                $producto->setIdCategoria($data['id_categoria']);
                $producto->setImagen($data['imagen']);
                
                // Añadir el objeto Producto al array de productos
                $productos[] = $producto;
            }
    
            // Retornar el array de objetos Producto
            return $productos;
    
        } catch (Exception $e) {
            throw new Exception('Error al obtener los productos: ' . $e->getMessage());
        }
    } 
    
    /*  public function obtenerProductos($categoria = null) {
        try {
            // Si se pasa una categoría, obtener solo los productos de esa categoría
            if ($categoria) {
                $stmt = $this->pdo->prepare("SELECT * FROM Productos WHERE id_categoria = ?");
                $stmt->execute([$categoria]);
            } else {
                // Si no se pasa categoría, obtener todos los productos
                $stmt = $this->pdo->query("SELECT * FROM Productos");
            }
    
            // Obtener los resultados de la consulta como un array asociativo
            $productosData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Crear un array de objetos Producto
            $productos = [];
            foreach ($productosData as $data) {
                $producto = new Producto($this->pdo);
                $producto->setIdProducto($data['id_producto']);
                $producto->setNombre($data['nombre']);
                $producto->setDescripcion($data['descripcion']);
                $producto->setDescripcionCorta($data['descripcion_corta']);
                $producto->setPrecioPVP($data['precioPVP']);
                $producto->setStock($data['stock']);
                $producto->setIdCategoria($data['id_categoria']);
                $producto->setImagen($data['imagen']);
                $productos[] = $producto;
            }
    
            return $productos;
    
        } catch (Exception $e) {
            throw new Exception('Error al obtener los productos: ' . $e->getMessage());
        }
    }
     */
    public function obtenerProductosHtml($categoria_id = null) {
        try {
            // Obtener los productos filtrados según la categoría
            $productos = $this->obtenerProductos($categoria_id);

            $html = ''; // Variable para almacenar el HTML generado

            // Generar HTML para cada producto
            foreach ($productos as $producto) {
                $html .= '<div class="producto" data-id="' . htmlspecialchars($producto->getIdProducto(), ENT_QUOTES, 'UTF-8') . '">';
                $html .= '<a href="detalleProducto?id_producto=' . htmlspecialchars($producto->getIdProducto(), ENT_QUOTES, 'UTF-8') . '">';
                $html .= '<img src="/productos/' . htmlspecialchars($producto->getImagen(), ENT_QUOTES, 'UTF-8') . '" alt="Imagen de ' . htmlspecialchars($producto->getNombre(), ENT_QUOTES, 'UTF-8') . '">';
                $html .= '<h4>' . htmlspecialchars($producto->getNombre(), ENT_QUOTES, 'UTF-8') . '</h4>';
                $html .= '<p>' . htmlspecialchars($producto->getPrecioPVP(), ENT_QUOTES, 'UTF-8') . ' €</p>';
                $html .= '</a></div>';
            }

            return $html; // Retornar el HTML generado
        } catch (Exception $e) {
            return '<p>Error al cargar los productos: ' . $e->getMessage() . '</p>';
        }
    }
 
/* 

public function obtenerProductos($categoria_id = null, $popularidad = null) {
    try {
        // Consulta base
        $query = "SELECT * FROM Productos WHERE 1";

        // Filtro por categoría
        if ($categoria_id) {
            $query .= " AND id_categoria = :categoria_id";
        }

        // Filtro por popularidad (productos más populares)
        if ($popularidad === '1') {
            $query .= " ORDER BY promedio_calificacion DESC";
        } elseif ($popularidad === '0') {
            $query .= " ORDER BY promedio_calificacion ASC";
        }

        // Preparar la consulta
        $stmt = $this->pdo->prepare($query);

        // Enlazar el parámetro de categoría si existe
        if ($categoria_id) {
            $stmt->bindParam(':categoria_id', $categoria_id, PDO::PARAM_INT);
        }

        // Ejecutar la consulta
        $stmt->execute();

        // Obtener los datos de los productos
        $productosData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Crear el array de objetos Producto
        $productos = [];
        foreach ($productosData as $data) {
            $producto = new Producto($this->pdo);  // Asegúrate de que el constructor esté correctamente configurado
            $producto->setIdProducto($data['id_producto']);
            $producto->setNombre($data['nombre']);
            $producto->setDescripcion($data['descripcion']);
            $producto->setDescripcionCorta($data['descripcion_corta']);
            $producto->setPrecioPVP($data['precioPVP']);
            $producto->setStock($data['stock']);
            $producto->setIdCategoria($data['id_categoria']);
            $producto->setImagen($data['imagen']);
            // Asignar calificación promedio al producto
            $producto->setCalificacionPromedio($data['promedio_calificacion']); // Asegúrate de que este campo exista en la base de datos
            $productos[] = $producto;
        }

        return $productos;

    } catch (Exception $e) {
        // Lanzar un error detallado si ocurre un problema
        throw new Exception('Error al obtener los productos: ' . $e->getMessage());
    }
}


public function obtenerProductosHtml($productos) {
    try {
        $html = ''; // Variable para almacenar el HTML generado

        // Generar HTML para cada producto
        foreach ($productos as $producto) {
            $html .= '<div class="producto" data-id="' . htmlspecialchars($producto->getIdProducto(), ENT_QUOTES, 'UTF-8') . '">';
            $html .= '<a href="detalleProducto?id_producto=' . htmlspecialchars($producto->getIdProducto(), ENT_QUOTES, 'UTF-8') . '">';
            $html .= '<img src="/productos/' . htmlspecialchars($producto->getImagen(), ENT_QUOTES, 'UTF-8') . '" alt="Imagen de ' . htmlspecialchars($producto->getNombre(), ENT_QUOTES, 'UTF-8') . '">';
            $html .= '<h4>' . htmlspecialchars($producto->getNombre(), ENT_QUOTES, 'UTF-8') . '</h4>';
            $html .= '<p>' . htmlspecialchars($producto->getPrecioPVP(), ENT_QUOTES, 'UTF-8') . ' €</p>';
            // Mostrar la calificación promedio
            $html .= '<p><strong>Calificación Promedio:</strong> ' . 
                      (isset($producto->calificacion_promedio) ? number_format($producto->getCalificacionPromedio(), 1) : 'No disponible') . 
                      ' estrellas</p>';
            $html .= '</a></div>';
        }

        return $html; // Retornar el HTML generado
    } catch (Exception $e) {
        return '<p>Error al cargar los productos: ' . $e->getMessage() . '</p>';
    }
} */


public function obtenerResenasPorProducto($idProducto) {
    $sql = "SELECT r.id_resena, r.comentario, r.calificacion, r.fecha_resena, r.id_usuario, u.nombre
            FROM Resenas r
            JOIN Usuarios u ON r.id_usuario = u.id_usuario
            WHERE r.id_producto = :id_producto";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindParam(':id_producto', $idProducto, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public function obtenerProductoPorId($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM Productos WHERE id_producto = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($data) {
            $producto = new Producto($this->pdo);
            $producto->setIdProducto($data['id_producto']);
            $producto->setNombre($data['nombre']);
            $producto->setDescripcion($data['descripcion']);
            $producto->setDescripcionCorta($data['descripcion_corta']);
            $producto->setPrecioPVP($data['precioPVP']);
            $producto->setStock($data['stock']);
            $producto->setIdCategoria($data['id_categoria']);
            $producto->setImagen($data['imagen']);
    
            return $producto;
        } else {
            return null;
        }
    }



public function obtenerCalificacionPromedio($idProducto) {
    $sql = "SELECT AVG(calificacion) AS promedio 
            FROM Resenas 
            WHERE id_producto = :id_producto";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindParam(':id_producto', $idProducto, PDO::PARAM_INT);
    $stmt->execute();
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    return $resultado['promedio'] ? round($resultado['promedio'], 1) : 0; // Retorna el promedio redondeado a un decimal o 0 si no hay reseñas
}
    
public function calcularYActualizarPromedioCalificacion($idProducto) {
    try {
        // Obtener el promedio de las calificaciones
        $stmt = $this->pdo->prepare("SELECT AVG(calificacion) AS promedio 
                                     FROM Resenas 
                                     WHERE id_producto = :id_producto");
        $stmt->bindParam(':id_producto', $idProducto, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        $promedio = $resultado['promedio'] ? round($resultado['promedio'], 1) : 0; // Redondear a 1 decimal o 0 si no hay reseñas

        // Actualizar el promedio_calificacion en la tabla Productos
        $updateStmt = $this->pdo->prepare("UPDATE Productos 
                                          SET promedio_calificacion = :promedio
                                          WHERE id_producto = :id_producto");
        $updateStmt->bindParam(':promedio', $promedio, PDO::PARAM_STR);
        $updateStmt->bindParam(':id_producto', $idProducto, PDO::PARAM_INT);
        $updateStmt->execute();

        return $promedio;
    } catch (Exception $e) {
        throw new Exception('Error al calcular y actualizar el promedio: ' . $e->getMessage());
    }
}
    public function añadirProducto($data) {
        $stmt = $this->pdo->prepare("INSERT INTO productos (nombre, descripcion, precio, categoria_id) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$data['nombre'], $data['descripcion'], $data['precio'], $data['categoria_id']]);
    }
    
    public function editarProducto($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, categoria_id = ? WHERE id_producto = ?");
        return $stmt->execute([$data['nombre'], $data['descripcion'], $data['precio'], $data['categoria_id'], $id]);
    }
    
    public function buscarPorTermino($termino) {
        $sql = "SELECT * FROM productos WHERE nombre LIKE :termino OR descripcion LIKE :termino";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':termino' => "%$termino%"]); // El uso de '%' permite la búsqueda parcial
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Crear un array de objetos Producto
        $productoObjs = [];
        foreach ($productos as $data) {
            $producto = new Producto($this->pdo);
            $producto->setIdProducto($data['id_producto']);
            $producto->setNombre($data['nombre']);
            $producto->setDescripcion($data['descripcion']);
            $producto->setDescripcionCorta($data['descripcion_corta']);
            $producto->setPrecioPVP($data['precioPVP']);
            $producto->setStock($data['stock']);
            $producto->setIdCategoria($data['id_categoria']);
            $producto->setImagen($data['imagen']);
    
            $productoObjs[] = $producto;
        }
    
        return $productoObjs;
    }
    
    public function obtenerVentasPorProducto() {
        $stmt = $this->pdo->prepare("
            SELECT p.nombre, SUM(pv.cantidad) AS total_vendido
            FROM productos p
            JOIN pedidos_vendidos pv ON p.id_producto = pv.producto_id
            GROUP BY p.id_producto
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function eliminarProducto($id) {
        $stmt = $this->pdo->prepare("DELETE FROM productos WHERE id_producto = ?");
        if ($stmt->execute([$id])) {
            return true; // El producto fue eliminado
        }
        return false; // Hubo un error al eliminar el producto
    }
    
    public function filtrar($filtros) {
        $sql = "SELECT * FROM productos WHERE 1=1"; // 1=1 es una técnica para simplificar la concatenación de condiciones
        $params = [];
    
        if (isset($filtros['categoria'])) {
            $sql .= " AND categoria_id = :categoria";
            $params[':categoria'] = $filtros['categoria'];
        }
    
        if (isset($filtros['precio_min'])) {
            $sql .= " AND precio >= :precio_min";
            $params[':precio_min'] = $filtros['precio_min'];
        }
    
        if (isset($filtros['precio_max'])) {
            $sql .= " AND precio <= :precio_max";
            $params[':precio_max'] = $filtros['precio_max'];
        }
    
        if (isset($filtros['talla'])) {
            $sql .= " AND talla_id = :talla";
            $params[':talla'] = $filtros['talla'];
        }
    
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Convertir cada producto en un objeto Producto
        $productoObjs = [];
        foreach ($productos as $data) {
            $producto = new Producto($this->pdo);
            $producto->setIdProducto($data['id_producto']);
            $producto->setNombre($data['nombre']);
            $producto->setDescripcion($data['descripcion']);
            $producto->setDescripcionCorta($data['descripcion_corta']);
            $producto->setPrecioPVP($data['precioPVP']);
            $producto->setStock($data['stock']);
            $producto->setIdCategoria($data['id_categoria']);
            $producto->setImagen($data['imagen']);
    
            $productoObjs[] = $producto;
        }
    
        return $productoObjs;
    }
    
  /*   public function registrarProducto($nombre, $descripcion, $descripcion_corta, $precioPVP, $stock, $id_categoria, $imagen) {
        $stmt = $this->pdo->prepare("
            INSERT INTO Productos (nombre, descripcion, descripcion_corta, precioPVP, stock, id_categoria, imagen)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        try {
            $stmt->execute([$nombre, $descripcion, $descripcion_corta, $precioPVP, $stock, $id_categoria, $imagen]);
            $idProducto = $this->pdo->lastInsertId();
    
            $producto = new Producto();
            $producto->setIdProducto($idProducto);
            $producto->setNombre($nombre);
            $producto->setDescripcion($descripcion);
            $producto->setDescripcionCorta($descripcion_corta);
            $producto->setPrecioPVP($precioPVP);
            $producto->setStock($stock);
            $producto->setIdCategoria($id_categoria);
            $producto->setImagen($imagen);
    
            return $producto;
        } catch (PDOException $e) {
            throw new Exception('Error al registrar el producto: ' . $e->getMessage());
        }
    } */
    
     public function registrarProducto($nombre, $descripcion, $descripcion_corta, $precioPVP, $stock, $id_categoria, $imagen) {
        $stmt = $this->pdo->prepare("
            INSERT INTO Productos (nombre, descripcion, descripcion_corta, precioPVP, stock, id_categoria, imagen)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        try {
            $stmt->execute([$nombre, $descripcion, $descripcion_corta, $precioPVP, $stock, $id_categoria, $imagen]);
        } catch (PDOException $e) {
            throw new Exception('Error al registrar el producto: ' . $e->getMessage());
        }
    } 
    
    
    public function actualizarProducto($id_producto, $nombre, $descripcion, $descripcion_corta, $precioPVP, $stock, $id_categoria, $imagen) {
        try {
            $stmt = $this->pdo->prepare("UPDATE productos SET nombre = ?, descripcion = ?, descripcion_corta = ?, precioPVP = ?, stock = ?, id_categoria = ?, imagen = ? WHERE id_producto = ?");
            return $stmt->execute([$nombre, $descripcion, $descripcion_corta, $precioPVP, $stock, $id_categoria, $imagen, $id_producto]);
        } catch (PDOException $e) {
            throw new Exception('Error al actualizar el producto: ' . $e->getMessage());
        }
    }

    public function obtenerProducto($id_producto) {
        $stmt = $this->pdo->prepare("SELECT * FROM Productos WHERE id_producto = ?");
        $stmt->execute([$id_producto]);
        return $stmt->fetch(PDO::FETCH_ASSOC); // Devuelve un solo producto
    }
}    