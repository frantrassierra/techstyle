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
    public $calificacion_promedio;

    // Otros métodos y propiedades...

    public function setCalificacionPromedio($calificacion) {
        $this->calificacion_promedio = $calificacion;
    }

    public function getCalificacionPromedio() {
        return $this->calificacion_promedio;
    }
    public function __construct($pdo) {
        $this->pdo = $pdo;
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

    
// Producto.php

public function eliminarProductoDelCarrito($idUsuario, $idProducto) {
    try {
        // Consultamos si el producto existe en el carrito del usuario con el estado 'en_carrito'
        $query = "
            SELECT * 
            FROM Carrito_Registro_Pedido 
            WHERE id_usuario = :id_usuario 
            AND codigoProducto = :id_producto 
            AND estado = 'en_carrito'
            LIMIT 1  -- Aseguramos que solo se devuelva un producto
        ";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id_usuario', $idUsuario, PDO::PARAM_INT);
        $stmt->bindParam(':id_producto', $idProducto, PDO::PARAM_INT);
        $stmt->execute();

        // Verificamos si el producto está en el carrito del usuario
        if ($stmt->rowCount() > 0) {
            // El producto está en el carrito, lo eliminamos
            $deleteQuery = "
                DELETE FROM Carrito_Registro_Pedido
                WHERE id_usuario = :id_usuario
                AND codigoProducto = :id_producto
                AND estado = 'en_carrito'
                LIMIT 1  -- Limitar la eliminación a solo una fila
            ";

            $deleteStmt = $this->pdo->prepare($deleteQuery);
            $deleteStmt->bindParam(':id_usuario', $idUsuario, PDO::PARAM_INT);
            $deleteStmt->bindParam(':id_producto', $idProducto, PDO::PARAM_INT);
            $deleteStmt->execute();

            return ['success' => true, 'message' => 'Producto eliminado del carrito'];
        } else {
            return ['success' => false, 'message' => 'Producto no encontrado en el carrito'];
        }
    } catch (PDOException $e) {
        throw new Exception("Error al eliminar el producto: " . $e->getMessage());
    }
}


    //AGREGAR AL CARRITO
    public function agregarProducto($idProducto, $idTalla, $cantidad, $precioUnitario, $idUsuario) {
        try {
            // Calcular el precio total
            $precioTotal = $cantidad * $precioUnitario;
    
            // Insertar el producto en el carrito
            $stmt = $this->pdo->prepare("
                INSERT INTO Carrito_Registro_Pedido 
                (idPedido, codigoProducto, codigoTalla, cantidad, precio_unitario, precio_total, id_usuario)
                VALUES (NULL, :codigoProducto, :codigoTalla, :cantidad, :precioUnitario, :precioTotal, :idUsuario)
            ");
            $stmt->execute([
                ':codigoProducto' => $idProducto,
                ':codigoTalla' => $idTalla,
                ':cantidad' => $cantidad,
                ':precioUnitario' => $precioUnitario,
                ':precioTotal' => $precioTotal,
                ':idUsuario' => $idUsuario
            ]);
    
            return ['success' => true, 'message' => 'Producto agregado al carrito exitosamente.'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error al agregar el producto al carrito: ' . $e->getMessage()];
        }
    }
    
    
    public function actualizarStock($idProducto, $nuevoStock) {
        $sql = "UPDATE Productos SET stock = :nuevoStock WHERE id_producto = :idProducto";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':nuevoStock' => $nuevoStock,
            ':idProducto' => $idProducto
        ]);
    
        // Obtener el producto actualizado y devolverlo
        return $this->obtenerProductoPorId($idProducto); 
    }
   
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

        // Verificar los resultados de la consulta
        if (count($productosData) === 0) {
            throw new Exception('No se encontraron productos para esta categoría o popularidad.');
        }

        // Crear el array de objetos Producto
        $productos = [];
        foreach ($productosData as $data) {
            // Diagnóstico: Verifica si ya existe un producto con el mismo id
            if (isset($productos[$data['id_producto']])) {
                throw new Exception('Producto duplicado detectado en la base de datos: ' . $data['id_producto']);
            }

            $producto = new Producto($this->pdo);  // Asegúrate de que el constructor esté correctamente configurado
            $producto->setIdProducto($data['id_producto']);
            $producto->setNombre($data['nombre']);
            $producto->setDescripcion($data['descripcion']);
            $producto->setDescripcionCorta($data['descripcion_corta']);
            $producto->setPrecioPVP($data['precioPVP']);
            $producto->setStock($data['stock']);
            $producto->setIdCategoria($data['id_categoria']);
            $producto->setImagen($data['imagen']);

            // Agregar el producto al array
            $productos[] = $producto;
        }

        // Devolver los productos
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
            $html .= '<div class="box" data-id="' . htmlspecialchars($producto->getIdProducto(), ENT_QUOTES, 'UTF-8') . '">';
            $html .= '<div class="box-content">';
            $html .= '<div class="img-box">';
            $html .= '<img src="productos/' . htmlspecialchars($producto->getImagen(), ENT_QUOTES, 'UTF-8') . '" alt="Imagen de ' . htmlspecialchars($producto->getNombre(), ENT_QUOTES, 'UTF-8') . '">';
            $html .= '</div>';
            $html .= '<div class="detail-box">';
            $html .= '<div class="text">';
            $html .= '<h6>' . htmlspecialchars($producto->getNombre(), ENT_QUOTES, 'UTF-8') . '</h6>';
            $html .= '<h5>' . htmlspecialchars($producto->getPrecioPVP(), ENT_QUOTES, 'UTF-8') . '<span>€</span></h5>';
            $html .= '</div>';
            $html .= '<div class="like">';
            $html .= '<div class="star_container">';
            // Generar estrellas según la calificación promedio
            for ($i = 0; $i < 5; $i++) {
                $html .= '<i class="fa fa-star' . ($i < round($producto->calificacion_promedio) ? '' : '-o') . '" aria-hidden="true"></i>';
            }
            $html .= '</div>'; // Cierra star_container
            $html .= '</div>'; // Cierra like
            $html .= '</div>'; // Cierra detail-box
            $html .= '</div>'; // Cierra box-content
            $html .= '<div class="btn-box">';
            $html .= '<a href="detalleProducto?id_producto=' . htmlspecialchars($producto->getIdProducto(), ENT_QUOTES, 'UTF-8') . '">Ver detalles</a>';
            $html .= '</div>';
            $html .= '</div>'; // Cierra box
        }

        return $html; // Retornar el HTML generado
    } catch (Exception $e) {
        return '<p>Error al cargar los productos: ' . $e->getMessage() . '</p>';
    }
}


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
        $stmt = $this->pdo->prepare("INSERT INTO Productos (nombre, descripcion, precio, categoria_id) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$data['nombre'], $data['descripcion'], $data['precio'], $data['categoria_id']]);
    }
    
    public function editarProducto($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE Productos SET nombre = ?, descripcion = ?, precio = ?, categoria_id = ? WHERE id_producto = ?");
        return $stmt->execute([$data['nombre'], $data['descripcion'], $data['precio'], $data['categoria_id'], $id]);
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
    
    public function actualizarProducto($id, $nombre, $descripcion, $descripcion_corta, $precioPVP, $stock, $id_categoria, $imagen) {
        $sql = "UPDATE Productos 
                SET nombre = ?, descripcion = ?, descripcion_corta = ?, precioPVP = ?, stock = ?, id_categoria = ?";
        
        // Agregar imagen solo si se proporcionó
        if ($imagen) {
            $sql .= ", imagen = ?";
            $params = [$nombre, $descripcion, $descripcion_corta, $precioPVP, $stock, $id_categoria, $imagen, $id];
        } else {
            $params = [$nombre, $descripcion, $descripcion_corta, $precioPVP, $stock, $id_categoria, $id];
        }
        $sql .= " WHERE id_producto = ?";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
    }
    
    
    public function filtrar($filtros) {
        $sql = "SELECT * FROM Productos WHERE 1=1"; // 1=1 es una técnica para simplificar la concatenación de condiciones
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
    
    public function obtenerProductosListar() {
        $stmt = $this->pdo->query("
            SELECT p.*, c.nombre AS nombre_categoria 
            FROM Productos p
            JOIN Categorias c ON p.id_categoria = c.id_categoria
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


   
    public function obtenerProducto($id_producto) {
        // Preparar la consulta SQL
        $stmt = $this->pdo->prepare("SELECT * FROM Productos WHERE id_producto = ?");
        $stmt->execute([$id_producto]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($producto) {
            return $producto;
        } else {
            throw new Exception("Producto no encontrado.");
        }
    }
    
    public function eliminarProducto($id_producto) {
        // Verificar si el administrador está logueado
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== "admin") {
            $_SESSION['error'] = "No tienes permisos para realizar esta acción. Por favor, inicia sesión como administrador.";
            header('Location: login');
            exit();
        }
    
        try {
            // Verificar si el producto tiene reseñas asociadas
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM Resenas WHERE id_producto = ?");
            $stmt->execute([$id_producto]);
            $reseñas_count = $stmt->fetchColumn();
    
            if ($reseñas_count > 0) {
                // Eliminar las reseñas asociadas al producto
                $stmt = $this->pdo->prepare("DELETE FROM Resenas WHERE id_producto = ?");
                $stmt->execute([$id_producto]);
                $_SESSION['success'] = "Reseñas asociadas eliminadas.";
            }
    
            // Eliminar el producto
            $stmt = $this->pdo->prepare("DELETE FROM Productos WHERE id_producto = ?");
            $resultado = $stmt->execute([$id_producto]);
    
            if ($resultado) {
                $_SESSION['success'] = "Producto y sus reseñas eliminados con éxito.";
            } else {
                $_SESSION['error'] = "El producto no existe o no se pudo eliminar.";
            }
    
        } catch (Exception $e) {
            $_SESSION['error'] = "Error al eliminar el producto: " . $e->getMessage();
        }
    
        // Redirigir a la lista de productos
        header('Location: listarProductos');
        exit();
    }
    public function obtenerProductoPorIdCrud($id_producto) {
        $stmt = $this->pdo->prepare("SELECT * FROM Productos WHERE id_producto = ?");
        $stmt->execute([$id_producto]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function actualizarProductoCrud($id_producto, $nombre, $descripcion, $descripcion_corta, $precioPVP, $stock, $id_categoria, $imagen) {
        // Actualizar el producto en la base de datos
        $stmt = $this->pdo->prepare("UPDATE Productos SET nombre = ?, descripcion = ?, descripcion_corta = ?, precioPVP = ?, stock = ?, id_categoria = ?, imagen = ? WHERE id_producto = ?");
        $resultado = $stmt->execute([$nombre, $descripcion, $descripcion_corta, $precioPVP, $stock, $id_categoria, $imagen, $id_producto]);
        
        return $resultado;
    }
    
  
    public function listarProductosMasValorados() {
        // Suponiendo que tienes una función que ejecuta la consulta
        $query = "SELECT * FROM Productos ORDER BY promedio_calificacion DESC LIMIT 3";
        $productos = $this->pdo->query($query);
        
        return $productos;
    }
    
    public function obtenerProductosMasValorados($limit = 3) {
        try {
            // Consulta SQL para obtener los productos más valorados, limitados a $limit productos
            $query = "SELECT * FROM Productos ORDER BY promedio_calificacion DESC LIMIT :limit";
    
            // Preparar la consulta
            $stmt = $this->pdo->prepare($query);
    
            // Enlazar el parámetro de límite
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    
            // Ejecutar la consulta
            $stmt->execute();
    
            // Obtener los datos de los productos
            $productosData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Verificar los resultados de la consulta
            if (count($productosData) === 0) {
                throw new Exception('No se encontraron productos más valorados.');
            }
    
            // Crear el array de objetos Producto
            $productos = [];
            foreach ($productosData as $data) {
                $producto = new Producto($this->pdo); // Asegúrate de que el constructor esté correctamente configurado
                $producto->setIdProducto($data['id_producto']);
                $producto->setNombre($data['nombre']);
                $producto->setDescripcion($data['descripcion']);
                $producto->setDescripcionCorta($data['descripcion_corta']);
                $producto->setPrecioPVP($data['precioPVP']);
                $producto->setStock($data['stock']);
                $producto->setIdCategoria($data['id_categoria']);
                $producto->setImagen($data['imagen']);
    
                // Agregar el producto al array
                $productos[] = $producto;
            }
    
            // Devolver los productos
            return $productos;
    
        } catch (Exception $e) {
            throw new Exception('Error al obtener los productos más valorados: ' . $e->getMessage());
        }
    }
    
  
    
}    