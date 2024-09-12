<?php

class GastosModelo {

    private $conexion;

    public function __construct() {
        try {
            $dsn = "mysql:host=localhost;dbname=gastosencasa_bd"; // Asegúrate de que el nombre de la base de datos sea correcto
            $usuario = "root"; // Ajusta el usuario si es necesario
            $contrasenya = ""; // Ajusta la contraseña si es necesario
            $this->conexion = new PDO($dsn, $usuario, $contrasenya);
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conexion->exec("SET NAMES 'utf8'");
        } catch (PDOException $e) {
            echo "Error de conexión a la base de datos: " . $e->getMessage();
            exit();
        }
    }

    public function pruebaConexion() {
        try {
            $stmt = $this->conexion->query("SELECT 1");
            return $stmt !== false;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    // Métodos adicionales relacionados con los refranes y usuarios...// -------------------------------
    // Métodos relacionados con refranes
    // -------------------------------

    /**
     * Obtener un refrán que no se haya usado en los últimos 365 días.
     */
    public function obtenerRefranNoUsado() {
        $sql = "SELECT * FROM refranes 
                WHERE idRefran NOT IN (
                    SELECT idRefran FROM envio_refranes 
                    WHERE fecha_envio > DATE_SUB(NOW(), INTERVAL 365 DAY)
                ) 
                LIMIT 1";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Registrar el envío de un refrán.
     * 
     * @param int $idRefran ID del refrán enviado.
     * @param int $idUser ID del usuario que recibe el refrán.
     * @param string $momento Momento del envío (puede ser 'mañana', 'tarde', etc.).
     */
    public function registrarEnvioRefran($idRefran, $idUser, $momento) {
        $sql = "INSERT INTO envio_refranes (idRefran, idUser, fecha_envio, momento) 
                VALUES (:idRefran, :idUser, NOW(), :momento)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idRefran', $idRefran, PDO::PARAM_INT);
        $stmt->bindValue(':idUser', $idUser, PDO::PARAM_INT);
        $stmt->bindValue(':momento', $momento, PDO::PARAM_STR);
        return $stmt->execute();
    }

    /**
     * Listar los refranes enviados a un usuario específico.
     * 
     * @param int $idUser ID del usuario.
     * @return array Listado de refranes enviados, con fecha y momento.
     */
    public function listarRefranesEnviados($idUser) {
        $sql = "SELECT r.refran, e.fecha_envio, e.momento 
                FROM refranes r 
                JOIN envio_refranes e ON r.idRefran = e.idRefran 
                WHERE e.idUser = :idUser";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idUser', $idUser, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // -------------------------------
    // Otros métodos relacionados con la aplicación
    // -------------------------------

    /**
     * Método para consultar un usuario por nombre de usuario.
     * 
     * @param string $nombreUsuario Nombre de usuario.
     * @return array Datos del usuario.
     */
    public function consultarUsuario($alias) {
        echo "DEBUG: Alias en consulta: " . $alias . "<br>";
        $sql = "SELECT * FROM usuarios WHERE alias = :alias";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':alias', $alias, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
        

    /**
     * Método para verificar si un nombre de usuario ya existe en la base de datos.
     * 
     * @param string $nombreUsuario Nombre de usuario.
     * @return bool True si el usuario existe, False si no.
     */
    public function existeUsuario($alias) {
        $sql = "SELECT COUNT(*) FROM usuarios WHERE alias = :alias";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':alias', $alias, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
    
    

    /**
     * Método para insertar un nuevo usuario en la base de datos.
     * 
     * @param string $nombre Nombre del usuario.
     * @param string $apellido Apellido del usuario.
     * @param string $nombreUsuario Nombre de usuario.
     * @param string $contrasenya Contraseña del usuario (encriptada).
     * @param string $nivel_usuario Nivel de usuario (admin, usuario, etc.).
     * @param string $fecha_nacimiento Fecha de nacimiento del usuario.
     * @param string $email Correo electrónico del usuario.
     * @param string $telefono Número de teléfono del usuario.
     * @return bool True si la inserción fue exitosa, False en caso contrario.
     */
    public function insertarUsuario($nombre, $apellido, $nombreUsuario, $contrasenya, $nivel_usuario, $fecha_nacimiento, $email, $telefono) {
        $sql = "INSERT INTO usuarios (nombre, apellido, nombreUsuario, contrasenya, nivel_usuario, fecha_nacimiento, email, telefono) 
                VALUES (:nombre, :apellido, :nombreUsuario, :contrasenya, :nivel_usuario, :fecha_nacimiento, :email, :telefono)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindValue(':apellido', $apellido, PDO::PARAM_STR);
        $stmt->bindValue(':nombreUsuario', $nombreUsuario, PDO::PARAM_STR);
        $stmt->bindValue(':contrasenya', $contrasenya, PDO::PARAM_STR);
        $stmt->bindValue(':nivel_usuario', $nivel_usuario, PDO::PARAM_STR);
        $stmt->bindValue(':fecha_nacimiento', $fecha_nacimiento, PDO::PARAM_STR);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->bindValue(':telefono', $telefono, PDO::PARAM_STR);
        return $stmt->execute();
    }

    // Aquí se pueden añadir otros métodos según las necesidades de la aplicación

}
?>

    

    
