<?php 
// app/models/Categoria.php

require_once __DIR__ . '/../../config/database.php'; // Ruta a la configuración de la base de datos

class Categoria {
    private $pdo;
    private $id_categoria;
    private $nombre;
    private $descripcion;

    // Constructor
    public function __construct($pdo, $id_categoria = null, $nombre = null, $descripcion = null) {
        $this->pdo = $pdo;
        if ($id_categoria !== null) {
            $this->id_categoria = $id_categoria;
            $this->nombre = $nombre;
            $this->descripcion = $descripcion;
        }
    }


    // Métodos getter y setter
    public function getIdCategoria() {
        return $this->id_categoria;
    }

    public function setIdCategoria($id_categoria) {
        $this->id_categoria = $id_categoria;
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

    public function obtenerCategorias() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM Categorias");
            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve las categorías como un array asociativo
        } catch (Exception $e) {
            throw new Exception('Error al obtener las categorías: ' . $e->getMessage());
        }
    }
    
    

    // Obtener una categoría por su ID
    public function obtenerPorId($id_categoria) {
        $stmt = $this->pdo->prepare("SELECT * FROM categorias WHERE id_categoria = :id_categoria");
        $stmt->bindParam(':id_categoria', $id_categoria, PDO::PARAM_INT);
        $stmt->execute();
        $categoria = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($categoria) {
            // Crear un objeto Categoria y asignar los valores
            $categoriaObj = new Categoria($this->pdo);
            $categoriaObj->setIdCategoria($categoria['id_categoria']);
            $categoriaObj->setNombre($categoria['nombre']);
            $categoriaObj->setDescripcion($categoria['descripcion']);
            return $categoriaObj;  // Retorna el objeto Categoria
        }

        return null;  // Si no se encuentra la categoría, devolvemos null
    }

 

    public function obtenerCategoriasCRUD() {
        $stmt = $this->pdo->prepare("SELECT * FROM Categorias");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Registrar una nueva categoría
    public function registrarCategoriaCrud($nombre_categoria, $descripcion) {
        $stmt = $this->pdo->prepare("INSERT INTO Categorias (nombre, descripcion) VALUES (?, ?)");
        $resultado = $stmt->execute([$nombre_categoria, $descripcion]);
        return $resultado;
    }

    // Obtener una categoría por su ID
    public function obtenerCategoriaPorIdCrud($id_categoria) {
        $stmt = $this->pdo->prepare("SELECT * FROM Categorias WHERE id_categoria = ?");
        $stmt->execute([$id_categoria]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualizar una categoría
    public function actualizarCategoriaCrud($id_categoria, $nombre_categoria, $descripcion) {
        $stmt = $this->pdo->prepare("UPDATE Categorias SET nombre = ?, descripcion = ? WHERE id_categoria = ?");
        $resultado = $stmt->execute([$nombre_categoria, $descripcion, $id_categoria]);
        return $resultado;
    }

    // Eliminar una categoría
    public function eliminarCategoria($id_categoria) {
        try {
            // Eliminar las reseñas asociadas a los productos de la categoría
            $stmt = $this->pdo->prepare("DELETE FROM Resenas WHERE id_producto IN (SELECT id_producto FROM Productos WHERE id_categoria = ?)");
            $stmt->execute([$id_categoria]);
    
            // Eliminar los productos de la categoría
            $stmt = $this->pdo->prepare("DELETE FROM Productos WHERE id_categoria = ?");
            $stmt->execute([$id_categoria]);
    
            // Ahora eliminar la categoría
            $stmt = $this->pdo->prepare("DELETE FROM Categorias WHERE id_categoria = ?");
            $resultado = $stmt->execute([$id_categoria]);
    
            if ($resultado) {
                $_SESSION['success'] = 'Categoría eliminada con éxito.';
            } else {
                $_SESSION['error'] = 'La categoría no existe o no se pudo eliminar.';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error al eliminar la categoría: ' . $e->getMessage();
        }
    
        // Redirigir a la lista de categorías
        header('Location: listarCategorias');
        exit();
    }
    
    
    
}
