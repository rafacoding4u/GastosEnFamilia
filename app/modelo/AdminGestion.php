<?php

require_once 'classModelo.php';

class AdminGestion extends GastosModelo
{
    protected $conexion; // Cambiado a protected
    private $adminId;

    public function __construct($adminId)
    {
        parent::__construct(); // Inicializa la conexión a la base de datos en la clase principal
        $this->conexion = $this->getConexion(); // Asigna la conexión desde el modelo
        $this->adminId = $adminId;
    }

    // Listar solo los usuarios gestionados por el administrador
    public function listarUsuariosGestionados()
    {
        return $this->obtenerUsuariosGestionados($this->adminId);
    }

    public function obtenerUsuariosGestionados($idAdmin, $filtros = [])
    {
        try {
            $sql = "
            SELECT u.*, 
                GROUP_CONCAT(DISTINCT f.nombre_familia ORDER BY f.nombre_familia ASC SEPARATOR ', ') AS familias,
                GROUP_CONCAT(DISTINCT g.nombre_grupo ORDER BY g.nombre_grupo ASC SEPARATOR ', ') AS grupos
            FROM usuarios u
            LEFT JOIN usuarios_familias uf ON u.idUser = uf.idUser
            LEFT JOIN familias f ON uf.idFamilia = f.idFamilia
            LEFT JOIN administradores_familias af ON af.idFamilia = f.idFamilia
            LEFT JOIN usuarios_grupos ug ON u.idUser = ug.idUser
            LEFT JOIN grupos g ON ug.idGrupo = g.idGrupo
            LEFT JOIN administradores_grupos ag ON ag.idGrupo = g.idGrupo
            WHERE (af.idAdmin = :idAdmin OR ag.idAdmin = :idAdmin OR u.idUser = :idAdmin)
        ";

            // Filtrado dinámico
            if (!empty($filtros['nombre'])) {
                $sql .= " AND u.nombre LIKE :nombre";
            }
            if (!empty($filtros['apellido'])) {
                $sql .= " AND u.apellido LIKE :apellido";
            }
            if (!empty($filtros['alias'])) {
                $sql .= " AND u.alias LIKE :alias";
            }
            if (!empty($filtros['email'])) {
                $sql .= " AND u.email LIKE :email";
            }
            if (!empty($filtros['nivel_usuario'])) {
                $sql .= " AND u.nivel_usuario = :nivel_usuario";
            }

            $sql .= " GROUP BY u.idUser";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':idAdmin', $idAdmin, PDO::PARAM_INT);

            // Vinculación de parámetros de filtro si existen
            if (!empty($filtros['nombre'])) {
                $stmt->bindValue(':nombre', '%' . $filtros['nombre'] . '%', PDO::PARAM_STR);
            }
            if (!empty($filtros['apellido'])) {
                $stmt->bindValue(':apellido', '%' . $filtros['apellido'] . '%', PDO::PARAM_STR);
            }
            if (!empty($filtros['alias'])) {
                $stmt->bindValue(':alias', '%' . $filtros['alias'] . '%', PDO::PARAM_STR);
            }
            if (!empty($filtros['email'])) {
                $stmt->bindValue(':email', '%' . $filtros['email'] . '%', PDO::PARAM_STR);
            }
            if (!empty($filtros['nivel_usuario'])) {
                $stmt->bindValue(':nivel_usuario', $filtros['nivel_usuario'], PDO::PARAM_STR);
            }

            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            error_log("Resultado de obtenerUsuariosGestionados: " . json_encode($result));
            return $result;
        } catch (PDOException $e) {
            error_log("Error en obtenerUsuariosGestionados(): " . $e->getMessage());
            throw new Exception('Error al listar los usuarios gestionados.');
        }
    }


    // Crear un nuevo usuario dentro de los límites de familia y grupo
    public function crearUsuario($datosUsuario)
    {
        $familiaUsuarios = $this->contarUsuariosFamilia($datosUsuario['idFamilia']);
        $grupoUsuarios = $this->contarUsuariosGrupo($datosUsuario['idGrupo']);

        if ($familiaUsuarios >= 10) {
            throw new Exception("Límite de 10 usuarios en la familia alcanzado.");
        }

        if ($grupoUsuarios >= 30) {
            throw new Exception("Límite de 30 usuarios en el grupo alcanzado.");
        }

        return $this->insertarUsuario(
            $datosUsuario['nombre'],
            $datosUsuario['apellido'],
            $datosUsuario['alias'],
            $datosUsuario['email'],
            $datosUsuario['contrasenya'],
            $datosUsuario['fecha_nacimiento'],
            $datosUsuario['telefono'],
            $datosUsuario['idFamilia']
        );
    }

    // Editar solo los datos de los usuarios regulares gestionados por el administrador
    public function editarUsuarioRegular($idUser, $nuevosDatos)
    {
        $usuario = $this->obtenerUsuarioPorId($idUser);

        if ($usuario && $usuario['nivel_usuario'] === 'usuario' && $this->esUsuarioGestionado($idUser)) {
            return $this->actualizarUsuario(
                $idUser,
                $nuevosDatos['nombre'],
                $nuevosDatos['apellido'],
                $nuevosDatos['alias'],
                $nuevosDatos['email'],
                $nuevosDatos['telefono'],
                'usuario', // Nivel de usuario como un parámetro adicional
                $nuevosDatos['fecha_nacimiento']
            );
        } else {
            throw new Exception("No tienes permisos para editar este usuario.");
        }
    }



    // Eliminar solo los usuarios regulares bajo la administración del admin
    public function eliminarUsuarioRegular($idUser)
    {
        $usuario = $this->obtenerUsuarioPorId($idUser);

        if ($usuario && $usuario['nivel_usuario'] === 'usuario' && $this->esUsuarioGestionado($idUser)) {
            return $this->eliminarUsuarioPorId($idUser);
        } else {
            throw new Exception("No tienes permisos para eliminar este usuario.");
        }
    }

    // Verificar si el usuario está gestionado por el admin (pertenece a sus familias o grupos)
    private function esUsuarioGestionado($idUser)
    {
        $familiasGestionadas = $this->obtenerFamiliasAdmin($this->adminId);
        $gruposGestionados = $this->obtenerGruposAdmin($this->adminId);

        foreach ($familiasGestionadas as $familia) {
            if ($this->usuarioYaEnFamilia($idUser, $familia['idFamilia'])) {
                return true;
            }
        }
        foreach ($gruposGestionados as $grupo) {
            if ($this->usuarioYaEnGrupo($idUser, $grupo['idGrupo'])) {
                return true;
            }
        }

        return false;
    }

    // Obtener familias administradas por el admin (respetando el límite de 5)
    public function obtenerFamiliasAdministradas()
    {
        $familias = $this->obtenerFamiliasAdmin($this->adminId);
        if (count($familias) >= 5) {
            throw new Exception("Límite de 5 familias administradas alcanzado.");
        }
        return $familias;
    }

    // Obtener grupos administrados por el admin (respetando el límite de 5)
    public function obtenerGruposAdministrados()
    {
        $grupos = $this->obtenerGruposAdmin($this->adminId);
        if (count($grupos) >= 5) {
            throw new Exception("Límite de 5 grupos administrados alcanzado.");
        }
        return $grupos;
    }

    public function obtenerUsuarioPorId($idUser)
    {
        return $this->obtenerUsuarioPorId($idUser);
    }
}
