<?php
require_once __DIR__ . '/../libs/Config.php';

class GastosModelo
{
    private $conexion;

    public function __construct()
    {
        try {
            // Usamos los métodos estáticos de la clase Config para obtener los valores
            $dsn = "mysql:host=" . Config::getDbHost() . ";dbname=" . Config::getDbName() . ";charset=" . Config::getDbCharset();
            $usuario = Config::getDbUser();
            $contrasenya = Config::getDbPass();

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

    public function getConexion()
    {
        return $this->conexion;
    }

    // Función para registrar el acceso en la tabla de auditoría
    public function registrarAcceso($idUser, $accion)
    {
        $sql = "INSERT INTO auditoria_accesos (idUser, accion) VALUES (:idUser, :accion)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':idUser', $idUser);
        $stmt->bindParam(':accion', $accion);
        $stmt->execute();
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
        if (empty($idFamilia) && empty($idGrupo)) {
            throw new Exception('El usuario debe estar asignado a una familia o un grupo.');
        }

        $sql = "INSERT INTO usuarios (nombre, apellido, alias, contrasenya, nivel_usuario, fecha_nacimiento, email, telefono, idFamilia, idGrupo) 
            VALUES (:nombre, :apellido, :alias, :contrasenya, :nivel_usuario, :fecha_nacimiento, :email, :telefono, :idFamilia, :idGrupo)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindValue(':apellido', $apellido, PDO::PARAM_STR);
        $stmt->bindValue(':alias', $alias, PDO::PARAM_STR);
        $stmt->bindValue(':contrasenya', $contrasenya, PDO::PARAM_STR);
        $stmt->bindValue(':nivel_usuario', $nivel_usuario, PDO::PARAM_STR);
        $stmt->bindValue(':fecha_nacimiento', $fecha_nacimiento, PDO::PARAM_STR);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->bindValue(':telefono', $telefono, PDO::PARAM_STR);
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
        // Verificar que el usuario esté asignado a una familia o grupo
        if (empty($idFamilia) && empty($idGrupo)) {
            throw new Exception('El usuario debe estar asignado a una familia o un grupo.');
        }

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

    // Obtener el total de ingresos para un usuario
    public function obtenerTotalIngresos($idUsuario)
    {
        $sql = "SELECT IFNULL(SUM(importe), 0) AS totalIngresos FROM ingresos WHERE idUser = :idUsuario";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    // Obtener el total de gastos para un usuario
    public function obtenerTotalGastos($idUsuario)
    {
        $sql = "SELECT IFNULL(SUM(importe), 0) AS totalGastos FROM gastos WHERE idUser = :idUsuario";
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
    // Obtener usuarios por familia
    public function obtenerUsuariosPorFamilia($idFamilia)
    {
        $sql = "SELECT * FROM usuarios WHERE idFamilia = :idFamilia";
        $stmt = $this->conexion->prepare($sql);  // Reemplazar $db con $this->conexion
        $stmt->bindParam(':idFamilia', $idFamilia);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener usuarios por grupo
    public function obtenerUsuariosPorGrupo($idGrupo)
    {
        $sql = "SELECT * FROM usuarios WHERE idGrupo = :idGrupo";
        $stmt = $this->conexion->prepare($sql);  // Reemplazar $db con $this->conexion
        $stmt->bindParam(':idGrupo', $idGrupo);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Añadir usuario a un grupo existente
    public function añadirUsuarioAGrupo($idUsuario, $idGrupo)
    {
        $sql = "UPDATE usuarios SET idGrupo = :idGrupo WHERE idUser = :idUsuario";
        $stmt = $this->conexion->prepare($sql);  // Reemplazar $db con $this->conexion
        $stmt->bindParam(':idGrupo', $idGrupo);
        $stmt->bindParam(':idUsuario', $idUsuario);
        return $stmt->execute();
    }

    // Añadir usuario a una familia existente
    public function añadirUsuarioAFamilia($idUsuario, $idFamilia)
    {
        $sql = "UPDATE usuarios SET idFamilia = :idFamilia WHERE idUser = :idUsuario";
        $stmt = $this->conexion->prepare($sql);  // Reemplazar $db con $this->conexion
        $stmt->bindParam(':idFamilia', $idFamilia);
        $stmt->bindParam(':idUsuario', $idUsuario);
        return $stmt->execute();
    }

    // Insertar gasto para un usuario
    public function insertarGasto($idUsuario, $monto, $categoria, $concepto, $origen, $idFamilia, $idGrupo)
    {
        // Verificar que el usuario esté asignado
        if (empty($idUsuario)) {
            throw new Exception('El gasto debe estar asociado a un usuario.');
        }

        $sql = "INSERT INTO gastos (idUser, importe, idCategoria, concepto, origen, fecha, idFamilia, idGrupo) 
            VALUES (:idUsuario, :monto, :categoria, :concepto, :origen, CURDATE(), :idFamilia, :idGrupo)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->bindValue(':monto', $monto, PDO::PARAM_STR);
        $stmt->bindValue(':categoria', $categoria, PDO::PARAM_INT);
        $stmt->bindValue(':concepto', $concepto, PDO::PARAM_STR);
        $stmt->bindValue(':origen', $origen, PDO::PARAM_STR);
        $stmt->bindValue(':idFamilia', $idFamilia, PDO::PARAM_INT);
        $stmt->bindValue(':idGrupo', $idGrupo, PDO::PARAM_INT);
        return $stmt->execute();
    }



    // Insertar ingreso para un usuario
    public function insertarIngreso($idUsuario, $monto, $categoria, $concepto, $origen, $idFamilia, $idGrupo)
    {
        // Verificar que el usuario esté asignado
        if (empty($idUsuario)) {
            throw new Exception('El ingreso debe estar asociado a un usuario.');
        }

        $sql = "INSERT INTO ingresos (idUser, importe, idCategoria, concepto, origen, fecha, idFamilia, idGrupo) 
            VALUES (:idUsuario, :monto, :categoria, :concepto, :origen, CURDATE(), :idFamilia, :idGrupo)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->bindValue(':monto', $monto, PDO::PARAM_STR);
        $stmt->bindValue(':categoria', $categoria, PDO::PARAM_INT);
        $stmt->bindValue(':concepto', $concepto, PDO::PARAM_STR);
        $stmt->bindValue(':origen', $origen, PDO::PARAM_STR);
        $stmt->bindValue(':idFamilia', $idFamilia, PDO::PARAM_INT);
        $stmt->bindValue(':idGrupo', $idGrupo, PDO::PARAM_INT);
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
        // Asegúrate de seleccionar el campo 'password'
        $sql = "SELECT idFamilia, nombre_familia, password FROM familias";
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

    // Insertar una familia con contraseña encriptada
    public function insertarFamilia($nombreFamilia, $passwordFamilia)
    {
        $sql = "INSERT INTO familias (nombre_familia, password) VALUES (:nombreFamilia, :password)";
        $stmt = $this->conexion->prepare($sql);
        $hashedPassword = password_hash($passwordFamilia, PASSWORD_DEFAULT);  // Encriptar la contraseña
        $stmt->bindValue(':nombreFamilia', $nombreFamilia, PDO::PARAM_STR);
        $stmt->bindValue(':password', $hashedPassword, PDO::PARAM_STR);
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

    // Eliminar una familia
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
        // Asegúrate de seleccionar el campo 'password'
        $sql = "SELECT idGrupo, nombre_grupo, password FROM grupos";
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

    // Insertar un grupo con contraseña encriptada
    public function insertarGrupo($nombreGrupo, $passwordGrupo)
    {
        $sql = "INSERT INTO grupos (nombre_grupo, password) VALUES (:nombreGrupo, :password)";
        $stmt = $this->conexion->prepare($sql);
        $hashedPassword = password_hash($passwordGrupo, PASSWORD_DEFAULT);  // Encriptar la contraseña
        $stmt->bindValue(':nombreGrupo', $nombreGrupo, PDO::PARAM_STR);
        $stmt->bindValue(':password', $hashedPassword, PDO::PARAM_STR);
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
    // Obtener categorías de gastos
    public function obtenerCategoriasGastos()
    {
        try {
            $sql = "SELECT * FROM categorias WHERE tipo_categoria = 'gasto'";
            $stmt = $this->conexion->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en la consulta: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerCategoriaGastoPorId($idCategoria)
    {
        try {
            $sql = "SELECT * FROM categorias WHERE idCategoria = :idCategoria";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':idCategoria', $idCategoria, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al obtener la categoría: " . $e->getMessage();
            return false;
        }
    }


    // Insertar nueva categoría de gasto
    public function insertarCategoriaGasto($nombreCategoria, $creadoPor)
    {
        try {
            $sql = "INSERT INTO categorias (nombreCategoria, tipo_categoria, creado_por) VALUES (:nombreCategoria, 'gasto', :creadoPor)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':nombreCategoria', $nombreCategoria);
            $stmt->bindValue(':creadoPor', $creadoPor); // Se guarda quién creó la categoría (admin o superadmin)
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error al insertar la categoría: " . $e->getMessage();
            return false;
        }
    }



    // Actualizar categoría de gasto
    public function actualizarCategoriaGasto($idCategoria, $nombreCategoria)
    {
        $sql = "UPDATE categorias SET nombreCategoria = :nombreCategoria WHERE idCategoria = :idCategoria";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':nombreCategoria', $nombreCategoria, PDO::PARAM_STR);
        $stmt->bindValue(':idCategoria', $idCategoria, PDO::PARAM_INT);
        return $stmt->execute();
    }


    // Verificar si una categoría está en uso
    public function categoriaEnUso($idCategoria, $tabla)
    {
        // Dependiendo de la tabla (gastos o ingresos), verifica si la categoría está en uso
        $sql = "SELECT COUNT(*) FROM $tabla WHERE idCategoria = :idCategoria";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idCategoria', $idCategoria, PDO::PARAM_INT);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        return $count > 0;
    }


    // Eliminar categoría de gasto solo si no está en uso
    public function eliminarCategoriaGasto($idCategoria)
    {
        $sql = "DELETE FROM categorias WHERE idCategoria = :idCategoria";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idCategoria', $idCategoria, PDO::PARAM_INT);
        return $stmt->execute();
    }



    // Obtener categorías de ingresos
    public function obtenerCategoriasIngresos()
    {
        try {
            $sql = "SELECT * FROM categorias WHERE tipo_categoria = 'ingreso'";
            $stmt = $this->conexion->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en la consulta: " . $e->getMessage());
            return [];
        }
    }


    public function insertarCategoriaIngreso($nombreCategoria, $creadoPor)
    {
        try {
            $sql = "INSERT INTO categorias (nombreCategoria, tipo_categoria, creado_por) VALUES (:nombreCategoria, 'ingreso', :creadoPor)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':nombreCategoria', $nombreCategoria);
            $stmt->bindValue(':creadoPor', $creadoPor); // Se guarda quién creó la categoría (admin o superadmin)
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error al insertar la categoría: " . $e->getMessage();
            return false;
        }
    }


    public function actualizarCategoriaIngreso($idCategoria, $nombreCategoria)
    {
        try {
            $sql = "UPDATE categorias SET nombreCategoria = :nombreCategoria WHERE idCategoria = :idCategoria";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':nombreCategoria', $nombreCategoria, PDO::PARAM_STR);
            $stmt->bindValue(':idCategoria', $idCategoria, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error al actualizar la categoría: " . $e->getMessage();
            return false;
        }
    }


    public function eliminarCategoriaIngreso($idCategoria)
    {
        try {
            $sql = "DELETE FROM categorias WHERE idCategoria = :idCategoria";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':idCategoria', $idCategoria, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error al eliminar la categoría: " . $e->getMessage();
            return false;
        }
    }

    public function categoriaIngresoEnUso($idCategoria)
    {
        try {
            $sql = "SELECT COUNT(*) FROM ingresos WHERE idCategoria = :idCategoria";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':idCategoria', $idCategoria, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchColumn() > 0; // Retorna true si está en uso
        } catch (PDOException $e) {
            error_log("Error al verificar si la categoría está en uso: " . $e->getMessage());
            return true; // Si hay un error, asumimos que está en uso para evitar eliminaciones erróneas
        }
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
            LEFT JOIN categorias c ON g.idCategoria = c.idCategoria 
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
        LEFT JOIN categorias c ON g.idCategoria = c.idCategoria
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
        LEFT JOIN categorias c ON i.idCategoria = c.idCategoria
        WHERE i.idUser = :idUsuario
        GROUP BY i.idCategoria";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener familias por administrador
    public function obtenerFamiliasPorAdministrador($idAdmin)
    {
        $sql = "SELECT f.* FROM familias f 
            JOIN administradores_familias af ON f.idFamilia = af.idFamilia
            WHERE af.idAdmin = :idAdmin";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idAdmin', $idAdmin, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Obtener grupos por administrador
    public function obtenerGruposPorAdministrador($idAdmin)
    {
        $sql = "SELECT g.* FROM grupos g
            JOIN administradores_grupos ag ON g.idGrupo = ag.idGrupo
            WHERE ag.idAdmin = :idAdmin";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idAdmin', $idAdmin, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Verificar la contraseña de una familia
    public function verificarPasswordFamilia($idFamilia, $password)
    {
        $sql = "SELECT password FROM familias WHERE idFamilia = :idFamilia";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idFamilia', $idFamilia, PDO::PARAM_INT);
        $stmt->execute();
        $familia = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($familia && password_verify($password, $familia['password'])) {
            return true;
        }
        return false;
    }

    // Verificar la contraseña de un grupo
    public function verificarPasswordGrupo($idGrupo, $password)
    {
        $sql = "SELECT password FROM grupos WHERE idGrupo = :idGrupo";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idGrupo', $idGrupo, PDO::PARAM_INT);
        $stmt->execute();
        $grupo = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($grupo && password_verify($password, $grupo['password'])) {
            return true;
        }
        return false;
    }

    public function obtenerAdministradoresFamilia($idFamilia)
    {
        $sql = "SELECT u.* FROM usuarios u 
            JOIN administradores_familias af ON u.idUser = af.idAdmin
            WHERE af.idFamilia = :idFamilia";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idFamilia', $idFamilia, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerAdministradoresGrupo($idGrupo)
    {
        $sql = "SELECT u.* FROM usuarios u 
            JOIN administradores_grupos ag ON u.idUser = ag.idAdmin
            WHERE ag.idGrupo = :idGrupo";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idGrupo', $idGrupo, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Añadir administrador a una familia
    public function añadirAdministradorAFamilia($idAdmin, $idFamilia)
    {
        $sql = "INSERT INTO administradores_familias (idAdmin, idFamilia) VALUES (:idAdmin, :idFamilia)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idAdmin', $idAdmin, PDO::PARAM_INT);
        $stmt->bindValue(':idFamilia', $idFamilia, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Añadir administrador a un grupo
    public function añadirAdministradorAGrupo($idAdmin, $idGrupo)
    {
        $sql = "INSERT INTO administradores_grupos (idAdmin, idGrupo) VALUES (:idAdmin, :idGrupo)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idAdmin', $idAdmin, PDO::PARAM_INT);
        $stmt->bindValue(':idGrupo', $idGrupo, PDO::PARAM_INT);
        return $stmt->execute();
    }


    // Eliminar un administrador de una familia
    public function eliminarAdministradorDeFamilia($idAdmin, $idFamilia)
    {
        $sql = "DELETE FROM administradores_familias WHERE idAdmin = :idAdmin AND idFamilia = :idFamilia";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idAdmin', $idAdmin, PDO::PARAM_INT);
        $stmt->bindValue(':idFamilia', $idFamilia, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Eliminar un administrador de un grupo
    public function eliminarAdministradorDeGrupo($idAdmin, $idGrupo)
    {
        $sql = "DELETE FROM administradores_grupos WHERE idAdmin = :idAdmin AND idGrupo = :idGrupo";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idAdmin', $idAdmin, PDO::PARAM_INT);
        $stmt->bindValue(':idGrupo', $idGrupo, PDO::PARAM_INT);
        return $stmt->execute();
    }


    // Eliminar todos los gastos de un usuario
    public function eliminarGastosPorUsuario($idUsuario)
    {
        $sql = "DELETE FROM gastos WHERE idUser = :idUsuario";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Eliminar todos los ingresos de un usuario
    public function eliminarIngresosPorUsuario($idUsuario)
    {
        $sql = "DELETE FROM ingresos WHERE idUser = :idUsuario";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function asignarUsuarioAFamilia($idUsuario, $idFamilia)
    {
        // Primero actualizar en la tabla usuarios
        $sql = "UPDATE usuarios SET idFamilia = :idFamilia WHERE idUser = :idUsuario";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idFamilia', $idFamilia, PDO::PARAM_INT);
        $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();

        // Insertar en usuarios_familias si no existe
        $sqlFamilia = "INSERT INTO usuarios_familias (idUser, idFamilia) 
                       VALUES (:idUsuario, :idFamilia) 
                       ON DUPLICATE KEY UPDATE idFamilia = :idFamilia";
        $stmtFamilia = $this->conexion->prepare($sqlFamilia);
        $stmtFamilia->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmtFamilia->bindValue(':idFamilia', $idFamilia, PDO::PARAM_INT);
        return $stmtFamilia->execute();
    }


    public function asignarUsuarioAGrupo($idUsuario, $idGrupo)
    {
        // Primero actualizar en la tabla usuarios
        $sql = "UPDATE usuarios SET idGrupo = :idGrupo WHERE idUser = :idUsuario";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idGrupo', $idGrupo, PDO::PARAM_INT);
        $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();

        // Insertar en usuarios_grupos si no existe
        $sqlGrupo = "INSERT INTO usuarios_grupos (idUser, idGrupo) 
                     VALUES (:idUsuario, :idGrupo) 
                     ON DUPLICATE KEY UPDATE idGrupo = :idGrupo";
        $stmtGrupo = $this->conexion->prepare($sqlGrupo);
        $stmtGrupo->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmtGrupo->bindValue(':idGrupo', $idGrupo, PDO::PARAM_INT);
        return $stmtGrupo->execute();
    }


    public function obtenerCategoriaIngresoPorId($idCategoria)
    {
        try {
            $sql = "SELECT * FROM categorias WHERE idCategoria = :idCategoria";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':idCategoria', $idCategoria, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al obtener la categoría: " . $e->getMessage();
            return false;
        }
    }
    public function obtenerIngresosFiltrados($idUsuario, $fechaInicio = null, $fechaFin = null, $categoria = null, $origen = null, $offset = 0, $limite = 10)
    {
        $sql = "SELECT * FROM ingresos WHERE idUser = :idUsuario";
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
        $sql .= " LIMIT :offset, :limite";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
        if ($fechaInicio) $stmt->bindValue(':fechaInicio', $fechaInicio, PDO::PARAM_STR);
        if ($fechaFin) $stmt->bindValue(':fechaFin', $fechaFin, PDO::PARAM_STR);
        if ($categoria) $stmt->bindValue(':categoria', $categoria, PDO::PARAM_INT);
        if ($origen) $stmt->bindValue(':origen', $origen, PDO::PARAM_STR);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contarIngresosFiltrados($idUsuario, $fechaInicio = null, $fechaFin = null, $categoria = null, $origen = null)
    {
        $sql = "SELECT COUNT(*) FROM ingresos WHERE idUser = :idUsuario";
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
        if ($fechaInicio) $stmt->bindValue(':fechaInicio', $fechaInicio, PDO::PARAM_STR);
        if ($fechaFin) $stmt->bindValue(':fechaFin', $fechaFin, PDO::PARAM_STR);
        if ($categoria) $stmt->bindValue(':categoria', $categoria, PDO::PARAM_INT);
        if ($origen) $stmt->bindValue(':origen', $origen, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    public function actualizarIngreso($idIngreso, $importe, $categoria, $concepto, $origen)
    {
        // Registrar el valor de idCategoria antes de ejecutar la consulta
        error_log("Actualizando ingreso con idCategoria: " . $categoria);

        $sql = "UPDATE ingresos 
            SET importe = :importe, idCategoria = :categoria, concepto = :concepto, origen = :origen 
            WHERE idIngreso = :idIngreso";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':importe', $importe, PDO::PARAM_STR);
        $stmt->bindValue(':categoria', $categoria, PDO::PARAM_INT);
        $stmt->bindValue(':concepto', $concepto, PDO::PARAM_STR);
        $stmt->bindValue(':origen', $origen, PDO::PARAM_STR);
        $stmt->bindValue(':idIngreso', $idIngreso, PDO::PARAM_INT);

        return $stmt->execute();
    }


    public function eliminarIngreso($idIngreso)
    {
        $sql = "DELETE FROM ingresos WHERE idIngreso = :idIngreso";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idIngreso', $idIngreso, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function archivarAccesosAntiguos()
    {
        try {
            // Mover los accesos antiguos a la tabla de archivo
            $sqlArchivar = "INSERT INTO auditoria_accesos_archivo SELECT * FROM auditoria_accesos WHERE fecha < NOW() - INTERVAL 1 YEAR";
            $stmtArchivar = $this->conexion->prepare($sqlArchivar);
            $stmtArchivar->execute();

            // Eliminar los accesos antiguos de la tabla principal
            $sqlEliminar = "DELETE FROM auditoria_accesos WHERE fecha < NOW() - INTERVAL 1 YEAR";
            $stmtEliminar = $this->conexion->prepare($sqlEliminar);
            $stmtEliminar->execute();
        } catch (PDOException $e) {
            error_log("Error al archivar accesos antiguos: " . $e->getMessage());
            throw new Exception("Error en la operación de archivo de accesos.");
        }
    }
    // Método para obtener la preferencia del usuario sobre los resultados por página
    public function obtenerPreferenciaUsuario($clave, $idUsuario)
    {
        try {
            $sql = "SELECT valor FROM preferencias_usuarios WHERE clave = :clave AND idUser = :idUsuario";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':clave', $clave, PDO::PARAM_STR);
            $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error al obtener la preferencia del usuario: " . $e->getMessage());
            return null;
        }
    }

    // Método para obtener el resumen financiero del usuario
    public function obtenerResumenFinancieroUsuario($idUsuario)
    {
        try {
            $sql = "
        SELECT 
            SUM(CASE WHEN i.importe IS NOT NULL THEN i.importe ELSE 0 END) AS ingresos_totales,
            SUM(CASE WHEN g.importe IS NOT NULL THEN g.importe ELSE 0 END) AS gastos_totales,
            (SUM(CASE WHEN i.importe IS NOT NULL THEN i.importe ELSE 0 END) - SUM(CASE WHEN g.importe IS NOT NULL THEN g.importe ELSE 0 END)) AS saldo_total
        FROM usuarios u
        LEFT JOIN ingresos i ON u.idUser = i.idUser
        LEFT JOIN gastos g ON u.idUser = g.idUser
        WHERE u.idUser = :idUsuario";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener el resumen financiero del usuario: " . $e->getMessage());
            return null;
        }
    }

    // Método para obtener un refrán aleatorio
    public function obtenerRefranAleatorio()
    {
        try {
            $sql = "SELECT idRefran, refran FROM refranes ORDER BY RAND() LIMIT 1";
            $stmt = $this->conexion->query($sql);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener un refrán aleatorio: " . $e->getMessage());
            return null;
        }
    }

    // Método para registrar el envío de la newsletter
    public function insertarNewsLetterEnvio($idUsuario, $idRefran, $saldoTotal, $gastosTotales, $ingresosTotales)
    {
        try {
            $sql = "INSERT INTO news_letter_envios (idUser, idRefran, saldo_total, gastos_totales, ingresos_totales, fecha_envio) 
                VALUES (:idUsuario, :idRefran, :saldoTotal, :gastosTotales, :ingresosTotales, NOW())";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
            $stmt->bindValue(':idRefran', $idRefran, PDO::PARAM_INT);
            $stmt->bindValue(':saldoTotal', $saldoTotal, PDO::PARAM_STR);
            $stmt->bindValue(':gastosTotales', $gastosTotales, PDO::PARAM_STR);
            $stmt->bindValue(':ingresosTotales', $ingresosTotales, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al insertar el envío de la newsletter: " . $e->getMessage());
            return false;
        }
    }
    // Método para consultar configuraciones de usuario
    public function consultarConfiguracion($clave, $idUser = null)
    {
        $sql = "SELECT valor FROM configuraciones WHERE clave = :clave";
        if ($idUser !== null) {
            $sql .= " AND idUser = :idUser";
        }
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':clave', $clave, PDO::PARAM_STR);
        if ($idUser !== null) {
            $stmt->bindValue(':idUser', $idUser, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    // Método para guardar configuraciones de usuario
    public function guardarConfiguracion($clave, $valor, $idUser = null)
    {
        // Si ya existe la configuración, la actualizamos
        $sql = "INSERT INTO configuraciones (clave, valor, idUser) 
            VALUES (:clave, :valor, :idUser) 
            ON DUPLICATE KEY UPDATE valor = :valor";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':clave', $clave, PDO::PARAM_STR);
        $stmt->bindValue(':valor', $valor, PDO::PARAM_STR);
        if ($idUser !== null) {
            $stmt->bindValue(':idUser', $idUser, PDO::PARAM_INT);
        } else {
            $stmt->bindValue(':idUser', null, PDO::PARAM_NULL);
        }
        return $stmt->execute();
    }
    // Obtener todos los usuarios registrados en el sistema
    public function obtenerTodosLosUsuarios()
    {
        $sql = "SELECT idUser, email FROM usuarios";
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
