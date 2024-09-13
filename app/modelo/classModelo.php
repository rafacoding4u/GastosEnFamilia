<?php

class GastosModelo {

    private $conexion;

    public function __construct() {
        try {
            $dsn = "mysql:host=localhost;dbname=gastosencasa_bd"; 
            $usuario = "root"; 
            $contrasenya = ""; 
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

    // -------------------------------
    // Métodos relacionados con refranes
    // -------------------------------
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

    public function registrarEnvioRefran($idRefran, $idUser, $momento) {
        $sql = "INSERT INTO envio_refranes (idRefran, idUser, fecha_envio, momento) 
                VALUES (:idRefran, :idUser, NOW(), :momento)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idRefran', $idRefran, PDO::PARAM_INT);
        $stmt->bindValue(':idUser', $idUser, PDO::PARAM_INT);
        $stmt->bindValue(':momento', $momento, PDO::PARAM_STR);
        return $stmt->execute();
    }

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
    // Métodos relacionados con usuarios
    // -------------------------------

    public function consultarUsuario($alias) {
        $sql = "SELECT * FROM usuarios WHERE alias = :alias";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':alias', $alias, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function existeUsuario($alias) {
        $sql = "SELECT COUNT(*) FROM usuarios WHERE alias = :alias";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':alias', $alias, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function insertarUsuario($nombre, $apellido, $nombreUsuario, $contrasenya, $nivel_usuario, $fecha_nacimiento, $email, $telefono) {
        $sql = "INSERT INTO usuarios (nombre, apellido, alias, contrasenya, nivel_usuario, fecha_nacimiento, email, telefono) 
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

    public function obtenerUsuarios() {
        $sql = "SELECT * FROM usuarios";
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function eliminarUsuario($idUsuario) {
        $sql = "DELETE FROM usuarios WHERE idUser = :idUsuario";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // -------------------------------
    // Métodos relacionados con los ingresos y gastos
    // -------------------------------

    public function obtenerTotalIngresos($idUsuario) {
        $sql = "SELECT SUM(importe) AS totalIngresos FROM ingresos WHERE idUser = :idUsuario";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function obtenerTotalGastos($idUsuario) {
        $sql = "SELECT SUM(importe) AS totalGastos FROM gastos WHERE idUser = :idUsuario";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function obtenerGastosPorUsuario($idUsuario) {
        $sql = "SELECT g.idGasto, g.importe, g.concepto, g.fecha, c.nombreCategoria, g.origen
                FROM gastos g
                JOIN categorias_gastos c ON g.idCategoria = c.idCategoria
                WHERE g.idUser = :idUsuario
                ORDER BY g.fecha DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerIngresosPorUsuario($idUsuario) {
        $sql = "SELECT i.idIngreso, i.importe, i.concepto, i.fecha, c.nombreCategoria, i.origen
                FROM ingresos i
                JOIN categorias_ingresos c ON i.idCategoria = c.idCategoria
                WHERE i.idUser = :idUsuario
                ORDER BY i.fecha DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerSituacionFinanciera($idUsuario) {
        $sql = "SELECT SUM(i.importe) AS totalIngresos, SUM(g.importe) AS totalGastos
                FROM ingresos i
                LEFT JOIN gastos g ON i.idUser = g.idUser
                WHERE i.idUser = :idUsuario";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insertarGasto($idUsuario, $importe, $idCategoria, $concepto, $origen) {
        $sql = "INSERT INTO gastos (idUser, importe, idCategoria, concepto, origen, fecha) 
                VALUES (:idUsuario, :importe, :idCategoria, :concepto, :origen, NOW())";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->bindValue(':importe', $importe, PDO::PARAM_STR);
        $stmt->bindValue(':idCategoria', $idCategoria, PDO::PARAM_INT);
        $stmt->bindValue(':concepto', $concepto, PDO::PARAM_STR);
        $stmt->bindValue(':origen', $origen, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function insertarIngreso($idUsuario, $importe, $idCategoria, $concepto, $origen) {
        $sql = "INSERT INTO ingresos (idUser, importe, idCategoria, concepto, origen, fecha) 
                VALUES (:idUsuario, :importe, :idCategoria, :concepto, :origen, NOW())";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->bindValue(':importe', $importe, PDO::PARAM_STR);
        $stmt->bindValue(':idCategoria', $idCategoria, PDO::PARAM_INT);
        $stmt->bindValue(':concepto', $concepto, PDO::PARAM_STR);
        $stmt->bindValue(':origen', $origen, PDO::PARAM_STR);
        return $stmt->execute();
    }

    // -------------------------------
    // Métodos relacionados con categorías
    // -------------------------------

    public function obtenerCategoriasGastos() {
        $sql = "SELECT * FROM categorias_gastos";
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerCategoriasIngresos() {
        $sql = "SELECT * FROM categorias_ingresos";
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
