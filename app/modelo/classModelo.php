<?php

class GastosModelo {

    private $conexion;

    // Constructor donde se inicializa la conexión a la base de datos
    public function __construct() {
        try {
            $dsn = "mysql:host=localhost;dbname=gastos_familia"; // Cambia los valores según tu configuración
            $usuario = "root"; // Cambia esto según tu usuario
            $contrasenya = ""; // Cambia esto según tu contraseña
            $this->conexion = new PDO($dsn, $usuario, $contrasenya);
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conexion->exec("SET NAMES 'utf8'"); // Configuración de codificación
        } catch (PDOException $e) {
            echo "Error de conexión a la base de datos: " . $e->getMessage();
            exit();
        }
    }

    // -------------------------------
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

    // Aquí irían otros métodos relacionados con tu aplicación

}
