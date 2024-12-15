<?php

require_once __DIR__ . '/../../config/database.php'; // Ruta a la configuración de la base de datos

class Usuario
{
    private $pdo;
    private $id_usuario;
    private $nombre;
    private $email;
    private $contrasena;
    private $rol;

    // Constructor con PDO para la base de datos
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // Getters y setters
    public function getIdUsuario()
    {
        return $this->id_usuario;
    }

    public function setIdUsuario($id_usuario)
    {
        $this->id_usuario = $id_usuario;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getContrasena()
    {
        return $this->contrasena;
    }

    public function setContrasena($contrasena)
    {
        $this->contrasena = $contrasena;
    }

    public function getRol()
    {
        return $this->rol;
    }

    public function setRol($rol)
    {
        $this->rol = $rol;
    }

    // Método para registrar un nuevo usuario
    public function registrar($nombre, $email, $contrasena)
    {
        // Validación de datos
        if (empty($nombre) || empty($email) || empty($contrasena)) {
            throw new InvalidArgumentException('Todos los campos son obligatorios');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('El formato de email es inválido');
        }

        // Verificar si el email ya está registrado
        $stmt = $this->pdo->prepare("SELECT id_usuario FROM Usuarios WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            throw new InvalidArgumentException('El correo electrónico ya está registrado');
        }

        // Hashear la contraseña
        $hashedPassword = password_hash($contrasena, PASSWORD_DEFAULT);

        try {
            // Preparar la consulta para insertar al nuevo usuario
            $stmt = $this->pdo->prepare("INSERT INTO Usuarios (nombre, email, contrasena, rol) VALUES (?, ?, ?, 'usuario')");
            // Ejecutar la consulta con los datos del nuevo usuario
            $stmt->execute([$nombre, $email, $hashedPassword]);

            // Obtener el ID del nuevo usuario insertado
            $this->id_usuario = $this->pdo->lastInsertId();

            return $this->id_usuario; // Retornar el ID del usuario insertado
        } catch (PDOException $e) {
            // Registrar el error en el archivo de log
            error_log($e->getMessage(), 3, 'errors.log');
            return false; // O puedes manejar el error de otra manera
        }
    }


    public function login($email, $contrasena)
    {
        $sql = "SELECT * FROM Usuarios WHERE email = :email LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        // Verificar si existe el usuario
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC); // Utilizar PDO::FETCH_ASSOC para obtener un array asociativo

        if ($usuario && password_verify($contrasena, $usuario['contrasena'])) {
            // Crear una instancia de Usuario y asignar los valores
            $usuarioObj = new Usuario($this->pdo);
            $usuarioObj->setIdUsuario($usuario['id_usuario']);
            $usuarioObj->setNombre($usuario['nombre']);
            $usuarioObj->setEmail($usuario['email']);
            $usuarioObj->setRol($usuario['rol']);
            return $usuarioObj; // Devolver el objeto Usuario
        } else {
            return false; // Si el login falla
        }
    }

    // Método para obtener un usuario específico
    public function obtenerObjetoUsuarioPorId($id_usuario)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM Usuarios WHERE id_usuario = ?");
        $stmt->execute([$id_usuario]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        // Si el usuario existe, lo retornamos como un objeto Usuario
        if ($data) {
            $usuario = new Usuario($this->pdo);
            $usuario->setIdUsuario($data['id_usuario']);
            $usuario->setNombre($data['nombre']);
            $usuario->setEmail($data['email']);
            $usuario->setContrasena($data['contrasena']);
            $usuario->setRol($data['rol']);

            return $usuario;  // Retornamos el objeto Usuario
        } else {
            return null;  // Si no se encuentra el usuario, devolvemos null
        }
    }

