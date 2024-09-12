<?php

/****
 * Librería con funciones generales y de validación
 * @author Heike Bonilla
 * 
 */

function sinTildes($frase): string {
    $no_permitidas = array("á", "é", "í", "ó", "ú", "Á", "É", "Í", "Ó", "Ú", "à", "è", "ì", "ò", "ù", "À", "È", "Ì", "Ò", "Ù");
    $permitidas = array("a", "e", "i", "o", "u", "A", "E", "I", "O", "U", "a", "e", "i", "o", "u", "A", "E", "I", "O", "U");
    $texto = str_replace($no_permitidas, $permitidas, $frase);
    return $texto;
}

function sinEspacios($frase) {
    $texto = trim(preg_replace('/ +/', ' ', $frase));
    return $texto;
}

function recoge(string $var) {
    if (isset($_REQUEST[$var]) && (!is_array($_REQUEST[$var]))) {
        $tmp = sinEspacios($_REQUEST[$var]);
        $tmp = strip_tags($tmp);
    } else
        $tmp = "";
    return $tmp;
}

function cTexto(string $text, string $campo, array &$errores, int $max = 30, int $min = 1, bool $espacios = TRUE, bool $case = TRUE): bool {
    $case = ($case === TRUE) ? "i" : "";
    $espacios = ($espacios === TRUE) ? " " : "";
    if ((preg_match("/^[a-zñ$espacios]{" . $min . "," . $max . "}$/u$case", sinTildes($text)))) {
        return true;
    }
    $errores[$campo] = "Error en el campo $campo";
    return false;
}

function cUser(string $text, string $campo, array &$errores, int $max = 30, int $min = 1): bool {
    if ((preg_match("/^[a-zA-Z0-9_]{" . $min . "," . $max . "}$/u", sinTildes($text)))) {
        return true;
    }
    $errores[$campo] = "Error en el campo $campo";
    return false;
}

function unixFechaAAAAMMDD($fecha, $campo, &$errores) {
    $arrayfecha = explode("-", $fecha);
    if (count($arrayfecha) == 3) {
        $fechavalida = checkdate($arrayfecha[1], $arrayfecha[2], $arrayfecha[0]);
        if ($fechavalida) {
            return mktime(0, 0, 0, $arrayfecha[2], $arrayfecha[1], $arrayfecha[0]);
        }
    }
    $errores[$campo] = "Fecha no valida";
    return false;
}

function cNum(string $num, string $campo, array &$errores, bool $requerido = TRUE, int $max = PHP_INT_MAX): bool {
    $cuantificador = ($requerido) ? "+" : "*";
    if ((preg_match("/^[0-9]" . $cuantificador . "$/", $num))) {
        if ($num <= $max) return true;
    }
    $errores[$campo] = "Error en el campo $campo";
    return false;
}

function cRadio(string $text, string $campo, array &$errores, array $valores, bool $requerido = TRUE) {
    if (in_array($text, $valores)) {
        return true;
    }
    if (!$requerido && $text == "") {
        return true;
    }
    $errores[$campo] = "Error en el campo $campo";
    return false;
}

function cSelect(string $text, string $campo, array &$errores, array $valores, bool $requerido = TRUE) {
    if (array_key_exists($text, $valores)) {
        return true;
    }
    if (!$requerido && $text == "") {
        return true;
    }
    $errores[$campo] = "Error en el campo $campo";
    return false;
}

function cCheck(array $text, string $campo, array &$errores, array $valores, bool $requerido = TRUE) {
    if (($requerido) && (count($text) == 0)) {
        $errores[$campo] = "Error en el campo $campo";
        return false;
    }
    foreach ($text as $valor) {
        if (!in_array($valor, $valores)) {
            $errores[$campo] = "Error en el campo $campo";
            return false;
        }
    }
    return true;
}

function cFile(string $nombre, array &$errores, array $extensionesValidas, string $directorio, int  $max_file_size,  bool $required = TRUE) {
    if ((!$required) && $_FILES[$nombre]['error'] === 4)
        return true;
    if ($_FILES[$nombre]['error'] != 0) {
        $errores["$nombre"] = "Error al subir el archivo " . $nombre . ". Prueba de nuevo";
        return false;
    } else {
        $nombreArchivo = strip_tags($_FILES["$nombre"]['name']);
        $directorioTemp = $_FILES["$nombre"]['tmp_name'];
        $tamanyoFile = filesize($directorioTemp);
        $extension = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));
        if (!in_array($extension, $extensionesValidas)) {
            $errores["$nombre"] = "La extensión del archivo no es válida";
            return false;
        }
        if ($tamanyoFile > $max_file_size) {
            $errores["$nombre"] = "La imagen debe de tener un tamaño inferior a $max_file_size kb";
            return false;
        }
        if (empty($errores)) {
            if (is_dir($directorio)) {
                $nombreArchivo = is_file($directorio . DIRECTORY_SEPARATOR . $nombreArchivo) ? time() . $nombreArchivo : $nombreArchivo;
                $nombreCompleto = $directorio . DIRECTORY_SEPARATOR . $nombreArchivo;
                if (move_uploaded_file($directorioTemp, $nombreCompleto)) {
                    return $nombreCompleto;
                } else {
                    $errores["$nombre"] = "Ha habido un error al subir el fichero";
                    return false;
                }
            } else {
                $errores["$nombre"] = "Ha habido un error al subir el fichero";
                return false;
            }
        }
    }
}

function crypt_blowfish($password) {
    $salt = '$2a$07$usesomesillystringforsalt$';
    $pass = crypt($password, $salt);
    return $pass;
}

// cookies
function setSecureCookie($name, $value, $expire) {
    setcookie($name, $value, $expire, "/", "", isset($_SERVER["HTTPS"]), true);
}

function getSecureCookie($name) {
    return isset($_COOKIE[$name]) ? htmlspecialchars($_COOKIE[$name], ENT_QUOTES, 'UTF-8') : null;
}

// Validar una contraseña (al menos 8 caracteres, una mayúscula y un número)
function cContrasenya(string $contrasenya, array &$errores): bool {
    if (strlen($contrasenya) < 8 || !preg_match('/[A-Z]/', $contrasenya) || !preg_match('/[0-9]/', $contrasenya)) {
        $errores['contrasenya'] = "La contraseña debe contener al menos 1 letra mayúscula, 1 número y tener un mínimo de 8 caracteres.";
        return false;
    }
    return true;
}

// Validar un email
function cEmail(string $email, array &$errores): bool {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores['email'] = "El correo electrónico no es válido.";
        return false;
    }
    return true;
}

// Validar teléfono (exactamente 9 dígitos)
function cTelefono(string $telefono, array &$errores): bool {
    if (!preg_match('/^[0-9]{9}$/', $telefono)) {
        $errores['telefono'] = "El número de teléfono debe tener 9 dígitos.";
        return false;
    }
    return true;
}

// Validar si el usuario tiene más de 18 años
function validarEdad(string $fecha_nacimiento, array &$errores): bool {
    $fecha_actual = new DateTime();
    $fecha_nacimiento_dt = new DateTime($fecha_nacimiento);
    $edad = $fecha_actual->diff($fecha_nacimiento_dt)->y;

    if ($edad < 18) {
        $errores['edad'] = "El usuario debe ser mayor de edad.";
        return false;
    }
    return true;
}


?>
