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

    public function insertarUsuario($nombre, $apellido, $alias, $contrasenya, $nivel_usuario, $fecha_nacimiento, $email, $telefono, $idFamilia = null, $idGrupo = null)
{
    $sql = "INSERT INTO usuarios (nombre, apellido, alias, contrasenya, nivel_usuario, fecha_nacimiento, email, telefono, idFamilia, idGrupo) 
            VALUES (:nombre, :apellido, :alias, :contrasenya, :nivel_usuario, :fecha_nacimiento, :email, :telefono, :idFamilia, :idGrupo)";
    $stmt = $this->conexion->prepare($sql);
    $stmt->bindValue(':nombre', $nombre, PDO::PARAM_STR);
    $stmt->bindValue(':apellido', $apellido, PDO::PARAM_STR);
    $stmt->bindValue(':alias', $alias, PDO::PARAM_STR);
    $stmt->bindValue(':contrasenya', $contrasenya, PDO::PARAM_STR);
    $stmt->bindValue(':nivel_usuario', $nivel_usuario, PDO::PARAM_STR);
    $stmt->bindValue(':fecha_nacimiento', $fecha_nacimiento, PDO::PARAM_STR); // Fecha de nacimiento añadida
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->bindValue(':telefono', $telefono, PDO::PARAM_STR); // Teléfono añadido
    $stmt->bindValue(':idFamilia', $idFamilia !== null ? $idFamilia : null, PDO::PARAM_INT);
    $stmt->bindValue(':idGrupo', $idGrupo !== null ? $idGrupo : null, PDO::PARAM_INT);
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

    public function actualizarUsuario($idUsuario, $nombre, $apellido, $alias, $email, $telefono, $nivel_usuario, $idFamilia = null, $idGrupo = null)
    {
        $sql = "UPDATE usuarios 
            SET nombre = :nombre, apellido = :apellido, alias = :alias, email = :email, telefono = :telefono, 
                nivel_usuario = :nivel_usuario, idFamilia = :idFamilia, idGrupo = :idGrupo
            WHERE idUser = :idUsuario";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindValue(':apellido', $apellido, PDO::PARAM_STR);
        $stmt->bindValue(':alias', $alias, PDO::PARAM_STR);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->bindValue(':telefono', $telefono, PDO::PARAM_STR);
        $stmt->bindValue(':nivel_usuario', $nivel_usuario, PDO::PARAM_STR);
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

    // Obtener los gastos de un usuario
    public function obtenerGastosPorUsuario($idUsuario, $offset = 0, $limite = 10)
    {
        $sql = "SELECT * FROM gastos WHERE idUser = :idUsuario LIMIT :offset, :limite";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contarGastosPorUsuario($idUsuario)
    {
        $sql = "SELECT COUNT(*) FROM gastos WHERE idUser = :idUsuario";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    // Obtener los ingresos de un usuario
    public function obtenerIngresosPorUsuario($idUsuario)
    {
        $sql = "SELECT * FROM ingresos WHERE idUser = :idUsuario";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Obtener situación financiera de un usuario específico
    public function obtenerSituacionFinanciera($idUsuario)
    {
        $sql = "
        SELECT 
            SUM(CASE WHEN i.importe IS NOT NULL THEN i.importe ELSE 0 END) AS totalIngresos,
            SUM(CASE WHEN g.importe IS NOT NULL THEN g.importe ELSE 0 END) AS totalGastos,
            (SUM(CASE WHEN i.importe IS NOT NULL THEN i.importe ELSE 0 END) - SUM(CASE WHEN g.importe IS NOT NULL THEN g.importe ELSE 0 END)) AS saldo
        FROM usuarios u
        LEFT JOIN ingresos i ON u.idUser = i.idUser
        LEFT JOIN gastos g ON u.idUser = g.idUser
        WHERE u.idUser = :idUsuario";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    // Obtener la situación financiera de una familia
    public function obtenerSituacionFinancieraFamilia($idFamilia)
    {
        $sql = "
            SELECT 
                SUM(CASE WHEN i.importe IS NOT NULL THEN i.importe ELSE 0 END) AS totalIngresos,
                SUM(CASE WHEN g.importe IS NOT NULL THEN g.importe ELSE 0 END) AS totalGastos,
                (SUM(CASE WHEN i.importe IS NOT NULL THEN i.importe ELSE 0 END) - SUM(CASE WHEN g.importe IS NOT NULL THEN g.importe ELSE 0 END)) AS saldo
            FROM usuarios u
            LEFT JOIN ingresos i ON u.idUser = i.idUser
            LEFT JOIN gastos g ON u.idUser = g.idUser
            WHERE u.idFamilia = :idFamilia";

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
    // Obtener usuarios de una familia
    public function obtenerUsuariosPorFamilia($idFamilia)
    {
        $sql = "SELECT * FROM usuarios WHERE idFamilia = :idFamilia";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idFamilia', $idFamilia, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Obtener usuarios de un grupo
    public function obtenerUsuariosPorGrupo($idGrupo)
    {
        $sql = "SELECT * FROM usuarios WHERE idGrupo = :idGrupo";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idGrupo', $idGrupo, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Insertar gasto para un usuario
    public function insertarGasto($idUsuario, $monto, $categoria, $concepto, $origen)
    {
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
    public function insertarIngreso($idUsuario, $monto, $categoria, $concepto, $origen)
    {
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

    public function obtenerSituacionDeTodosLosUsuarios()
    {
        $sql = "SELECT u.idUser, u.nombre, u.apellido, 
                   IFNULL(SUM(i.importe), 0) AS totalIngresos, 
                   IFNULL(SUM(g.importe), 0) AS totalGastos,
                   (IFNULL(SUM(i.importe), 0) - IFNULL(SUM(g.importe), 0)) AS saldo
            FROM usuarios u
            LEFT JOIN ingresos i ON u.idUser = i.idUser
            LEFT JOIN gastos g ON u.idUser = g.idUser
            GROUP BY u.idUser, u.nombre, u.apellido";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    public function obtenerGastosPorFamilia($idFamilia)
    {
        $sql = "SELECT * FROM gastos WHERE idUser IN (SELECT idUser FROM usuarios WHERE idFamilia = :idFamilia)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idFamilia', $idFamilia, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Obtener ingresos por familia
    public function obtenerIngresosPorFamilia($idFamilia)
    {
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
    // Obtener la situación financiera de un grupo
    public function obtenerSituacionFinancieraGrupo($idGrupo)
    {
        $sql = "SELECT SUM(i.importe) AS totalIngresos, SUM(g.importe) AS totalGastos 
            FROM usuarios u 
            LEFT JOIN ingresos i ON u.idUser = i.idUser 
            LEFT JOIN gastos g ON u.idUser = g.idUser 
            WHERE u.idGrupo = :idGrupo";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idGrupo', $idGrupo, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // Obtener gastos por grupo
    public function obtenerGastosPorGrupo($idGrupo)
    {
        $sql = "SELECT * FROM gastos WHERE idUser IN (SELECT idUser FROM usuarios WHERE idGrupo = :idGrupo)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idGrupo', $idGrupo, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Obtener ingresos por grupo
    public function obtenerIngresosPorGrupo($idGrupo)
    {
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
    try {
        $sql = "SELECT * FROM categorias_gastos";
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error en la consulta: " . $e->getMessage();
        return [];
    }
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
    $sql = "SELECT * FROM categorias_ingresos"; // Asegúrate de que esta tabla exista en tu base de datos
    $stmt = $this->conexion->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC); // Devolver las categorías como un array asociativo
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

    public function obtenerTotalesPorUsuarioGrupo($idGrupo)
    {
        $sql = "SELECT u.idUser, u.nombre, u.apellido, 
                   SUM(i.importe) AS totalIngresos, 
                   SUM(g.importe) AS totalGastos,
                   (SUM(i.importe) - SUM(g.importe)) AS saldo
            FROM usuarios u
            LEFT JOIN ingresos i ON u.idUser = i.idUser
            LEFT JOIN gastos g ON u.idUser = g.idUser
            WHERE u.idGrupo = :idGrupo
            GROUP BY u.idUser";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idGrupo', $idGrupo, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Obtener la situación financiera global
    public function obtenerSituacionGlobal()
    {
        $sql = "
            SELECT 
                SUM(CASE WHEN i.importe IS NOT NULL THEN i.importe ELSE 0 END) AS totalIngresos,
                SUM(CASE WHEN g.importe IS NOT NULL THEN g.importe ELSE 0 END) AS totalGastos,
                (SUM(CASE WHEN i.importe IS NOT NULL THEN i.importe ELSE 0 END) - SUM(CASE WHEN g.importe IS NOT NULL THEN g.importe ELSE 0 END)) AS saldo
            FROM ingresos i
            LEFT JOIN gastos g ON i.idUser = g.idUser";

        $stmt = $this->conexion->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    // Obtener todos los gastos
    public function obtenerTodosGastos()
    {
        $sql = "SELECT * FROM gastos";
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener todos los ingresos
    public function obtenerTodosIngresos()
    {
        $sql = "SELECT * FROM ingresos";
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener un ingreso por su ID
    public function obtenerIngresoPorId($idIngreso)
    {
        $sql = "SELECT * FROM ingresos WHERE idIngreso = :idIngreso";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idIngreso', $idIngreso, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener un gasto por su ID
    public function obtenerGastoPorId($idGasto)
    {
        $sql = "SELECT * FROM gastos WHERE idGasto = :idGasto";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idGasto', $idGasto, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualizar un gasto
    public function actualizarGasto($idGasto, $concepto, $importe, $fecha, $origen, $categoria)
    {
        $sql = "UPDATE gastos 
            SET concepto = :concepto, importe = :importe, fecha = :fecha, origen = :origen, idCategoria = :categoria 
            WHERE idGasto = :idGasto";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':concepto', $concepto, PDO::PARAM_STR);
        $stmt->bindValue(':importe', $importe, PDO::PARAM_STR);
        $stmt->bindValue(':fecha', $fecha, PDO::PARAM_STR);
        $stmt->bindValue(':origen', $origen, PDO::PARAM_STR);
        $stmt->bindValue(':categoria', $categoria, PDO::PARAM_INT);
        $stmt->bindValue(':idGasto', $idGasto, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Eliminar un gasto
    public function eliminarGasto($idGasto)
    {
        $sql = "DELETE FROM gastos WHERE idGasto = :idGasto";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idGasto', $idGasto, PDO::PARAM_INT);
        return $stmt->execute();
    }
    public function obtenerGastosFiltrados($idUsuario, $fechaInicio = null, $fechaFin = null, $categoria = null, $origen = null, $offset = 0, $limite = 10)
    {
        $sql = "SELECT g.*, c.nombreCategoria 
            FROM gastos g 
            LEFT JOIN categorias_gastos c ON g.idCategoria = c.idCategoria 
            WHERE g.idUser = :idUsuario";

        // Aplicar los filtros
        if ($fechaInicio) {
            $sql .= " AND g.fecha >= :fechaInicio";
        }
        if ($fechaFin) {
            $sql .= " AND g.fecha <= :fechaFin";
        }
        if ($categoria) {
            $sql .= " AND g.idCategoria = :categoria";
        }
        if ($origen) {
            $sql .= " AND g.origen = :origen";
        }

        // Añadir límite y desplazamiento para la paginación
        $sql .= " LIMIT :offset, :limite";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
        if ($fechaInicio) {
            $stmt->bindValue(':fechaInicio', $fechaInicio, PDO::PARAM_STR);
        }
        if ($fechaFin) {
            $stmt->bindValue(':fechaFin', $fechaFin, PDO::PARAM_STR);
        }
        if ($categoria) {
            $stmt->bindValue(':categoria', $categoria, PDO::PARAM_INT);
        }
        if ($origen) {
            $stmt->bindValue(':origen', $origen, PDO::PARAM_STR);
        }
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para contar los resultados filtrados
    public function contarGastosFiltrados($idUsuario, $fechaInicio = null, $fechaFin = null, $categoria = null, $origen = null)
    {
        $sql = "SELECT COUNT(*) FROM gastos WHERE idUser = :idUsuario";

        if ($fechaInicio) {
            $sql .= " AND fecha >= :fechaInicio";
        }
        if ($fechaFin) {
            $sql .= " AND fecha <= :fechaFin";
        }
        if ($categoria) {
            $sql .= " AND idCategoria = :categoria";
        }
        if ($origen) {
            $sql .= " AND origen = :origen";
        }

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
        if ($fechaInicio) {
            $stmt->bindValue(':fechaInicio', $fechaInicio, PDO::PARAM_STR);
        }
        if ($fechaFin) {
            $stmt->bindValue(':fechaFin', $fechaFin, PDO::PARAM_STR);
        }
        if ($categoria) {
            $stmt->bindValue(':categoria', $categoria, PDO::PARAM_INT);
        }
        if ($origen) {
            $stmt->bindValue(':origen', $origen, PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    // Obtener gastos por categoría
public function obtenerGastosPorCategoria($idUsuario)
{
    $sql = "
        SELECT c.nombreCategoria, SUM(g.importe) AS total
        FROM gastos g
        LEFT JOIN categorias_gastos c ON g.idCategoria = c.idCategoria
        WHERE g.idUser = :idUsuario
        GROUP BY g.idCategoria";
    
    $stmt = $this->conexion->prepare($sql);
    $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Obtener ingresos por categoría
public function obtenerIngresosPorCategoria($idUsuario)
{
    $sql = "
        SELECT c.nombreCategoria, SUM(i.importe) AS total
        FROM ingresos i
        LEFT JOIN categorias_ingresos c ON i.idCategoria = c.idCategoria
        WHERE i.idUser = :idUsuario
        GROUP BY i.idCategoria";
    
    $stmt = $this->conexion->prepare($sql);
    $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}
