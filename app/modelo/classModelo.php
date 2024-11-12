<?php
require_once __DIR__ . '/../libs/Config.php';

class GastosModelo
{
    protected $conexion;

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
            throw new Exception("Error de conexión a la base de datos: " . $e->getMessage());
        }
    }

    public function getConexion()
    {
        if ($this->conexion === null) {
            throw new Exception("No se ha establecido la conexión a la base de datos.");
        }
        return $this->conexion;
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
    public function consultarUsuario($alias)
    {
        try {
            $sql = "SELECT * FROM usuarios WHERE alias = :alias";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':alias', $alias, PDO::PARAM_STR);
            $stmt->execute();
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$usuario) {
                error_log("Usuario con alias '$alias' no encontrado en consultarUsuario().");
                return false;
            }

            return $usuario;
        } catch (PDOException $e) {
            error_log("Error en consultarUsuario(): " . $e->getMessage());
            return false;
        }
    }

    public function registrarAcceso($idUser, $accion)
    {
        try {
            $sql = "INSERT INTO auditoria_accesos (idUser, accion, timestamp) VALUES (:idUser, :accion, NOW())";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
            $stmt->bindParam(':accion', $accion, PDO::PARAM_STR);
            $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al registrar el acceso en auditoria_accesos: " . $e->getMessage());
        }
    }


    // Obtiene todos los usuarios, incluyendo familias y grupos asignados
    public function obtenerUsuarios()
    {
        try {
            $sql = "SELECT u.*, 
                           COALESCE(f.nombre_familia, 'Sin Familia') AS nombre_familia, 
                           COALESCE(g.nombre_grupo, 'Sin Grupo') AS nombre_grupo
                    FROM usuarios u
                    LEFT JOIN usuarios_familias uf ON u.idUser = uf.idUser
                    LEFT JOIN familias f ON uf.idFamilia = f.idFamilia
                    LEFT JOIN usuarios_grupos ug ON u.idUser = ug.idUser
                    LEFT JOIN grupos g ON ug.idGrupo = g.idGrupo";

            error_log("Ejecutando consulta SQL: " . $sql);
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Resultado de obtenerUsuarios: " . json_encode($result));
            return $result;
        } catch (PDOException $e) {
            error_log("Error en obtenerUsuarios(): " . $e->getMessage());
            throw new Exception('Error al listar los usuarios.');
        }
    }


    public function obtenerTodosLosUsuarios()
    {
        try {
            $sql = "SELECT idUser, nombre, apellido, alias, email, telefono, nivel_usuario, estado_usuario, fecha_registro FROM usuarios";
            $stmt = $this->conexion->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log("Error al obtener todos los usuarios: " . $e->getMessage());
            return [];
        }
    }
    public function insertarUsuario($nombre, $apellido, $alias, $hashedPassword, $nivel_usuario, $fecha_nacimiento, $email, $telefono)
    {
        try {
            $sql = "INSERT INTO usuarios (nombre, apellido, alias, contrasenya, nivel_usuario, fecha_nacimiento, email, telefono)
                VALUES (:nombre, :apellido, :alias, :contrasenya, :nivel_usuario, :fecha_nacimiento, :email, :telefono)";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':apellido', $apellido);
            $stmt->bindParam(':alias', $alias);
            $stmt->bindParam(':contrasenya', $hashedPassword);
            $stmt->bindParam(':nivel_usuario', $nivel_usuario);
            $stmt->bindParam(':fecha_nacimiento', $fecha_nacimiento);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':telefono', $telefono);

            if ($stmt->execute()) {
                // Retorna el ID del último registro insertado
                return $this->conexion->lastInsertId();
            } else {
                return false;  // Si hay algún error en la ejecución
            }
        } catch (PDOException $e) {
            error_log("Error en insertarUsuario(): " . $e->getMessage());
            return false;
        }
    }

    public function obtenerIdFamiliaPorNombre($nombre_familia)
    {
        $sql = "SELECT idFamilia FROM familias WHERE nombre_familia = :nombre_familia";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':nombre_familia', $nombre_familia, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    public function obtenerIdGrupoPorNombre($nombreGrupo)
    {
        try {
            $sql = "SELECT idGrupo FROM grupos WHERE nombre_grupo = :nombreGrupo";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':nombreGrupo', $nombreGrupo, PDO::PARAM_STR);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado ? $resultado['idGrupo'] : false;
        } catch (Exception $e) {
            error_log("Error al obtener el ID del grupo por nombre: " . $e->getMessage());
            return false;
        }
    }

    // Método para contar familias por administrador
    public function contarFamiliasPorAdmin($idAdmin)
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM administradores_familias WHERE idAdmin = :idAdmin";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':idAdmin', $idAdmin, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (Exception $e) {
            error_log("Error en contarFamiliasPorAdmin(): " . $e->getMessage());
            return 0;
        }
    }


    // Insertar una nueva familia con el nombre y la contraseña encriptada
    public function insertarFamilia($nombreFamilia, $passwordFamilia)
    {
        try {
            $sql = "INSERT INTO familias (nombre_familia, password) VALUES (:nombreFamilia, :password)";
            $stmt = $this->conexion->prepare($sql);
            $hashedPassword = password_hash($passwordFamilia, PASSWORD_DEFAULT); // Encriptar la contraseña
            $stmt->bindValue(':nombreFamilia', $nombreFamilia, PDO::PARAM_STR);
            $stmt->bindValue(':password', $hashedPassword, PDO::PARAM_STR);

            if ($stmt->execute()) {
                return true;
            } else {
                throw new Exception('Error al insertar familia: ' . implode(", ", $stmt->errorInfo()));
            }
        } catch (Exception $e) {
            error_log("Error en insertarFamilia: " . $e->getMessage());
            throw $e;
        }
    }

    // Contar grupos administrados por un usuario específico
    public function contarGruposPorAdmin($idAdmin)
    {
        try {
            $sql = "SELECT COUNT(*) AS total FROM administradores_grupos WHERE idAdmin = :idAdmin";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':idAdmin', $idAdmin, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        } catch (Exception $e) {
            error_log("Error en contarGruposPorAdmin: " . $e->getMessage());
            return 0;
        }
    }

    // Insertar un nuevo grupo con el nombre y la contraseña encriptada
    public function insertarGrupo($nombreGrupo, $passwordGrupo)
    {
        try {
            $sql = "INSERT INTO grupos (nombre_grupo, password) VALUES (:nombreGrupo, :password)";
            $stmt = $this->conexion->prepare($sql);
            $hashedPassword = password_hash($passwordGrupo, PASSWORD_DEFAULT); // Encriptar la contraseña
            $stmt->bindValue(':nombreGrupo', $nombreGrupo, PDO::PARAM_STR);
            $stmt->bindValue(':password', $hashedPassword, PDO::PARAM_STR);

            if ($stmt->execute()) {
                return true;
            } else {
                throw new Exception('Error al insertar grupo: ' . implode(", ", $stmt->errorInfo()));
            }
        } catch (Exception $e) {
            error_log("Error en insertarGrupo: " . $e->getMessage());
            throw $e;
        }
    }


    // Obtener el último ID insertado en la base de datos
    public function getLastInsertId()
    {
        return $this->conexion->lastInsertId();
    }

    

    

    public function obtenerGruposPorAdministrador($idAdmin)
    {
        try {
            $sql = "SELECT g.* FROM grupos g
                JOIN administradores_grupos ag ON g.idGrupo = ag.idGrupo
                WHERE ag.idAdmin = :idAdmin";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':idAdmin', $idAdmin, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerGruposPorAdministrador(): " . $e->getMessage());
            return []; // Devuelve un array vacío en caso de error
        }
    }
    // Verifica si un alias de usuario ya existe en la base de datos
    public function existeUsuario($alias)
    {
        $sql = "SELECT COUNT(*) FROM usuarios WHERE alias = :alias";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':alias', $alias, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    // Obtiene todas las familias y registra la cantidad
    public function obtenerFamilias($filtros = [])
    {
        try {
            // Construcción de la consulta base con un JOIN para capturar administradores y usuarios
            $sql = "
        SELECT 
            f.idFamilia,
            f.nombre_familia,
            GROUP_CONCAT(DISTINCT a.alias ORDER BY a.alias ASC SEPARATOR ', ') AS administradores,
            GROUP_CONCAT(DISTINCT u.alias ORDER BY u.alias ASC SEPARATOR ', ') AS usuarios
        FROM familias f
        LEFT JOIN administradores_familias af ON f.idFamilia = af.idFamilia
        LEFT JOIN usuarios a ON af.idAdmin = a.idUser
        LEFT JOIN usuarios_familias uf ON f.idFamilia = uf.idFamilia
        LEFT JOIN usuarios u ON uf.idUser = u.idUser
        WHERE 1=1
        ";

            // Array para almacenar los parámetros de la consulta
            $params = [];

            // Filtros opcionales
            if (!empty($filtros['id'])) {
                $sql .= " AND f.idFamilia = :id";
                $params[':id'] = $filtros['id'];
            }
            if (!empty($filtros['nombre_familia'])) {
                $sql .= " AND f.nombre_familia LIKE :nombre_familia";
                $params[':nombre_familia'] = '%' . $filtros['nombre_familia'] . '%';
            }

            $sql .= " GROUP BY f.idFamilia, f.nombre_familia";

            // Preparar y ejecutar la consulta
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);

            $familias = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Total de familias obtenidas: " . count($familias));
            return $familias;
        } catch (PDOException $e) {
            error_log("Error en obtenerFamilias(): " . $e->getMessage());
            throw new Exception('Error al listar las familias.');
        }
    }



    public function obtenerFamiliasPorAdministrador($idAdmin)
    {
        $sql = "
        SELECT f.idFamilia, f.nombre_familia
        FROM familias f
        INNER JOIN administradores_familias af ON f.idFamilia = af.idFamilia
        WHERE af.idAdmin = :idAdmin
    ";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':idAdmin', $idAdmin, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Obtiene todos los grupos y registra la cantidad
    public function obtenerGrupos($filtros = [])
    {
        try {
            // Construcción de la consulta base con JOINs para capturar administradores y usuarios de grupos
            $sql = "
        SELECT 
            g.idGrupo,
            g.nombre_grupo,
            GROUP_CONCAT(DISTINCT a.alias ORDER BY a.alias ASC SEPARATOR ', ') AS administradores,
            GROUP_CONCAT(DISTINCT u.alias ORDER BY u.alias ASC SEPARATOR ', ') AS usuarios
        FROM grupos g
        LEFT JOIN administradores_grupos ag ON g.idGrupo = ag.idGrupo
        LEFT JOIN usuarios a ON ag.idAdmin = a.idUser
        LEFT JOIN usuarios_grupos ug ON g.idGrupo = ug.idGrupo
        LEFT JOIN usuarios u ON ug.idUser = u.idUser
        WHERE 1=1
        ";

            // Array para almacenar los parámetros de la consulta
            $params = [];

            // Filtros opcionales
            if (!empty($filtros['id'])) {
                $sql .= " AND g.idGrupo = :id";
                $params[':id'] = $filtros['id'];
            }
            if (!empty($filtros['nombre_grupo'])) {
                $sql .= " AND g.nombre_grupo LIKE :nombre_grupo";
                $params[':nombre_grupo'] = '%' . $filtros['nombre_grupo'] . '%';
            }

            $sql .= " GROUP BY g.idGrupo, g.nombre_grupo";

            // Preparar y ejecutar la consulta
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);

            $grupos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Total de grupos obtenidos: " . count($grupos));
            return $grupos;
        } catch (PDOException $e) {
            error_log("Error en obtenerGrupos(): " . $e->getMessage());
            throw new Exception('Error al listar los grupos.');
        }
    }


    // Retorna el último ID insertado en una tabla
    public function obtenerUltimoId()
    {
        return $this->conexion->lastInsertId();
    }

    // Asigna un usuario como administrador de una familia específica
    public function asignarAdministradorAFamilia($idUser, $idFamilia)
    {
        $sql = "INSERT INTO administradores_familias (idAdmin, idFamilia) VALUES (:idAdmin, :idFamilia)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':idAdmin', $idUser, PDO::PARAM_INT);
        $stmt->bindParam(':idFamilia', $idFamilia, PDO::PARAM_INT);
        $stmt->execute();
    }

    // Verifica si la contraseña de la familia es correcta con depuración
    public function verificarPasswordFamilia($idFamilia, $password)
    {
        $sql = "SELECT password FROM familias WHERE idFamilia = :idFamilia";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':idFamilia', $idFamilia, PDO::PARAM_INT);
        $stmt->execute();
        $hashedPassword = $stmt->fetchColumn();
        $isValid = password_verify($password, $hashedPassword);

        error_log("Verificación de contraseña para familia $idFamilia: " . ($isValid ? "válida" : "inválida"));
        return $isValid;
    }

    // Asigna un usuario como administrador de un grupo específico
    public function asignarAdministradorAGrupo($idUser, $idGrupo)
    {
        $sql = "INSERT INTO administradores_grupos (idAdmin, idGrupo) VALUES (:idAdmin, :idGrupo)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':idAdmin', $idUser, PDO::PARAM_INT);
        $stmt->bindParam(':idGrupo', $idGrupo, PDO::PARAM_INT);
        $stmt->execute();
    }

    // Verifica si la contraseña de un grupo es correcta con depuración
    public function verificarPasswordGrupo($idGrupo, $password)
    {
        $sql = "SELECT password FROM grupos WHERE idGrupo = :idGrupo";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':idGrupo', $idGrupo, PDO::PARAM_INT);
        $stmt->execute();
        $hashedPassword = $stmt->fetchColumn();
        $isValid = password_verify($password, $hashedPassword);

        error_log("Verificación de contraseña para grupo $idGrupo: " . ($isValid ? "válida" : "inválida"));
        return $isValid;
    }

    // Obtiene una familia por su ID
    public function obtenerFamiliaPorId($idFamilia)
    {
        $sql = "SELECT * FROM familias WHERE idFamilia = :idFamilia";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':idFamilia', $idFamilia, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtiene un grupo por su ID
    public function obtenerGrupoPorId($idGrupo)
    {
        $sql = "SELECT * FROM grupos WHERE idGrupo = :idGrupo";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':idGrupo', $idGrupo, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtiene un usuario específico por su ID
    public function obtenerUsuarioPorId($idUser)
    {
        try {
            $sql = "SELECT u.*, 
                       f.nombre_familia, 
                       g.nombre_grupo
                FROM usuarios u
                LEFT JOIN usuarios_familias uf ON u.idUser = uf.idUser
                LEFT JOIN familias f ON uf.idFamilia = f.idFamilia
                LEFT JOIN usuarios_grupos ug ON u.idUser = ug.idUser
                LEFT JOIN grupos g ON ug.idGrupo = g.idGrupo
                WHERE u.idUser = :idUser";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerUsuarioPorId(): " . $e->getMessage());
            return false;
        }
    }



    // Elimina todos los registros de gastos asociados a un usuario
    public function eliminarGastosPorUsuario($idUser)
    {
        $sql = "DELETE FROM gastos WHERE idUser = :idUser";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Elimina todos los registros de ingresos asociados a un usuario
    public function eliminarIngresosPorUsuario($idUser)
    {
        $sql = "DELETE FROM ingresos WHERE idUser = :idUser";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Elimina un usuario de la base de datos
    public function eliminarUsuario($idUser)
    {
        $sql = "DELETE FROM usuarios WHERE idUser = :idUser";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
        return $stmt->execute();
    }
    public function usuarioYaEnFamilia($idUser, $idFamilia)
    {
        try {
            $sql = "SELECT COUNT(*) FROM usuarios_familias WHERE idUser = :idUser AND idFamilia = :idFamilia";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
            $stmt->bindParam(':idFamilia', $idFamilia, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Error en usuarioYaEnFamilia: " . $e->getMessage());
            return false;
        }
    }

    public function usuarioYaEnGrupo($idUser, $idGrupo)
    {
        try {
            $sql = "SELECT COUNT(*) FROM usuarios_grupos WHERE idUser = :idUser AND idGrupo = :idGrupo";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
            $stmt->bindParam(':idGrupo', $idGrupo, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Error en usuarioYaEnGrupo: " . $e->getMessage());
            return false;
        }
    }
    public function obtenerFamiliaPorNombre($nombreFamilia)
    {
        $sql = "SELECT * FROM familias WHERE nombre_familia = :nombreFamilia";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':nombreFamilia', $nombreFamilia, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function añadirAdministradorAFamilia($idUser, $idFamilia)
    {
        $sql = "INSERT INTO administradores_familias (idAdmin, idFamilia) VALUES (:idUser, :idFamilia)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
        $stmt->bindParam(':idFamilia', $idFamilia, PDO::PARAM_INT);
        return $stmt->execute();
    }
    public function obtenerAdministradores()
    {
        $sql = "SELECT * FROM usuarios WHERE nivel_usuario = 'admin'";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function actualizarPasswordPremium($idUser, $hashedPasswordPremium)
    {
        try {
            $sql = "UPDATE usuarios SET password_premium = :hashedPasswordPremium WHERE idUser = :idUser";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':hashedPasswordPremium', $hashedPasswordPremium, PDO::PARAM_STR);
            $stmt->bindValue(':idUser', $idUser, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar la contraseña premium: " . $e->getMessage());
            return false;
        }
    }
    // Obtener la situación financiera global
    public function obtenerSituacionGlobal()
    {
        $sql = "
        SELECT 
            SUM(CASE WHEN i.importe IS NOT NULL THEN i.importe ELSE 0 END) AS totalIngresos,
            SUM(CASE WHEN g.importe IS NOT NULL THEN g.importe ELSE 0 END) AS totalGastos,
            (SUM(CASE WHEN i.importe IS NOT NULL THEN i.importe ELSE 0 END) - SUM(CASE WHEN g.importe IS NOT NULL THEN g.importe ELSE 0 END)) AS saldo
        FROM usuarios u
        LEFT JOIN ingresos i ON u.idUser = i.idUser
        LEFT JOIN gastos g ON u.idUser = g.idUser";

        $stmt = $this->conexion->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // Obtener situación financiera de un usuario específico
    public function obtenerSituacionFinanciera($idUser)
    {
        $sql = "
        SELECT 
            SUM(CASE WHEN i.importe IS NOT NULL THEN i.importe ELSE 0 END) AS totalIngresos,
            SUM(CASE WHEN g.importe IS NOT NULL THEN g.importe ELSE 0 END) AS totalGastos,
            (SUM(CASE WHEN i.importe IS NOT NULL THEN i.importe ELSE 0 END) - SUM(CASE WHEN g.importe IS NOT NULL THEN g.importe ELSE 0 END)) AS saldo
        FROM usuarios u
        LEFT JOIN ingresos i ON u.idUser = i.idUser
        LEFT JOIN gastos g ON u.idUser = g.idUser
        WHERE u.idUser = :idUser";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idUser', $idUser, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function consultarFamiliaPorId($idFamilia)
    {
        try {
            // Consulta para obtener los datos de la familia junto con su administrador
            $sql = "SELECT f.idFamilia, f.nombre_familia, af.idAdmin
                FROM familias f
                LEFT JOIN administradores_familias af ON f.idFamilia = af.idFamilia
                WHERE f.idFamilia = :idFamilia";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':idFamilia', $idFamilia, PDO::PARAM_INT);
            $stmt->execute();

            // Retornar el resultado de la consulta
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al consultar la familia por ID: " . $e->getMessage());
            return false;
        }
    }

    public function actualizarFamilia($idFamilia, $nombreFamilia, $idAdmin)
    {
        try {
            // Validar idAdmin e idFamilia
            if (empty($idAdmin) || filter_var($idAdmin, FILTER_VALIDATE_INT) === false) {
                throw new Exception("ID de administrador no válido.");
            }
            if (filter_var($idFamilia, FILTER_VALIDATE_INT) === false) {
                throw new Exception("ID de familia no válido.");
            }

            // Actualización en la base de datos
            $sql = "UPDATE familias SET nombre_familia = :nombreFamilia, idAdmin = :idAdmin WHERE idFamilia = :idFamilia";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':nombreFamilia', $nombreFamilia, PDO::PARAM_STR);
            $stmt->bindParam(':idAdmin', $idAdmin, PDO::PARAM_INT);
            $stmt->bindParam(':idFamilia', $idFamilia, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar la familia: " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            error_log("Error de validación en actualizarFamilia: " . $e->getMessage());
            return false;
        }
    }
    public function actualizarNombreFamilia($idFamilia, $nombreFamilia)
    {
        try {
            $sql = "UPDATE familias SET nombre_familia = :nombreFamilia WHERE idFamilia = :idFamilia";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':nombreFamilia', $nombreFamilia, PDO::PARAM_STR);
            $stmt->bindParam(':idFamilia', $idFamilia, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar el nombre de la familia: " . $e->getMessage());
            return false;
        }
    }

    public function actualizarAdministradorFamilia($idFamilia, $idAdmin)
    {
        try {
            // Primero, elimina cualquier administrador existente para esta familia
            $sqlDelete = "DELETE FROM administradores_familias WHERE idFamilia = :idFamilia";
            $stmtDelete = $this->conexion->prepare($sqlDelete);
            $stmtDelete->bindParam(':idFamilia', $idFamilia, PDO::PARAM_INT);
            $stmtDelete->execute();

            // Luego, inserta el nuevo administrador
            $sqlInsert = "INSERT INTO administradores_familias (idFamilia, idAdmin) VALUES (:idFamilia, :idAdmin)";
            $stmtInsert = $this->conexion->prepare($sqlInsert);
            $stmtInsert->bindParam(':idFamilia', $idFamilia, PDO::PARAM_INT);
            $stmtInsert->bindParam(':idAdmin', $idAdmin, PDO::PARAM_INT);
            return $stmtInsert->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar el administrador de la familia: " . $e->getMessage());
            return false;
        }
    }
    // Actualiza los datos de un usuario
    public function actualizarUsuario($userId, $nombre, $apellido, $alias, $email, $telefono, $nivel_usuario, $fecha_nacimiento)
    {
        try {
            $sql = "UPDATE usuarios SET nombre = :nombre, apellido = :apellido, alias = :alias, email = :email, 
                telefono = :telefono, nivel_usuario = :nivel_usuario, fecha_nacimiento = :fecha_nacimiento
                WHERE idUser = :userId";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $stmt->bindParam(':apellido', $apellido, PDO::PARAM_STR);
            $stmt->bindParam(':alias', $alias, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindValue(':telefono', $telefono, $telefono !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':nivel_usuario', $nivel_usuario, PDO::PARAM_STR);
            $stmt->bindValue(':fecha_nacimiento', $fecha_nacimiento, $fecha_nacimiento !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);

            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error en actualizarUsuario: " . $e->getMessage());
            throw $e;
        }
    }



    // Actualizar la relación de un usuario con una familia
    public function actualizarUsuarioFamilia($idUser, $idFamilia)
    {
        try {
            // Eliminar cualquier relación de familia previa
            $sqlDelete = "DELETE FROM usuarios_familias WHERE idUser = :idUser";
            $stmtDelete = $this->conexion->prepare($sqlDelete);
            $stmtDelete->bindParam(':idUser', $idUser, PDO::PARAM_INT);
            $stmtDelete->execute();

            // Insertar la nueva relación de familia
            $sqlInsert = "INSERT INTO usuarios_familias (idUser, idFamilia) VALUES (:idUser, :idFamilia)";
            $stmtInsert = $this->conexion->prepare($sqlInsert);
            $stmtInsert->bindParam(':idUser', $idUser, PDO::PARAM_INT);
            $stmtInsert->bindParam(':idFamilia', $idFamilia, PDO::PARAM_INT);
            return $stmtInsert->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar la relación de familia: " . $e->getMessage());
            return false;
        }
    }

    // Actualizar la relación de un usuario con un grupo
    public function actualizarUsuarioGrupo($idUser, $idGrupo)
    {
        try {
            // Eliminar cualquier relación de grupo previa
            $sqlDelete = "DELETE FROM usuarios_grupos WHERE idUser = :idUser";
            $stmtDelete = $this->conexion->prepare($sqlDelete);
            $stmtDelete->bindParam(':idUser', $idUser, PDO::PARAM_INT);
            $stmtDelete->execute();

            // Insertar la nueva relación de grupo
            $sqlInsert = "INSERT INTO usuarios_grupos (idUser, idGrupo) VALUES (:idUser, :idGrupo)";
            $stmtInsert = $this->conexion->prepare($sqlInsert);
            $stmtInsert->bindParam(':idUser', $idUser, PDO::PARAM_INT);
            $stmtInsert->bindParam(':idGrupo', $idGrupo, PDO::PARAM_INT);
            return $stmtInsert->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar la relación de grupo: " . $e->getMessage());
            return false;
        }
    }
    // Actualiza el nivel de un usuario
    public function actualizarUsuarioNivel($idUser, $nivel_usuario)
    {
        try {
            $sql = "UPDATE usuarios SET nivel_usuario = :nivel_usuario WHERE idUser = :idUser";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':nivel_usuario', $nivel_usuario, PDO::PARAM_STR);
            $stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar el nivel de usuario: " . $e->getMessage());
            return false;
        }
    }
    // Método en el modelo para eliminar una familia por su ID
    public function eliminarFamilia($idFamilia)
    {
        try {
            $sql = "DELETE FROM familias WHERE idFamilia = :idFamilia";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':idFamilia', $idFamilia, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al eliminar la familia: " . $e->getMessage());
            return false;
        }
    }
    // Método en GastosModelo para obtener usuarios asociados a una familia
    public function obtenerUsuariosPorFamilia($idFamilia)
    {
        try {
            $sql = "SELECT u.* FROM usuarios u
                INNER JOIN usuarios_familias uf ON u.idUser = uf.idUser
                WHERE uf.idFamilia = :idFamilia";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':idFamilia', $idFamilia, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Retorna un array de usuarios asociados
        } catch (PDOException $e) {
            error_log("Error en obtenerUsuariosPorFamilia(): " . $e->getMessage());
            return [];
        }
    }
    public function obtenerUsuariosPorGrupo($idGrupo)
    {
        try {
            error_log("Intentando obtener usuarios asociados al grupo con ID: $idGrupo");

            $sql = "SELECT u.* FROM usuarios u
                INNER JOIN usuarios_grupos ug ON u.idUser = ug.idUser
                WHERE ug.idGrupo = :idGrupo";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':idGrupo', $idGrupo, PDO::PARAM_INT);

            // Ejecutar la consulta y verificar si se ejecutó correctamente
            if (!$stmt->execute()) {
                error_log("Error al ejecutar la consulta en obtenerUsuariosPorGrupo(): " . implode(", ", $stmt->errorInfo()));
                return [];
            }

            $usuariosAsociados = $stmt->fetchAll(PDO::FETCH_ASSOC); // Retorna un array de usuarios asociados

            // Registro del resultado de la consulta
            error_log("Usuarios asociados al grupo con ID $idGrupo: " . json_encode($usuariosAsociados));

            return $usuariosAsociados;
        } catch (PDOException $e) {
            error_log("Error en obtenerUsuariosPorGrupo(): " . $e->getMessage());
            return [];
        }
    }



    public function eliminarGrupo($idGrupo)
    {
        try {
            $sql = "DELETE FROM grupos WHERE idGrupo = :idGrupo";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':idGrupo', $idGrupo, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al eliminar el grupo: " . $e->getMessage());
            return false;
        }
    }


    // Método para actualizar el nombre de un grupo
    public function actualizarNombreGrupo($idGrupo, $nombreGrupo)
    {
        try {
            $sql = "UPDATE grupos SET nombre_grupo = :nombreGrupo WHERE idGrupo = :idGrupo";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':nombreGrupo', $nombreGrupo, PDO::PARAM_STR);
            $stmt->bindParam(':idGrupo', $idGrupo, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar el nombre del grupo: " . $e->getMessage());
            return false;
        }
    }


    // Método para actualizar el administrador de un grupo
    public function actualizarAdministradorGrupo($idGrupo, $idAdmin)
    {
        try {
            // Primero, eliminar cualquier administrador existente para el grupo
            $sqlDelete = "DELETE FROM administradores_grupos WHERE idGrupo = :idGrupo";
            $stmtDelete = $this->conexion->prepare($sqlDelete);
            $stmtDelete->bindParam(':idGrupo', $idGrupo, PDO::PARAM_INT);
            $stmtDelete->execute();

            // Luego, asignar el nuevo administrador al grupo
            $sqlInsert = "INSERT INTO administradores_grupos (idGrupo, idAdmin) VALUES (:idGrupo, :idAdmin)";
            $stmtInsert = $this->conexion->prepare($sqlInsert);
            $stmtInsert->bindParam(':idGrupo', $idGrupo, PDO::PARAM_INT);
            $stmtInsert->bindParam(':idAdmin', $idAdmin, PDO::PARAM_INT);
            return $stmtInsert->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar el administrador del grupo: " . $e->getMessage());
            return false;
        }
    }
    public function obtenerGrupoPorNombre($nombreGrupo)
    {
        try {
            $sql = "SELECT * FROM grupos WHERE nombre_grupo = :nombreGrupo";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':nombreGrupo', $nombreGrupo, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerGrupoPorNombre(): " . $e->getMessage());
            return false;
        }
    }

    public function añadirAdministradorAGrupo($idUser, $idGrupo)
    {
        try {
            $sql = "INSERT INTO administradores_grupos (idAdmin, idGrupo) VALUES (:idUser, :idGrupo)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
            $stmt->bindParam(':idGrupo', $idGrupo, PDO::PARAM_INT);

            if ($stmt->execute()) {
                error_log("Administrador con ID $idUser asignado al grupo con ID $idGrupo exitosamente.");
                return true;
            } else {
                throw new Exception('Fallo al asignar el administrador: ' . implode(", ", $stmt->errorInfo()));
            }
        } catch (PDOException $e) {
            error_log("Error en añadirAdministradorAGrupo(): " . $e->getMessage());
            return false;
        }
    }



    // Método para consultar un grupo por su ID
    public function consultarGrupoPorId($idGrupo)
    {
        try {
            $sql = "SELECT g.idGrupo, g.nombre_grupo, ag.idAdmin
                FROM grupos g
                LEFT JOIN administradores_grupos ag ON g.idGrupo = ag.idGrupo
                WHERE g.idGrupo = :idGrupo";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':idGrupo', $idGrupo, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al consultar el grupo por ID: " . $e->getMessage());
            return false;
        }
    }
    public function eliminarUsuarioPorId($idUser)
    {
        try {
            $sql = "DELETE FROM usuarios WHERE idUser = :idUser";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en eliminarUsuarioPorId(): " . $e->getMessage());
            return false;
        }
    }
    // Verifica si un usuario ya está asignado a una familia específica
    public function verificarUsuarioEnFamilia($idUser, $idFamilia)
    {
        $sql = "SELECT COUNT(*) FROM usuarios_familias WHERE idUser = :idUser AND idFamilia = :idFamilia";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
        $stmt->bindParam(':idFamilia', $idFamilia, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() > 0; // Retorna true si existe la asignación
    }

    // Verifica si un usuario ya está asignado a un grupo específico
    public function verificarUsuarioEnGrupo($idUser, $idGrupo)
    {
        $sql = "SELECT COUNT(*) FROM usuarios_grupos WHERE idUser = :idUser AND idGrupo = :idGrupo";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
        $stmt->bindParam(':idGrupo', $idGrupo, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() > 0; // Retorna true si existe la asignación
    }

    // classModelo.php

    // Método para obtener todos los usuarios, incluidos los administradores y sus familias/grupos
    public function obtenerUsuariosConRoles()
    {
        $sql = "
        SELECT u.*, 
               GROUP_CONCAT(DISTINCT f.nombre_familia) AS familias,
               GROUP_CONCAT(DISTINCT g.nombre_grupo) AS grupos,
               GROUP_CONCAT(DISTINCT af.idFamilia) AS familias_admin,
               GROUP_CONCAT(DISTINCT ag.idGrupo) AS grupos_admin
        FROM usuarios u
        LEFT JOIN usuarios_familias uf ON u.idUser = uf.idUser
        LEFT JOIN familias f ON uf.idFamilia = f.idFamilia
        LEFT JOIN usuarios_grupos ug ON u.idUser = ug.idUser
        LEFT JOIN grupos g ON ug.idGrupo = g.idGrupo
        LEFT JOIN administradores_familias af ON u.idUser = af.idAdmin
        LEFT JOIN familias f_admin ON af.idFamilia = f_admin.idFamilia
        LEFT JOIN administradores_grupos ag ON u.idUser = ag.idAdmin
        LEFT JOIN grupos g_admin ON ag.idGrupo = g_admin.idGrupo
        GROUP BY u.idUser";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerUsuariosGestionadosConRoles($idAdmin, $filtros = [])
    {
        // Construcción de la consulta base
        $sql = "
        SELECT u.*, 
               GROUP_CONCAT(DISTINCT f.nombre_familia) AS familias,
               GROUP_CONCAT(DISTINCT g.nombre_grupo) AS grupos,
               GROUP_CONCAT(DISTINCT f_admin.nombre_familia) AS familias_admin,
               GROUP_CONCAT(DISTINCT g_admin.nombre_grupo) AS grupos_admin
        FROM usuarios u
        LEFT JOIN usuarios_familias uf ON u.idUser = uf.idUser
        LEFT JOIN familias f ON uf.idFamilia = f.idFamilia
        LEFT JOIN usuarios_grupos ug ON u.idUser = ug.idUser
        LEFT JOIN grupos g ON ug.idGrupo = g.idGrupo
        LEFT JOIN administradores_familias af ON u.idUser = af.idAdmin AND af.idAdmin = :idAdmin
        LEFT JOIN familias f_admin ON af.idFamilia = f_admin.idFamilia
        LEFT JOIN administradores_grupos ag ON u.idUser = ag.idAdmin AND ag.idAdmin = :idAdmin
        LEFT JOIN grupos g_admin ON ag.idGrupo = g_admin.idGrupo
        WHERE u.idUser IS NOT NULL";

        // Agregar filtros condicionales si están presentes
        $params = [':idAdmin' => $idAdmin];
        if (!empty($filtros['nombre'])) {
            $sql .= " AND u.nombre LIKE :nombre";
            $params[':nombre'] = '%' . $filtros['nombre'] . '%';
        }
        if (!empty($filtros['apellido'])) {
            $sql .= " AND u.apellido LIKE :apellido";
            $params[':apellido'] = '%' . $filtros['apellido'] . '%';
        }
        if (!empty($filtros['alias'])) {
            $sql .= " AND u.alias LIKE :alias";
            $params[':alias'] = '%' . $filtros['alias'] . '%';
        }
        if (!empty($filtros['email'])) {
            $sql .= " AND u.email LIKE :email";
            $params[':email'] = '%' . $filtros['email'] . '%';
        }
        if (!empty($filtros['nivel_usuario'])) {
            $sql .= " AND u.nivel_usuario = :nivel_usuario";
            $params[':nivel_usuario'] = $filtros['nivel_usuario'];
        }

        $sql .= " GROUP BY u.idUser";

        // Preparar y ejecutar consulta con los parámetros
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    // Método para asociar un administrador a una familia
    public function asignarAdminAFamilia($idAdmin, $idFamilia)
    {
        $sql = "INSERT INTO administradores_familias (idAdmin, idFamilia) VALUES (:idAdmin, :idFamilia)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':idAdmin', $idAdmin, PDO::PARAM_INT);
        $stmt->bindParam(':idFamilia', $idFamilia, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Método para asociar un administrador a un grupo
    public function asignarAdminAGrupo($idAdmin, $idGrupo)
    {
        $sql = "INSERT INTO administradores_grupos (idAdmin, idGrupo) VALUES (:idAdmin, :idGrupo)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':idAdmin', $idAdmin, PDO::PARAM_INT);
        $stmt->bindParam(':idGrupo', $idGrupo, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Método para obtener las familias que administra un usuario
    public function obtenerFamiliasAdministradasPorUsuario($idUser)
    {
        $sql = "SELECT f.* FROM familias f
            INNER JOIN administradores_familias af ON f.idFamilia = af.idFamilia
            WHERE af.idAdmin = :idUser";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para obtener los grupos que administra un usuario
    public function obtenerGruposAdministradosPorUsuario($idUser)
    {
        $sql = "SELECT g.* FROM grupos g
            INNER JOIN administradores_grupos ag ON g.idGrupo = ag.idGrupo
            WHERE ag.idAdmin = :idUser";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function obtenerUsuariosConAdministradores()
    {
        try {
            $sql = "
            SELECT u.*, 
                   CASE 
                       WHEN u.nivel_usuario = 'admin' THEN 'Administrador'
                       ELSE 'Usuario'
                   END AS tipo_usuario
            FROM usuarios u
            WHERE u.estado_usuario = 'activo'
              AND (u.nivel_usuario = 'usuario' OR u.nivel_usuario = 'admin')
            ORDER BY u.nivel_usuario DESC, u.nombre ASC
        ";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerUsuariosConAdministradores(): " . $e->getMessage());
            return [];
        }
    }
    public function actualizarRolUsuario($idUsuario, $rolUsuario)
    {
        try {
            // Asegurarse de que el rol es válido
            $rolesValidos = ['superadmin', 'admin', 'usuario', 'registro'];
            if (!in_array($rolUsuario, $rolesValidos)) {
                throw new Exception("Rol inválido: $rolUsuario");
            }

            // Preparar la consulta de actualización
            $sql = "UPDATE usuarios SET nivel_usuario = :rolUsuario WHERE idUser = :idUsuario";
            $stmt = $this->getConexion()->prepare($sql);

            // Ejecutar la consulta
            $stmt->bindParam(':rolUsuario', $rolUsuario);
            $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
            $stmt->execute();

            // Verificar que se realizó la actualización
            if ($stmt->rowCount() > 0) {
                error_log("Rol actualizado correctamente para el usuario ID $idUsuario a $rolUsuario");
                return true;
            } else {
                error_log("No se pudo actualizar el rol para el usuario ID $idUsuario. Verifica si el usuario existe.");
                return false;
            }
        } catch (Exception $e) {
            error_log("Error en actualizarRolUsuario(): " . $e->getMessage());
            return false;
        }
    }
    public function obtenerUsuariosConAsociaciones()
    {
        $sql = "
        SELECT u.*, 
            GROUP_CONCAT(DISTINCT f.nombre_familia SEPARATOR ', ') AS familias,
            GROUP_CONCAT(DISTINCT g.nombre_grupo SEPARATOR ', ') AS grupos
        FROM usuarios u
        LEFT JOIN usuarios_familias uf ON u.idUser = uf.idUser
        LEFT JOIN familias f ON uf.idFamilia = f.idFamilia
        LEFT JOIN usuarios_grupos ug ON u.idUser = ug.idUser
        LEFT JOIN grupos g ON ug.idGrupo = g.idGrupo
        LEFT JOIN administradores_familias af ON u.idUser = af.idAdmin
        LEFT JOIN familias f2 ON af.idFamilia = f2.idFamilia
        LEFT JOIN administradores_grupos ag ON u.idUser = ag.idAdmin
        LEFT JOIN grupos g2 ON ag.idGrupo = g2.idGrupo
        GROUP BY u.idUser
    ";

        $stmt = $this->getConexion()->prepare($sql);
        $stmt->execute();

        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($usuarios as &$usuario) {
            $usuario['familias'] = $usuario['familias'] ?: 'Sin Familia';
            $usuario['grupos'] = $usuario['grupos'] ?: 'Sin Grupo';
            $usuario['tipo_usuario'] = $this->determinarTipoUsuario($usuario);
        }

        return $usuarios;
    }
    public function obtenerUsuariosConFamiliasYGrupos($filtros = [])
    {
        // Construcción de la consulta base
        $sql = "
        SELECT u.idUser, u.nombre, u.apellido, u.alias, u.email, u.nivel_usuario,
               GROUP_CONCAT(DISTINCT f.nombre_familia ORDER BY f.nombre_familia ASC) AS familias,
               GROUP_CONCAT(DISTINCT g.nombre_grupo ORDER BY g.nombre_grupo ASC) AS grupos
        FROM usuarios u
        LEFT JOIN usuarios_familias uf ON u.idUser = uf.idUser
        LEFT JOIN familias f ON uf.idFamilia = f.idFamilia
        LEFT JOIN usuarios_grupos ug ON u.idUser = ug.idUser
        LEFT JOIN grupos g ON ug.idGrupo = g.idGrupo
        WHERE u.idUser IS NOT NULL";

        // Agregar filtros condicionales si están presentes
        $params = [];
        if (!empty($filtros['nombre'])) {
            $sql .= " AND u.nombre LIKE :nombre";
            $params[':nombre'] = '%' . $filtros['nombre'] . '%';
        }
        if (!empty($filtros['apellido'])) {
            $sql .= " AND u.apellido LIKE :apellido";
            $params[':apellido'] = '%' . $filtros['apellido'] . '%';
        }
        if (!empty($filtros['alias'])) {
            $sql .= " AND u.alias LIKE :alias";
            $params[':alias'] = '%' . $filtros['alias'] . '%';
        }
        if (!empty($filtros['email'])) {
            $sql .= " AND u.email LIKE :email";
            $params[':email'] = '%' . $filtros['email'] . '%';
        }
        if (!empty($filtros['nivel_usuario'])) {
            $sql .= " AND u.nivel_usuario = :nivel_usuario";
            $params[':nivel_usuario'] = $filtros['nivel_usuario'];
        }

        $sql .= " GROUP BY u.idUser";

        // Preparar y ejecutar consulta con los parámetros
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método auxiliar para determinar el tipo de usuario
    private function determinarTipoUsuario($usuario)
    {
        if ($usuario['familias'] === 'Sin Familia' && $usuario['grupos'] === 'Sin Grupo') {
            return "Individual";
        } elseif ($usuario['familias'] !== 'Sin Familia' && $usuario['grupos'] !== 'Sin Grupo') {
            return "Familiar y en Grupo";
        } elseif ($usuario['familias'] !== 'Sin Familia') {
            return "Familiar";
        } elseif ($usuario['grupos'] !== 'Sin Grupo') {
            return "En Grupo";
        }
        return "Individual";
    }
    public function obtenerGruposConUsuariosYAdministradores($filtros = [])
    {
        try {
            // Construcción de la consulta base con joins para obtener administradores y usuarios
            $sql = "
        SELECT g.idGrupo, g.nombre_grupo,
               GROUP_CONCAT(DISTINCT a.alias ORDER BY a.alias ASC SEPARATOR '\n') AS administradores,
               GROUP_CONCAT(DISTINCT u.alias ORDER BY u.alias ASC SEPARATOR '\n') AS usuarios
        FROM grupos g
        LEFT JOIN administradores_grupos ag ON g.idGrupo = ag.idGrupo
        LEFT JOIN usuarios a ON ag.idAdmin = a.idUser
        LEFT JOIN usuarios_grupos ug ON g.idGrupo = ug.idGrupo
        LEFT JOIN usuarios u ON ug.idUser = u.idUser
        WHERE 1=1";

            // Array para almacenar los parámetros de la consulta
            $params = [];

            // Filtros opcionales
            if (!empty($filtros['id'])) {
                $sql .= " AND g.idGrupo = :id";
                $params[':id'] = $filtros['id'];
            }
            if (!empty($filtros['nombre_grupo'])) {
                $sql .= " AND g.nombre_grupo LIKE :nombre_grupo";
                $params[':nombre_grupo'] = '%' . $filtros['nombre_grupo'] . '%';
            }
            if (!empty($filtros['administrador'])) {
                $sql .= " AND EXISTS (
                      SELECT 1
                      FROM administradores_grupos ag_inner
                      INNER JOIN usuarios a_inner ON ag_inner.idAdmin = a_inner.idUser
                      WHERE ag_inner.idGrupo = g.idGrupo AND a_inner.alias LIKE :administrador
                  )";
                $params[':administrador'] = '%' . $filtros['administrador'] . '%';
            }
            if (!empty($filtros['usuario'])) {
                $sql .= " AND EXISTS (
                      SELECT 1
                      FROM usuarios_grupos ug_inner
                      INNER JOIN usuarios u_inner ON ug_inner.idUser = u_inner.idUser
                      WHERE ug_inner.idGrupo = g.idGrupo AND u_inner.alias LIKE :usuario
                  )";
                $params[':usuario'] = '%' . $filtros['usuario'] . '%';
            }

            // Agrupar por ID del grupo para consolidar administradores y usuarios en un solo registro por grupo
            $sql .= " GROUP BY g.idGrupo";

            // Preparar y ejecutar la consulta
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);

            $grupos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Total de grupos obtenidos: " . count($grupos));
            return $grupos;
        } catch (PDOException $e) {
            error_log("Error en obtenerGruposConUsuariosYAdministradores(): " . $e->getMessage());
            throw new Exception('Error al listar los grupos.');
        }
    }


    public function obtenerFamiliasConUsuariosYAdministradores($filtros = [])
    {
        try {
            // Construir la consulta SQL base
            $sql = "
        SELECT f.idFamilia, f.nombre_familia,
            GROUP_CONCAT(DISTINCT u_admin.alias ORDER BY u_admin.alias ASC SEPARATOR '\n') AS administradores,
            GROUP_CONCAT(DISTINCT u_user.alias ORDER BY u_user.alias ASC SEPARATOR '\n') AS usuarios
        FROM familias f
        LEFT JOIN administradores_familias af ON f.idFamilia = af.idFamilia
        LEFT JOIN usuarios u_admin ON af.idAdmin = u_admin.idUser
        LEFT JOIN usuarios_familias uf ON f.idFamilia = uf.idFamilia
        LEFT JOIN usuarios u_user ON uf.idUser = u_user.idUser
        WHERE 1=1
        ";

            // Array para los parámetros de la consulta
            $params = [];

            // Aplicar filtros si existen
            if (!empty($filtros['id'])) {
                $sql .= " AND f.idFamilia = :id";
                $params[':id'] = $filtros['id'];
            }
            if (!empty($filtros['nombre_familia'])) {
                $sql .= " AND f.nombre_familia LIKE :nombre_familia";
                $params[':nombre_familia'] = '%' . $filtros['nombre_familia'] . '%';
            }
            if (!empty($filtros['administrador'])) {
                $sql .= " AND u_admin.alias LIKE :administrador";
                $params[':administrador'] = '%' . $filtros['administrador'] . '%';
            }
            if (!empty($filtros['usuario'])) {
                $sql .= " AND u_user.alias LIKE :usuario";
                $params[':usuario'] = '%' . $filtros['usuario'] . '%';
            }

            // Agrupar resultados para combinar administradores y usuarios en una sola fila por familia
            $sql .= " GROUP BY f.idFamilia";

            // Preparar y ejecutar la consulta
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);

            // Obtener resultados
            $familias = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Total de familias obtenidas con filtros: " . count($familias));
            return $familias;
        } catch (PDOException $e) {
            error_log("Error en obtenerFamiliasConUsuariosYAdministradores(): " . $e->getMessage());
            throw new Exception('Error al listar las familias con administradores y usuarios.');
        }
    }
    // Método para actualizar los usuarios asignados a una familia
    public function actualizarUsuariosFamilia($idFamilia, $usuariosAsignados)
    {
        try {
            // Iniciar transacción
            $this->conexion->beginTransaction();

            // Eliminar todas las relaciones actuales de usuarios con la familia
            $sqlDelete = "DELETE FROM usuarios_familias WHERE idFamilia = :idFamilia";
            $stmtDelete = $this->conexion->prepare($sqlDelete);
            $stmtDelete->bindParam(':idFamilia', $idFamilia, PDO::PARAM_INT);
            $stmtDelete->execute();

            // Insertar nuevas relaciones de usuarios con la familia
            $sqlInsert = "INSERT INTO usuarios_familias (idFamilia, idUser) VALUES (:idFamilia, :idUser)";
            $stmtInsert = $this->conexion->prepare($sqlInsert);
            foreach ($usuariosAsignados as $idUser) {
                $stmtInsert->bindParam(':idFamilia', $idFamilia, PDO::PARAM_INT);
                $stmtInsert->bindParam(':idUser', $idUser, PDO::PARAM_INT);
                $stmtInsert->execute();
            }

            // Confirmar transacción
            $this->conexion->commit();
            return true;
        } catch (PDOException $e) {
            $this->conexion->rollBack();
            error_log("Error en actualizarUsuariosFamilia(): " . $e->getMessage());
            return false;
        }
    }

    // Método para actualizar los usuarios asignados a un grupo
    public function actualizarUsuariosGrupo($idGrupo, $usuariosAsignados)
    {
        try {
            // Iniciar transacción
            $this->conexion->beginTransaction();

            // Eliminar todas las relaciones actuales de usuarios con el grupo
            $sqlDelete = "DELETE FROM usuarios_grupos WHERE idGrupo = :idGrupo";
            $stmtDelete = $this->conexion->prepare($sqlDelete);
            $stmtDelete->bindParam(':idGrupo', $idGrupo, PDO::PARAM_INT);
            $stmtDelete->execute();

            // Insertar nuevas relaciones de usuarios con el grupo
            $sqlInsert = "INSERT INTO usuarios_grupos (idGrupo, idUser) VALUES (:idGrupo, :idUser)";
            $stmtInsert = $this->conexion->prepare($sqlInsert);
            foreach ($usuariosAsignados as $idUser) {
                $stmtInsert->bindParam(':idGrupo', $idGrupo, PDO::PARAM_INT);
                $stmtInsert->bindParam(':idUser', $idUser, PDO::PARAM_INT);
                $stmtInsert->execute();
            }

            // Confirmar transacción
            $this->conexion->commit();
            return true;
        } catch (PDOException $e) {
            $this->conexion->rollBack();
            error_log("Error en actualizarUsuariosGrupo(): " . $e->getMessage());
            return false;
        }
    }
    // Método para actualizar los administradores asignados a una familia
    public function actualizarAdministradoresFamilia($idFamilia, $administradoresAsignados)
    {
        try {
            // Eliminar administradores actuales de la familia en la tabla de relación
            $sqlEliminar = "DELETE FROM administradores_familias WHERE idFamilia = :idFamilia";
            $stmtEliminar = $this->conexion->prepare($sqlEliminar);
            $stmtEliminar->bindParam(':idFamilia', $idFamilia, PDO::PARAM_INT);
            $stmtEliminar->execute();

            // Insertar los nuevos administradores asignados
            $sqlInsertar = "INSERT INTO administradores_familias (idFamilia, idAdmin) VALUES (:idFamilia, :idAdmin)";
            $stmtInsertar = $this->conexion->prepare($sqlInsertar);

            foreach ($administradoresAsignados as $idAdmin) {
                $stmtInsertar->bindParam(':idFamilia', $idFamilia, PDO::PARAM_INT);
                $stmtInsertar->bindParam(':idAdmin', $idAdmin, PDO::PARAM_INT);
                $stmtInsertar->execute();
            }

            return true;
        } catch (PDOException $e) {
            error_log("Error en actualizarAdministradoresFamilia(): " . $e->getMessage());
            return false;
        }
    }

    // Método para obtener la lista de administradores asignados a una familia
    public function obtenerAdministradoresPorFamilia($idFamilia)
    {
        try {
            $sql = "SELECT idAdmin FROM administradores_familias WHERE idFamilia = :idFamilia";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':idFamilia', $idFamilia, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_COLUMN); // Devuelve solo los IDs de los administradores
        } catch (PDOException $e) {
            error_log("Error en obtenerAdministradoresPorFamilia(): " . $e->getMessage());
            return [];
        }
    }

    // Método para actualizar los administradores asignados a un grupo
    public function actualizarAdministradoresGrupo($idGrupo, $administradoresAsignados)
    {
        try {
            // Eliminar administradores actuales del grupo en la tabla de relación
            $sqlEliminar = "DELETE FROM administradores_grupos WHERE idGrupo = :idGrupo";
            $stmtEliminar = $this->conexion->prepare($sqlEliminar);
            $stmtEliminar->bindParam(':idGrupo', $idGrupo, PDO::PARAM_INT);
            $stmtEliminar->execute();

            // Insertar los nuevos administradores asignados
            $sqlInsertar = "INSERT INTO administradores_grupos (idGrupo, idAdmin) VALUES (:idGrupo, :idAdmin)";
            $stmtInsertar = $this->conexion->prepare($sqlInsertar);

            foreach ($administradoresAsignados as $idAdmin) {
                $stmtInsertar->bindParam(':idGrupo', $idGrupo, PDO::PARAM_INT);
                $stmtInsertar->bindParam(':idAdmin', $idAdmin, PDO::PARAM_INT);
                $stmtInsertar->execute();
            }

            return true;
        } catch (PDOException $e) {
            error_log("Error en actualizarAdministradoresGrupo(): " . $e->getMessage());
            return false;
        }
    }

    // Método para obtener la lista de administradores asignados a un grupo
    public function obtenerAdministradoresPorGrupo($idGrupo)
    {
        try {
            $sql = "SELECT idAdmin FROM administradores_grupos WHERE idGrupo = :idGrupo";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':idGrupo', $idGrupo, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_COLUMN); // Devuelve solo los IDs de los administradores
        } catch (PDOException $e) {
            error_log("Error en obtenerAdministradoresPorGrupo(): " . $e->getMessage());
            return [];
        }
    }
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

    // Insertar nueva categoría de gasto
    public function insertarCategoriaGasto($nombreCategoria, $creadoPor)
    {
        try {
            $sql = "INSERT INTO categorias (nombreCategoria, tipo_categoria, creado_por) VALUES (:nombreCategoria, 'gasto', :creadoPor)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':nombreCategoria', $nombreCategoria, PDO::PARAM_STR);
            $stmt->bindValue(':creadoPor', $creadoPor, PDO::PARAM_INT); // Asegura el tipo de parámetro para 'creado_por'

            // Ejecutar la inserción y retornar true si es exitosa
            if ($stmt->execute()) {
                return true;
            } else {
                error_log("Error al ejecutar la inserción de categoría de gasto: " . implode(", ", $stmt->errorInfo()));
                return false;
            }
        } catch (PDOException $e) {
            error_log("Error al insertar la categoría de gasto: " . $e->getMessage());
            return false;
        }
    }


    public function insertarCategoriaIngreso($nombreCategoria, $creadoPor)
    {
        try {
            $sql = "INSERT INTO categorias (nombreCategoria, tipo_categoria, creado_por) VALUES (:nombreCategoria, 'ingreso', :creadoPor)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':nombreCategoria', $nombreCategoria);
            $stmt->bindValue(':creadoPor', $creadoPor);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al insertar categoría de ingreso: " . $e->getMessage());
            return false;
        }
    }


    // Actualizar categoría de gasto
    public function actualizarCategoriaGasto($idCategoria, $nombreCategoria)
    {
        try {
            $sql = "UPDATE categorias SET nombreCategoria = :nombreCategoria WHERE idCategoria = :idCategoria";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':nombreCategoria', $nombreCategoria, PDO::PARAM_STR);
            $stmt->bindValue(':idCategoria', $idCategoria, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en actualizarCategoriaGasto(): " . $e->getMessage());
            return false;
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

    public function actualizarCategoriaIngreso($idCategoria, $nombreCategoria)
    {
        try {
            $sql = "UPDATE categorias SET nombreCategoria = :nombreCategoria WHERE idCategoria = :idCategoria AND tipo_categoria = 'ingreso'";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':nombreCategoria', $nombreCategoria);
            $stmt->bindValue(':idCategoria', $idCategoria, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en actualizarCategoriaIngreso(): " . $e->getMessage());
            return false;
        }
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

    // Eliminar categoría de gasto solo si no está en uso
    public function eliminarCategoriaGasto($idCategoria)
    {
        $sql = "DELETE FROM categorias WHERE idCategoria = :idCategoria";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idCategoria', $idCategoria, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function categoriaIngresoEnUso($idCategoria)
    {
        try {
            $sql = "SELECT COUNT(*) FROM ingresos WHERE idCategoria = :idCategoria";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':idCategoria', $idCategoria, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (Exception $e) {
            error_log("Error en categoriaIngresoEnUso(): " . $e->getMessage());
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
    public function obtenerCategoriasGastosConDetalles($filtros)
    {
        try {
            $sql = "SELECT 
                    c.idCategoria,
                    c.nombreCategoria,
                    c.creado_por AS idUser,
                    u.alias AS creado_por_alias,
                    u.nivel_usuario AS creado_por_rol,
                    c.estado_categoria,
                    c.tipo_categoria
                FROM categorias c
                LEFT JOIN usuarios u ON c.creado_por = u.idUser
                WHERE c.tipo_categoria = 'gasto'";

            // Aplicar filtros dinámicos
            $params = [];
            if (!empty($filtros['idCategoria'])) {
                $sql .= " AND c.idCategoria = :idCategoria";
                $params[':idCategoria'] = $filtros['idCategoria'];
            }
            if (!empty($filtros['nombreCategoria'])) {
                $sql .= " AND c.nombreCategoria LIKE :nombreCategoria";
                $params[':nombreCategoria'] = '%' . $filtros['nombreCategoria'] . '%';
            }
            if (!empty($filtros['creado_por_alias'])) {
                $sql .= " AND u.alias LIKE :creado_por_alias";
                $params[':creado_por_alias'] = '%' . $filtros['creado_por_alias'] . '%';
            }
            if (!empty($filtros['creado_por_id'])) {
                $sql .= " AND u.idUser = :creado_por_id";
                $params[':creado_por_id'] = $filtros['creado_por_id'];
            }
            if (!empty($filtros['creado_por_rol'])) {
                $sql .= " AND u.nivel_usuario = :creado_por_rol";
                $params[':creado_por_rol'] = $filtros['creado_por_rol'];
            }

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerCategoriasGastosConDetalles: " . $e->getMessage());
            return [];
        }
    }


    public function obtenerCategoriasIngresosConDetalles($filtros)
    {
        try {
            $sql = "SELECT 
                    c.idCategoria,
                    c.nombreCategoria,
                    c.creado_por AS idUser,
                    u.alias AS creado_por_alias,
                    u.nivel_usuario AS creado_por_rol,
                    c.estado_categoria,
                    c.tipo_categoria
                FROM categorias c
                LEFT JOIN usuarios u ON c.creado_por = u.idUser
                WHERE c.tipo_categoria = 'ingreso'";

            // Aplicar filtros dinámicos
            $params = [];
            if (!empty($filtros['idCategoria'])) {
                $sql .= " AND c.idCategoria = :idCategoria";
                $params[':idCategoria'] = $filtros['idCategoria'];
            }
            if (!empty($filtros['nombreCategoria'])) {
                $sql .= " AND c.nombreCategoria LIKE :nombreCategoria";
                $params[':nombreCategoria'] = '%' . $filtros['nombreCategoria'] . '%';
            }
            if (!empty($filtros['creado_por_alias'])) {
                $sql .= " AND u.alias LIKE :creado_por_alias";
                $params[':creado_por_alias'] = '%' . $filtros['creado_por_alias'] . '%';
            }
            if (!empty($filtros['creado_por_id'])) {
                $sql .= " AND u.idUser = :creado_por_id";
                $params[':creado_por_id'] = $filtros['creado_por_id'];
            }
            if (!empty($filtros['creado_por_rol'])) {
                $sql .= " AND u.nivel_usuario = :creado_por_rol";
                $params[':creado_por_rol'] = $filtros['creado_por_rol'];
            }

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerCategoriasIngresosConDetalles: " . $e->getMessage());
            return [];
        }
    }
    function obtenerFamiliasAdmin($adminId)
    {
        // Consulta a la base de datos para obtener las familias que administra el usuario actual
        $conexion = (new GastosModelo())->getConexion();
        $stmt = $conexion->prepare("SELECT * FROM administradores_familias WHERE idAdmin = :adminId");
        $stmt->execute(['adminId' => $adminId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function obtenerGruposAdmin($adminId)
    {
        // Consulta a la base de datos para obtener los grupos que administra el usuario actual
        $conexion = (new GastosModelo())->getConexion();
        $stmt = $conexion->prepare("SELECT * FROM administradores_grupos WHERE idAdmin = :adminId");
        $stmt->execute(['adminId' => $adminId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ******** Métodos para gestionar los gastos ********

    // Obtener todos los gastos de un usuario
    public function obtenerGastosPorUsuario($userId)
    {
        $sql = "SELECT * FROM gastos WHERE idUser = :userId";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Insertar un nuevo gasto
    public function insertarGasto($datosGasto)
    {
        $sql = "INSERT INTO gastos (idUser, descripcion, monto, fecha) VALUES (:idUser, :descripcion, :monto, :fecha)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':idUser', $datosGasto['idUser'], PDO::PARAM_INT);
        $stmt->bindParam(':descripcion', $datosGasto['descripcion']);
        $stmt->bindParam(':monto', $datosGasto['monto']);
        $stmt->bindParam(':fecha', $datosGasto['fecha']);
        return $stmt->execute();
    }

    // Obtener un gasto por su ID
    public function obtenerGastoPorId($gastoId)
    {
        $sql = "SELECT * FROM gastos WHERE id = :gastoId";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':gastoId', $gastoId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualizar un gasto por ID
    public function actualizarGasto($gastoId, $nuevosDatos)
    {
        $sql = "UPDATE gastos SET descripcion = :descripcion, monto = :monto, fecha = :fecha WHERE id = :gastoId";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':gastoId', $gastoId, PDO::PARAM_INT);
        $stmt->bindParam(':descripcion', $nuevosDatos['descripcion']);
        $stmt->bindParam(':monto', $nuevosDatos['monto']);
        $stmt->bindParam(':fecha', $nuevosDatos['fecha']);
        return $stmt->execute();
    }

    // Eliminar un gasto por ID
    public function eliminarGastoPorId($gastoId)
    {
        $sql = "DELETE FROM gastos WHERE id = :gastoId";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':gastoId', $gastoId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // ******** Métodos para gestionar los ingresos ********

    // Obtener todos los ingresos de un usuario
    public function obtenerIngresosPorUsuario($userId)
    {
        $sql = "SELECT * FROM ingresos WHERE idUser = :userId";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Insertar un nuevo ingreso
    public function insertarIngreso($datosIngreso)
    {
        $sql = "INSERT INTO ingresos (idUser, descripcion, monto, fecha) VALUES (:idUser, :descripcion, :monto, :fecha)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':idUser', $datosIngreso['idUser'], PDO::PARAM_INT);
        $stmt->bindParam(':descripcion', $datosIngreso['descripcion']);
        $stmt->bindParam(':monto', $datosIngreso['monto']);
        $stmt->bindParam(':fecha', $datosIngreso['fecha']);
        return $stmt->execute();
    }

    // Obtener un ingreso por ID
    public function obtenerIngresoPorId($ingresoId)
    {
        $sql = "SELECT * FROM ingresos WHERE id = :ingresoId";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':ingresoId', $ingresoId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualizar un ingreso por ID
    public function actualizarIngreso($ingresoId, $nuevosDatos)
    {
        $sql = "UPDATE ingresos SET descripcion = :descripcion, monto = :monto, fecha = :fecha WHERE id = :ingresoId";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':ingresoId', $ingresoId, PDO::PARAM_INT);
        $stmt->bindParam(':descripcion', $nuevosDatos['descripcion']);
        $stmt->bindParam(':monto', $nuevosDatos['monto']);
        $stmt->bindParam(':fecha', $nuevosDatos['fecha']);
        return $stmt->execute();
    }

    // Eliminar un ingreso por ID
    public function eliminarIngresoPorId($ingresoId)
    {
        $sql = "DELETE FROM ingresos WHERE id = :ingresoId";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':ingresoId', $ingresoId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // ******** Métodos adicionales para restricciones de familias y grupos ********

    // Contar la cantidad de familias a las que pertenece un usuario
    public function contarFamiliasUsuario($userId)
    {
        $sql = "SELECT COUNT(*) FROM usuarios_familias WHERE idUser = :userId";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    // Contar la cantidad de usuarios en una familia
    public function contarUsuariosFamilia($familiaId)
    {
        $sql = "SELECT COUNT(*) FROM usuarios_familias WHERE id_familia = :familiaId";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':familiaId', $familiaId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    // Contar la cantidad de grupos a los que pertenece un usuario
    public function contarGruposUsuario($userId)
    {
        $sql = "SELECT COUNT(*) FROM usuarios_grupos WHERE idUser = :userId";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    // Contar la cantidad de usuarios en un grupo
    public function contarUsuariosGrupo($grupoId)
    {
        $sql = "SELECT COUNT(*) FROM usuarios_grupos WHERE id_grupo = :grupoId";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':grupoId', $grupoId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    public function obtenerSituacionPorUsuario($userId)
    {
        // Ajusta esta consulta según la estructura de tus tablas de situación financiera
        $sql = "SELECT * FROM situacion_financiera WHERE idUser = :userId";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener el resumen financiero de un usuario específico
    public function obtenerResumenFinancieroPorUsuario($userId)
    {
        $sql = "SELECT SUM(cantidad) AS total_gastos FROM gastos WHERE idUser = :userId
            UNION ALL
            SELECT SUM(cantidad) AS total_ingresos FROM ingresos WHERE idUser = :userId";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);  // Devuelve un resumen con total de gastos e ingresos
    }
    public function agregarUsuarioAFamilia($idUser, $idFamilia)
    {
        try {
            $sql = "INSERT INTO usuarios_familias (idUser, idFamilia) VALUES (:idUser, :idFamilia)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
            $stmt->bindParam(':idFamilia', $idFamilia, PDO::PARAM_INT);
            $stmt->execute();

            return true;
        } catch (PDOException $e) {
            error_log("Error en agregarUsuarioAFamilia: " . $e->getMessage());
            throw new Exception("No se pudo asignar el usuario a la familia.");
        }
    }
    


    





    ////////////////////////////NO USUADO AHORA//////////////////////////////////////////////////
    /*public function obtenerValoresUnicos($campo)
    {
        $sql = "SELECT DISTINCT $campo FROM usuarios WHERE $campo IS NOT NULL ORDER BY $campo ASC";
        // Depuración
        error_log("Ejecutando obtenerValoresUnicos para el campo: $campo");

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_COLUMN);

        error_log("Resultados obtenidos para $campo: " . json_encode($result));
        return $result;
    }*/
    ////////////////////////////NO USUADO AHORA//////////////////////////////////////////////////
}
