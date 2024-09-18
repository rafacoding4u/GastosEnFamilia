<?php

class GastosModelo
{

    private $conexion;

    public function __construct()
    {
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

    public function pruebaConexion()
    {
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

    public function consultarUsuario($alias)
    {
        $sql = "SELECT * FROM usuarios WHERE alias = :alias";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':alias', $alias, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerUsuarioPorId($idUsuario)
    {
        $sql = "SELECT * FROM usuarios WHERE idUser = :idUsuario";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function existeUsuario($alias)
    {
        $sql = "SELECT COUNT(*) FROM usuarios WHERE alias = :alias";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':alias', $alias, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function insertarUsuario($nombre, $apellido, $nombreUsuario, $contrasenya, $nivel_usuario, $fecha_nacimiento, $email, $telefono, $idGrupo = null, $idFamilia = null)
{
    $sql = "INSERT INTO usuarios (nombre, apellido, alias, contrasenya, nivel_usuario, fecha_nacimiento, email, telefono, idGrupo, idFamilia) 
            VALUES (:nombre, :apellido, :nombreUsuario, :contrasenya, :nivel_usuario, :fecha_nacimiento, :email, :telefono, :idGrupo, :idFamilia)";
    $stmt = $this->conexion->prepare($sql);
    $stmt->bindValue(':nombre', $nombre, PDO::PARAM_STR);
    $stmt->bindValue(':apellido', $apellido, PDO::PARAM_STR);
    $stmt->bindValue(':nombreUsuario', $nombreUsuario, PDO::PARAM_STR);
    $stmt->bindValue(':contrasenya', $contrasenya, PDO::PARAM_STR);
    $stmt->bindValue(':nivel_usuario', $nivel_usuario, PDO::PARAM_STR);
    $stmt->bindValue(':fecha_nacimiento', $fecha_nacimiento, PDO::PARAM_STR);
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->bindValue(':telefono', $telefono, PDO::PARAM_STR);
    $stmt->bindValue(':idGrupo', $idGrupo !== null ? $idGrupo : null, PDO::PARAM_INT);
    $stmt->bindValue(':idFamilia', $idFamilia !== null ? $idFamilia : null, PDO::PARAM_INT);
    return $stmt->execute();
}

public function obtenerUsuarios()
{
    $sql = "SELECT u.*, 
                   f.nombre_familia, 
                   g.nombre_grupo 
            FROM usuarios u
            LEFT JOIN familias f ON u.idFamilia = f.idFamilia
            LEFT JOIN grupos g ON u.idGrupo = g.idGrupo";
    $stmt = $this->conexion->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    public function eliminarUsuario($idUsuario)
    {
        $sql = "DELETE FROM usuarios WHERE idUser = :idUsuario";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function actualizarUsuario($idUsuario, $nombre, $apellido, $alias, $email, $telefono, $idFamilia = null, $idGrupo = null)
{
    $sql = "UPDATE usuarios 
            SET nombre = :nombre, apellido = :apellido, alias = :alias, email = :email, telefono = :telefono, 
                idFamilia = :idFamilia, idGrupo = :idGrupo
            WHERE idUser = :idUsuario";
    $stmt = $this->conexion->prepare($sql);
    $stmt->bindValue(':nombre', $nombre, PDO::PARAM_STR);
    $stmt->bindValue(':apellido', $apellido, PDO::PARAM_STR);
    $stmt->bindValue(':alias', $alias, PDO::PARAM_STR);
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->bindValue(':telefono', $telefono, PDO::PARAM_STR);
    $stmt->bindValue(':idFamilia', $idFamilia !== null ? $idFamilia : null, PDO::PARAM_INT);
    $stmt->bindValue(':idGrupo', $idGrupo !== null ? $idGrupo : null, PDO::PARAM_INT);
    $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
    return $stmt->execute();
}


    // -------------------------------
    // Métodos relacionados con ingresos y gastos
    // -------------------------------

    public function obtenerTotalIngresos($idUsuario)
    {
        $sql = "SELECT SUM(importe) AS totalIngresos FROM ingresos WHERE idUser = :idUsuario";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function obtenerTotalGastos($idUsuario)
    {
        $sql = "SELECT SUM(importe) AS totalGastos FROM gastos WHERE idUser = :idUsuario";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    // Obtener gastos por usuario
    public function obtenerGastosPorUsuario($idUsuario) {
        $sql = "SELECT * FROM gastos WHERE idUser = :idUsuario";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener ingresos por usuario
    public function obtenerIngresosPorUsuario($idUsuario) {
        $sql = "SELECT * FROM ingresos WHERE idUser = :idUsuario";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

     // Obtener situación financiera de un usuario específico
     public function obtenerSituacionFinanciera($idUsuario) {
        $sql = "SELECT total_gastos, total_ingresos FROM situacion WHERE idUser = :idUsuario";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener situación financiera por familia
    public function obtenerSituacionFinancieraFamilia($idFamilia) {
        $sql = "SELECT SUM(total_gastos) as totalGastos, SUM(total_ingresos) as totalIngresos 
                FROM situacion WHERE idUser IN (SELECT idUser FROM usuarios WHERE idFamilia = :idFamilia)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idFamilia', $idFamilia, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function calcularSaldoGlobalFamilia($idFamilia)
    {
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

    // Insertar gasto para un usuario
    public function insertarGasto($idUsuario, $monto, $categoria, $concepto, $origen) {
        $sql = "INSERT INTO gastos (idUser, importe, idCategoria, concepto, origen, fecha) 
                VALUES (:idUsuario, :monto, :categoria, :concepto, :origen, CURDATE())";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->bindValue(':monto', $monto, PDO::PARAM_STR);
        $stmt->bindValue(':categoria', $categoria, PDO::PARAM_INT);
        $stmt->bindValue(':concepto', $concepto, PDO::PARAM_STR);
        $stmt->bindValue(':origen', $origen, PDO::PARAM_STR);
        return $stmt->execute();
    }

    // Insertar ingreso para un usuario
    public function insertarIngreso($idUsuario, $monto, $categoria, $concepto, $origen) {
        $sql = "INSERT INTO ingresos (idUser, importe, idCategoria, concepto, origen, fecha) 
                VALUES (:idUsuario, :monto, :categoria, :concepto, :origen, CURDATE())";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->bindValue(':monto', $monto, PDO::PARAM_STR);
        $stmt->bindValue(':categoria', $categoria, PDO::PARAM_INT);
        $stmt->bindValue(':concepto', $concepto, PDO::PARAM_STR);
        $stmt->bindValue(':origen', $origen, PDO::PARAM_STR);
        return $stmt->execute();
    }

    // -------------------------------
    // Métodos relacionados con familias
    // -------------------------------

    public function obtenerFamilias()
    {
        $sql = "SELECT idFamilia, nombre_familia FROM familias";
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerFamiliaPorId($idFamilia)
    {
        $sql = "SELECT * FROM familias WHERE idFamilia = :idFamilia";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idFamilia', $idFamilia, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insertarFamilia($nombreFamilia, $passwordFamilia)
    {
        $sql = "INSERT INTO familias (nombre_familia, password) VALUES (:nombreFamilia, :password)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':nombreFamilia', $nombreFamilia, PDO::PARAM_STR);
        $stmt->bindValue(':password', password_hash($passwordFamilia, PASSWORD_DEFAULT), PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function actualizarFamilia($idFamilia, $nombreFamilia)
    {
        $sql = "UPDATE familias SET nombre_familia = :nombreFamilia WHERE idFamilia = :idFamilia";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':nombreFamilia', $nombreFamilia, PDO::PARAM_STR);
        $stmt->bindValue(':idFamilia', $idFamilia, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function eliminarFamilia($idFamilia)
    {
        $sql = "DELETE FROM familias WHERE idFamilia = :idFamilia";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idFamilia', $idFamilia, PDO::PARAM_INT);
        return $stmt->execute();
    }
    // Obtener gastos por familia
    public function obtenerGastosPorFamilia($idFamilia) {
        $sql = "SELECT * FROM gastos WHERE idUser IN (SELECT idUser FROM usuarios WHERE idFamilia = :idFamilia)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idFamilia', $idFamilia, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
     // Obtener ingresos por familia
     public function obtenerIngresosPorFamilia($idFamilia) {
        $sql = "SELECT * FROM ingresos WHERE idUser IN (SELECT idUser FROM usuarios WHERE idFamilia = :idFamilia)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idFamilia', $idFamilia, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Actualizar familia y/o grupo asignado a un usuario
    public function actualizarUsuarioFamiliaGrupo($idUsuario, $idFamilia = null, $idGrupo = null)
    {
        $sql = "UPDATE usuarios SET idFamilia = :idFamilia, idGrupo = :idGrupo WHERE idUser = :idUsuario";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->bindValue(':idFamilia', $idFamilia ? $idFamilia : null, PDO::PARAM_INT);
        $stmt->bindValue(':idGrupo', $idGrupo ? $idGrupo : null, PDO::PARAM_INT);
        return $stmt->execute();
    }


    // -------------------------------
    // Métodos relacionados con grupos
    // -------------------------------

    public function obtenerGrupos()
    {
        $sql = "SELECT idGrupo, nombre_grupo FROM grupos";
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerGrupoPorId($idGrupo)
    {
        $sql = "SELECT * FROM grupos WHERE idGrupo = :idGrupo";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idGrupo', $idGrupo, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insertarGrupo($nombreGrupo, $passwordGrupo)
    {
        $sql = "INSERT INTO grupos (nombre_grupo, password) VALUES (:nombreGrupo, :password)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':nombreGrupo', $nombreGrupo, PDO::PARAM_STR);
        $stmt->bindValue(':password', password_hash($passwordGrupo, PASSWORD_DEFAULT), PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function actualizarGrupo($idGrupo, $nombreGrupo)
    {
        $sql = "UPDATE grupos SET nombre_grupo = :nombreGrupo WHERE idGrupo = :idGrupo";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':nombreGrupo', $nombreGrupo, PDO::PARAM_STR);
        $stmt->bindValue(':idGrupo', $idGrupo, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function eliminarGrupo($idGrupo)
    {
        $sql = "DELETE FROM grupos WHERE idGrupo = :idGrupo";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idGrupo', $idGrupo, PDO::PARAM_INT);
        return $stmt->execute();
    }
    // Obtener situación financiera por grupo
    public function obtenerSituacionFinancieraGrupo($idGrupo) {
        $sql = "SELECT SUM(total_gastos) as totalGastos, SUM(total_ingresos) as totalIngresos 
                FROM situacion WHERE idUser IN (SELECT idUser FROM usuarios WHERE idGrupo = :idGrupo)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idGrupo', $idGrupo, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // Obtener gastos por grupo
    public function obtenerGastosPorGrupo($idGrupo) {
        $sql = "SELECT * FROM gastos WHERE idUser IN (SELECT idUser FROM usuarios WHERE idGrupo = :idGrupo)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idGrupo', $idGrupo, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Obtener ingresos por grupo
    public function obtenerIngresosPorGrupo($idGrupo) {
        $sql = "SELECT * FROM ingresos WHERE idUser IN (SELECT idUser FROM usuarios WHERE idGrupo = :idGrupo)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idGrupo', $idGrupo, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // -------------------------------
    // Métodos relacionados con categorías
    // ------------------------------- 
    public function obtenerCategoriasGastos()
    {
        $sql = "SELECT * FROM categorias_gastos";
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertarCategoriaGasto($nombreCategoria)
    {
        $sql = "INSERT INTO categorias_gastos (nombreCategoria) VALUES (:nombreCategoria)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':nombreCategoria', $nombreCategoria, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function actualizarCategoriaGasto($idCategoria, $nombreCategoria)
    {
        $sql = "UPDATE categorias_gastos SET nombreCategoria = :nombreCategoria WHERE idCategoria = :idCategoria";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':nombreCategoria', $nombreCategoria, PDO::PARAM_STR);
        $stmt->bindValue(':idCategoria', $idCategoria, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function eliminarCategoriaGasto($idCategoria)
    {
        $sql = "DELETE FROM categorias_gastos WHERE idCategoria = :idCategoria";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idCategoria', $idCategoria, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function obtenerCategoriasIngresos()
    {
        $sql = "SELECT * FROM categorias_ingresos";
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertarCategoriaIngreso($nombreCategoria)
    {
        $sql = "INSERT INTO categorias_ingresos (nombreCategoria) VALUES (:nombreCategoria)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':nombreCategoria', $nombreCategoria, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function actualizarCategoriaIngreso($idCategoria, $nombreCategoria)
    {
        $sql = "UPDATE categorias_ingresos SET nombreCategoria = :nombreCategoria WHERE idCategoria = :idCategoria";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':nombreCategoria', $nombreCategoria, PDO::PARAM_STR);
        $stmt->bindValue(':idCategoria', $idCategoria, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function eliminarCategoriaIngreso($idCategoria)
    {
        $sql = "DELETE FROM categorias_ingresos WHERE idCategoria = :idCategoria";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idCategoria', $idCategoria, PDO::PARAM_INT);
        return $stmt->execute();
    }
     // -------------------------------
    // Métodos relacionados con grupos
    // -------------------------------
    
      // Obtener situación financiera de todos los usuarios (superadmin)
      public function obtenerSituacionGlobal() {
        $sql = "SELECT SUM(total_gastos) as totalGastos, SUM(total_ingresos) as totalIngresos 
                FROM situacion";
        $stmt = $this->conexion->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