    // Método para verificar si el email existe
    public function emailExiste($email)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM Usuarios WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetchColumn() > 0;
    }




    // Método para actualizar el perfil de un usuario
    public function actualizarPerfilBaseDatos($id_usuario, $data)
    {
        $stmt = $this->pdo->prepare("UPDATE Usuarios SET nombre = ?, email = ? WHERE id_usuario = ?");
        return $stmt->execute([$data['nombre'], $data['email'], $id_usuario]);
    }

    public function obtenerUsuarios()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM Usuarios");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener un usuario por su ID
    public function obtenerUsuarioPorId($id_usuario)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM Usuarios WHERE id_usuario = ?");
        $stmt->execute([$id_usuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Registrar un nuevo usuario
    public function registrarUsuario($nombre, $email, $contrasena, $rol = 'usuario')
    {
        $stmt = $this->pdo->prepare("INSERT INTO Usuarios (nombre, email, contrasena, rol) VALUES (?, ?, ?, ?)");
        $resultado = $stmt->execute([$nombre, $email, password_hash($contrasena, PASSWORD_BCRYPT), $rol]);
        return $resultado;
    }
    public function obtenerContrasenaActual($id_usuario)
    {
        // Obtener la contraseña actual del usuario
        $stmt = $this->pdo->prepare("SELECT contrasena FROM Usuarios WHERE id_usuario = ?");
        $stmt->execute([$id_usuario]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            return $usuario['contrasena']; // Retorna la contraseña actual
        } else {
            throw new Exception("Usuario no encontrado.");
        }
    }

    // Actualizar un usuario
    public function actualizarUsuario($id_usuario, $nombre, $email, $contrasena, $rol)
    {
        // Preparar la consulta para actualizar los datos del usuario
        if ($contrasena !== null) {
            // Si la contraseña no es null, la actualizamos
            $stmt = $this->pdo->prepare("UPDATE Usuarios SET nombre = ?, email = ?, contrasena = ?, rol = ? WHERE id_usuario = ?");
            $stmt->execute([$nombre, $email, $contrasena, $rol, $id_usuario]);
        } else {
            // Si la contraseña es null, solo actualizamos los demás campos
            $stmt = $this->pdo->prepare("UPDATE Usuarios SET nombre = ?, email = ?, rol = ? WHERE id_usuario = ?");
            $stmt->execute([$nombre, $email, $rol, $id_usuario]);
        }
    }


    // Eliminar un usuario
    public function eliminarUsuario($id_usuario)
    {
        try {
            // Iniciar la transacción para asegurarnos de que todas las eliminaciones ocurran de manera atómica.
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare("DELETE FROM Resenas WHERE id_usuario = ?");
            $stmt->execute([$id_usuario]);

            // Eliminar las referencias del usuario en otras tablas, como el carrito de pedidos
            $stmt = $this->pdo->prepare("DELETE FROM Carrito_Registro_Pedido WHERE id_usuario = ?");
            $stmt->execute([$id_usuario]);

            // Eliminar los pedidos del usuario
            $stmt = $this->pdo->prepare("DELETE FROM Pedidos WHERE id_usuario = ?");
            $stmt->execute([$id_usuario]);

            // Eliminar la dirección del usuario
            $stmt = $this->pdo->prepare("DELETE FROM Direcciones WHERE id_usuario = ?");
            $stmt->execute([$id_usuario]);

            // Finalmente, eliminar el usuario
            $stmt = $this->pdo->prepare("DELETE FROM Usuarios WHERE id_usuario = ?");
            $stmt->execute([$id_usuario]);

            // Si todo va bien, confirmamos la transacción.
            $this->pdo->commit();

            $_SESSION['success'] = 'Usuario y sus datos eliminados con éxito.';
        } catch (Exception $e) {
            // Si hay algún error, revertimos la transacción.
            $this->pdo->rollBack();

            // Guardamos el mensaje de error
            $_SESSION['error'] = 'Error al eliminar el usuario: ' . $e->getMessage();
        }

        // Redirigimos a la lista de usuarios
        header('Location: listarUsuarios');
        exit();
    }








}
