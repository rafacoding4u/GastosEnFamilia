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
    public function obtenerUltimoId()
    {
        try {
            // Devuelve el último ID insertado usando la conexión PDO
            return $this->conexion->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error al obtener el último ID insertado: " . $e->getMessage());
            return false;
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

    // Función para registrar el acceso en la tabla de auditoría
    public function registrarAcceso($idUser, $accion)
    {
        try {
            $sql = "INSERT INTO auditoria_accesos (idUser, accion) VALUES (:idUser, :accion)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':idUser', $idUser);
            $stmt->bindParam(':accion', $accion);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Error al registrar el acceso: " . $e->getMessage();
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

    public function obtenerUsuarioPorId($idUser)
    {
        $sql = "SELECT * FROM usuarios WHERE idUser = :idUser";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idUser', $idUser, PDO::PARAM_INT);
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

    public function insertarUsuario($nombre, $apellido, $alias, $hashedPassword, $nivel_usuario, $fecha_nacimiento = null, $email = null, $telefono = null, $hashedPasswordPremium = null)
    {
        try {
            $sql = "INSERT INTO usuarios (nombre, apellido, alias, contrasenya, nivel_usuario, fecha_nacimiento, email, telefono, password_premium)
                VALUES (:nombre, :apellido, :alias, :contrasenya, :nivel_usuario, :fecha_nacimiento, :email, :telefono, :password_premium)";

            $stmt = $this->getConexion()->prepare($sql);
            $stmt->bindValue(':nombre', $nombre);
            $stmt->bindValue(':apellido', $apellido);
            $stmt->bindValue(':alias', $alias);
            $stmt->bindValue(':contrasenya', $hashedPassword);
            $stmt->bindValue(':nivel_usuario', $nivel_usuario);
            $stmt->bindValue(':fecha_nacimiento', $fecha_nacimiento);
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':telefono', $telefono);
            $stmt->bindValue(':password_premium', $hashedPasswordPremium);

            $stmt->execute();
            return $this->getConexion()->lastInsertId();
        } catch (Exception $e) {
            error_log("Error al insertar usuario: " . $e->getMessage());
            return false;
        }
    }

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
    // Para actualizar las contraseñas no encriptadas
    public function actualizarContraseñasUsuariosGruposFamilias()
    {
        // Función para detectar si una contraseña está encriptada o no
        function esContraseñaEncriptada($password)
        {
            // Las contraseñas encriptadas con password_hash() suelen tener una longitud mínima de 60 caracteres
            return strlen($password) === 60;
        }

        // Actualizar las contraseñas de todas las familias
        $familias = $this->obtenerFamilias();
        foreach ($familias as $familia) {
            echo "Familia ID: " . $familia['idFamilia'] . "<br>";

            if (!isset($familia['password']) || empty($familia['password'])) {
                echo "La familia con ID {$familia['idFamilia']} no tiene una contraseña definida.\n";
                continue;
            }

            // Comprobar si la contraseña ya está encriptada
            if (!esContraseñaEncriptada($familia['password'])) {
                // Mantener la contraseña actual pero encriptarla
                $hashedPassword = password_hash($familia['password'], PASSWORD_DEFAULT);
                $sql = "UPDATE familias SET password = :hashedPassword WHERE idFamilia = :idFamilia";
                $stmt = $this->getConexion()->prepare($sql);
                $stmt->bindValue(':hashedPassword', $hashedPassword);
                $stmt->bindValue(':idFamilia', $familia['idFamilia']);
                $stmt->execute();

                echo "Contraseña de la familia con ID {$familia['idFamilia']} encriptada correctamente.\n";
            } else {
                echo "Contraseña de la familia con ID {$familia['idFamilia']} ya estaba encriptada.\n";
            }
        }

        // Actualizar las contraseñas de todos los grupos
        $grupos = $this->obtenerGrupos();
        foreach ($grupos as $grupo) {
            echo "Grupo ID: " . $grupo['idGrupo'] . "<br>";

            if (!isset($grupo['password']) || empty($grupo['password'])) {
                echo "El grupo con ID {$grupo['idGrupo']} no tiene una contraseña definida.\n";
                continue;
            }

            // Comprobar si la contraseña ya está encriptada
            if (!esContraseñaEncriptada($grupo['password'])) {
                // Mantener la contraseña actual pero encriptarla
                $hashedPassword = password_hash($grupo['password'], PASSWORD_DEFAULT);
                $sql = "UPDATE grupos SET password = :hashedPassword WHERE idGrupo = :idGrupo";
                $stmt = $this->getConexion()->prepare($sql);
                $stmt->bindValue(':hashedPassword', $hashedPassword);
                $stmt->bindValue(':idGrupo', $grupo['idGrupo']);
                $stmt->execute();

                echo "Contraseña del grupo con ID {$grupo['idGrupo']} encriptada correctamente.\n";
            } else {
                echo "Contraseña del grupo con ID {$grupo['idGrupo']} ya estaba encriptada.\n";
            }
        }

        // Actualizar las contraseñas de todos los usuarios
        $usuarios = $this->obtenerUsuarios();
        foreach ($usuarios as $usuario) {
            // Verificar si la contraseña está encriptada
            if (!esContraseñaEncriptada($usuario['contrasenya'])) {
                // Encriptar la contraseña actual sin cambiarla
                $hashedPassword = password_hash($usuario['contrasenya'], PASSWORD_DEFAULT);
                $sql = "UPDATE usuarios SET contrasenya = :hashedPassword WHERE idUser = :idUser";
                $stmt = $this->getConexion()->prepare($sql);
                $stmt->bindValue(':hashedPassword', $hashedPassword);
                $stmt->bindValue(':idUser', $usuario['idUser']);
                $stmt->execute();

                echo "Contraseña del usuario con ID {$usuario['idUser']} encriptada correctamente.\n";
            } else {
                echo "Contraseña del usuario con ID {$usuario['idUser']} ya estaba encriptada.\n";
            }

            // Verificar y actualizar la contraseña premium si tiene un valor asignado
            if (!empty($usuario['password_premium'])) {
                if (!esContraseñaEncriptada($usuario['password_premium'])) {
                    $hashedPasswordPremium = password_hash($usuario['password_premium'], PASSWORD_DEFAULT);
                    $sql = "UPDATE usuarios SET password_premium = :hashedPasswordPremium WHERE idUser = :idUser";
                    $stmt = $this->getConexion()->prepare($sql);
                    $stmt->bindValue(':hashedPasswordPremium', $hashedPasswordPremium);
                    $stmt->bindValue(':idUser', $usuario['idUser']);
                    $stmt->execute();

                    echo "Contraseña premium del usuario con ID {$usuario['idUser']} encriptada correctamente.\n";
                } else {
                    echo "Contraseña premium del usuario con ID {$usuario['idUser']} ya estaba encriptada.\n";
                }
            }
        }

        echo "Contraseñas de usuarios, familias, grupos y contraseñas premium verificadas y encriptadas correctamente.";
    }

    public function eliminarUsuario($idUser)
    {
        $sql = "DELETE FROM usuarios WHERE idUser = :idUser";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idUser', $idUser, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function actualizarUsuario($idUser, $nombre, $apellido, $alias, $email, $telefono, $nivel_usuario, $idFamilia = null, $idGrupo = null, $passwordFamiliaGrupo = null)
    {
        try {
            // Verificar que el usuario esté asignado
            if (empty($idUser)) {
                throw new Exception('El usuario a actualizar debe estar definido.');
            }

            // Validar si se proporciona una familia o un grupo
            $asignacionExitosa = false;
            if ($idFamilia !== null) {
                if (!$this->verificarPasswordFamilia($idFamilia, $passwordFamiliaGrupo)) {
                    throw new Exception('Contraseña de la familia incorrecta.');
                }
                // No asignar el resultado a una variable ya que el método no devuelve nada
                $this->asignarUsuarioAFamilia($idUser, $idFamilia);
                $asignacionExitosa = true;  // Indicar éxito manualmente
            }

            if ($idGrupo !== null) {
                if (!$this->verificarPasswordGrupo($idGrupo, $passwordFamiliaGrupo)) {
                    throw new Exception('Contraseña del grupo incorrecta.');
                }
                // No asignar el resultado a una variable ya que el método no devuelve nada
                $this->asignarUsuarioAGrupo($idUser, $idGrupo);
                $asignacionExitosa = true;  // Indicar éxito manualmente
            }


            // Si no se asignó ni a familia ni a grupo, quitar cualquier asociación existente
            if ($idFamilia === null && $idGrupo === null) {
                $this->quitarUsuarioDeFamiliaOGrupo($idUser);
            }

            // Permitir que el usuario no esté asignado a una familia o grupo (usuario individual)
            if (empty($idFamilia) && empty($idGrupo)) {
                $idFamilia = null;
                $idGrupo = null;
            }

            // Actualizar información del usuario en la tabla usuarios
            $sql = "UPDATE usuarios 
            SET nombre = :nombre, apellido = :apellido, alias = :alias, email = :email, telefono = :telefono, 
                nivel_usuario = :nivel_usuario, idFamilia = :idFamilia, idGrupo = :idGrupo
            WHERE idUser = :idUser";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':nombre', $nombre, PDO::PARAM_STR);
            $stmt->bindValue(':apellido', $apellido, PDO::PARAM_STR);
            $stmt->bindValue(':alias', $alias, PDO::PARAM_STR);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->bindValue(':telefono', $telefono, PDO::PARAM_STR);
            $stmt->bindValue(':nivel_usuario', $nivel_usuario, PDO::PARAM_STR);
            $stmt->bindValue(':idFamilia', $idFamilia, PDO::PARAM_INT);
            $stmt->bindValue(':idGrupo', $idGrupo, PDO::PARAM_INT);
            $stmt->bindValue(':idUser', $idUser, PDO::PARAM_INT);

            if (!$stmt->execute()) {
                throw new Exception("Error al actualizar el usuario.");
            }

            return true;
        } catch (Exception $e) {
            error_log("Error en actualizarUsuario: " . $e->getMessage());
            return false;
        }
    }
    // Método para quitar al usuario de cualquier asociación de familia o grupo
    public function quitarUsuarioDeFamiliaOGrupo($idUser)
    {
        try {
            // Eliminar de la tabla usuarios_familias
            $sqlFamilia = "DELETE FROM usuarios_familias WHERE idUser = :idUser";
            $stmtFamilia = $this->conexion->prepare($sqlFamilia);
            $stmtFamilia->bindValue(':idUser', $idUser, PDO::PARAM_INT);
            $stmtFamilia->execute();

            // Eliminar de la tabla usuarios_grupos
            $sqlGrupo = "DELETE FROM usuarios_grupos WHERE idUser = :idUser";
            $stmtGrupo = $this->conexion->prepare($sqlGrupo);
            $stmtGrupo->bindValue(':idUser', $idUser, PDO::PARAM_INT);
            $stmtGrupo->execute();
        } catch (Exception $e) {
            error_log("Error al quitar al usuario de la familia o grupo: " . $e->getMessage());
        }
    }





    // -------------------------------
    // Métodos relacionados con ingresos y gastos
    // -------------------------------

    // Obtener el total de ingresos para un usuario
    public function obtenerTotalIngresos($idUser)
    {
        $sql = "SELECT IFNULL(SUM(importe), 0) AS totalIngresos FROM ingresos WHERE idUser = :idUser";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idUser', $idUser, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    // Obtener el total de gastos para un usuario
    public function obtenerTotalGastos($idUser)
    {
        $sql = "SELECT IFNULL(SUM(importe), 0) AS totalGastos FROM gastos WHERE idUser = :idUser";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idUser', $idUser, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }


    // Obtener los gastos de un usuario
    public function obtenerGastosPorUsuario($idUser, $offset = 0, $limite = 10)
    {
        $sql = "SELECT * FROM gastos WHERE idUser = :idUser LIMIT :offset, :limite";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idUser', $idUser, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contarGastosPorUsuario($idUser)
    {
        $sql = "SELECT COUNT(*) FROM gastos WHERE idUser = :idUser";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idUser', $idUser, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    // Obtener los ingresos de un usuario
    public function obtenerIngresosPorUsuario($idUser)
    {
        $sql = "SELECT * FROM ingresos WHERE idUser = :idUser";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idUser', $idUser, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        JOIN usuarios_familias uf ON u.idUser = uf.idUser
        WHERE uf.idFamilia = :idFamilia";
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
        $sql = "
    SELECT u.* 
    FROM usuarios u 
    JOIN usuarios_familias uf ON u.idUser = uf.idUser 
    WHERE uf.idFamilia = :idFamilia";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idFamilia', $idFamilia, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Obtener usuarios por grupo
    public function obtenerUsuariosPorGrupo($idGrupo)
    {
        $sql = "
    SELECT u.* 
    FROM usuarios u 
    JOIN usuarios_grupos ug ON u.idUser = ug.idUser 
    WHERE ug.idGrupo = :idGrupo";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idGrupo', $idGrupo, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }




    // Añadir usuario a un grupo existente
    public function añadirUsuarioAGrupo($idUser, $idGrupo)
    {
        $sql = "UPDATE usuarios SET idGrupo = :idGrupo WHERE idUser = :idUser";
        $stmt = $this->conexion->prepare($sql);  // Reemplazar $db con $this->conexion
        $stmt->bindParam(':idGrupo', $idGrupo);
        $stmt->bindParam(':idUser', $idUser);
        return $stmt->execute();
    }

    // Añadir usuario a una familia existente
    public function añadirUsuarioAFamilia($idUser, $idFamilia)
    {
        $sql = "UPDATE usuarios SET idFamilia = :idFamilia WHERE idUser = :idUser";
        $stmt = $this->conexion->prepare($sql);  // Reemplazar $db con $this->conexion
        $stmt->bindParam(':idFamilia', $idFamilia);
        $stmt->bindParam(':idUser', $idUser);
        return $stmt->execute();
    }

    // Insertar gasto para un usuario

    public function insertarGasto($idUser, $monto, $categoria, $concepto, $origen, $fecha, $idFamilia = null, $idGrupo = null)
    {
        $sql = "INSERT INTO gastos (idUser, importe, idCategoria, concepto, origen, fecha, idFamilia, idGrupo) 
        VALUES (:idUser, :monto, :categoria, :concepto, :origen, :fecha, :idFamilia, :idGrupo)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idUser', $idUser, PDO::PARAM_INT);
        $stmt->bindValue(':monto', $monto, PDO::PARAM_STR);
        $stmt->bindValue(':categoria', $categoria, PDO::PARAM_INT);
        $stmt->bindValue(':concepto', $concepto, PDO::PARAM_STR);
        $stmt->bindValue(':origen', $origen, PDO::PARAM_STR);
        $stmt->bindValue(':fecha', $fecha, PDO::PARAM_STR);
        $stmt->bindValue(':idFamilia', $idFamilia, PDO::PARAM_INT);
        $stmt->bindValue(':idGrupo', $idGrupo, PDO::PARAM_INT);

        return $stmt->execute();
    }


    // Método para insertar ingreso para un usuario con manejo de excepciones y lógica corregida
    public function insertarIngreso($idUser, $monto, $categoria, $concepto, $origen, $fecha, $idFamilia = null, $idGrupo = null)
    {
        $sql = "INSERT INTO ingresos (idUser, importe, idCategoria, concepto, origen, fecha, idFamilia, idGrupo) 
        VALUES (:idUser, :monto, :categoria, :concepto, :origen, :fecha, :idFamilia, :idGrupo)";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idUser', $idUser, PDO::PARAM_INT);
        $stmt->bindValue(':monto', $monto, PDO::PARAM_STR);
        $stmt->bindValue(':categoria', $categoria, PDO::PARAM_INT);
        $stmt->bindValue(':concepto', $concepto, PDO::PARAM_STR);
        $stmt->bindValue(':origen', $origen, PDO::PARAM_STR);
        $stmt->bindValue(':fecha', $fecha, PDO::PARAM_STR);
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
    // Método para verificar si ya existe una familia por nombre
    public function obtenerFamiliaPorNombre($nombreFamilia)
    {
        $sql = "SELECT * FROM familias WHERE nombre_familia = :nombre_familia";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':nombre_familia', $nombreFamilia, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
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

        if ($stmt->execute()) {
            return true;
        } else {
            throw new Exception('Error al insertar familia: ' . implode(", ", $stmt->errorInfo()));
        }
    }



    public function actualizarFamilia($idFamilia, $nombreFamilia, $idAdmin)
    {
        try {
            // Verificar que idAdmin no sea NULL
            if (empty($idAdmin)) {
                throw new Exception("El administrador no puede ser nulo o vacío.");
            }

            $sql = "UPDATE familias SET nombre_familia = :nombreFamilia, idAdmin = :idAdmin WHERE idFamilia = :idFamilia";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':nombreFamilia', $nombreFamilia, PDO::PARAM_STR);
            $stmt->bindParam(':idAdmin', $idAdmin, PDO::PARAM_INT);
            $stmt->bindParam(':idFamilia', $idFamilia, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar la familia: " . $e->getMessage());
            return false;
        }
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
    public function actualizarUsuarioFamiliaGrupo($idUser, $familias = [], $grupos = [])
    {
        try {
            // Validar que el usuario esté asignado
            if (empty($idUser)) {
                throw new Exception('El usuario a actualizar debe estar definido.');
            }

            // Asignar al usuario a múltiples familias
            foreach ($familias as $idFamilia) {
                $this->asignarUsuarioAFamilia($idUser, $idFamilia);
            }

            // Asignar al usuario a múltiples grupos
            foreach ($grupos as $idGrupo) {
                $this->asignarUsuarioAGrupo($idUser, $idGrupo);
            }

            return true;
        } catch (Exception $e) {
            error_log("Error en actualizarUsuarioFamiliaGrupo: " . $e->getMessage());
            return false;
        }
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
    // Método para verificar si ya existe un grupo por nombre
    public function obtenerGrupoPorNombre($nombreGrupo)
    {
        $sql = "SELECT * FROM grupos WHERE nombre_grupo = :nombre_grupo";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':nombre_grupo', $nombreGrupo, PDO::PARAM_STR);
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


    public function actualizarGrupo($idGrupo, $nombreGrupo, $idAdmin)
    {
        $sql = "UPDATE grupos SET nombre_grupo = :nombreGrupo, idAdmin = :idAdmin WHERE idGrupo = :idGrupo";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':nombreGrupo', $nombreGrupo, PDO::PARAM_STR);
        $stmt->bindValue(':idAdmin', $idAdmin, PDO::PARAM_INT);
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
            JOIN usuarios_grupos ug ON u.idUser = ug.idUser
            WHERE ug.idGrupo = :idGrupo";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idGrupo', $idGrupo, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerSituacionFinancieraPorAdmin($idAdmin)
    {
        $sql = "
    SELECT 
        SUM(CASE WHEN i.importe IS NOT NULL THEN i.importe ELSE 0 END) AS totalIngresos,
        SUM(CASE WHEN g.importe IS NOT NULL THEN g.importe ELSE 0 END) AS totalGastos,
        (SUM(CASE WHEN i.importe IS NOT NULL THEN i.importe ELSE 0 END) - SUM(CASE WHEN g.importe IS NOT NULL THEN g.importe ELSE 0 END)) AS saldo
    FROM usuarios u
    LEFT JOIN ingresos i ON u.idUser = i.idUser
    LEFT JOIN gastos g ON u.idUser = g.idUser
    WHERE u.idUser IN (
        SELECT af.idUser 
        FROM administradores_familias af
        WHERE af.idAdmin = :idAdmin
    )";

        try {
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':idAdmin', $idAdmin, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerSituacionFinancieraPorAdmin(): " . $e->getMessage());
            return false;
        }
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
        FROM usuarios u
        LEFT JOIN ingresos i ON u.idUser = i.idUser
        LEFT JOIN gastos g ON u.idUser = g.idUser";
        
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
    public function obtenerGastosFiltrados($idUsuario, $fechaInicio, $fechaFin, $categoria, $origen, $asignado, $nombre, $offset, $limit)
    {
        $sql = "SELECT g.idGasto, g.importe, g.idCategoria, g.origen, g.concepto, g.fecha, 
                   COALESCE(f.nombre_familia, gr.nombre_grupo, u.alias, 'No especificado') AS nombre_asociacion,
                   COALESCE(c.nombreCategoria, 'Sin categoría') AS nombreCategoria
            FROM gastos AS g
            LEFT JOIN familias AS f ON g.idFamilia = f.idFamilia
            LEFT JOIN grupos AS gr ON g.idGrupo = gr.idGrupo
            LEFT JOIN usuarios AS u ON g.idUser = u.idUser
            LEFT JOIN categorias AS c ON g.idCategoria = c.idCategoria
            WHERE g.idUser = :idUsuario";

        // Agregar condiciones según los filtros
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
        if ($asignado) {
            if ($asignado === 'Familia') {
                $sql .= " AND f.nombre_familia IS NOT NULL";
            } elseif ($asignado === 'Grupo') {
                $sql .= " AND gr.nombre_grupo IS NOT NULL";
            } else {
                $sql .= " AND f.nombre_familia IS NULL AND gr.nombre_grupo IS NULL";
            }
        }
        if ($nombre) {
            $sql .= " AND COALESCE(f.nombre_familia, gr.nombre_grupo, u.alias) = :nombre";
        }

        $sql .= " ORDER BY g.idGasto DESC LIMIT :offset, :limit";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
        if ($fechaInicio) {
            $stmt->bindParam(':fechaInicio', $fechaInicio);
        }
        if ($fechaFin) {
            $stmt->bindParam(':fechaFin', $fechaFin);
        }
        if ($categoria) {
            $stmt->bindParam(':categoria', $categoria, PDO::PARAM_INT);
        }
        if ($origen) {
            $stmt->bindParam(':origen', $origen, PDO::PARAM_STR);
        }
        if ($nombre) {
            $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        }
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Método para contar los resultados filtrados
    public function contarGastosFiltrados($idUser, $fechaInicio = null, $fechaFin = null, $categoria = null, $origen = null)
    {
        $sql = "SELECT COUNT(*) FROM gastos WHERE idUser = :idUser";

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
        $stmt->bindValue(':idUser', $idUser, PDO::PARAM_INT);
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
    public function obtenerGastosPorCategoria($idUser)
    {
        $sql = "
        SELECT c.nombreCategoria, SUM(g.importe) AS total
        FROM gastos g
        LEFT JOIN categorias c ON g.idCategoria = c.idCategoria
        WHERE g.idUser = :idUser
        GROUP BY g.idCategoria";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idUser', $idUser, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener ingresos por categoría
    public function obtenerIngresosPorCategoria($idUser)
    {
        $sql = "
        SELECT c.nombreCategoria, SUM(i.importe) AS total
        FROM ingresos i
        LEFT JOIN categorias c ON i.idCategoria = c.idCategoria
        WHERE i.idUser = :idUser
        GROUP BY i.idCategoria";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idUser', $idUser, PDO::PARAM_INT);
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

    public function obtenerFamiliasPorUsuario($idUser)
    {
        try {
            $sql = "
        SELECT f.*
        FROM familias f
        JOIN usuarios_familias uf ON f.idFamilia = uf.idFamilia
        WHERE uf.idUser = :idUser";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':idUser', $idUser, PDO::PARAM_INT);

            // Ejecutar y verificar si hay resultados
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result ?: []; // Devuelve un array vacío si no hay resultados

        } catch (PDOException $e) {
            error_log("Error en obtenerFamiliasPorUsuario: " . $e->getMessage());
            return []; // Retorna un array vacío en caso de error
        }
    }



    public function obtenerGruposPorUsuario($idUser)
    {
        try {
            $sql = "
        SELECT g.*
        FROM grupos g
        JOIN usuarios_grupos ug ON g.idGrupo = ug.idGrupo
        WHERE ug.idUser = :idUser";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':idUser', $idUser, PDO::PARAM_INT);

            // Ejecutar y verificar si hay resultados
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result ?: []; // Devuelve un array vacío si no hay resultados

        } catch (PDOException $e) {
            error_log("Error en obtenerGruposPorUsuario: " . $e->getMessage());
            return []; // Retorna un array vacío en caso de error
        }
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

        if ($familia) {
            error_log("Contraseña proporcionada: $password");
            error_log("Contraseña en la base de datos (hash): " . $familia['password']);

            if (password_verify($password, $familia['password'])) {
                error_log("Contraseña correcta.");
                return true;
            } else {
                error_log("Contraseña incorrecta.");
            }
        } else {
            error_log("No se encontró la familia con ID: $idFamilia");
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

        if ($grupo) {
            error_log("Contraseña proporcionada: $password");
            error_log("Contraseña en la base de datos (hash): " . $grupo['password']);

            if (password_verify($password, $grupo['password'])) {
                error_log("Contraseña correcta para el grupo.");
                return true;
            } else {
                error_log("Contraseña incorrecta para el grupo.");
            }
        } else {
            error_log("No se encontró el grupo con ID: $idGrupo");
        }
        return false;
    }



    public function obtenerAdministradoresFamilia($idFamilia)
    {
        try {
            $sql = "SELECT u.* FROM usuarios u 
                JOIN administradores_familias af ON u.idUser = af.idAdmin
                WHERE af.idFamilia = :idFamilia";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':idFamilia', $idFamilia, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Registra el error si ocurre
            Config::registrarError("Error al obtener administradores de la familia $idFamilia: " . $e->getMessage());
            return false;
        }
    }


    public function obtenerAdministradoresGrupo($idGrupo)
    {
        try {
            $sql = "SELECT u.* FROM usuarios u 
                JOIN administradores_grupos ag ON u.idUser = ag.idAdmin
                WHERE ag.idGrupo = :idGrupo";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':idGrupo', $idGrupo, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Registra el error si ocurre
            Config::registrarError("Error al obtener administradores del grupo $idGrupo: " . $e->getMessage());
            return false;
        }
    }



    // Añadir administrador a una familia
    public function añadirAdministradorAFamilia($idAdmin, $idFamilia)
    {
        $sql = "INSERT INTO administradores_familias (idAdmin, idFamilia) 
            VALUES (:idAdmin, :idFamilia) 
            ON DUPLICATE KEY UPDATE idAdmin = VALUES(idAdmin)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idAdmin', $idAdmin, PDO::PARAM_INT);
        $stmt->bindValue(':idFamilia', $idFamilia, PDO::PARAM_INT);
        return $stmt->execute();
    }



    // Añadir administrador a un grupo
    public function añadirAdministradorAGrupo($idAdmin, $idGrupo)
    {
        $sql = "INSERT INTO administradores_grupos (idAdmin, idGrupo) 
            VALUES (:idAdmin, :idGrupo) 
            ON DUPLICATE KEY UPDATE idAdmin = VALUES(idAdmin)";
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


    public function obtenerIdFamiliaPorNombre($nombre_familia)
    {
        $sql = "SELECT idFamilia FROM familias WHERE nombre_familia = :nombre_familia";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':nombre_familia', $nombre_familia, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn();
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
    public function eliminarGastosPorUsuario($idUser)
    {
        $sql = "DELETE FROM gastos WHERE idUser = :idUser";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idUser', $idUser, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Eliminar todos los ingresos de un usuario
    public function eliminarIngresosPorUsuario($idUser)
    {
        $sql = "DELETE FROM ingresos WHERE idUser = :idUser";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idUser', $idUser, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function asignarUsuarioAFamilia($idUser, $idFamilia)
    {
        $sql = "INSERT INTO usuarios_familias (idUser, idFamilia) VALUES (:idUser, :idFamilia)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
        $stmt->bindParam(':idFamilia', $idFamilia, PDO::PARAM_INT);
        $stmt->execute();
    }






    public function asignarFamilia($idUser, $idFamilia = null, $nombreNuevaFamilia = null, $passwordNuevaFamilia = null)
    {
        try {
            // Si se proporciona el nombre de una nueva familia, la creamos
            if ($nombreNuevaFamilia && $passwordNuevaFamilia) {
                $sqlFamilia = "INSERT INTO familias (nombre_familia, password) VALUES (:nombre_familia, :password)";
                $stmtFamilia = $this->conexion->prepare($sqlFamilia);
                $stmtFamilia->bindValue(':nombre_familia', $nombreNuevaFamilia, PDO::PARAM_STR);
                $stmtFamilia->bindValue(':password', password_hash($passwordNuevaFamilia, PASSWORD_DEFAULT), PDO::PARAM_STR);

                if ($stmtFamilia->execute()) {
                    // Obtener el id de la nueva familia
                    $idFamilia = $this->conexion->lastInsertId();
                } else {
                    throw new Exception("Error al crear la nueva familia.");
                }
            }

            // Asignamos el usuario a la familia (existente o recién creada)
            if ($idFamilia) {
                $sqlAsignar = "INSERT INTO usuarios_familias (idUser, idFamilia) VALUES (:idUser, :idFamilia)";
                $stmtAsignar = $this->conexion->prepare($sqlAsignar);
                $stmtAsignar->bindValue(':idUser', $idUser, PDO::PARAM_INT);
                $stmtAsignar->bindValue(':idFamilia', $idFamilia, PDO::PARAM_INT);
                return $stmtAsignar->execute();
            }
        } catch (Exception $e) {
            error_log("Error en asignarFamilia: " . $e->getMessage());
            return false;
        }
    }


    public function asignarUsuarioAGrupo($idUser, $idGrupo)
    {
        $sql = "INSERT INTO usuarios_grupos (idUser, idGrupo) VALUES (:idUser, :idGrupo)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
        $stmt->bindParam(':idGrupo', $idGrupo, PDO::PARAM_INT);
        $stmt->execute();
    }




    public function asignarGrupo($idUser, $idGrupo = null, $nombreNuevoGrupo = null, $passwordNuevoGrupo = null)
    {
        try {
            // Si se proporciona el nombre de un nuevo grupo, lo creamos
            if ($nombreNuevoGrupo && $passwordNuevoGrupo) {
                $sqlGrupo = "INSERT INTO grupos (nombre_grupo, password) VALUES (:nombre_grupo, :password)";
                $stmtGrupo = $this->conexion->prepare($sqlGrupo);
                $stmtGrupo->bindValue(':nombre_grupo', $nombreNuevoGrupo, PDO::PARAM_STR);
                $stmtGrupo->bindValue(':password', password_hash($passwordNuevoGrupo, PASSWORD_DEFAULT), PDO::PARAM_STR);

                if ($stmtGrupo->execute()) {
                    // Obtener el id del nuevo grupo
                    $idGrupo = $this->conexion->lastInsertId();
                } else {
                    throw new Exception("Error al crear el nuevo grupo.");
                }
            }

            // Asignamos el usuario al grupo (existente o recién creado)
            if ($idGrupo) {
                $sqlAsignar = "INSERT INTO usuarios_grupos (idUser, idGrupo) VALUES (:idUser, :idGrupo)";
                $stmtAsignar = $this->conexion->prepare($sqlAsignar);
                $stmtAsignar->bindValue(':idUser', $idUser, PDO::PARAM_INT);
                $stmtAsignar->bindValue(':idGrupo', $idGrupo, PDO::PARAM_INT);
                return $stmtAsignar->execute();
            }
        } catch (Exception $e) {
            error_log("Error en asignarGrupo: " . $e->getMessage());
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
    public function obtenerIngresosFiltrados($idUser, $fechaInicio = null, $fechaFin = null, $categoria = null, $origen = null, $asignado = null, $nombre = null, $offset = 0, $limit = 10)
    {
        $sql = "SELECT i.idIngreso, i.importe, i.idCategoria, i.origen, i.concepto, i.fecha,
                   COALESCE(f.nombre_familia, gr.nombre_grupo, u.alias, 'No especificado') AS nombre_asociacion,
                   COALESCE(c.nombreCategoria, 'Sin categoría') AS nombreCategoria
            FROM ingresos AS i
            LEFT JOIN familias AS f ON i.idFamilia = f.idFamilia
            LEFT JOIN grupos AS gr ON i.idGrupo = gr.idGrupo
            LEFT JOIN usuarios AS u ON i.idUser = u.idUser
            LEFT JOIN categorias AS c ON i.idCategoria = c.idCategoria
            WHERE i.idUser = :idUser";

        // Filtros adicionales
        if ($fechaInicio) {
            $sql .= " AND i.fecha >= :fechaInicio";
        }
        if ($fechaFin) {
            $sql .= " AND i.fecha <= :fechaFin";
        }
        if ($categoria) {
            $sql .= " AND i.idCategoria = :categoria";
        }
        if ($origen) {
            $sql .= " AND i.origen = :origen";
        }
        if ($asignado) {
            if ($asignado === 'Familia') {
                $sql .= " AND f.nombre_familia IS NOT NULL";
            } elseif ($asignado === 'Grupo') {
                $sql .= " AND gr.nombre_grupo IS NOT NULL";
            } else {
                $sql .= " AND f.nombre_familia IS NULL AND gr.nombre_grupo IS NULL";
            }
        }
        if ($nombre) {
            $sql .= " AND COALESCE(f.nombre_familia, gr.nombre_grupo, u.alias) = :nombre";
        }

        $sql .= " ORDER BY i.idIngreso DESC LIMIT :offset, :limit";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
        if ($fechaInicio) $stmt->bindParam(':fechaInicio', $fechaInicio);
        if ($fechaFin) $stmt->bindParam(':fechaFin', $fechaFin);
        if ($categoria) $stmt->bindParam(':categoria', $categoria, PDO::PARAM_INT);
        if ($origen) $stmt->bindParam(':origen', $origen, PDO::PARAM_STR);
        if ($nombre) $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function contarIngresosFiltrados($idUser, $fechaInicio = null, $fechaFin = null, $categoria = null, $origen = null, $asignado = null, $nombre = null)
    {
        $sql = "SELECT COUNT(*) FROM ingresos AS i
            LEFT JOIN familias AS f ON i.idFamilia = f.idFamilia
            LEFT JOIN grupos AS gr ON i.idGrupo = gr.idGrupo
            LEFT JOIN usuarios AS u ON i.idUser = u.idUser
            WHERE i.idUser = :idUser";

        // Filtros adicionales
        if ($fechaInicio) {
            $sql .= " AND i.fecha >= :fechaInicio";
        }
        if ($fechaFin) {
            $sql .= " AND i.fecha <= :fechaFin";
        }
        if ($categoria) {
            $sql .= " AND i.idCategoria = :categoria";
        }
        if ($origen) {
            $sql .= " AND i.origen = :origen";
        }
        if ($asignado) {
            if ($asignado === 'Familia') {
                $sql .= " AND f.nombre_familia IS NOT NULL";
            } elseif ($asignado === 'Grupo') {
                $sql .= " AND gr.nombre_grupo IS NOT NULL";
            } else {
                $sql .= " AND f.nombre_familia IS NULL AND gr.nombre_grupo IS NULL";
            }
        }
        if ($nombre) {
            $sql .= " AND COALESCE(f.nombre_familia, gr.nombre_grupo, u.alias) = :nombre";
        }

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':idUser', $idUser, PDO::PARAM_INT);
        if ($fechaInicio) $stmt->bindValue(':fechaInicio', $fechaInicio, PDO::PARAM_STR);
        if ($fechaFin) $stmt->bindValue(':fechaFin', $fechaFin, PDO::PARAM_STR);
        if ($categoria) $stmt->bindValue(':categoria', $categoria, PDO::PARAM_INT);
        if ($origen) $stmt->bindValue(':origen', $origen, PDO::PARAM_STR);
        if ($nombre) $stmt->bindValue(':nombre', $nombre, PDO::PARAM_STR);

        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function actualizarIngreso($idIngreso, $concepto, $importe, $fecha, $origen, $categoria)
    {
        $sql = "UPDATE ingresos 
            SET concepto = :concepto, importe = :importe, fecha = :fecha, origen = :origen, idCategoria = :categoria 
            WHERE idIngreso = :idIngreso";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':concepto', $concepto, PDO::PARAM_STR);
        $stmt->bindValue(':importe', $importe, PDO::PARAM_STR);
        $stmt->bindValue(':fecha', $fecha, PDO::PARAM_STR); // Vinculando la fecha
        $stmt->bindValue(':origen', $origen, PDO::PARAM_STR);
        $stmt->bindValue(':categoria', $categoria, PDO::PARAM_INT);
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
    public function obtenerPreferenciaUsuario($clave, $idUser)
    {
        try {
            $sql = "SELECT valor FROM preferencias_usuarios WHERE clave = :clave AND idUser = :idUser";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':clave', $clave, PDO::PARAM_STR);
            $stmt->bindValue(':idUser', $idUser, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error al obtener la preferencia del usuario: " . $e->getMessage());
            return null;
        }
    }

    public function obtenerAdministradores()
    {
        $sql = "SELECT idUser, nombre, apellido FROM usuarios WHERE nivel_usuario = 'admin'";
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve todos los administradores como un array asociativo
    }


    // Método para obtener el resumen financiero del usuario
    public function obtenerResumenFinancieroUsuario($idUser)
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
        WHERE u.idUser = :idUser";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':idUser', $idUser, PDO::PARAM_INT);
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
    public function insertarNewsLetterEnvio($idUser, $idRefran, $saldoTotal, $gastosTotales, $ingresosTotales)
    {
        try {
            $sql = "INSERT INTO news_letter_envios (idUser, idRefran, saldo_total, gastos_totales, ingresos_totales, fecha_envio) 
                VALUES (:idUser, :idRefran, :saldoTotal, :gastosTotales, :ingresosTotales, NOW())";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':idUser', $idUser, PDO::PARAM_INT);
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
        try {
            $sql = "SELECT idUser, email FROM usuarios";
            $stmt = $this->conexion->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener todos los usuarios: " . $e->getMessage());
            return false;
        }
    }
    public function consultarFamiliaPorId($idFamilia)
    {
        try {
            // Conexión a la base de datos
            $stmt = $this->conexion->prepare("SELECT idFamilia, nombre_familia, idAdmin FROM familias WHERE idFamilia = :idFamilia");
            $stmt->bindParam(':idFamilia', $idFamilia, PDO::PARAM_INT);
            $stmt->execute();

            // Retornar el resultado de la consulta
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al consultar la familia por ID: " . $e->getMessage());
            return false;
        }
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

    public function existeFamilia($idFamilia)
    {
        $sql = "SELECT COUNT(*) FROM familias WHERE idFamilia = :idFamilia";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':idFamilia', $idFamilia);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function existeGrupo($idGrupo)
    {
        $sql = "SELECT COUNT(*) FROM grupos WHERE idGrupo = :idGrupo";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':idGrupo', $idGrupo);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
    public function obteneridUserPorAlias($alias)
    {
        $sql = "SELECT idUser FROM usuarios WHERE alias = :alias";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':alias', $alias, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    public function actualizarUsuarioNivel($idUser, $nivel_usuario)
    {
        $sql = "UPDATE usuarios SET nivel_usuario = :nivel_usuario WHERE idUser = :idUser";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':nivel_usuario', $nivel_usuario, PDO::PARAM_STR);
        $stmt->bindValue(':idUser', $idUser, PDO::PARAM_INT);
        return $stmt->execute();
    }
    // Obtener todos los registros de auditoría
    public function obtenerAuditoriaGlobal()
    {
        try {
            $sql = "SELECT * FROM auditoria ORDER BY fecha DESC";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerAuditoriaGlobal(): " . $e->getMessage());
            return [];
        }
    }

    // Obtener registros de auditoría de un usuario específico
    public function obtenerAuditoriaPorUsuario($idUser)
    {
        try {
            $sql = "SELECT * FROM auditoria WHERE idUser = :idUser ORDER BY fecha DESC";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':idUser', $idUser, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerAuditoriaPorUsuario(): " . $e->getMessage());
            return [];
        }
    }

    // Registrar una acción en la auditoría
    public function registrarAccionAuditoria($idUser, $accion, $detalles)
    {
        try {
            $sql = "INSERT INTO auditoria (idUser, accion, detalles, fecha) VALUES (:idUser, :accion, :detalles, NOW())";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':idUser', $idUser, PDO::PARAM_INT);
            $stmt->bindValue(':accion', $accion, PDO::PARAM_STR);
            $stmt->bindValue(':detalles', $detalles, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en registrarAccionAuditoria(): " . $e->getMessage());
            return false;
        }
    }
    public function obtenerPresupuestosPorUsuario($idUser)
    {
        try {
            $stmt = $this->conexion->prepare('SELECT * FROM presupuestos WHERE idUser = :idUser');
            $stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Error al obtener presupuestos: ' . $e->getMessage());
            return false;
        }
    }

    // Obtener metas globales
    public function obtenerMetasGlobales()
    {
        // Aquí realizamos la consulta para obtener las metas globales de la base de datos
        $sql = "SELECT * FROM metas_globales"; // Asumiendo que existe una tabla 'metas_globales'

        try {
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            $metasGlobales = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $metasGlobales;
        } catch (PDOException $e) {
            error_log("Error al obtener metas globales: " . $e->getMessage());
            return false;
        }
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
    public function usuarioPerteneceAFamiliaOGrupo($idUser, $idFamilia = null, $idGrupo = null)
    {
        $sql = "SELECT COUNT(*) as conteo FROM usuarios_familias WHERE idUser = :idUser";
        if ($idFamilia) {
            $sql .= " AND idFamilia = :idFamilia";
        }
        if ($idGrupo) {
            $sql .= " AND idGrupo = :idGrupo";
        }

        try {
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
            if ($idFamilia) {
                $stmt->bindParam(':idFamilia', $idFamilia, PDO::PARAM_INT);
            }
            if ($idGrupo) {
                $stmt->bindParam(':idGrupo', $idGrupo, PDO::PARAM_INT);
            }
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return ($resultado && $resultado['conteo'] > 0);
        } catch (Exception $e) {
            error_log("Error en usuarioPerteneceAFamiliaOGrupo(): " . $e->getMessage());
            return false;
        }
    }
    public function buscarFamiliasPorNombre($query)
    {
        $sql = "SELECT idFamilia, nombre_familia FROM familias WHERE nombre_familia LIKE :query LIMIT 10";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':query', '%' . $query . '%');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarGruposPorNombre($query)
    {
        $sql = "SELECT idGrupo, nombre_grupo FROM grupos WHERE nombre_grupo LIKE :query LIMIT 10";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':query', '%' . $query . '%');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function insertarVariasFamilias($familias)
    {
        try {
            $this->conexion->beginTransaction(); // Iniciar la transacción
            foreach ($familias as $familia) {
                $sql = "INSERT INTO familias (nombre_familia, password) VALUES (:nombreFamilia, :password)";
                $stmt = $this->conexion->prepare($sql);
                $hashedPassword = password_hash($familia['password'], PASSWORD_DEFAULT);  // Encriptar la contraseña
                $stmt->bindValue(':nombreFamilia', $familia['nombre'], PDO::PARAM_STR);
                $stmt->bindValue(':password', $hashedPassword, PDO::PARAM_STR);
                $stmt->execute();
            }
            $this->conexion->commit(); // Confirmar la transacción
            return true;
        } catch (Exception $e) {
            $this->conexion->rollBack(); // Revertir si hay algún error
            error_log("Error al insertar familias: " . $e->getMessage());
            return false;
        }
    }
    public function insertarVariosGrupos($grupos)
    {
        try {
            $this->conexion->beginTransaction(); // Iniciar la transacción
            foreach ($grupos as $grupo) {
                $sql = "INSERT INTO grupos (nombre_grupo, password) VALUES (:nombreGrupo, :password)";
                $stmt = $this->conexion->prepare($sql);
                $hashedPassword = password_hash($grupo['password'], PASSWORD_DEFAULT);  // Encriptar la contraseña
                $stmt->bindValue(':nombreGrupo', $grupo['nombre'], PDO::PARAM_STR);
                $stmt->bindValue(':password', $hashedPassword, PDO::PARAM_STR);
                $stmt->execute();
            }
            $this->conexion->commit(); // Confirmar la transacción
            return true;
        } catch (Exception $e) {
            $this->conexion->rollBack(); // Revertir si hay algún error
            error_log("Error al insertar grupos: " . $e->getMessage());
            return false;
        }
    }
    public function contarFamiliasPorAdmin($idAdmin)
    {
        $sql = "SELECT COUNT(*) AS total FROM administradores_familias WHERE idAdmin = :idAdmin";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':idAdmin', $idAdmin, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }



    public function contarGruposPorAdmin($idAdmin)
    {
        $sql = "SELECT COUNT(*) AS total FROM administradores_grupos WHERE idAdmin = :idAdmin";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':idAdmin', $idAdmin, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }




    public function asignarAdministradorAFamilia($idAdmin, $idFamilia)
    {
        $sql = "INSERT INTO administradores_familias (idAdmin, idFamilia) VALUES (:idAdmin, :idFamilia)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':idAdmin', $idAdmin, PDO::PARAM_INT);
        $stmt->bindParam(':idFamilia', $idFamilia, PDO::PARAM_INT);
        $stmt->execute();
    }
    public function asignarAdministradorAGrupo($idAdmin, $idGrupo)
    {
        $sql = "INSERT INTO administradores_grupos (idAdmin, idGrupo) VALUES (:idAdmin, :idGrupo)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':idAdmin', $idAdmin, PDO::PARAM_INT);
        $stmt->bindParam(':idGrupo', $idGrupo, PDO::PARAM_INT);
        $stmt->execute();
    }
    public function getLastInsertId()
    {
        return $this->conexion->lastInsertId();
    }
    public function insertarUsuarioConPremium($nombre, $apellido, $alias, $hashedPassword, $nivel_usuario, $fecha_nacimiento, $email, $telefono, $password_premium)
    {
        $sql = "INSERT INTO usuarios (nombre, apellido, alias, contrasenya, nivel_usuario, fecha_nacimiento, email, telefono, password_premium)
            VALUES (:nombre, :apellido, :alias, :contrasenya, :nivel_usuario, :fecha_nacimiento, :email, :telefono, :password_premium)";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellido', $apellido);
        $stmt->bindParam(':alias', $alias);
        $stmt->bindParam(':contrasenya', $hashedPassword);
        $stmt->bindParam(':nivel_usuario', $nivel_usuario);
        $stmt->bindParam(':fecha_nacimiento', $fecha_nacimiento);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':password_premium', $password_premium);

        return $stmt->execute();
    }
    // Para Usuarios Premium
    public function obtenerUsuarioPorNivel($nivel_usuario)
    {
        try {
            // Consulta SQL para obtener el usuario con el nivel especificado
            $sql = "SELECT * FROM usuarios WHERE nivel_usuario = :nivel_usuario LIMIT 1";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':nivel_usuario', $nivel_usuario, PDO::PARAM_STR);
            $stmt->execute();

            // Devuelve el resultado de la consulta
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerUsuarioPorNivel(): " . $e->getMessage());
            return false;
        }
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
    // Obtener gastos agrupados por familia y grupo
    public function obtenerGastosAgrupadosPorFamiliaYGrupo($idUsuario, $fechaInicio = null, $fechaFin = null, $categoria = null, $origen = null)
    {
        $condiciones = [];
        $params = [':idUsuario' => $idUsuario];

        // Condiciones opcionales para el filtro
        if ($fechaInicio) {
            $condiciones[] = 'g.fecha >= :fechaInicio';
            $params[':fechaInicio'] = $fechaInicio;
        }
        if ($fechaFin) {
            $condiciones[] = 'g.fecha <= :fechaFin';
            $params[':fechaFin'] = $fechaFin;
        }
        if ($categoria) {
            $condiciones[] = 'g.idCategoria = :categoria';
            $params[':categoria'] = $categoria;
        }
        if ($origen) {
            $condiciones[] = 'g.origen = :origen';
            $params[':origen'] = $origen;
        }

        $whereClause = '';
        if (!empty($condiciones)) {
            $whereClause = ' AND ' . implode(' AND ', $condiciones);
        }

        $sql = "
    SELECT f.nombre_familia, g.nombre_grupo, ga.*
    FROM gastos ga
    LEFT JOIN familias f ON ga.idFamilia = f.idFamilia
    LEFT JOIN grupos g ON ga.idGrupo = g.idGrupo
    WHERE ga.idUser = :idUsuario $whereClause
    ORDER BY f.nombre_familia, g.nombre_grupo, ga.fecha DESC
    ";

        $stmt = $this->conexion->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function obtenerGastosConDetalles($userId, $fechaInicio = null, $fechaFin = null, $categoria = null, $origen = null, $offset = 0, $limit = 10)
    {
        $sql = "SELECT g.idGasto, g.importe, g.idCategoria, g.origen, g.concepto, g.fecha, 
                   IFNULL(f.nombre_familia, IFNULL(gr.nombre_grupo, u.alias)) AS nombre_asociacion, 
                   c.nombreCategoria
            FROM gastos AS g
            LEFT JOIN familias AS f ON g.idFamilia = f.idFamilia
            LEFT JOIN grupos AS gr ON g.idGrupo = gr.idGrupo
            LEFT JOIN usuarios AS u ON g.idUser = u.idUser
            LEFT JOIN categorias AS c ON g.idCategoria = c.idCategoria
            WHERE g.idUser = :userId";

        // Condicionales para filtros
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

        $sql .= " ORDER BY g.idGasto DESC LIMIT :offset, :limit";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);

        if ($fechaInicio) {
            $stmt->bindParam(':fechaInicio', $fechaInicio);
        }
        if ($fechaFin) {
            $stmt->bindParam(':fechaFin', $fechaFin);
        }
        if ($categoria) {
            $stmt->bindParam(':categoria', $categoria, PDO::PARAM_INT);
        }
        if ($origen) {
            $stmt->bindParam(':origen', $origen);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function obtenerNombresDisponibles()
    {
        $sql = "SELECT DISTINCT COALESCE(f.nombre_familia, gr.nombre_grupo, u.alias, 'No especificado') AS nombre_asociacion
            FROM gastos AS g
            LEFT JOIN familias AS f ON g.idFamilia = f.idFamilia
            LEFT JOIN grupos AS gr ON g.idGrupo = gr.idGrupo
            LEFT JOIN usuarios AS u ON g.idUser = u.idUser";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function obtenerNombresAsociaciones()
    {
        $sql = "SELECT DISTINCT COALESCE(f.nombre_familia, gr.nombre_grupo, u.alias) AS nombre_asociacion
            FROM ingresos AS i
            LEFT JOIN familias AS f ON i.idFamilia = f.idFamilia
            LEFT JOIN grupos AS gr ON i.idGrupo = gr.idGrupo
            LEFT JOIN usuarios AS u ON i.idUser = u.idUser
            WHERE i.idUser = :idUser";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':idUser', $_SESSION['usuario']['id'], PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
