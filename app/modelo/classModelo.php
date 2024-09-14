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
    // Métodos relacionados con usuarios
    // -------------------------------

    public function consultarUsuario($alias) {
        $sql = "SELECT * FROM usuarios WHERE alias = :alias";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':alias', $alias, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerUsuarioPorId($idUsuario) {
        $sql = "SELECT * FROM usuarios WHERE idUser = :idUsuario";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
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

    public function actualizarUsuario($idUsuario, $nombre, $apellido, $alias, $email, $telefono) {
        $sql = "UPDATE usuarios 
                SET nombre = :nombre, apellido = :apellido, alias = :alias, email = :email, telefono = :telefono 
                WHERE idUser = :idUsuario";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindValue(':apellido', $apellido, PDO::PARAM_STR);
        $stmt->bindValue(':alias', $alias, PDO::PARAM_STR);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->bindValue(':telefono', $telefono, PDO::PARAM_STR);
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
        $sql = "SELECT 
                    (SELECT SUM(importe) FROM ingresos WHERE idUser = :idUsuario) AS totalIngresos, 
                    (SELECT SUM(importe) FROM gastos WHERE idUser = :idUsuario) AS totalGastos";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener la situación financiera a nivel de familia (si el usuario es superadmin)
    public function obtenerSituacionFinancieraFamilia($idFamilia) {
        $sql = "SELECT 
                    u.nombre, u.apellido, 
                    SUM(i.importe) AS totalIngresos, 
                    SUM(g.importe) AS totalGastos, 
                    (SUM(i.importe) - SUM(g.importe)) AS saldo
                FROM usuarios u
                LEFT JOIN ingresos i ON u.idUser = i.idUser
                LEFT JOIN gastos g ON u.idUser = g.idUser
                WHERE u.idFamilia = :idFamilia
                GROUP BY u.idUser";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idFamilia', $idFamilia, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function calcularSaldoGlobalFamilia($idFamilia) {
        $sql = "SELECT 
                    SUM(i.importe) - SUM(g.importe) AS saldoGlobal
                FROM usuarios u
                LEFT JOIN ingresos i ON u.idUser = i.idUser
                LEFT JOIN gastos g ON u.idUser = g.idUser
                WHERE u.idFamilia = :idFamilia";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idFamilia', $idFamilia, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
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
    // Métodos relacionados con familias
    // -------------------------------

    public function obtenerFamilias() {
        $sql = "SELECT * FROM familias";
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertarFamilia($nombreFamilia) {
        $sql = "INSERT INTO familias (nombreFamilia) VALUES (:nombreFamilia)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':nombreFamilia', $nombreFamilia, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function actualizarFamilia($idFamilia, $nombreFamilia) {
        $sql = "UPDATE familias SET nombreFamilia = :nombreFamilia WHERE idFamilia = :idFamilia";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':nombreFamilia', $nombreFamilia, PDO::PARAM_STR);
        $stmt->bindValue(':idFamilia', $idFamilia, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function eliminarFamilia($idFamilia) {
        $sql = "DELETE FROM familias WHERE idFamilia = :idFamilia";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idFamilia', $idFamilia, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // -------------------------------
    // Métodos relacionados con grupos
    // -------------------------------

    public function obtenerGrupos() {
        $sql = "SELECT * FROM grupos";
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertarGrupo($nombreGrupo) {
        $sql = "INSERT INTO grupos (nombreGrupo) VALUES (:nombreGrupo)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':nombreGrupo', $nombreGrupo, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function actualizarGrupo($idGrupo, $nombreGrupo) {
        $sql = "UPDATE grupos SET nombreGrupo = :nombreGrupo WHERE idGrupo = :idGrupo";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':nombreGrupo', $nombreGrupo, PDO::PARAM_STR);
        $stmt->bindValue(':idGrupo', $idGrupo, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function eliminarGrupo($idGrupo) {
        $sql = "DELETE FROM grupos WHERE idGrupo = :idGrupo";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idGrupo', $idGrupo, PDO::PARAM_INT);
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

    public function insertarCategoriaGasto($nombreCategoria) {
        $sql = "INSERT INTO categorias_gastos (nombreCategoria) VALUES (:nombreCategoria)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':nombreCategoria', $nombreCategoria, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function actualizarCategoriaGasto($idCategoria, $nombreCategoria) {
        $sql = "UPDATE categorias_gastos SET nombreCategoria = :nombreCategoria WHERE idCategoria = :idCategoria";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':nombreCategoria', $nombreCategoria, PDO::PARAM_STR);
        $stmt->bindValue(':idCategoria', $idCategoria, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function eliminarCategoriaGasto($idCategoria) {
        $sql = "DELETE FROM categorias_gastos WHERE idCategoria = :idCategoria";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idCategoria', $idCategoria, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function obtenerCategoriasIngresos() {
        $sql = "SELECT * FROM categorias_ingresos";
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertarCategoriaIngreso($nombreCategoria) {
        $sql = "INSERT INTO categorias_ingresos (nombreCategoria) VALUES (:nombreCategoria)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':nombreCategoria', $nombreCategoria, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function actualizarCategoriaIngreso($idCategoria, $nombreCategoria) {
        $sql = "UPDATE categorias_ingresos SET nombreCategoria = :nombreCategoria WHERE idCategoria = :idCategoria";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':nombreCategoria', $nombreCategoria, PDO::PARAM_STR);
        $stmt->bindValue(':idCategoria', $idCategoria, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function eliminarCategoriaIngreso($idCategoria) {
        $sql = "DELETE FROM categorias_ingresos WHERE idCategoria = :idCategoria";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idCategoria', $idCategoria, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

