-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-10-2024 a las 20:28:43
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `gastosencasa_bd`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `verificar_permiso_cacheado` (IN `idUser` INT, IN `nombrePermiso` VARCHAR(255), IN `tipoPermiso` VARCHAR(255))   BEGIN
    DECLARE tiene_permiso TINYINT DEFAULT 0;

    -- Buscar permiso en la cache
    SELECT tiene_permiso 
    INTO tiene_permiso 
    FROM permisos_cache 
    WHERE idUser = idUser 
      AND nombrePermiso = nombrePermiso
      AND tipoPermiso = tipoPermiso
      AND TIMESTAMPDIFF(MINUTE, fecha_cache, NOW()) < 60;

    -- Si no existe en cache o está desactualizado, recalcular el permiso
    IF tiene_permiso IS NULL THEN
        SELECT COUNT(*)
        INTO tiene_permiso
        FROM usuarios u
        JOIN roles_permisos rp ON u.nivel_usuario = rp.idRol
        WHERE u.idUser = idUser
          AND rp.nombrePermiso = nombrePermiso
          AND rp.tipoPermiso = tipoPermiso;

        -- Actualizar la cache
        INSERT INTO permisos_cache (idUser, nombrePermiso, tipoPermiso, tiene_permiso)
        VALUES (idUser, nombrePermiso, tipoPermiso, tiene_permiso)
        ON DUPLICATE KEY UPDATE tiene_permiso = VALUES(tiene_permiso), fecha_cache = NOW();
    END IF;

    -- Devolver el resultado
    SELECT tiene_permiso;
END$$

--
-- Funciones
--
CREATE DEFINER=`root`@`localhost` FUNCTION `verificar_permiso` (`idUser` INT, `tipoPermiso` VARCHAR(255), `nombrePermiso` VARCHAR(255)) RETURNS TINYINT(4) DETERMINISTIC BEGIN
  DECLARE tiene_permiso TINYINT DEFAULT 0;

  -- Verifica si el usuario tiene el permiso
  SELECT COUNT(*)
  INTO tiene_permiso
  FROM usuarios u
  JOIN roles_permisos rp ON u.nivel_usuario = rp.idRol
  WHERE u.idUser = idUser
    AND rp.nombrePermiso = nombrePermiso
    AND rp.tipoPermiso = tipoPermiso;

  RETURN tiene_permiso;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administradores_familias`
--

CREATE TABLE `administradores_familias` (
  `id` int(11) NOT NULL,
  `idAdmin` int(11) NOT NULL,
  `idFamilia` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `administradores_familias`
--

INSERT INTO `administradores_familias` (`id`, `idAdmin`, `idFamilia`) VALUES
(1, 2, 1),
(11, 16, 16),
(14, 20, 23),
(15, 20, 24),
(18, 27, 30),
(28, 39, 48),
(27, 60, 47),
(26, 61, 46),
(22, 65, 34),
(23, 65, 35),
(24, 65, 36),
(25, 65, 37),
(29, 70, 1),
(31, 75, 59),
(30, 75, 60),
(33, 87, 60),
(32, 87, 62),
(34, 89, 63);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administradores_grupos`
--

CREATE TABLE `administradores_grupos` (
  `idAdmin` int(11) NOT NULL,
  `idGrupo` int(11) NOT NULL,
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `administradores_grupos`
--

INSERT INTO `administradores_grupos` (`idAdmin`, `idGrupo`, `id`) VALUES
(2, 1, 1),
(65, 17, 2),
(65, 18, 3),
(65, 19, 4),
(65, 20, 5),
(75, 23, 6),
(75, 22, 7),
(87, 25, 8),
(87, 23, 9),
(89, 26, 10);

--
-- Disparadores `administradores_grupos`
--
DELIMITER $$
CREATE TRIGGER `before_insert_administradores_grupos` BEFORE INSERT ON `administradores_grupos` FOR EACH ROW BEGIN
  -- Validar si el administrador existe
  IF (SELECT COUNT(*) FROM usuarios WHERE idUser = NEW.idAdmin) = 0 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El administrador no existe';
  END IF;

  -- Validar si el grupo existe
  IF (SELECT COUNT(*) FROM grupos WHERE idGrupo = NEW.idGrupo) = 0 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El grupo no existe';
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria`
--

CREATE TABLE `auditoria` (
  `idAuditoria` int(11) NOT NULL,
  `accion` varchar(255) NOT NULL,
  `tabla_afectada` varchar(255) NOT NULL,
  `idRegistro` int(11) NOT NULL,
  `usuario` varchar(255) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `auditoria`
--

INSERT INTO `auditoria` (`idAuditoria`, `accion`, `tabla_afectada`, `idRegistro`, `usuario`, `fecha`) VALUES
(1, 'UPDATE', 'usuarios', 1, '1', '2024-10-06 14:55:55'),
(2, 'UPDATE', 'usuarios', 2, '2', '2024-10-06 14:55:55'),
(3, 'UPDATE', 'usuarios', 3, '3', '2024-10-06 14:55:55'),
(4, 'UPDATE', 'usuarios', 4, '4', '2024-10-06 14:55:55'),
(5, 'UPDATE', 'usuarios', 5, '5', '2024-10-06 14:55:55'),
(6, 'UPDATE', 'usuarios', 6, '6', '2024-10-06 14:55:55'),
(7, 'UPDATE', 'usuarios', 7, '7', '2024-10-06 14:55:55'),
(8, 'UPDATE', 'usuarios', 8, '8', '2024-10-06 14:55:55'),
(9, 'UPDATE', 'usuarios', 9, '9', '2024-10-06 14:55:55'),
(10, 'UPDATE', 'usuarios', 10, '10', '2024-10-06 14:55:55'),
(11, 'UPDATE', 'usuarios', 11, '11', '2024-10-06 14:55:55'),
(12, 'UPDATE', 'usuarios', 12, '12', '2024-10-06 14:55:55'),
(13, 'UPDATE', 'usuarios', 13, '13', '2024-10-06 14:55:55'),
(14, 'UPDATE', 'usuarios', 1, '1', '2024-10-06 14:57:22'),
(15, 'UPDATE', 'usuarios', 2, '2', '2024-10-06 14:57:22'),
(16, 'UPDATE', 'usuarios', 3, '3', '2024-10-06 14:57:22'),
(17, 'UPDATE', 'usuarios', 4, '4', '2024-10-06 14:57:22'),
(18, 'UPDATE', 'usuarios', 5, '5', '2024-10-06 14:57:22'),
(19, 'UPDATE', 'usuarios', 6, '6', '2024-10-06 14:57:22'),
(20, 'UPDATE', 'usuarios', 7, '7', '2024-10-06 14:57:22'),
(21, 'UPDATE', 'usuarios', 8, '8', '2024-10-06 14:57:22'),
(22, 'UPDATE', 'usuarios', 9, '9', '2024-10-06 14:57:22'),
(23, 'UPDATE', 'usuarios', 10, '10', '2024-10-06 14:57:22'),
(24, 'UPDATE', 'usuarios', 11, '11', '2024-10-06 14:57:22'),
(25, 'UPDATE', 'usuarios', 12, '12', '2024-10-06 14:57:22'),
(26, 'UPDATE', 'usuarios', 13, '13', '2024-10-06 14:57:22'),
(27, 'UPDATE', 'usuarios', 1, '1', '2024-10-06 18:56:57'),
(28, 'UPDATE', 'usuarios', 7, '7', '2024-10-06 18:57:13'),
(29, 'UPDATE', 'usuarios', 11, '11', '2024-10-06 18:57:21'),
(30, 'UPDATE', 'usuarios', 10, '10', '2024-10-06 18:57:27'),
(31, 'UPDATE', 'usuarios', 3, '3', '2024-10-06 18:57:32'),
(32, 'UPDATE', 'usuarios', 6, '6', '2024-10-06 18:57:38'),
(33, 'UPDATE', 'usuarios', 5, '5', '2024-10-06 18:57:43'),
(34, 'UPDATE', 'usuarios', 9, '9', '2024-10-06 18:57:48'),
(35, 'UPDATE', 'usuarios', 13, '13', '2024-10-06 18:57:53'),
(36, 'UPDATE', 'usuarios', 13, '13', '2024-10-06 18:58:09'),
(37, 'UPDATE', 'usuarios', 2, '2', '2024-10-06 18:58:15'),
(38, 'UPDATE', 'usuarios', 12, '12', '2024-10-06 18:58:19'),
(39, 'UPDATE', 'usuarios', 8, '8', '2024-10-06 18:58:24'),
(40, 'UPDATE', 'usuarios', 4, '4', '2024-10-06 18:58:28'),
(41, 'UPDATE', 'usuarios', 2, '2', '2024-10-06 18:58:59'),
(42, 'UPDATE', 'usuarios', 8, '8', '2024-10-06 18:59:17'),
(43, 'INSERT', 'gastos', 14, '2', '2024-10-06 19:09:40'),
(44, 'INSERT', 'gastos', 15, '2', '2024-10-06 19:10:45'),
(45, 'UPDATE', 'gastos', 1, '2', '2024-10-06 19:12:47'),
(46, 'DELETE', 'gastos', 1, '2', '2024-10-06 19:12:49'),
(47, 'INSERT', 'categorias', 15, '2', '2024-10-07 08:41:30'),
(48, 'INSERT', 'categorias', 16, '2', '2024-10-07 08:41:30'),
(49, 'UPDATE', 'usuarios', 1, '1', '2024-10-07 15:08:10'),
(50, 'UPDATE', 'usuarios', 3, '3', '2024-10-07 15:08:10'),
(51, 'UPDATE', 'usuarios', 4, '4', '2024-10-07 15:08:10'),
(52, 'UPDATE', 'usuarios', 9, '9', '2024-10-07 15:08:10'),
(53, 'UPDATE', 'usuarios', 10, '10', '2024-10-07 15:08:10'),
(54, 'UPDATE', 'usuarios', 11, '11', '2024-10-07 15:08:10'),
(55, 'UPDATE', 'usuarios', 12, '12', '2024-10-07 15:08:10'),
(56, 'UPDATE', 'usuarios', 13, '13', '2024-10-07 15:08:10'),
(57, 'UPDATE', 'usuarios', 1, '1', '2024-10-07 15:13:07'),
(58, 'UPDATE', 'usuarios', 3, '3', '2024-10-07 15:13:07'),
(59, 'UPDATE', 'usuarios', 4, '4', '2024-10-07 15:13:07'),
(60, 'UPDATE', 'usuarios', 5, '5', '2024-10-07 15:13:07'),
(61, 'UPDATE', 'usuarios', 6, '6', '2024-10-07 15:13:07'),
(62, 'UPDATE', 'usuarios', 7, '7', '2024-10-07 15:13:07'),
(63, 'UPDATE', 'usuarios', 8, '8', '2024-10-07 15:13:07'),
(64, 'UPDATE', 'usuarios', 12, '12', '2024-10-07 15:13:07'),
(65, 'UPDATE', 'usuarios', 13, '13', '2024-10-07 15:13:07'),
(66, 'UPDATE', 'usuarios', 1, '1', '2024-10-07 15:15:09'),
(67, 'UPDATE', 'usuarios', 3, '3', '2024-10-07 15:15:09'),
(68, 'UPDATE', 'usuarios', 4, '4', '2024-10-07 15:15:09'),
(69, 'UPDATE', 'usuarios', 5, '5', '2024-10-07 15:15:09'),
(70, 'UPDATE', 'usuarios', 6, '6', '2024-10-07 15:15:09'),
(71, 'UPDATE', 'usuarios', 7, '7', '2024-10-07 15:15:09'),
(72, 'UPDATE', 'usuarios', 8, '8', '2024-10-07 15:15:09'),
(73, 'UPDATE', 'usuarios', 12, '12', '2024-10-07 15:15:09'),
(74, 'UPDATE', 'usuarios', 13, '13', '2024-10-07 15:15:09'),
(75, 'UPDATE', 'usuarios', 1, '1', '2024-10-07 15:18:14'),
(76, 'UPDATE', 'usuarios', 3, '3', '2024-10-07 15:18:14'),
(77, 'UPDATE', 'usuarios', 4, '4', '2024-10-07 15:18:14'),
(78, 'UPDATE', 'usuarios', 5, '5', '2024-10-07 15:18:14'),
(79, 'UPDATE', 'usuarios', 6, '6', '2024-10-07 15:18:14'),
(80, 'UPDATE', 'usuarios', 7, '7', '2024-10-07 15:18:14'),
(81, 'UPDATE', 'usuarios', 8, '8', '2024-10-07 15:18:14'),
(82, 'UPDATE', 'usuarios', 12, '12', '2024-10-07 15:18:14'),
(83, 'UPDATE', 'usuarios', 13, '13', '2024-10-07 15:18:14'),
(84, 'UPDATE', 'usuarios', 1, '1', '2024-10-07 15:19:48'),
(85, 'UPDATE', 'usuarios', 3, '3', '2024-10-07 15:19:48'),
(86, 'UPDATE', 'usuarios', 4, '4', '2024-10-07 15:19:48'),
(87, 'UPDATE', 'usuarios', 5, '5', '2024-10-07 15:19:48'),
(88, 'UPDATE', 'usuarios', 6, '6', '2024-10-07 15:19:48'),
(89, 'UPDATE', 'usuarios', 7, '7', '2024-10-07 15:19:48'),
(90, 'UPDATE', 'usuarios', 8, '8', '2024-10-07 15:19:48'),
(91, 'UPDATE', 'usuarios', 12, '12', '2024-10-07 15:19:48'),
(92, 'UPDATE', 'usuarios', 13, '13', '2024-10-07 15:19:48'),
(93, 'UPDATE', 'gastos', 4, '2', '2024-10-07 16:06:24'),
(94, 'UPDATE', 'gastos', 9, '3', '2024-10-07 16:06:24'),
(95, 'UPDATE', 'gastos', 14, '2', '2024-10-07 16:06:24'),
(96, 'UPDATE', 'gastos', 15, '2', '2024-10-07 16:06:24'),
(97, 'UPDATE', 'gastos', 2, '2', '2024-10-08 17:19:53'),
(98, 'UPDATE', 'gastos', 3, '2', '2024-10-08 17:19:53'),
(99, 'UPDATE', 'gastos', 4, '2', '2024-10-08 17:19:53'),
(100, 'UPDATE', 'gastos', 5, '2', '2024-10-08 17:19:53'),
(101, 'UPDATE', 'gastos', 6, '2', '2024-10-08 17:19:53'),
(102, 'UPDATE', 'gastos', 7, '2', '2024-10-08 17:19:53'),
(103, 'UPDATE', 'gastos', 8, '2', '2024-10-08 17:19:53'),
(104, 'UPDATE', 'ingresos', 1, '2', '2024-10-08 17:20:53'),
(105, 'UPDATE', 'ingresos', 2, '2', '2024-10-08 17:20:53'),
(106, 'UPDATE', 'ingresos', 3, '2', '2024-10-08 17:20:53'),
(107, 'UPDATE', 'ingresos', 4, '2', '2024-10-08 17:20:53'),
(108, 'UPDATE', 'ingresos', 5, '2', '2024-10-08 17:20:53'),
(109, 'UPDATE', 'ingresos', 6, '2', '2024-10-08 17:20:53'),
(110, 'UPDATE', 'ingresos', 7, '2', '2024-10-08 17:20:53'),
(111, 'UPDATE', 'ingresos', 8, '2', '2024-10-08 17:20:53'),
(112, 'UPDATE', 'gastos', 9, '3', '2024-10-08 17:22:01'),
(113, 'UPDATE', 'gastos', 10, '3', '2024-10-08 17:22:24'),
(114, 'UPDATE', 'gastos', 11, '3', '2024-10-08 17:24:28'),
(115, 'UPDATE', 'gastos', 12, '3', '2024-10-08 17:24:28'),
(116, 'UPDATE', 'gastos', 13, '3', '2024-10-08 17:24:28'),
(117, 'UPDATE', 'ingresos', 9, '3', '2024-10-08 17:24:42'),
(118, 'UPDATE', 'ingresos', 10, '3', '2024-10-08 17:24:42'),
(119, 'UPDATE', 'ingresos', 11, '3', '2024-10-08 17:24:42'),
(120, 'UPDATE', 'ingresos', 12, '3', '2024-10-08 17:24:42'),
(121, 'UPDATE', 'ingresos', 13, '3', '2024-10-08 17:24:42'),
(122, 'UPDATE', 'usuarios', 3, '3', '2024-10-08 17:43:14'),
(123, 'UPDATE', 'usuarios', 4, '4', '2024-10-08 17:43:14'),
(124, 'INSERT', 'gastos', 16, '3', '2024-10-08 17:43:32'),
(125, 'INSERT', 'ingresos', 15, '4', '2024-10-08 17:46:09'),
(126, 'INSERT', 'gastos', 17, '2', '2024-10-08 17:57:28'),
(127, 'INSERT', 'ingresos', 16, '3', '2024-10-08 17:57:43'),
(128, 'UPDATE', 'ingresos', 1, '2', '2024-10-08 18:23:01'),
(129, 'UPDATE', 'ingresos', 4, '2', '2024-10-08 18:23:01'),
(130, 'UPDATE', 'ingresos', 9, '3', '2024-10-08 18:23:01'),
(131, 'UPDATE', 'usuarios', 2, '2', '2024-10-08 18:47:47'),
(132, 'DELETE', 'usuarios', 3, '3', '2024-10-08 19:34:46'),
(133, 'DELETE', 'usuarios', 4, '4', '2024-10-08 19:34:52'),
(134, 'DELETE', 'gastos', 2, '2', '2024-10-08 19:37:50'),
(135, 'DELETE', 'gastos', 3, '2', '2024-10-08 19:37:50'),
(136, 'DELETE', 'gastos', 4, '2', '2024-10-08 19:37:50'),
(137, 'DELETE', 'gastos', 5, '2', '2024-10-08 19:37:50'),
(138, 'DELETE', 'gastos', 6, '2', '2024-10-08 19:37:50'),
(139, 'DELETE', 'gastos', 7, '2', '2024-10-08 19:37:50'),
(140, 'DELETE', 'gastos', 8, '2', '2024-10-08 19:37:50'),
(141, 'DELETE', 'gastos', 9, '3', '2024-10-08 19:37:50'),
(142, 'DELETE', 'gastos', 10, '3', '2024-10-08 19:37:50'),
(143, 'DELETE', 'gastos', 11, '3', '2024-10-08 19:37:50'),
(144, 'DELETE', 'gastos', 12, '3', '2024-10-08 19:37:50'),
(145, 'DELETE', 'gastos', 13, '3', '2024-10-08 19:37:50'),
(146, 'DELETE', 'gastos', 14, '2', '2024-10-08 19:37:50'),
(147, 'DELETE', 'gastos', 15, '2', '2024-10-08 19:37:50'),
(148, 'DELETE', 'gastos', 16, '3', '2024-10-08 19:37:50'),
(149, 'DELETE', 'gastos', 17, '2', '2024-10-08 19:37:50'),
(150, 'DELETE', 'ingresos', 1, '2', '2024-10-08 19:38:10'),
(151, 'DELETE', 'ingresos', 2, '2', '2024-10-08 19:38:10'),
(152, 'DELETE', 'ingresos', 3, '2', '2024-10-08 19:38:10'),
(153, 'DELETE', 'ingresos', 4, '2', '2024-10-08 19:38:10'),
(154, 'DELETE', 'ingresos', 5, '2', '2024-10-08 19:38:10'),
(155, 'DELETE', 'ingresos', 6, '2', '2024-10-08 19:38:10'),
(156, 'DELETE', 'ingresos', 7, '2', '2024-10-08 19:38:10'),
(157, 'DELETE', 'ingresos', 8, '2', '2024-10-08 19:38:10'),
(158, 'UPDATE', 'ingresos', 9, '2', '2024-10-08 19:39:40'),
(159, 'UPDATE', 'ingresos', 10, '2', '2024-10-08 19:39:54'),
(160, 'UPDATE', 'ingresos', 11, '2', '2024-10-08 19:40:05'),
(161, 'UPDATE', 'ingresos', 13, '2', '2024-10-08 19:40:18'),
(162, 'UPDATE', 'ingresos', 12, '2', '2024-10-08 19:40:31'),
(163, 'UPDATE', 'ingresos', 15, '2', '2024-10-08 19:40:42'),
(164, 'UPDATE', 'ingresos', 16, '2', '2024-10-08 19:40:54'),
(165, 'DELETE', 'ingresos', 9, '2', '2024-10-08 19:41:13'),
(166, 'DELETE', 'ingresos', 10, '2', '2024-10-08 19:41:13'),
(167, 'DELETE', 'ingresos', 11, '2', '2024-10-08 19:41:13'),
(168, 'DELETE', 'ingresos', 12, '2', '2024-10-08 19:41:13'),
(169, 'DELETE', 'ingresos', 13, '2', '2024-10-08 19:41:13'),
(170, 'DELETE', 'ingresos', 15, '2', '2024-10-08 19:41:13'),
(171, 'DELETE', 'ingresos', 16, '2', '2024-10-08 19:41:13'),
(172, 'DELETE', 'usuarios', 12, '12', '2024-10-08 19:49:41'),
(173, 'DELETE', 'usuarios', 13, '13', '2024-10-08 19:49:41'),
(174, 'UPDATE', 'usuarios', 1, '1', '2024-10-08 19:52:33'),
(175, 'UPDATE', 'usuarios', 1, '1', '2024-10-08 19:52:40'),
(176, 'UPDATE', 'usuarios', 1, '1', '2024-10-08 20:09:40'),
(177, 'UPDATE', 'usuarios', 1, '1', '2024-10-08 20:09:52'),
(178, 'INSERT', 'categorias', 17, '2', '2024-10-08 20:23:39'),
(179, 'INSERT', 'categorias', 18, '2', '2024-10-08 20:23:53'),
(180, 'INSERT', 'categorias', 19, '2', '2024-10-08 20:24:02'),
(181, 'INSERT', 'categorias', 20, '2', '2024-10-08 20:24:08'),
(182, 'INSERT', 'categorias', 23, '2', '2024-10-08 20:25:51'),
(183, 'INSERT', 'categorias', 24, '2', '2024-10-08 20:26:00'),
(184, 'INSERT', 'categorias', 25, '2', '2024-10-08 20:26:05'),
(185, 'INSERT', 'categorias', 26, '2', '2024-10-08 20:26:09'),
(186, 'INSERT', 'categorias', 29, '2', '2024-10-08 20:49:33'),
(187, 'INSERT', 'categorias', 30, '2', '2024-10-08 20:49:41'),
(188, 'INSERT', 'categorias', 31, '2', '2024-10-08 20:49:46'),
(189, 'INSERT', 'categorias', 32, '2', '2024-10-08 20:49:56'),
(190, 'INSERT', 'gastos', 18, '2', '2024-10-08 20:59:42'),
(191, 'INSERT', 'gastos', 19, '2', '2024-10-08 21:00:25'),
(192, 'INSERT', 'gastos', 20, '2', '2024-10-08 21:00:54'),
(193, 'INSERT', 'gastos', 21, '2', '2024-10-08 21:01:19'),
(194, 'INSERT', 'ingresos', 17, '2', '2024-10-08 21:04:31'),
(195, 'INSERT', 'ingresos', 18, '2', '2024-10-08 21:05:12'),
(196, 'INSERT', 'ingresos', 19, '2', '2024-10-08 21:05:42'),
(197, 'INSERT', 'ingresos', 20, '2', '2024-10-08 21:06:05'),
(198, 'UPDATE', 'usuarios', 1, '1', '2024-10-08 23:53:27'),
(199, 'UPDATE', 'usuarios', 1, '1', '2024-10-10 14:06:43'),
(200, 'UPDATE', 'usuarios', 11, '11', '2024-10-10 14:49:32'),
(201, 'INSERT', 'usuarios', 15, '15', '2024-10-10 16:24:33'),
(202, 'INSERT', 'usuarios', 15, '15', '2024-10-10 16:24:33'),
(203, 'INSERT', 'usuarios', 15, 'sistema', '2024-10-10 16:24:33'),
(204, 'UPDATE', 'usuarios', 15, '15', '2024-10-10 16:30:27'),
(205, 'DELETE', 'usuarios', 15, '15', '2024-10-10 16:30:38'),
(206, 'INSERT', 'usuarios', 16, '16', '2024-10-10 16:31:32'),
(207, 'INSERT', 'usuarios', 16, '16', '2024-10-10 16:31:32'),
(208, 'INSERT', 'usuarios', 16, 'sistema', '2024-10-10 16:31:32'),
(209, 'UPDATE', 'usuarios', 16, '16', '2024-10-10 16:31:43'),
(210, 'INSERT', 'familias', 7, 'sistema', '2024-10-10 17:12:31'),
(211, 'delete', 'familias', 7, '', '2024-10-10 17:48:13'),
(212, 'INSERT', 'familias', 8, 'sistema', '2024-10-10 17:49:13'),
(213, 'delete', 'familias', 8, '', '2024-10-10 18:12:47'),
(214, 'INSERT', 'familias', 9, 'sistema', '2024-10-10 18:32:53'),
(215, 'INSERT', 'familias', 10, 'sistema', '2024-10-11 08:04:44'),
(216, 'INSERT', 'familias', 11, 'sistema', '2024-10-11 11:10:25'),
(217, 'delete', 'familias', 9, '', '2024-10-11 12:50:17'),
(218, 'delete', 'familias', 10, '', '2024-10-11 12:50:20'),
(219, 'delete', 'familias', 11, '', '2024-10-11 12:50:23'),
(220, 'INSERT', 'familias', 12, 'sistema', '2024-10-11 13:04:03'),
(221, 'delete', 'familias', 12, '', '2024-10-11 13:46:21'),
(222, 'INSERT', 'familias', 13, 'sistema', '2024-10-11 13:47:29'),
(223, 'delete', 'familias', 13, '', '2024-10-11 13:49:41'),
(224, 'INSERT', 'familias', 14, 'sistema', '2024-10-11 13:50:02'),
(225, 'INSERT', 'familias', 15, 'sistema', '2024-10-11 14:05:01'),
(226, 'delete', 'familias', 14, '', '2024-10-11 15:05:55'),
(227, 'delete', 'familias', 15, '', '2024-10-11 15:05:58'),
(228, 'INSERT', 'familias', 16, 'sistema', '2024-10-11 15:07:27'),
(229, 'INSERT', 'familias', 17, 'sistema', '2024-10-11 16:29:11'),
(230, 'INSERT', 'familias', 18, 'sistema', '2024-10-11 17:01:50'),
(231, 'UPDATE', 'familias', 18, 'sistema', '2024-10-11 17:02:16'),
(232, 'UPDATE', 'familias', 18, 'sistema', '2024-10-11 17:03:47'),
(233, 'UPDATE', 'familias', 17, 'sistema', '2024-10-11 17:04:10'),
(234, 'UPDATE', 'familias', 18, 'sistema', '2024-10-11 17:04:21'),
(235, 'INSERT', 'familias', 19, 'sistema', '2024-10-11 17:05:17'),
(236, 'UPDATE', 'familias', 19, 'sistema', '2024-10-11 17:05:30'),
(237, 'INSERT', 'usuarios', 17, '17', '2024-10-12 11:13:29'),
(238, 'INSERT', 'usuarios', 17, '17', '2024-10-12 11:13:29'),
(239, 'INSERT', 'usuarios', 17, 'sistema', '2024-10-12 11:13:29'),
(240, 'INSERT', 'grupos', 9, 'Sistema', '2024-10-12 11:58:29'),
(241, 'UPDATE', 'grupos', 9, 'Sistema', '2024-10-12 12:20:59'),
(242, 'UPDATE', 'grupos', 9, 'Sistema', '2024-10-12 12:21:29'),
(243, 'UPDATE', 'grupos', 9, 'Sistema', '2024-10-12 12:21:46'),
(244, 'UPDATE', 'grupos', 9, 'Sistema', '2024-10-12 12:22:01'),
(245, 'UPDATE', 'grupos', 9, 'Sistema', '2024-10-12 12:23:12'),
(246, 'UPDATE', 'grupos', 9, 'Sistema', '2024-10-12 12:57:39'),
(247, 'INSERT', 'grupos', 10, 'Sistema', '2024-10-12 12:58:27'),
(248, 'INSERT', 'familias', 20, 'sistema', '2024-10-12 12:59:39'),
(249, 'UPDATE', 'familias', 20, 'sistema', '2024-10-12 12:59:49'),
(250, 'UPDATE', 'grupos', 10, 'Sistema', '2024-10-12 14:09:48'),
(251, 'DELETE', 'grupos', 10, '', '2024-10-12 14:09:55'),
(252, 'INSERT', 'grupos', 11, 'Sistema', '2024-10-12 14:10:06'),
(253, 'INSERT', 'categorias', 33, '1', '2024-10-12 16:32:52'),
(254, 'INSERT', 'categorias', 35, '1', '2024-10-12 16:33:49'),
(255, 'INSERT', 'categorias', 36, '1', '2024-10-12 16:48:43'),
(256, 'INSERT', 'categorias', 37, '1', '2024-10-12 16:49:14'),
(257, 'INSERT', 'categorias', 38, '1', '2024-10-12 16:50:23'),
(258, 'INSERT', 'categorias', 39, '1', '2024-10-12 16:50:41'),
(259, 'UPDATE', 'usuarios', 17, '17', '2024-10-12 16:53:00'),
(260, 'INSERT', 'usuarios', 18, '18', '2024-10-12 17:42:45'),
(261, 'INSERT', 'usuarios', 18, '18', '2024-10-12 17:42:45'),
(262, 'INSERT', 'usuarios', 18, 'sistema', '2024-10-12 17:42:45'),
(263, 'UPDATE', 'usuarios', 18, '18', '2024-10-12 18:02:40'),
(264, 'UPDATE', 'usuarios', 18, '18', '2024-10-12 18:03:01'),
(265, 'UPDATE', 'usuarios', 11, '11', '2024-10-12 18:04:16'),
(266, 'INSERT', 'familias', 21, 'sistema', '2024-10-12 18:28:50'),
(267, 'INSERT', 'usuarios', 19, '19', '2024-10-12 18:28:50'),
(268, 'INSERT', 'usuarios', 19, '19', '2024-10-12 18:28:50'),
(269, 'INSERT', 'usuarios', 19, 'sistema', '2024-10-12 18:28:50'),
(270, 'UPDATE', 'usuarios', 16, '16', '2024-10-13 08:00:16'),
(271, 'UPDATE', 'usuarios', 19, '19', '2024-10-13 08:00:55'),
(272, 'UPDATE', 'usuarios', 17, '17', '2024-10-13 08:01:42'),
(273, 'UPDATE', 'usuarios', 18, '18', '2024-10-13 08:02:07'),
(274, 'UPDATE', 'grupos', 11, 'Sistema', '2024-10-13 08:17:31'),
(275, 'UPDATE', 'familias', 21, 'sistema', '2024-10-13 08:18:08'),
(276, 'INSERT', 'grupos', 12, 'Sistema', '2024-10-13 08:22:55'),
(277, 'INSERT', 'grupos', 13, 'Sistema', '2024-10-13 08:28:06'),
(278, 'INSERT', 'usuarios', 20, '20', '2024-10-13 08:28:06'),
(279, 'INSERT', 'usuarios', 20, '20', '2024-10-13 08:28:06'),
(280, 'INSERT', 'usuarios', 20, 'sistema', '2024-10-13 08:28:06'),
(281, 'UPDATE', 'usuarios', 20, '20', '2024-10-13 08:31:55'),
(282, 'INSERT', 'familias', 22, 'sistema', '2024-10-13 08:34:52'),
(283, 'INSERT', 'usuarios', 21, '21', '2024-10-13 08:34:52'),
(284, 'INSERT', 'usuarios', 21, '21', '2024-10-13 08:34:52'),
(285, 'INSERT', 'usuarios', 21, 'sistema', '2024-10-13 08:34:52'),
(286, 'UPDATE', 'familias', 16, 'sistema', '2024-10-13 08:38:59'),
(287, 'delete', 'familias', 17, '', '2024-10-13 08:44:28'),
(288, 'delete', 'familias', 21, '', '2024-10-13 08:44:42'),
(289, 'delete', 'familias', 19, '', '2024-10-13 08:44:49'),
(290, 'delete', 'familias', 18, '', '2024-10-13 08:44:54'),
(291, 'delete', 'familias', 20, '', '2024-10-13 08:44:59'),
(292, 'UPDATE', 'familias', 22, 'sistema', '2024-10-13 08:45:31'),
(293, 'UPDATE', 'familias', 1, 'sistema', '2024-10-13 14:01:26'),
(294, 'UPDATE', 'familias', 16, 'sistema', '2024-10-13 14:01:26'),
(295, 'UPDATE', 'familias', 22, 'sistema', '2024-10-13 14:01:26'),
(296, 'UPDATE', 'grupos', 1, 'Sistema', '2024-10-13 14:01:26'),
(297, 'UPDATE', 'grupos', 9, 'Sistema', '2024-10-13 14:01:26'),
(298, 'UPDATE', 'grupos', 11, 'Sistema', '2024-10-13 14:01:26'),
(299, 'UPDATE', 'grupos', 12, 'Sistema', '2024-10-13 14:01:26'),
(300, 'UPDATE', 'grupos', 13, 'Sistema', '2024-10-13 14:01:26'),
(301, 'UPDATE', 'usuarios', 1, '1', '2024-10-13 14:01:26'),
(302, 'UPDATE', 'usuarios', 2, '2', '2024-10-13 14:01:26'),
(303, 'UPDATE', 'usuarios', 5, '5', '2024-10-13 14:01:26'),
(304, 'UPDATE', 'usuarios', 6, '6', '2024-10-13 14:01:26'),
(305, 'UPDATE', 'usuarios', 7, '7', '2024-10-13 14:01:26'),
(306, 'UPDATE', 'usuarios', 8, '8', '2024-10-13 14:01:26'),
(307, 'UPDATE', 'usuarios', 9, '9', '2024-10-13 14:01:26'),
(308, 'UPDATE', 'usuarios', 10, '10', '2024-10-13 14:01:26'),
(309, 'UPDATE', 'usuarios', 11, '11', '2024-10-13 14:01:26'),
(310, 'UPDATE', 'usuarios', 16, '16', '2024-10-13 14:01:26'),
(311, 'UPDATE', 'usuarios', 17, '17', '2024-10-13 14:01:26'),
(312, 'UPDATE', 'usuarios', 18, '18', '2024-10-13 14:01:26'),
(313, 'UPDATE', 'usuarios', 19, '19', '2024-10-13 14:01:26'),
(314, 'UPDATE', 'usuarios', 20, '20', '2024-10-13 14:01:26'),
(315, 'UPDATE', 'usuarios', 21, '21', '2024-10-13 14:01:26'),
(316, 'UPDATE', 'usuarios', 21, '21', '2024-10-13 14:02:17'),
(317, 'INSERT', 'familias', 23, 'sistema', '2024-10-13 14:03:11'),
(318, 'INSERT', 'familias', 24, 'sistema', '2024-10-13 14:07:18'),
(319, 'UPDATE', 'usuarios', 20, '20', '2024-10-13 14:10:30'),
(320, 'UPDATE', 'familias', 23, 'sistema', '2024-10-13 14:12:50'),
(321, 'UPDATE', 'familias', 24, 'sistema', '2024-10-13 14:13:01'),
(322, 'INSERT', 'familias', 25, 'sistema', '2024-10-13 14:37:54'),
(323, 'INSERT', 'usuarios', 22, '22', '2024-10-13 14:37:54'),
(324, 'INSERT', 'usuarios', 22, '22', '2024-10-13 14:37:54'),
(325, 'INSERT', 'usuarios', 22, 'sistema', '2024-10-13 14:37:54'),
(326, 'UPDATE', 'familias', 1, 'sistema', '2024-10-13 14:39:01'),
(327, 'UPDATE', 'familias', 16, 'sistema', '2024-10-13 14:39:01'),
(328, 'UPDATE', 'familias', 22, 'sistema', '2024-10-13 14:39:01'),
(329, 'UPDATE', 'familias', 23, 'sistema', '2024-10-13 14:39:01'),
(330, 'UPDATE', 'familias', 24, 'sistema', '2024-10-13 14:39:01'),
(331, 'UPDATE', 'familias', 25, 'sistema', '2024-10-13 14:39:01'),
(332, 'UPDATE', 'grupos', 1, 'Sistema', '2024-10-13 14:39:01'),
(333, 'UPDATE', 'grupos', 9, 'Sistema', '2024-10-13 14:39:01'),
(334, 'UPDATE', 'grupos', 11, 'Sistema', '2024-10-13 14:39:01'),
(335, 'UPDATE', 'grupos', 12, 'Sistema', '2024-10-13 14:39:01'),
(336, 'UPDATE', 'grupos', 13, 'Sistema', '2024-10-13 14:39:01'),
(337, 'UPDATE', 'usuarios', 1, '1', '2024-10-13 14:39:01'),
(338, 'UPDATE', 'usuarios', 2, '2', '2024-10-13 14:39:01'),
(339, 'UPDATE', 'usuarios', 5, '5', '2024-10-13 14:39:01'),
(340, 'UPDATE', 'usuarios', 6, '6', '2024-10-13 14:39:01'),
(341, 'UPDATE', 'usuarios', 7, '7', '2024-10-13 14:39:01'),
(342, 'UPDATE', 'usuarios', 8, '8', '2024-10-13 14:39:01'),
(343, 'UPDATE', 'usuarios', 9, '9', '2024-10-13 14:39:01'),
(344, 'UPDATE', 'usuarios', 10, '10', '2024-10-13 14:39:01'),
(345, 'UPDATE', 'usuarios', 11, '11', '2024-10-13 14:39:01'),
(346, 'UPDATE', 'usuarios', 16, '16', '2024-10-13 14:39:01'),
(347, 'UPDATE', 'usuarios', 17, '17', '2024-10-13 14:39:01'),
(348, 'UPDATE', 'usuarios', 18, '18', '2024-10-13 14:39:01'),
(349, 'UPDATE', 'usuarios', 19, '19', '2024-10-13 14:39:01'),
(350, 'UPDATE', 'usuarios', 20, '20', '2024-10-13 14:39:01'),
(351, 'UPDATE', 'usuarios', 21, '21', '2024-10-13 14:39:01'),
(352, 'UPDATE', 'usuarios', 22, '22', '2024-10-13 14:39:01'),
(353, 'INSERT', 'usuarios', 23, '23', '2024-10-13 14:44:17'),
(354, 'INSERT', 'usuarios', 23, '23', '2024-10-13 14:44:17'),
(355, 'INSERT', 'usuarios', 23, 'sistema', '2024-10-13 14:44:17'),
(356, 'INSERT', 'familias', 26, 'sistema', '2024-10-13 15:45:19'),
(357, 'INSERT', 'usuarios', 24, '24', '2024-10-13 15:59:43'),
(358, 'INSERT', 'usuarios', 24, '24', '2024-10-13 15:59:43'),
(359, 'INSERT', 'usuarios', 24, 'sistema', '2024-10-13 15:59:43'),
(360, 'INSERT', 'usuarios', 25, '25', '2024-10-13 17:07:26'),
(361, 'INSERT', 'usuarios', 25, '25', '2024-10-13 17:07:26'),
(362, 'INSERT', 'usuarios', 25, 'sistema', '2024-10-13 17:07:26'),
(363, 'INSERT', 'familias', 28, 'sistema', '2024-10-13 17:07:26'),
(364, 'UPDATE', 'familias', 28, 'sistema', '2024-10-13 17:07:26'),
(365, 'UPDATE', 'usuarios', 25, '25', '2024-10-13 17:07:26'),
(366, 'UPDATE', 'usuarios', 25, '25', '2024-10-13 17:07:26'),
(367, 'INSERT', 'usuarios', 26, '26', '2024-10-13 17:39:57'),
(368, 'INSERT', 'usuarios', 26, '26', '2024-10-13 17:39:57'),
(369, 'INSERT', 'usuarios', 26, 'sistema', '2024-10-13 17:39:57'),
(370, 'INSERT', 'familias', 29, 'sistema', '2024-10-13 17:39:57'),
(371, 'UPDATE', 'familias', 29, 'sistema', '2024-10-13 17:39:57'),
(372, 'UPDATE', 'usuarios', 26, '26', '2024-10-13 17:39:57'),
(373, 'UPDATE', 'usuarios', 26, '26', '2024-10-13 17:39:57'),
(374, 'INSERT', 'categorias', 40, '26', '2024-10-13 17:43:49'),
(375, 'INSERT', 'categorias', 41, '26', '2024-10-13 17:44:16'),
(376, 'DELETE', 'usuarios', 25, '25', '2024-10-13 18:17:41'),
(377, 'DELETE', 'usuarios', 24, '24', '2024-10-13 18:17:46'),
(378, 'delete', 'familias', 26, '', '2024-10-13 18:18:22'),
(379, 'delete', 'familias', 28, '', '2024-10-13 18:18:25'),
(380, 'UPDATE', 'usuarios', 26, '26', '2024-10-13 18:19:46'),
(381, 'delete', 'familias', 29, '', '2024-10-13 18:20:47'),
(382, 'delete', 'familias', 25, '', '2024-10-13 18:20:57'),
(383, 'DELETE', 'grupos', 12, '', '2024-10-13 18:21:14'),
(384, 'DELETE', 'grupos', 13, '', '2024-10-13 18:21:17'),
(385, 'DELETE', 'usuarios', 22, '22', '2024-10-13 18:22:18'),
(386, 'DELETE', 'usuarios', 23, '23', '2024-10-13 18:22:18'),
(387, 'INSERT', 'usuarios', 27, '27', '2024-10-13 18:28:47'),
(388, 'INSERT', 'usuarios', 27, '27', '2024-10-13 18:28:47'),
(389, 'INSERT', 'usuarios', 27, 'sistema', '2024-10-13 18:28:47'),
(390, 'INSERT', 'familias', 30, 'sistema', '2024-10-13 18:28:47'),
(391, 'UPDATE', 'familias', 30, 'sistema', '2024-10-13 18:28:47'),
(392, 'UPDATE', 'usuarios', 27, '27', '2024-10-13 18:28:47'),
(393, 'UPDATE', 'usuarios', 27, '27', '2024-10-13 18:28:47'),
(394, 'INSERT', 'gastos', 22, '27', '2024-10-13 18:42:54'),
(395, 'INSERT', 'gastos', 23, '27', '2024-10-13 18:49:01'),
(396, 'INSERT', 'gastos', 24, '27', '2024-10-13 19:07:20'),
(397, 'INSERT', 'gastos', 25, '27', '2024-10-13 19:14:43'),
(398, 'INSERT', 'gastos', 26, '27', '2024-10-13 19:27:37'),
(399, 'INSERT', 'gastos', 27, '27', '2024-10-13 19:41:43'),
(400, 'INSERT', 'gastos', 28, '27', '2024-10-13 19:50:15'),
(401, 'INSERT', 'ingresos', 22, '27', '2024-10-13 21:49:23'),
(402, 'INSERT', 'ingresos', 23, '27', '2024-10-13 21:52:30'),
(403, 'INSERT', 'ingresos', 24, '27', '2024-10-13 21:53:00'),
(404, 'INSERT', 'categorias', 42, '27', '2024-10-13 21:54:13'),
(405, 'INSERT', 'usuarios', 28, '28', '2024-10-14 16:28:08'),
(406, 'INSERT', 'usuarios', 28, '28', '2024-10-14 16:28:08'),
(407, 'INSERT', 'usuarios', 28, 'sistema', '2024-10-14 16:28:08'),
(408, 'UPDATE', 'familias', 1, 'sistema', '2024-10-14 16:28:24'),
(409, 'UPDATE', 'familias', 16, 'sistema', '2024-10-14 16:28:24'),
(410, 'UPDATE', 'familias', 22, 'sistema', '2024-10-14 16:28:24'),
(411, 'UPDATE', 'familias', 23, 'sistema', '2024-10-14 16:28:24'),
(412, 'UPDATE', 'familias', 24, 'sistema', '2024-10-14 16:28:24'),
(413, 'UPDATE', 'familias', 30, 'sistema', '2024-10-14 16:28:24'),
(414, 'UPDATE', 'grupos', 1, 'Sistema', '2024-10-14 16:28:24'),
(415, 'UPDATE', 'grupos', 9, 'Sistema', '2024-10-14 16:28:24'),
(416, 'UPDATE', 'grupos', 11, 'Sistema', '2024-10-14 16:28:24'),
(417, 'UPDATE', 'usuarios', 1, '1', '2024-10-14 16:28:24'),
(418, 'UPDATE', 'usuarios', 2, '2', '2024-10-14 16:28:24'),
(419, 'UPDATE', 'usuarios', 5, '5', '2024-10-14 16:28:24'),
(420, 'UPDATE', 'usuarios', 6, '6', '2024-10-14 16:28:24'),
(421, 'UPDATE', 'usuarios', 7, '7', '2024-10-14 16:28:24'),
(422, 'UPDATE', 'usuarios', 8, '8', '2024-10-14 16:28:24'),
(423, 'UPDATE', 'usuarios', 9, '9', '2024-10-14 16:28:24'),
(424, 'UPDATE', 'usuarios', 10, '10', '2024-10-14 16:28:24'),
(425, 'UPDATE', 'usuarios', 11, '11', '2024-10-14 16:28:24'),
(426, 'UPDATE', 'usuarios', 16, '16', '2024-10-14 16:28:24'),
(427, 'UPDATE', 'usuarios', 17, '17', '2024-10-14 16:28:24'),
(428, 'UPDATE', 'usuarios', 18, '18', '2024-10-14 16:28:24'),
(429, 'UPDATE', 'usuarios', 19, '19', '2024-10-14 16:28:24'),
(430, 'UPDATE', 'usuarios', 20, '20', '2024-10-14 16:28:24'),
(431, 'UPDATE', 'usuarios', 21, '21', '2024-10-14 16:28:24'),
(432, 'UPDATE', 'usuarios', 26, '26', '2024-10-14 16:28:24'),
(433, 'UPDATE', 'usuarios', 27, '27', '2024-10-14 16:28:24'),
(434, 'UPDATE', 'usuarios', 28, '28', '2024-10-14 16:28:24'),
(435, 'INSERT', 'usuarios', 29, '29', '2024-10-14 16:30:53'),
(436, 'INSERT', 'usuarios', 29, '29', '2024-10-14 16:30:53'),
(437, 'INSERT', 'usuarios', 29, 'sistema', '2024-10-14 16:30:53'),
(438, 'UPDATE', 'usuarios', 29, '29', '2024-10-14 16:30:54'),
(439, 'INSERT', 'usuarios', 30, '30', '2024-10-14 17:24:50'),
(440, 'INSERT', 'usuarios', 30, '30', '2024-10-14 17:24:50'),
(441, 'INSERT', 'usuarios', 30, 'sistema', '2024-10-14 17:24:50'),
(442, 'UPDATE', 'usuarios', 30, '30', '2024-10-14 17:24:50'),
(443, 'INSERT', 'usuarios', 31, '31', '2024-10-14 18:11:36'),
(444, 'INSERT', 'usuarios', 31, '31', '2024-10-14 18:11:36'),
(445, 'INSERT', 'usuarios', 31, 'sistema', '2024-10-14 18:11:36'),
(446, 'UPDATE', 'usuarios', 31, '31', '2024-10-14 18:11:37'),
(447, 'UPDATE', 'usuarios', 31, '31', '2024-10-14 18:11:37'),
(448, 'INSERT', 'ingresos', 25, '31', '2024-10-14 18:18:30'),
(449, 'INSERT', 'ingresos', 26, '31', '2024-10-14 18:19:11'),
(450, 'INSERT', 'gastos', 29, '31', '2024-10-14 18:19:50'),
(451, 'INSERT', 'gastos', 30, '31', '2024-10-14 18:21:01'),
(452, 'DELETE', 'ingresos', 22, '27', '2024-10-15 17:11:45'),
(453, 'UPDATE', 'usuarios', 19, '19', '2024-10-16 16:14:53'),
(454, 'UPDATE', 'usuarios', 18, '18', '2024-10-16 16:15:24'),
(455, 'DELETE', 'usuarios', 17, '17', '2024-10-18 16:53:32'),
(456, 'INSERT', 'usuarios', 32, '32', '2024-10-20 09:21:21'),
(457, 'INSERT', 'usuarios', 32, '32', '2024-10-20 09:21:21'),
(458, 'INSERT', 'usuarios', 32, 'sistema', '2024-10-20 09:21:21'),
(459, 'UPDATE', 'familias', 1, 'sistema', '2024-10-20 09:21:37'),
(460, 'UPDATE', 'familias', 16, 'sistema', '2024-10-20 09:21:37'),
(461, 'UPDATE', 'familias', 22, 'sistema', '2024-10-20 09:21:37'),
(462, 'UPDATE', 'familias', 23, 'sistema', '2024-10-20 09:21:37'),
(463, 'UPDATE', 'familias', 24, 'sistema', '2024-10-20 09:21:37'),
(464, 'UPDATE', 'familias', 30, 'sistema', '2024-10-20 09:21:37'),
(465, 'UPDATE', 'grupos', 1, 'Sistema', '2024-10-20 09:21:37'),
(466, 'UPDATE', 'grupos', 9, 'Sistema', '2024-10-20 09:21:37'),
(467, 'UPDATE', 'grupos', 11, 'Sistema', '2024-10-20 09:21:37'),
(468, 'UPDATE', 'usuarios', 1, '1', '2024-10-20 09:21:37'),
(469, 'UPDATE', 'usuarios', 2, '2', '2024-10-20 09:21:37'),
(470, 'UPDATE', 'usuarios', 5, '5', '2024-10-20 09:21:37'),
(471, 'UPDATE', 'usuarios', 6, '6', '2024-10-20 09:21:37'),
(472, 'UPDATE', 'usuarios', 7, '7', '2024-10-20 09:21:37'),
(473, 'UPDATE', 'usuarios', 8, '8', '2024-10-20 09:21:37'),
(474, 'UPDATE', 'usuarios', 9, '9', '2024-10-20 09:21:37'),
(475, 'UPDATE', 'usuarios', 10, '10', '2024-10-20 09:21:37'),
(476, 'UPDATE', 'usuarios', 11, '11', '2024-10-20 09:21:37'),
(477, 'UPDATE', 'usuarios', 11, '11', '2024-10-20 09:21:37'),
(478, 'UPDATE', 'usuarios', 16, '16', '2024-10-20 09:21:37'),
(479, 'UPDATE', 'usuarios', 18, '18', '2024-10-20 09:21:37'),
(480, 'UPDATE', 'usuarios', 19, '19', '2024-10-20 09:21:37'),
(481, 'UPDATE', 'usuarios', 19, '19', '2024-10-20 09:21:37'),
(482, 'UPDATE', 'usuarios', 20, '20', '2024-10-20 09:21:37'),
(483, 'UPDATE', 'usuarios', 20, '20', '2024-10-20 09:21:37'),
(484, 'UPDATE', 'usuarios', 20, '20', '2024-10-20 09:21:37'),
(485, 'UPDATE', 'usuarios', 21, '21', '2024-10-20 09:21:37'),
(486, 'UPDATE', 'usuarios', 26, '26', '2024-10-20 09:21:37'),
(487, 'UPDATE', 'usuarios', 27, '27', '2024-10-20 09:21:37'),
(488, 'UPDATE', 'usuarios', 28, '28', '2024-10-20 09:21:37'),
(489, 'UPDATE', 'usuarios', 29, '29', '2024-10-20 09:21:37'),
(490, 'UPDATE', 'usuarios', 30, '30', '2024-10-20 09:21:37'),
(491, 'UPDATE', 'usuarios', 31, '31', '2024-10-20 09:21:37'),
(492, 'UPDATE', 'usuarios', 32, '32', '2024-10-20 09:21:37'),
(493, 'UPDATE', 'familias', 1, 'sistema', '2024-10-20 09:21:39'),
(494, 'UPDATE', 'familias', 16, 'sistema', '2024-10-20 09:21:39'),
(495, 'UPDATE', 'familias', 22, 'sistema', '2024-10-20 09:21:39'),
(496, 'UPDATE', 'familias', 23, 'sistema', '2024-10-20 09:21:39'),
(497, 'UPDATE', 'familias', 24, 'sistema', '2024-10-20 09:21:39'),
(498, 'UPDATE', 'familias', 30, 'sistema', '2024-10-20 09:21:39'),
(499, 'UPDATE', 'grupos', 1, 'Sistema', '2024-10-20 09:21:39'),
(500, 'UPDATE', 'grupos', 9, 'Sistema', '2024-10-20 09:21:39'),
(501, 'UPDATE', 'grupos', 11, 'Sistema', '2024-10-20 09:21:39'),
(502, 'UPDATE', 'usuarios', 1, '1', '2024-10-20 09:21:39'),
(503, 'UPDATE', 'usuarios', 2, '2', '2024-10-20 09:21:39'),
(504, 'UPDATE', 'usuarios', 5, '5', '2024-10-20 09:21:39'),
(505, 'UPDATE', 'usuarios', 6, '6', '2024-10-20 09:21:39'),
(506, 'UPDATE', 'usuarios', 7, '7', '2024-10-20 09:21:39'),
(507, 'UPDATE', 'usuarios', 8, '8', '2024-10-20 09:21:39'),
(508, 'UPDATE', 'usuarios', 9, '9', '2024-10-20 09:21:39'),
(509, 'UPDATE', 'usuarios', 10, '10', '2024-10-20 09:21:39'),
(510, 'UPDATE', 'usuarios', 11, '11', '2024-10-20 09:21:39'),
(511, 'UPDATE', 'usuarios', 11, '11', '2024-10-20 09:21:39'),
(512, 'UPDATE', 'usuarios', 16, '16', '2024-10-20 09:21:39'),
(513, 'UPDATE', 'usuarios', 18, '18', '2024-10-20 09:21:39'),
(514, 'UPDATE', 'usuarios', 19, '19', '2024-10-20 09:21:39'),
(515, 'UPDATE', 'usuarios', 19, '19', '2024-10-20 09:21:39'),
(516, 'UPDATE', 'usuarios', 20, '20', '2024-10-20 09:21:39'),
(517, 'UPDATE', 'usuarios', 20, '20', '2024-10-20 09:21:39'),
(518, 'UPDATE', 'usuarios', 20, '20', '2024-10-20 09:21:39'),
(519, 'UPDATE', 'usuarios', 21, '21', '2024-10-20 09:21:39'),
(520, 'UPDATE', 'usuarios', 26, '26', '2024-10-20 09:21:39'),
(521, 'UPDATE', 'usuarios', 27, '27', '2024-10-20 09:21:39'),
(522, 'UPDATE', 'usuarios', 28, '28', '2024-10-20 09:21:39'),
(523, 'UPDATE', 'usuarios', 29, '29', '2024-10-20 09:21:39'),
(524, 'UPDATE', 'usuarios', 30, '30', '2024-10-20 09:21:39'),
(525, 'UPDATE', 'usuarios', 31, '31', '2024-10-20 09:21:39'),
(526, 'UPDATE', 'usuarios', 32, '32', '2024-10-20 09:21:39'),
(527, 'DELETE', 'usuarios', 32, '32', '2024-10-20 09:24:17'),
(528, 'INSERT', 'usuarios', 33, '33', '2024-10-20 09:25:11'),
(529, 'INSERT', 'usuarios', 33, '33', '2024-10-20 09:25:11'),
(530, 'INSERT', 'usuarios', 33, 'sistema', '2024-10-20 09:25:11'),
(531, 'INSERT', 'usuarios', 34, '34', '2024-10-20 09:27:59'),
(532, 'INSERT', 'usuarios', 34, '34', '2024-10-20 09:27:59'),
(533, 'INSERT', 'usuarios', 34, 'sistema', '2024-10-20 09:27:59'),
(534, 'INSERT', 'familias', 31, 'sistema', '2024-10-20 09:27:59'),
(535, 'INSERT', 'usuarios', 35, '35', '2024-10-20 09:29:23'),
(536, 'INSERT', 'usuarios', 35, '35', '2024-10-20 09:29:23'),
(537, 'INSERT', 'usuarios', 35, 'sistema', '2024-10-20 09:29:23'),
(538, 'INSERT', 'usuarios', 36, '36', '2024-10-20 09:31:36'),
(539, 'INSERT', 'usuarios', 36, '36', '2024-10-20 09:31:36'),
(540, 'INSERT', 'usuarios', 36, 'sistema', '2024-10-20 09:31:36'),
(541, 'INSERT', 'usuarios', 37, '37', '2024-10-20 09:34:04'),
(542, 'INSERT', 'usuarios', 37, '37', '2024-10-20 09:34:04'),
(543, 'INSERT', 'usuarios', 37, 'sistema', '2024-10-20 09:34:04'),
(544, 'INSERT', 'grupos', 14, 'Sistema', '2024-10-20 09:34:04'),
(545, 'INSERT', 'usuarios', 38, '38', '2024-10-20 09:36:05'),
(546, 'INSERT', 'usuarios', 38, '38', '2024-10-20 09:36:05'),
(547, 'INSERT', 'usuarios', 38, 'sistema', '2024-10-20 09:36:05'),
(548, 'UPDATE', 'familias', 1, 'sistema', '2024-10-20 09:36:20'),
(549, 'UPDATE', 'familias', 16, 'sistema', '2024-10-20 09:36:20'),
(550, 'UPDATE', 'familias', 22, 'sistema', '2024-10-20 09:36:20'),
(551, 'UPDATE', 'familias', 23, 'sistema', '2024-10-20 09:36:20'),
(552, 'UPDATE', 'familias', 24, 'sistema', '2024-10-20 09:36:20'),
(553, 'UPDATE', 'familias', 30, 'sistema', '2024-10-20 09:36:20'),
(554, 'UPDATE', 'familias', 31, 'sistema', '2024-10-20 09:36:20'),
(555, 'UPDATE', 'grupos', 1, 'Sistema', '2024-10-20 09:36:20'),
(556, 'UPDATE', 'grupos', 9, 'Sistema', '2024-10-20 09:36:20'),
(557, 'UPDATE', 'grupos', 11, 'Sistema', '2024-10-20 09:36:20'),
(558, 'UPDATE', 'grupos', 14, 'Sistema', '2024-10-20 09:36:20'),
(559, 'UPDATE', 'usuarios', 1, '1', '2024-10-20 09:36:20'),
(560, 'UPDATE', 'usuarios', 2, '2', '2024-10-20 09:36:20'),
(561, 'UPDATE', 'usuarios', 5, '5', '2024-10-20 09:36:20'),
(562, 'UPDATE', 'usuarios', 6, '6', '2024-10-20 09:36:20'),
(563, 'UPDATE', 'usuarios', 7, '7', '2024-10-20 09:36:20'),
(564, 'UPDATE', 'usuarios', 8, '8', '2024-10-20 09:36:20'),
(565, 'UPDATE', 'usuarios', 9, '9', '2024-10-20 09:36:20'),
(566, 'UPDATE', 'usuarios', 10, '10', '2024-10-20 09:36:20'),
(567, 'UPDATE', 'usuarios', 11, '11', '2024-10-20 09:36:20'),
(568, 'UPDATE', 'usuarios', 11, '11', '2024-10-20 09:36:20'),
(569, 'UPDATE', 'usuarios', 16, '16', '2024-10-20 09:36:20'),
(570, 'UPDATE', 'usuarios', 18, '18', '2024-10-20 09:36:20'),
(571, 'UPDATE', 'usuarios', 19, '19', '2024-10-20 09:36:20'),
(572, 'UPDATE', 'usuarios', 19, '19', '2024-10-20 09:36:20'),
(573, 'UPDATE', 'usuarios', 20, '20', '2024-10-20 09:36:20'),
(574, 'UPDATE', 'usuarios', 20, '20', '2024-10-20 09:36:20'),
(575, 'UPDATE', 'usuarios', 20, '20', '2024-10-20 09:36:20'),
(576, 'UPDATE', 'usuarios', 21, '21', '2024-10-20 09:36:20'),
(577, 'UPDATE', 'usuarios', 26, '26', '2024-10-20 09:36:20'),
(578, 'UPDATE', 'usuarios', 27, '27', '2024-10-20 09:36:20'),
(579, 'UPDATE', 'usuarios', 28, '28', '2024-10-20 09:36:20'),
(580, 'UPDATE', 'usuarios', 29, '29', '2024-10-20 09:36:20'),
(581, 'UPDATE', 'usuarios', 30, '30', '2024-10-20 09:36:20'),
(582, 'UPDATE', 'usuarios', 31, '31', '2024-10-20 09:36:20'),
(583, 'UPDATE', 'usuarios', 33, '33', '2024-10-20 09:36:20'),
(584, 'UPDATE', 'usuarios', 34, '34', '2024-10-20 09:36:20'),
(585, 'UPDATE', 'usuarios', 35, '35', '2024-10-20 09:36:20'),
(586, 'UPDATE', 'usuarios', 36, '36', '2024-10-20 09:36:20'),
(587, 'UPDATE', 'usuarios', 37, '37', '2024-10-20 09:36:20'),
(588, 'UPDATE', 'usuarios', 38, '38', '2024-10-20 09:36:20'),
(589, 'INSERT', 'usuarios', 39, '39', '2024-10-20 09:40:26'),
(590, 'INSERT', 'usuarios', 39, '39', '2024-10-20 09:40:26'),
(591, 'INSERT', 'usuarios', 39, 'sistema', '2024-10-20 09:40:26'),
(592, 'UPDATE', 'familias', 1, 'sistema', '2024-10-20 09:40:49'),
(593, 'UPDATE', 'familias', 16, 'sistema', '2024-10-20 09:40:49'),
(594, 'UPDATE', 'familias', 22, 'sistema', '2024-10-20 09:40:49'),
(595, 'UPDATE', 'familias', 23, 'sistema', '2024-10-20 09:40:49'),
(596, 'UPDATE', 'familias', 24, 'sistema', '2024-10-20 09:40:49'),
(597, 'UPDATE', 'familias', 30, 'sistema', '2024-10-20 09:40:49'),
(598, 'UPDATE', 'familias', 31, 'sistema', '2024-10-20 09:40:49'),
(599, 'UPDATE', 'grupos', 1, 'Sistema', '2024-10-20 09:40:49'),
(600, 'UPDATE', 'grupos', 9, 'Sistema', '2024-10-20 09:40:49'),
(601, 'UPDATE', 'grupos', 11, 'Sistema', '2024-10-20 09:40:49'),
(602, 'UPDATE', 'grupos', 14, 'Sistema', '2024-10-20 09:40:49'),
(603, 'UPDATE', 'usuarios', 1, '1', '2024-10-20 09:40:49'),
(604, 'UPDATE', 'usuarios', 2, '2', '2024-10-20 09:40:49'),
(605, 'UPDATE', 'usuarios', 5, '5', '2024-10-20 09:40:49'),
(606, 'UPDATE', 'usuarios', 6, '6', '2024-10-20 09:40:49'),
(607, 'UPDATE', 'usuarios', 7, '7', '2024-10-20 09:40:49'),
(608, 'UPDATE', 'usuarios', 8, '8', '2024-10-20 09:40:49'),
(609, 'UPDATE', 'usuarios', 9, '9', '2024-10-20 09:40:49'),
(610, 'UPDATE', 'usuarios', 10, '10', '2024-10-20 09:40:49'),
(611, 'UPDATE', 'usuarios', 11, '11', '2024-10-20 09:40:49'),
(612, 'UPDATE', 'usuarios', 11, '11', '2024-10-20 09:40:49'),
(613, 'UPDATE', 'usuarios', 16, '16', '2024-10-20 09:40:49'),
(614, 'UPDATE', 'usuarios', 18, '18', '2024-10-20 09:40:49'),
(615, 'UPDATE', 'usuarios', 19, '19', '2024-10-20 09:40:49'),
(616, 'UPDATE', 'usuarios', 19, '19', '2024-10-20 09:40:49'),
(617, 'UPDATE', 'usuarios', 20, '20', '2024-10-20 09:40:49'),
(618, 'UPDATE', 'usuarios', 20, '20', '2024-10-20 09:40:49'),
(619, 'UPDATE', 'usuarios', 20, '20', '2024-10-20 09:40:49'),
(620, 'UPDATE', 'usuarios', 21, '21', '2024-10-20 09:40:49'),
(621, 'UPDATE', 'usuarios', 26, '26', '2024-10-20 09:40:49'),
(622, 'UPDATE', 'usuarios', 27, '27', '2024-10-20 09:40:49'),
(623, 'UPDATE', 'usuarios', 28, '28', '2024-10-20 09:40:49'),
(624, 'UPDATE', 'usuarios', 29, '29', '2024-10-20 09:40:49'),
(625, 'UPDATE', 'usuarios', 30, '30', '2024-10-20 09:40:49'),
(626, 'UPDATE', 'usuarios', 31, '31', '2024-10-20 09:40:49'),
(627, 'UPDATE', 'usuarios', 33, '33', '2024-10-20 09:40:49'),
(628, 'UPDATE', 'usuarios', 34, '34', '2024-10-20 09:40:49'),
(629, 'UPDATE', 'usuarios', 35, '35', '2024-10-20 09:40:49'),
(630, 'UPDATE', 'usuarios', 36, '36', '2024-10-20 09:40:49'),
(631, 'UPDATE', 'usuarios', 37, '37', '2024-10-20 09:40:49'),
(632, 'UPDATE', 'usuarios', 38, '38', '2024-10-20 09:40:49'),
(633, 'UPDATE', 'usuarios', 39, '39', '2024-10-20 09:40:49'),
(634, 'INSERT', 'usuarios', 40, '40', '2024-10-20 09:42:58'),
(635, 'INSERT', 'usuarios', 40, '40', '2024-10-20 09:42:58'),
(636, 'INSERT', 'usuarios', 40, 'sistema', '2024-10-20 09:42:58'),
(637, 'INSERT', 'usuarios', 41, '41', '2024-10-20 09:56:31'),
(638, 'INSERT', 'usuarios', 41, '41', '2024-10-20 09:56:31'),
(639, 'INSERT', 'usuarios', 41, 'sistema', '2024-10-20 09:56:31'),
(640, 'INSERT', 'usuarios', 42, '42', '2024-10-20 10:10:40'),
(641, 'INSERT', 'usuarios', 42, '42', '2024-10-20 10:10:40'),
(642, 'INSERT', 'usuarios', 42, 'sistema', '2024-10-20 10:10:40'),
(643, 'INSERT', 'usuarios', 43, '43', '2024-10-20 10:14:01'),
(644, 'INSERT', 'usuarios', 43, '43', '2024-10-20 10:14:01'),
(645, 'INSERT', 'usuarios', 43, 'sistema', '2024-10-20 10:14:01'),
(646, 'UPDATE', 'usuarios', 43, '43', '2024-10-20 10:14:02'),
(647, 'INSERT', 'usuarios', 44, '44', '2024-10-20 10:20:19'),
(648, 'INSERT', 'usuarios', 44, '44', '2024-10-20 10:20:19'),
(649, 'INSERT', 'usuarios', 44, 'sistema', '2024-10-20 10:20:19'),
(650, 'UPDATE', 'usuarios', 44, '44', '2024-10-20 10:20:19'),
(651, 'INSERT', 'usuarios', 45, '45', '2024-10-20 10:47:20'),
(652, 'INSERT', 'usuarios', 45, '45', '2024-10-20 10:47:20'),
(653, 'INSERT', 'usuarios', 45, 'sistema', '2024-10-20 10:47:20'),
(654, 'UPDATE', 'usuarios', 45, '45', '2024-10-20 10:47:20'),
(655, 'INSERT', 'usuarios', 46, '46', '2024-10-20 10:56:38'),
(656, 'INSERT', 'usuarios', 46, '46', '2024-10-20 10:56:38'),
(657, 'INSERT', 'usuarios', 46, 'sistema', '2024-10-20 10:56:38'),
(658, 'UPDATE', 'usuarios', 46, '46', '2024-10-20 10:56:38'),
(659, 'INSERT', 'usuarios', 47, '47', '2024-10-20 11:10:56'),
(660, 'INSERT', 'usuarios', 47, '47', '2024-10-20 11:10:56'),
(661, 'INSERT', 'usuarios', 47, 'sistema', '2024-10-20 11:10:56'),
(662, 'UPDATE', 'usuarios', 47, '47', '2024-10-20 11:10:56'),
(663, 'INSERT', 'usuarios', 48, '48', '2024-10-20 11:17:56'),
(664, 'INSERT', 'usuarios', 48, '48', '2024-10-20 11:17:56'),
(665, 'INSERT', 'usuarios', 48, 'sistema', '2024-10-20 11:17:56'),
(666, 'UPDATE', 'usuarios', 48, '48', '2024-10-20 11:17:56'),
(667, 'INSERT', 'usuarios', 49, '49', '2024-10-20 11:24:30'),
(668, 'INSERT', 'usuarios', 49, '49', '2024-10-20 11:24:30'),
(669, 'INSERT', 'usuarios', 49, 'sistema', '2024-10-20 11:24:30'),
(670, 'UPDATE', 'usuarios', 49, '49', '2024-10-20 11:24:30'),
(671, 'INSERT', 'usuarios', 50, '50', '2024-10-20 11:35:17'),
(672, 'INSERT', 'usuarios', 50, '50', '2024-10-20 11:35:17'),
(673, 'INSERT', 'usuarios', 50, 'sistema', '2024-10-20 11:35:17'),
(674, 'UPDATE', 'usuarios', 50, '50', '2024-10-20 11:35:17'),
(675, 'INSERT', 'usuarios', 51, '51', '2024-10-20 11:49:59'),
(676, 'INSERT', 'usuarios', 51, '51', '2024-10-20 11:49:59'),
(677, 'INSERT', 'usuarios', 51, 'sistema', '2024-10-20 11:49:59'),
(678, 'UPDATE', 'usuarios', 51, '51', '2024-10-20 11:49:59'),
(679, 'INSERT', 'usuarios', 52, '52', '2024-10-20 11:51:53'),
(680, 'INSERT', 'usuarios', 52, '52', '2024-10-20 11:51:53'),
(681, 'INSERT', 'usuarios', 52, 'sistema', '2024-10-20 11:51:53'),
(682, 'UPDATE', 'usuarios', 52, '52', '2024-10-20 11:51:53'),
(683, 'INSERT', 'usuarios', 53, '53', '2024-10-20 12:02:41'),
(684, 'INSERT', 'usuarios', 53, '53', '2024-10-20 12:02:41'),
(685, 'INSERT', 'usuarios', 53, 'sistema', '2024-10-20 12:02:41'),
(686, 'UPDATE', 'usuarios', 53, '53', '2024-10-20 12:02:41'),
(687, 'INSERT', 'usuarios', 54, '54', '2024-10-20 13:29:53'),
(688, 'INSERT', 'usuarios', 54, '54', '2024-10-20 13:29:53'),
(689, 'INSERT', 'usuarios', 54, 'sistema', '2024-10-20 13:29:53'),
(690, 'UPDATE', 'usuarios', 54, '54', '2024-10-20 13:29:53'),
(691, 'INSERT', 'usuarios', 55, '55', '2024-10-20 13:33:15'),
(692, 'INSERT', 'usuarios', 55, '55', '2024-10-20 13:33:15'),
(693, 'INSERT', 'usuarios', 55, 'sistema', '2024-10-20 13:33:15'),
(694, 'INSERT', 'familias', 32, 'sistema', '2024-10-20 13:33:15'),
(695, 'INSERT', 'grupos', 15, 'Sistema', '2024-10-20 13:33:15'),
(696, 'INSERT', 'usuarios', 56, '56', '2024-10-20 13:38:38'),
(697, 'INSERT', 'usuarios', 56, '56', '2024-10-20 13:38:38'),
(698, 'INSERT', 'usuarios', 56, 'sistema', '2024-10-20 13:38:38'),
(699, 'UPDATE', 'usuarios', 56, '56', '2024-10-20 13:38:38'),
(700, 'INSERT', 'usuarios', 57, '57', '2024-10-20 13:45:49'),
(701, 'INSERT', 'usuarios', 57, '57', '2024-10-20 13:45:49'),
(702, 'INSERT', 'usuarios', 57, 'sistema', '2024-10-20 13:45:49'),
(703, 'UPDATE', 'usuarios', 57, '57', '2024-10-20 13:45:49'),
(704, 'INSERT', 'usuarios', 58, '58', '2024-10-20 13:55:11'),
(705, 'INSERT', 'usuarios', 58, '58', '2024-10-20 13:55:11'),
(706, 'INSERT', 'usuarios', 58, 'sistema', '2024-10-20 13:55:11'),
(707, 'UPDATE', 'usuarios', 58, '58', '2024-10-20 13:55:11'),
(708, 'INSERT', 'usuarios', 59, '59', '2024-10-20 13:58:07'),
(709, 'INSERT', 'usuarios', 59, '59', '2024-10-20 13:58:07'),
(710, 'INSERT', 'usuarios', 59, 'sistema', '2024-10-20 13:58:07'),
(711, 'INSERT', 'familias', 33, 'sistema', '2024-10-20 13:58:07'),
(712, 'INSERT', 'grupos', 16, 'Sistema', '2024-10-20 13:58:07'),
(713, 'INSERT', 'usuarios', 60, '60', '2024-10-20 14:03:29'),
(714, 'INSERT', 'usuarios', 60, '60', '2024-10-20 14:03:29'),
(715, 'INSERT', 'usuarios', 60, 'sistema', '2024-10-20 14:03:29'),
(716, 'INSERT', 'usuarios', 61, '61', '2024-10-20 14:05:27'),
(717, 'INSERT', 'usuarios', 61, '61', '2024-10-20 14:05:27'),
(718, 'INSERT', 'usuarios', 61, 'sistema', '2024-10-20 14:05:27'),
(719, 'INSERT', 'familias', 34, 'sistema', '2024-10-20 14:05:27'),
(720, 'INSERT', 'grupos', 17, 'Sistema', '2024-10-20 14:05:27'),
(721, 'INSERT', 'usuarios', 62, '62', '2024-10-20 14:08:36'),
(722, 'INSERT', 'usuarios', 62, '62', '2024-10-20 14:08:36'),
(723, 'INSERT', 'usuarios', 62, 'sistema', '2024-10-20 14:08:36'),
(724, 'UPDATE', 'usuarios', 62, '62', '2024-10-20 14:08:36'),
(725, 'INSERT', 'usuarios', 63, '63', '2024-10-20 14:55:14'),
(726, 'INSERT', 'usuarios', 63, '63', '2024-10-20 14:55:14'),
(727, 'INSERT', 'usuarios', 63, 'sistema', '2024-10-20 14:55:14'),
(728, 'INSERT', 'familias', 35, 'sistema', '2024-10-20 14:55:14'),
(729, 'INSERT', 'grupos', 18, 'Sistema', '2024-10-20 14:55:15'),
(730, 'INSERT', 'usuarios', 64, '64', '2024-10-20 15:27:05'),
(731, 'INSERT', 'usuarios', 64, '64', '2024-10-20 15:27:05'),
(732, 'INSERT', 'usuarios', 64, 'sistema', '2024-10-20 15:27:05'),
(733, 'INSERT', 'usuarios', 65, '65', '2024-10-20 15:28:56'),
(734, 'INSERT', 'usuarios', 65, '65', '2024-10-20 15:28:56'),
(735, 'INSERT', 'usuarios', 65, 'sistema', '2024-10-20 15:28:56'),
(736, 'INSERT', 'familias', 36, 'sistema', '2024-10-20 15:30:47'),
(737, 'INSERT', 'familias', 37, 'sistema', '2024-10-20 15:30:47'),
(738, 'INSERT', 'grupos', 19, 'Sistema', '2024-10-20 15:31:44'),
(739, 'INSERT', 'grupos', 20, 'Sistema', '2024-10-20 15:31:44'),
(740, 'INSERT', 'familias', 38, 'sistema', '2024-10-20 17:10:59'),
(741, 'INSERT', 'familias', 39, 'sistema', '2024-10-20 17:10:59'),
(742, 'INSERT', 'familias', 40, 'sistema', '2024-10-20 17:10:59'),
(743, 'INSERT', 'familias', 41, 'sistema', '2024-10-20 17:10:59'),
(744, 'INSERT', 'familias', 43, 'sistema', '2024-10-20 17:18:56'),
(745, 'INSERT', 'familias', 44, 'sistema', '2024-10-20 17:18:56'),
(746, 'INSERT', 'familias', 45, 'sistema', '2024-10-20 17:29:14'),
(747, 'INSERT', 'familias', 46, 'sistema', '2024-10-20 17:48:59'),
(748, 'INSERT', 'familias', 47, 'sistema', '2024-10-20 18:42:31'),
(749, 'INSERT', 'familias', 48, 'sistema', '2024-10-20 18:44:27'),
(750, 'INSERT', 'familias', 50, 'sistema', '2024-10-20 19:22:33'),
(751, 'INSERT', 'usuarios', 66, '66', '2024-10-20 19:26:49'),
(752, 'INSERT', 'usuarios', 66, '66', '2024-10-20 19:26:49'),
(753, 'INSERT', 'usuarios', 66, 'sistema', '2024-10-20 19:26:49'),
(754, 'INSERT', 'usuarios', 67, '67', '2024-10-20 19:28:40'),
(755, 'INSERT', 'usuarios', 67, '67', '2024-10-20 19:28:40'),
(756, 'INSERT', 'usuarios', 67, 'sistema', '2024-10-20 19:28:40'),
(757, 'UPDATE', 'usuarios', 67, '67', '2024-10-20 19:28:40'),
(758, 'INSERT', 'familias', 51, 'sistema', '2024-10-20 19:31:23'),
(759, 'INSERT', 'familias', 53, 'sistema', '2024-10-20 19:42:16'),
(760, 'INSERT', 'familias', 55, 'sistema', '2024-10-20 20:34:34'),
(761, 'INSERT', 'familias', 56, 'sistema', '2024-10-20 20:40:56'),
(762, 'INSERT', 'usuarios', 68, '68', '2024-10-20 21:07:01'),
(763, 'INSERT', 'usuarios', 68, '68', '2024-10-20 21:07:01'),
(764, 'INSERT', 'usuarios', 68, 'sistema', '2024-10-20 21:07:01'),
(765, 'INSERT', 'usuarios', 70, '70', '2024-10-20 21:08:55'),
(766, 'INSERT', 'usuarios', 70, '70', '2024-10-20 21:08:55'),
(767, 'INSERT', 'usuarios', 70, 'sistema', '2024-10-20 21:08:55'),
(768, 'INSERT', 'usuarios', 71, '71', '2024-10-20 21:23:39'),
(769, 'INSERT', 'usuarios', 71, '71', '2024-10-20 21:23:39'),
(770, 'INSERT', 'usuarios', 71, 'sistema', '2024-10-20 21:23:39'),
(771, 'UPDATE', 'familias', 1, 'sistema', '2024-10-20 21:23:50'),
(772, 'UPDATE', 'familias', 16, 'sistema', '2024-10-20 21:23:50'),
(773, 'UPDATE', 'familias', 22, 'sistema', '2024-10-20 21:23:50'),
(774, 'UPDATE', 'familias', 23, 'sistema', '2024-10-20 21:23:50'),
(775, 'UPDATE', 'familias', 24, 'sistema', '2024-10-20 21:23:50'),
(776, 'UPDATE', 'familias', 30, 'sistema', '2024-10-20 21:23:50'),
(777, 'UPDATE', 'familias', 31, 'sistema', '2024-10-20 21:23:50'),
(778, 'UPDATE', 'familias', 32, 'sistema', '2024-10-20 21:23:50'),
(779, 'UPDATE', 'familias', 33, 'sistema', '2024-10-20 21:23:50'),
(780, 'UPDATE', 'familias', 34, 'sistema', '2024-10-20 21:23:50'),
(781, 'UPDATE', 'familias', 35, 'sistema', '2024-10-20 21:23:50'),
(782, 'UPDATE', 'familias', 36, 'sistema', '2024-10-20 21:23:50'),
(783, 'UPDATE', 'familias', 37, 'sistema', '2024-10-20 21:23:50'),
(784, 'UPDATE', 'familias', 38, 'sistema', '2024-10-20 21:23:50'),
(785, 'UPDATE', 'familias', 39, 'sistema', '2024-10-20 21:23:50'),
(786, 'UPDATE', 'familias', 40, 'sistema', '2024-10-20 21:23:50'),
(787, 'UPDATE', 'familias', 41, 'sistema', '2024-10-20 21:23:50'),
(788, 'UPDATE', 'familias', 43, 'sistema', '2024-10-20 21:23:50'),
(789, 'UPDATE', 'familias', 44, 'sistema', '2024-10-20 21:23:50'),
(790, 'UPDATE', 'familias', 45, 'sistema', '2024-10-20 21:23:50'),
(791, 'UPDATE', 'familias', 46, 'sistema', '2024-10-20 21:23:50'),
(792, 'UPDATE', 'familias', 47, 'sistema', '2024-10-20 21:23:50'),
(793, 'UPDATE', 'familias', 48, 'sistema', '2024-10-20 21:23:50'),
(794, 'UPDATE', 'familias', 50, 'sistema', '2024-10-20 21:23:50'),
(795, 'UPDATE', 'familias', 51, 'sistema', '2024-10-20 21:23:50'),
(796, 'UPDATE', 'familias', 53, 'sistema', '2024-10-20 21:23:50'),
(797, 'UPDATE', 'familias', 55, 'sistema', '2024-10-20 21:23:50'),
(798, 'UPDATE', 'familias', 56, 'sistema', '2024-10-20 21:23:50'),
(799, 'UPDATE', 'grupos', 1, 'Sistema', '2024-10-20 21:23:50'),
(800, 'UPDATE', 'grupos', 9, 'Sistema', '2024-10-20 21:23:50'),
(801, 'UPDATE', 'grupos', 11, 'Sistema', '2024-10-20 21:23:50'),
(802, 'UPDATE', 'grupos', 14, 'Sistema', '2024-10-20 21:23:50'),
(803, 'UPDATE', 'grupos', 15, 'Sistema', '2024-10-20 21:23:50'),
(804, 'UPDATE', 'grupos', 16, 'Sistema', '2024-10-20 21:23:50'),
(805, 'UPDATE', 'grupos', 17, 'Sistema', '2024-10-20 21:23:50'),
(806, 'UPDATE', 'grupos', 18, 'Sistema', '2024-10-20 21:23:50'),
(807, 'UPDATE', 'grupos', 19, 'Sistema', '2024-10-20 21:23:50'),
(808, 'UPDATE', 'grupos', 20, 'Sistema', '2024-10-20 21:23:50'),
(809, 'UPDATE', 'usuarios', 1, '1', '2024-10-20 21:23:50'),
(810, 'UPDATE', 'usuarios', 2, '2', '2024-10-20 21:23:50'),
(811, 'UPDATE', 'usuarios', 5, '5', '2024-10-20 21:23:50'),
(812, 'UPDATE', 'usuarios', 6, '6', '2024-10-20 21:23:50'),
(813, 'UPDATE', 'usuarios', 7, '7', '2024-10-20 21:23:50'),
(814, 'UPDATE', 'usuarios', 8, '8', '2024-10-20 21:23:50'),
(815, 'UPDATE', 'usuarios', 9, '9', '2024-10-20 21:23:50'),
(816, 'UPDATE', 'usuarios', 10, '10', '2024-10-20 21:23:50'),
(817, 'UPDATE', 'usuarios', 11, '11', '2024-10-20 21:23:50'),
(818, 'UPDATE', 'usuarios', 11, '11', '2024-10-20 21:23:50'),
(819, 'UPDATE', 'usuarios', 16, '16', '2024-10-20 21:23:50'),
(820, 'UPDATE', 'usuarios', 18, '18', '2024-10-20 21:23:50'),
(821, 'UPDATE', 'usuarios', 19, '19', '2024-10-20 21:23:50'),
(822, 'UPDATE', 'usuarios', 19, '19', '2024-10-20 21:23:50'),
(823, 'UPDATE', 'usuarios', 20, '20', '2024-10-20 21:23:50'),
(824, 'UPDATE', 'usuarios', 20, '20', '2024-10-20 21:23:50');
INSERT INTO `auditoria` (`idAuditoria`, `accion`, `tabla_afectada`, `idRegistro`, `usuario`, `fecha`) VALUES
(825, 'UPDATE', 'usuarios', 20, '20', '2024-10-20 21:23:50'),
(826, 'UPDATE', 'usuarios', 21, '21', '2024-10-20 21:23:50'),
(827, 'UPDATE', 'usuarios', 26, '26', '2024-10-20 21:23:50'),
(828, 'UPDATE', 'usuarios', 27, '27', '2024-10-20 21:23:50'),
(829, 'UPDATE', 'usuarios', 27, '27', '2024-10-20 21:23:50'),
(830, 'UPDATE', 'usuarios', 28, '28', '2024-10-20 21:23:50'),
(831, 'UPDATE', 'usuarios', 29, '29', '2024-10-20 21:23:50'),
(832, 'UPDATE', 'usuarios', 30, '30', '2024-10-20 21:23:50'),
(833, 'UPDATE', 'usuarios', 31, '31', '2024-10-20 21:23:50'),
(834, 'UPDATE', 'usuarios', 33, '33', '2024-10-20 21:23:50'),
(835, 'UPDATE', 'usuarios', 34, '34', '2024-10-20 21:23:50'),
(836, 'UPDATE', 'usuarios', 35, '35', '2024-10-20 21:23:50'),
(837, 'UPDATE', 'usuarios', 36, '36', '2024-10-20 21:23:50'),
(838, 'UPDATE', 'usuarios', 37, '37', '2024-10-20 21:23:50'),
(839, 'UPDATE', 'usuarios', 38, '38', '2024-10-20 21:23:50'),
(840, 'UPDATE', 'usuarios', 39, '39', '2024-10-20 21:23:50'),
(841, 'UPDATE', 'usuarios', 40, '40', '2024-10-20 21:23:50'),
(842, 'UPDATE', 'usuarios', 41, '41', '2024-10-20 21:23:50'),
(843, 'UPDATE', 'usuarios', 41, '41', '2024-10-20 21:23:50'),
(844, 'UPDATE', 'usuarios', 42, '42', '2024-10-20 21:23:50'),
(845, 'UPDATE', 'usuarios', 43, '43', '2024-10-20 21:23:50'),
(846, 'UPDATE', 'usuarios', 44, '44', '2024-10-20 21:23:50'),
(847, 'UPDATE', 'usuarios', 45, '45', '2024-10-20 21:23:50'),
(848, 'UPDATE', 'usuarios', 46, '46', '2024-10-20 21:23:50'),
(849, 'UPDATE', 'usuarios', 47, '47', '2024-10-20 21:23:50'),
(850, 'UPDATE', 'usuarios', 48, '48', '2024-10-20 21:23:50'),
(851, 'UPDATE', 'usuarios', 49, '49', '2024-10-20 21:23:50'),
(852, 'UPDATE', 'usuarios', 50, '50', '2024-10-20 21:23:50'),
(853, 'UPDATE', 'usuarios', 51, '51', '2024-10-20 21:23:50'),
(854, 'UPDATE', 'usuarios', 52, '52', '2024-10-20 21:23:50'),
(855, 'UPDATE', 'usuarios', 53, '53', '2024-10-20 21:23:50'),
(856, 'UPDATE', 'usuarios', 54, '54', '2024-10-20 21:23:50'),
(857, 'UPDATE', 'usuarios', 55, '55', '2024-10-20 21:23:50'),
(858, 'UPDATE', 'usuarios', 56, '56', '2024-10-20 21:23:50'),
(859, 'UPDATE', 'usuarios', 57, '57', '2024-10-20 21:23:50'),
(860, 'UPDATE', 'usuarios', 58, '58', '2024-10-20 21:23:50'),
(861, 'UPDATE', 'usuarios', 59, '59', '2024-10-20 21:23:50'),
(862, 'UPDATE', 'usuarios', 60, '60', '2024-10-20 21:23:50'),
(863, 'UPDATE', 'usuarios', 61, '61', '2024-10-20 21:23:50'),
(864, 'UPDATE', 'usuarios', 61, '61', '2024-10-20 21:23:50'),
(865, 'UPDATE', 'usuarios', 62, '62', '2024-10-20 21:23:50'),
(866, 'UPDATE', 'usuarios', 63, '63', '2024-10-20 21:23:50'),
(867, 'UPDATE', 'usuarios', 63, '63', '2024-10-20 21:23:50'),
(868, 'UPDATE', 'usuarios', 63, '63', '2024-10-20 21:23:50'),
(869, 'UPDATE', 'usuarios', 63, '63', '2024-10-20 21:23:50'),
(870, 'UPDATE', 'usuarios', 64, '64', '2024-10-20 21:23:50'),
(871, 'UPDATE', 'usuarios', 65, '65', '2024-10-20 21:23:50'),
(872, 'UPDATE', 'usuarios', 65, '65', '2024-10-20 21:23:50'),
(873, 'UPDATE', 'usuarios', 65, '65', '2024-10-20 21:23:50'),
(874, 'UPDATE', 'usuarios', 65, '65', '2024-10-20 21:23:50'),
(875, 'UPDATE', 'usuarios', 65, '65', '2024-10-20 21:23:50'),
(876, 'UPDATE', 'usuarios', 65, '65', '2024-10-20 21:23:50'),
(877, 'UPDATE', 'usuarios', 65, '65', '2024-10-20 21:23:50'),
(878, 'UPDATE', 'usuarios', 65, '65', '2024-10-20 21:23:50'),
(879, 'UPDATE', 'usuarios', 65, '65', '2024-10-20 21:23:50'),
(880, 'UPDATE', 'usuarios', 65, '65', '2024-10-20 21:23:50'),
(881, 'UPDATE', 'usuarios', 65, '65', '2024-10-20 21:23:50'),
(882, 'UPDATE', 'usuarios', 65, '65', '2024-10-20 21:23:50'),
(883, 'UPDATE', 'usuarios', 65, '65', '2024-10-20 21:23:50'),
(884, 'UPDATE', 'usuarios', 65, '65', '2024-10-20 21:23:50'),
(885, 'UPDATE', 'usuarios', 65, '65', '2024-10-20 21:23:50'),
(886, 'UPDATE', 'usuarios', 65, '65', '2024-10-20 21:23:50'),
(887, 'UPDATE', 'usuarios', 66, '66', '2024-10-20 21:23:50'),
(888, 'UPDATE', 'usuarios', 67, '67', '2024-10-20 21:23:50'),
(889, 'UPDATE', 'usuarios', 68, '68', '2024-10-20 21:23:50'),
(890, 'UPDATE', 'usuarios', 70, '70', '2024-10-20 21:23:50'),
(891, 'UPDATE', 'usuarios', 71, '71', '2024-10-20 21:23:50'),
(892, 'INSERT', 'usuarios', 72, '72', '2024-10-20 21:25:22'),
(893, 'INSERT', 'usuarios', 72, '72', '2024-10-20 21:25:22'),
(894, 'INSERT', 'usuarios', 72, 'sistema', '2024-10-20 21:25:22'),
(895, 'INSERT', 'familias', 57, 'sistema', '2024-10-20 21:26:47'),
(896, 'DELETE', 'usuarios', 71, '71', '2024-10-20 21:34:55'),
(897, 'DELETE', 'usuarios', 68, '68', '2024-10-20 21:35:09'),
(898, 'INSERT', 'usuarios', 73, '73', '2024-10-21 07:42:36'),
(899, 'INSERT', 'usuarios', 73, '73', '2024-10-21 07:42:36'),
(900, 'INSERT', 'usuarios', 73, 'sistema', '2024-10-21 07:42:36'),
(901, 'UPDATE', 'usuarios', 73, '73', '2024-10-21 07:42:36'),
(902, 'UPDATE', 'usuarios', 73, '73', '2024-10-21 07:51:19'),
(903, 'INSERT', 'familias', 58, 'sistema', '2024-10-21 07:51:59'),
(904, 'INSERT', 'grupos', 21, 'Sistema', '2024-10-21 07:54:35'),
(905, 'INSERT', 'usuarios', 74, '74', '2024-10-21 08:02:32'),
(906, 'INSERT', 'usuarios', 74, '74', '2024-10-21 08:02:32'),
(907, 'INSERT', 'usuarios', 74, 'sistema', '2024-10-21 08:02:32'),
(908, 'INSERT', 'familias', 59, 'sistema', '2024-10-21 08:02:32'),
(909, 'INSERT', 'grupos', 22, 'Sistema', '2024-10-21 08:02:32'),
(910, 'INSERT', 'usuarios', 75, '75', '2024-10-21 09:03:38'),
(911, 'INSERT', 'usuarios', 75, '75', '2024-10-21 09:03:38'),
(912, 'INSERT', 'usuarios', 75, 'sistema', '2024-10-21 09:03:38'),
(913, 'INSERT', 'familias', 60, 'sistema', '2024-10-21 09:03:38'),
(914, 'INSERT', 'grupos', 23, 'Sistema', '2024-10-21 09:03:39'),
(915, 'INSERT', 'usuarios', 76, '76', '2024-10-21 10:45:37'),
(916, 'INSERT', 'usuarios', 76, '76', '2024-10-21 10:45:37'),
(917, 'INSERT', 'usuarios', 76, 'sistema', '2024-10-21 10:45:37'),
(918, 'UPDATE', 'usuarios', 76, '76', '2024-10-21 10:45:37'),
(919, 'INSERT', 'usuarios', 77, '77', '2024-10-21 10:51:30'),
(920, 'INSERT', 'usuarios', 77, '77', '2024-10-21 10:51:30'),
(921, 'INSERT', 'usuarios', 77, 'sistema', '2024-10-21 10:51:30'),
(922, 'UPDATE', 'usuarios', 77, '77', '2024-10-21 10:51:30'),
(923, 'INSERT', 'usuarios', 78, '78', '2024-10-21 11:20:39'),
(924, 'INSERT', 'usuarios', 78, '78', '2024-10-21 11:20:39'),
(925, 'INSERT', 'usuarios', 78, 'sistema', '2024-10-21 11:20:39'),
(926, 'UPDATE', 'usuarios', 78, '78', '2024-10-21 11:20:39'),
(927, 'INSERT', 'usuarios', 79, '79', '2024-10-21 11:42:34'),
(928, 'INSERT', 'usuarios', 79, '79', '2024-10-21 11:42:34'),
(929, 'INSERT', 'usuarios', 79, 'sistema', '2024-10-21 11:42:34'),
(930, 'UPDATE', 'usuarios', 79, '79', '2024-10-21 11:42:34'),
(931, 'INSERT', 'usuarios', 80, '80', '2024-10-21 12:07:21'),
(932, 'INSERT', 'usuarios', 80, '80', '2024-10-21 12:07:21'),
(933, 'INSERT', 'usuarios', 80, 'sistema', '2024-10-21 12:07:21'),
(934, 'UPDATE', 'usuarios', 80, '80', '2024-10-21 12:07:21'),
(935, 'INSERT', 'usuarios', 81, '81', '2024-10-21 12:18:43'),
(936, 'INSERT', 'usuarios', 81, '81', '2024-10-21 12:18:43'),
(937, 'INSERT', 'usuarios', 81, 'sistema', '2024-10-21 12:18:43'),
(938, 'UPDATE', 'usuarios', 81, '81', '2024-10-21 12:18:43'),
(939, 'INSERT', 'usuarios', 82, '82', '2024-10-21 12:36:35'),
(940, 'INSERT', 'usuarios', 82, '82', '2024-10-21 12:36:35'),
(941, 'INSERT', 'usuarios', 82, 'sistema', '2024-10-21 12:36:35'),
(942, 'UPDATE', 'usuarios', 82, '82', '2024-10-21 12:36:35'),
(943, 'INSERT', 'usuarios', 83, '83', '2024-10-21 14:10:04'),
(944, 'INSERT', 'usuarios', 83, '83', '2024-10-21 14:10:04'),
(945, 'INSERT', 'usuarios', 83, 'sistema', '2024-10-21 14:10:04'),
(946, 'UPDATE', 'usuarios', 83, '83', '2024-10-21 14:10:04'),
(947, 'INSERT', 'usuarios', 84, '84', '2024-10-21 14:15:26'),
(948, 'INSERT', 'usuarios', 84, '84', '2024-10-21 14:15:26'),
(949, 'INSERT', 'usuarios', 84, 'sistema', '2024-10-21 14:15:26'),
(950, 'UPDATE', 'usuarios', 84, '84', '2024-10-21 14:15:26'),
(951, 'INSERT', 'usuarios', 85, '85', '2024-10-21 15:27:11'),
(952, 'INSERT', 'usuarios', 85, '85', '2024-10-21 15:27:11'),
(953, 'INSERT', 'usuarios', 85, 'sistema', '2024-10-21 15:27:11'),
(954, 'INSERT', 'usuarios', 86, '86', '2024-10-21 15:42:33'),
(955, 'INSERT', 'usuarios', 86, '86', '2024-10-21 15:42:33'),
(956, 'INSERT', 'usuarios', 86, 'sistema', '2024-10-21 15:42:33'),
(957, 'UPDATE', 'usuarios', 86, '86', '2024-10-21 15:42:33'),
(958, 'DELETE', 'usuarios', 83, '83', '2024-10-21 15:45:25'),
(959, 'DELETE', 'usuarios', 84, '84', '2024-10-21 15:45:33'),
(960, 'INSERT', 'familias', 61, 'sistema', '2024-10-21 15:47:50'),
(961, 'INSERT', 'grupos', 24, 'Sistema', '2024-10-21 15:49:11'),
(962, 'INSERT', 'usuarios', 87, '87', '2024-10-21 15:59:34'),
(963, 'INSERT', 'usuarios', 87, '87', '2024-10-21 15:59:34'),
(964, 'INSERT', 'usuarios', 87, 'sistema', '2024-10-21 15:59:34'),
(965, 'INSERT', 'familias', 62, 'sistema', '2024-10-21 15:59:34'),
(966, 'INSERT', 'grupos', 25, 'Sistema', '2024-10-21 15:59:34'),
(967, 'INSERT', 'usuarios', 88, '88', '2024-10-21 18:07:12'),
(968, 'INSERT', 'usuarios', 88, '88', '2024-10-21 18:07:12'),
(969, 'INSERT', 'usuarios', 88, 'sistema', '2024-10-21 18:07:12'),
(970, 'UPDATE', 'usuarios', 88, '88', '2024-10-21 18:07:12'),
(971, 'INSERT', 'usuarios', 89, '89', '2024-10-21 18:15:54'),
(972, 'INSERT', 'usuarios', 89, '89', '2024-10-21 18:15:54'),
(973, 'INSERT', 'usuarios', 89, 'sistema', '2024-10-21 18:15:54'),
(974, 'INSERT', 'familias', 63, 'sistema', '2024-10-21 18:15:54'),
(975, 'INSERT', 'grupos', 26, 'Sistema', '2024-10-21 18:15:54'),
(976, 'UPDATE', 'usuarios', 89, '89', '2024-10-21 18:15:54'),
(977, 'INSERT', 'usuarios', 90, '90', '2024-10-21 18:21:43'),
(978, 'INSERT', 'usuarios', 90, '90', '2024-10-21 18:21:43'),
(979, 'INSERT', 'usuarios', 90, 'sistema', '2024-10-21 18:21:43'),
(980, 'UPDATE', 'usuarios', 90, '90', '2024-10-21 18:21:43'),
(981, 'INSERT', 'usuarios', 91, '91', '2024-10-21 18:24:58'),
(982, 'INSERT', 'usuarios', 91, '91', '2024-10-21 18:24:58'),
(983, 'INSERT', 'usuarios', 91, 'sistema', '2024-10-21 18:24:58'),
(984, 'UPDATE', 'usuarios', 91, '91', '2024-10-21 18:24:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria_accesos`
--

CREATE TABLE `auditoria_accesos` (
  `idAcceso` int(11) NOT NULL,
  `idUser` int(11) NOT NULL,
  `accion` varchar(255) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `auditoria_accesos`
--

INSERT INTO `auditoria_accesos` (`idAcceso`, `idUser`, `accion`, `fecha`) VALUES
(1, 2, 'logout', '2024-10-07 13:34:25'),
(2, 2, 'login', '2024-10-07 13:34:32'),
(3, 2, 'login', '2024-10-08 17:01:30'),
(4, 2, 'logout', '2024-10-08 17:21:03'),
(5, 2, 'login', '2024-10-08 17:21:08'),
(6, 2, 'login', '2024-10-08 17:48:00'),
(7, 2, 'logout', '2024-10-08 17:48:10'),
(10, 6, 'login', '2024-10-08 17:48:25'),
(11, 6, 'logout', '2024-10-08 17:48:28'),
(12, 1, 'login', '2024-10-08 17:48:35'),
(13, 1, 'logout', '2024-10-08 17:48:53'),
(14, 2, 'login', '2024-10-08 17:48:58'),
(15, 2, 'logout', '2024-10-08 17:58:48'),
(16, 2, 'login', '2024-10-08 17:58:52'),
(17, 2, 'logout', '2024-10-08 18:06:37'),
(18, 2, 'login', '2024-10-08 18:06:41'),
(19, 2, 'logout', '2024-10-08 18:09:06'),
(20, 2, 'login', '2024-10-08 18:09:15'),
(21, 2, 'logout', '2024-10-08 18:09:39'),
(22, 2, 'login', '2024-10-08 18:10:55'),
(23, 2, 'logout', '2024-10-08 19:32:55'),
(24, 1, 'login', '2024-10-08 19:33:01'),
(25, 2, 'login', '2024-10-08 20:21:45'),
(26, 2, 'logout', '2024-10-08 20:22:21'),
(27, 2, 'login', '2024-10-08 20:22:30'),
(28, 2, 'logout', '2024-10-08 20:52:25'),
(29, 1, 'login', '2024-10-08 20:52:32'),
(30, 1, 'logout', '2024-10-08 20:55:02'),
(31, 2, 'login', '2024-10-08 20:55:08'),
(32, 2, 'logout', '2024-10-08 21:40:22'),
(33, 2, 'login', '2024-10-08 21:40:27'),
(34, 2, 'logout', '2024-10-08 21:54:40'),
(35, 1, 'login', '2024-10-08 22:22:02'),
(36, 1, 'logout', '2024-10-08 23:41:19'),
(37, 1, 'login', '2024-10-08 23:41:26'),
(38, 1, 'logout', '2024-10-08 23:44:37'),
(39, 1, 'login', '2024-10-08 23:44:43'),
(40, 1, 'logout', '2024-10-08 23:48:51'),
(41, 1, 'login', '2024-10-08 23:48:56'),
(42, 1, 'logout', '2024-10-08 23:54:04'),
(43, 2, 'login', '2024-10-08 23:54:10'),
(44, 2, 'logout', '2024-10-08 23:55:13'),
(45, 1, 'login', '2024-10-10 14:05:42'),
(46, 1, 'logout', '2024-10-10 14:49:07'),
(47, 1, 'login', '2024-10-10 14:49:13'),
(48, 1, 'logout', '2024-10-10 15:04:48'),
(49, 1, 'login', '2024-10-10 15:04:53'),
(50, 1, 'logout', '2024-10-10 17:12:07'),
(51, 1, 'login', '2024-10-10 17:12:13'),
(52, 1, 'logout', '2024-10-10 17:27:59'),
(53, 1, 'login', '2024-10-10 17:28:05'),
(54, 1, 'logout', '2024-10-10 17:37:25'),
(55, 1, 'login', '2024-10-10 17:37:32'),
(56, 1, 'logout', '2024-10-10 18:12:34'),
(57, 1, 'login', '2024-10-10 18:12:40'),
(58, 1, 'login', '2024-10-11 06:55:13'),
(59, 1, 'logout', '2024-10-11 07:24:50'),
(60, 1, 'login', '2024-10-11 07:24:57'),
(61, 1, 'logout', '2024-10-11 07:59:49'),
(62, 2, 'login', '2024-10-11 07:59:55'),
(63, 2, 'logout', '2024-10-11 08:00:08'),
(64, 1, 'login', '2024-10-11 08:00:13'),
(65, 1, 'logout', '2024-10-11 08:38:34'),
(66, 1, 'login', '2024-10-11 08:38:40'),
(67, 1, 'logout', '2024-10-11 11:01:46'),
(68, 1, 'login', '2024-10-11 11:01:52'),
(69, 1, 'logout', '2024-10-11 11:23:43'),
(70, 1, 'login', '2024-10-11 11:23:50'),
(71, 1, 'logout', '2024-10-11 11:28:36'),
(72, 1, 'login', '2024-10-11 11:28:42'),
(73, 1, 'logout', '2024-10-11 11:33:47'),
(74, 1, 'login', '2024-10-11 11:33:56'),
(75, 1, 'logout', '2024-10-11 12:15:48'),
(76, 1, 'login', '2024-10-11 12:15:56'),
(77, 1, 'logout', '2024-10-11 12:29:19'),
(78, 1, 'login', '2024-10-11 12:29:25'),
(79, 1, 'logout', '2024-10-11 12:30:17'),
(80, 1, 'login', '2024-10-11 12:31:47'),
(81, 1, 'logout', '2024-10-11 13:03:23'),
(82, 1, 'login', '2024-10-11 13:03:31'),
(83, 1, 'logout', '2024-10-11 13:23:22'),
(84, 1, 'login', '2024-10-11 13:23:33'),
(85, 1, 'logout', '2024-10-11 13:26:38'),
(86, 1, 'login', '2024-10-11 13:26:47'),
(87, 1, 'logout', '2024-10-11 13:42:52'),
(88, 1, 'login', '2024-10-11 13:42:59'),
(89, 1, 'login', '2024-10-11 15:05:22'),
(90, 1, 'logout', '2024-10-11 15:06:04'),
(91, 1, 'login', '2024-10-11 15:06:22'),
(92, 1, 'logout', '2024-10-11 17:54:32'),
(93, 1, 'login', '2024-10-11 17:56:39'),
(94, 1, 'logout', '2024-10-11 17:59:43'),
(95, 1, 'login', '2024-10-11 17:59:54'),
(96, 1, 'logout', '2024-10-11 18:04:53'),
(97, 1, 'login', '2024-10-11 18:05:01'),
(98, 1, 'logout', '2024-10-11 18:08:16'),
(99, 1, 'login', '2024-10-11 18:08:24'),
(100, 1, 'logout', '2024-10-11 18:16:07'),
(101, 1, 'login', '2024-10-11 18:16:13'),
(102, 1, 'login', '2024-10-11 18:46:16'),
(103, 1, 'login', '2024-10-12 11:10:12'),
(104, 1, 'logout', '2024-10-12 11:23:57'),
(105, 1, 'login', '2024-10-12 11:24:04'),
(106, 1, 'logout', '2024-10-12 11:52:57'),
(107, 1, 'login', '2024-10-12 11:53:04'),
(108, 1, 'logout', '2024-10-12 11:57:39'),
(109, 1, 'login', '2024-10-12 11:57:59'),
(110, 1, 'logout', '2024-10-12 12:17:26'),
(111, 1, 'login', '2024-10-12 12:17:36'),
(112, 1, 'logout', '2024-10-12 12:51:06'),
(113, 1, 'login', '2024-10-12 12:51:13'),
(114, 1, 'logout', '2024-10-12 14:21:15'),
(115, 1, 'login', '2024-10-12 14:21:23'),
(116, 1, 'logout', '2024-10-12 14:36:05'),
(117, 1, 'login', '2024-10-12 14:36:19'),
(118, 1, 'logout', '2024-10-12 14:45:40'),
(119, 1, 'login', '2024-10-12 14:45:59'),
(120, 1, 'logout', '2024-10-12 14:47:46'),
(121, 1, 'login', '2024-10-12 14:48:02'),
(122, 1, 'logout', '2024-10-12 14:50:23'),
(123, 1, 'login', '2024-10-12 14:50:31'),
(124, 1, 'logout', '2024-10-12 14:57:32'),
(125, 1, 'login', '2024-10-12 14:57:44'),
(126, 1, 'logout', '2024-10-12 16:09:52'),
(127, 1, 'login', '2024-10-12 16:10:07'),
(128, 1, 'logout', '2024-10-12 16:15:26'),
(129, 1, 'login', '2024-10-12 16:15:38'),
(130, 1, 'logout', '2024-10-12 16:18:52'),
(131, 1, 'login', '2024-10-12 16:18:59'),
(132, 1, 'logout', '2024-10-12 16:40:59'),
(133, 1, 'login', '2024-10-12 16:41:04'),
(134, 1, 'logout', '2024-10-12 16:53:12'),
(135, 2, 'login', '2024-10-12 17:10:21'),
(136, 2, 'logout', '2024-10-12 17:13:02'),
(137, 6, 'login', '2024-10-12 17:14:15'),
(138, 6, 'logout', '2024-10-12 17:14:37'),
(139, 2, 'login', '2024-10-12 17:14:56'),
(140, 2, 'logout', '2024-10-12 17:14:58'),
(141, 1, 'login', '2024-10-12 17:34:50'),
(142, 1, 'logout', '2024-10-12 17:34:53'),
(143, 18, 'login', '2024-10-12 17:43:19'),
(144, 18, 'login', '2024-10-12 18:07:41'),
(145, 18, 'logout', '2024-10-12 18:08:20'),
(146, 19, 'login', '2024-10-12 18:29:19'),
(147, 2, 'login', '2024-10-12 19:09:21'),
(148, 2, 'logout', '2024-10-12 19:09:27'),
(149, 9, 'login', '2024-10-12 19:10:10'),
(150, 9, 'logout', '2024-10-12 19:11:34'),
(151, 1, 'login', '2024-10-12 19:33:59'),
(152, 1, 'logout', '2024-10-12 19:34:02'),
(153, 1, 'login', '2024-10-13 07:59:33'),
(154, 1, 'logout', '2024-10-13 08:08:13'),
(155, 1, 'login', '2024-10-13 08:09:59'),
(156, 1, 'login', '2024-10-13 08:28:50'),
(157, 1, 'logout', '2024-10-13 08:29:31'),
(158, 1, 'login', '2024-10-13 08:30:26'),
(159, 1, 'logout', '2024-10-13 08:30:31'),
(160, 1, 'login', '2024-10-13 08:31:21'),
(161, 1, 'logout', '2024-10-13 08:32:19'),
(162, 20, 'login', '2024-10-13 08:32:28'),
(163, 20, 'logout', '2024-10-13 08:33:37'),
(164, 21, 'login', '2024-10-13 08:35:13'),
(165, 21, 'logout', '2024-10-13 08:35:24'),
(166, 1, 'login', '2024-10-13 08:38:28'),
(167, 1, 'logout', '2024-10-13 08:59:46'),
(168, 1, 'login', '2024-10-13 13:59:38'),
(169, 1, 'logout', '2024-10-13 13:59:46'),
(170, 1, 'login', '2024-10-13 14:01:47'),
(171, 1, 'logout', '2024-10-13 14:03:19'),
(172, 1, 'login', '2024-10-13 14:06:10'),
(173, 1, 'logout', '2024-10-13 14:06:16'),
(174, 2, 'login', '2024-10-13 14:06:21'),
(175, 2, 'logout', '2024-10-13 14:06:30'),
(176, 1, 'login', '2024-10-13 14:06:35'),
(177, 1, 'logout', '2024-10-13 14:13:33'),
(178, 2, 'login', '2024-10-13 14:36:09'),
(179, 2, 'logout', '2024-10-13 14:36:12'),
(184, 1, 'login', '2024-10-13 14:39:53'),
(185, 1, 'logout', '2024-10-13 14:41:01'),
(191, 1, 'login', '2024-10-13 16:00:01'),
(192, 1, 'login', '2024-10-13 16:02:21'),
(193, 1, 'logout', '2024-10-13 17:03:43'),
(197, 26, 'login', '2024-10-13 17:41:27'),
(198, 26, 'login', '2024-10-13 18:16:16'),
(199, 26, 'logout', '2024-10-13 18:17:18'),
(200, 1, 'login', '2024-10-13 18:17:29'),
(201, 27, 'login', '2024-10-13 18:29:18'),
(202, 27, 'logout', '2024-10-13 18:32:05'),
(203, 27, 'login', '2024-10-13 18:32:20'),
(204, 27, 'logout', '2024-10-13 18:48:08'),
(205, 27, 'login', '2024-10-13 18:48:21'),
(206, 27, 'logout', '2024-10-13 18:56:53'),
(207, 27, 'login', '2024-10-13 18:57:15'),
(208, 27, 'logout', '2024-10-13 19:03:00'),
(209, 27, 'login', '2024-10-13 19:06:11'),
(210, 27, 'logout', '2024-10-13 19:12:53'),
(211, 27, 'login', '2024-10-13 19:12:59'),
(212, 27, 'logout', '2024-10-13 19:25:56'),
(213, 27, 'login', '2024-10-13 19:26:12'),
(214, 27, 'logout', '2024-10-13 19:39:40'),
(215, 27, 'login', '2024-10-13 19:39:48'),
(216, 27, 'logout', '2024-10-13 19:49:35'),
(217, 27, 'login', '2024-10-13 19:49:41'),
(218, 27, 'logout', '2024-10-13 20:07:11'),
(219, 27, 'login', '2024-10-13 20:09:24'),
(220, 27, 'logout', '2024-10-13 20:18:27'),
(221, 27, 'login', '2024-10-13 20:18:34'),
(222, 27, 'login', '2024-10-13 20:33:07'),
(223, 27, 'logout', '2024-10-13 20:38:50'),
(224, 27, 'login', '2024-10-13 20:40:13'),
(225, 27, 'logout', '2024-10-13 20:48:58'),
(226, 27, 'login', '2024-10-13 20:49:03'),
(227, 2, 'login', '2024-10-13 22:29:47'),
(228, 2, 'logout', '2024-10-13 22:30:20'),
(229, 27, 'login', '2024-10-14 16:25:52'),
(230, 27, 'logout', '2024-10-14 16:26:40'),
(231, 27, 'login', '2024-10-14 16:33:06'),
(232, 27, 'logout', '2024-10-14 16:33:14'),
(233, 27, 'login', '2024-10-14 16:33:36'),
(234, 27, 'logout', '2024-10-14 16:33:59'),
(235, 27, 'login', '2024-10-14 16:34:32'),
(236, 27, 'logout', '2024-10-14 16:34:36'),
(237, 27, 'login', '2024-10-14 17:11:01'),
(238, 27, 'logout', '2024-10-14 17:11:06'),
(239, 27, 'login', '2024-10-14 17:15:01'),
(240, 27, 'logout', '2024-10-14 17:18:08'),
(242, 27, 'login', '2024-10-14 17:23:21'),
(243, 27, 'logout', '2024-10-14 17:23:25'),
(244, 31, 'login', '2024-10-14 18:15:37'),
(245, 31, 'logout', '2024-10-14 18:21:21'),
(246, 27, 'login', '2024-10-14 18:21:29'),
(247, 31, 'login', '2024-10-15 14:09:25'),
(248, 31, 'logout', '2024-10-15 14:11:53'),
(249, 27, 'login', '2024-10-15 14:12:01'),
(250, 27, 'logout', '2024-10-15 14:15:30'),
(251, 1, 'login', '2024-10-15 14:15:36'),
(252, 1, 'logout', '2024-10-15 15:05:55'),
(253, 2, 'login', '2024-10-15 15:06:03'),
(254, 2, 'login', '2024-10-16 07:41:07'),
(255, 2, 'logout', '2024-10-16 07:41:28'),
(256, 1, 'login', '2024-10-16 07:52:32'),
(257, 1, 'logout', '2024-10-16 07:58:09'),
(258, 1, 'login', '2024-10-16 07:58:19'),
(259, 1, 'logout', '2024-10-16 07:58:24'),
(260, 1, 'login', '2024-10-16 08:21:45'),
(261, 1, 'logout', '2024-10-16 08:25:03'),
(262, 2, 'login', '2024-10-16 08:25:11'),
(263, 2, 'logout', '2024-10-16 08:25:43'),
(264, 31, 'login', '2024-10-16 08:25:52'),
(265, 31, 'logout', '2024-10-16 11:44:50'),
(266, 1, 'login', '2024-10-16 11:44:58'),
(267, 1, 'logout', '2024-10-16 11:46:07'),
(268, 27, 'login', '2024-10-16 11:46:14'),
(269, 1, 'login', '2024-10-16 13:24:01'),
(270, 1, 'logout', '2024-10-16 13:28:03'),
(271, 1, 'login', '2024-10-16 13:28:22'),
(272, 1, 'logout', '2024-10-16 13:28:41'),
(273, 1, 'login', '2024-10-16 13:28:59'),
(274, 1, 'login', '2024-10-16 14:36:17'),
(275, 1, 'logout', '2024-10-16 14:39:05'),
(276, 1, 'login', '2024-10-16 15:17:44'),
(277, 1, 'logout', '2024-10-16 16:12:38'),
(278, 1, 'login', '2024-10-16 16:12:48'),
(279, 1, 'logout', '2024-10-16 16:15:47'),
(280, 27, 'login', '2024-10-16 16:16:10'),
(281, 27, 'login', '2024-10-16 16:55:05'),
(282, 31, 'login', '2024-10-16 17:05:28'),
(283, 31, 'logout', '2024-10-16 17:05:56'),
(284, 27, 'login', '2024-10-16 17:06:01'),
(285, 2, 'login', '2024-10-16 17:06:15'),
(286, 2, 'logout', '2024-10-16 19:40:31'),
(287, 27, 'login', '2024-10-16 19:40:38'),
(288, 27, 'logout', '2024-10-16 19:41:39'),
(289, 31, 'login', '2024-10-16 19:45:47'),
(290, 31, 'logout', '2024-10-16 19:46:03'),
(291, 27, 'login', '2024-10-16 19:46:11'),
(292, 27, 'logout', '2024-10-16 19:46:17'),
(293, 1, 'login', '2024-10-16 19:46:23'),
(294, 1, 'logout', '2024-10-16 19:47:23'),
(295, 27, 'login', '2024-10-17 15:54:12'),
(296, 27, 'logout', '2024-10-17 18:21:06'),
(297, 27, 'login', '2024-10-17 18:21:11'),
(298, 27, 'logout', '2024-10-17 18:21:32'),
(299, 1, 'login', '2024-10-17 18:21:37'),
(300, 1, 'logout', '2024-10-17 18:22:02'),
(301, 31, 'login', '2024-10-17 18:22:07'),
(302, 31, 'logout', '2024-10-17 18:42:35'),
(303, 27, 'login', '2024-10-17 18:42:40'),
(304, 27, 'logout', '2024-10-17 18:51:00'),
(305, 1, 'login', '2024-10-17 18:51:04'),
(306, 1, 'logout', '2024-10-17 18:51:30'),
(307, 27, 'login', '2024-10-17 18:51:38'),
(308, 27, 'logout', '2024-10-17 18:51:53'),
(309, 31, 'login', '2024-10-17 18:52:03'),
(310, 31, 'logout', '2024-10-17 18:52:11'),
(312, 6, 'login', '2024-10-17 18:52:24'),
(313, 1, 'login', '2024-10-18 11:01:52'),
(314, 1, 'logout', '2024-10-18 11:03:28'),
(315, 1, 'login', '2024-10-18 11:03:36'),
(316, 1, 'logout', '2024-10-18 11:22:07'),
(317, 1, 'login', '2024-10-18 11:22:13'),
(318, 1, 'logout', '2024-10-18 11:24:06'),
(319, 1, 'login', '2024-10-18 11:24:16'),
(320, 1, 'logout', '2024-10-18 11:24:46'),
(321, 1, 'login', '2024-10-18 11:24:53'),
(322, 1, 'logout', '2024-10-18 11:55:00'),
(323, 1, 'login', '2024-10-18 11:56:03'),
(324, 1, 'logout', '2024-10-18 12:08:49'),
(325, 1, 'login', '2024-10-18 12:09:21'),
(326, 1, 'logout', '2024-10-18 12:29:24'),
(327, 1, 'login', '2024-10-18 12:29:31'),
(328, 1, 'logout', '2024-10-18 12:30:04'),
(329, 1, 'login', '2024-10-18 12:30:30'),
(330, 1, 'logout', '2024-10-18 14:07:26'),
(331, 1, 'login', '2024-10-18 14:07:44'),
(332, 1, 'logout', '2024-10-18 14:15:17'),
(333, 1, 'login', '2024-10-18 14:15:25'),
(334, 1, 'logout', '2024-10-18 14:22:46'),
(335, 1, 'login', '2024-10-18 14:22:57'),
(336, 1, 'logout', '2024-10-18 14:28:14'),
(337, 1, 'login', '2024-10-18 14:28:34'),
(338, 1, 'logout', '2024-10-18 14:30:10'),
(339, 1, 'login', '2024-10-18 14:30:24'),
(340, 1, 'logout', '2024-10-18 14:42:20'),
(341, 1, 'login', '2024-10-18 14:42:30'),
(342, 1, 'logout', '2024-10-18 14:43:31'),
(343, 1, 'login', '2024-10-18 14:43:42'),
(344, 1, 'logout', '2024-10-18 14:47:29'),
(345, 1, 'login', '2024-10-18 14:47:36'),
(346, 1, 'logout', '2024-10-18 14:54:40'),
(347, 1, 'login', '2024-10-18 14:55:08'),
(348, 1, 'logout', '2024-10-18 15:07:48'),
(349, 1, 'login', '2024-10-18 15:08:04'),
(350, 1, 'logout', '2024-10-18 15:27:50'),
(351, 27, 'login', '2024-10-18 15:27:59'),
(352, 27, 'logout', '2024-10-18 15:28:29'),
(353, 1, 'login', '2024-10-18 15:28:36'),
(354, 1, 'logout', '2024-10-18 16:34:28'),
(355, 1, 'login', '2024-10-18 16:34:34'),
(356, 1, 'login', '2024-10-19 09:47:48'),
(357, 1, 'logout', '2024-10-19 11:02:21'),
(358, 1, 'login', '2024-10-19 11:02:56'),
(359, 1, 'logout', '2024-10-19 12:34:58'),
(360, 1, 'login', '2024-10-19 12:35:19'),
(361, 1, 'logout', '2024-10-19 14:12:51'),
(362, 27, 'login', '2024-10-19 14:13:02'),
(363, 27, 'logout', '2024-10-19 14:13:57'),
(364, 1, 'login', '2024-10-19 14:48:30'),
(365, 1, 'logout', '2024-10-19 15:32:10'),
(366, 2, 'login', '2024-10-19 15:32:16'),
(367, 2, 'logout', '2024-10-19 15:33:19'),
(368, 27, 'login', '2024-10-19 15:33:27'),
(369, 27, 'logout', '2024-10-19 15:33:41'),
(370, 1, 'login', '2024-10-19 15:36:57'),
(371, 1, 'logout', '2024-10-19 15:58:29'),
(372, 1, 'login', '2024-10-19 15:58:36'),
(373, 1, 'logout', '2024-10-19 16:07:17'),
(374, 1, 'login', '2024-10-19 16:07:26'),
(375, 1, 'login', '2024-10-20 08:13:00'),
(376, 1, 'logout', '2024-10-20 09:18:33'),
(377, 1, 'login', '2024-10-20 09:19:02'),
(378, 1, 'logout', '2024-10-20 09:19:27'),
(379, 1, 'login', '2024-10-20 09:19:45'),
(380, 1, 'logout', '2024-10-20 09:21:56'),
(381, 1, 'login', '2024-10-20 09:22:04'),
(382, 1, 'logout', '2024-10-20 10:12:12'),
(383, 1, 'login', '2024-10-20 10:14:24'),
(384, 1, 'logout', '2024-10-20 10:15:28'),
(385, 1, 'login', '2024-10-20 10:20:27'),
(386, 1, 'logout', '2024-10-20 10:44:40'),
(387, 1, 'login', '2024-10-20 10:47:28'),
(388, 1, 'logout', '2024-10-20 10:53:26'),
(389, 1, 'login', '2024-10-20 10:56:48'),
(390, 1, 'logout', '2024-10-20 10:57:51'),
(391, 1, 'login', '2024-10-20 11:11:15'),
(392, 1, 'logout', '2024-10-20 11:15:35'),
(393, 1, 'login', '2024-10-20 11:18:06'),
(394, 1, 'logout', '2024-10-20 11:20:15'),
(395, 1, 'login', '2024-10-20 11:24:39'),
(396, 1, 'logout', '2024-10-20 11:26:19'),
(397, 1, 'login', '2024-10-20 11:35:34'),
(398, 1, 'logout', '2024-10-20 11:37:33'),
(399, 1, 'login', '2024-10-20 11:44:20'),
(400, 1, 'logout', '2024-10-20 11:48:44'),
(401, 1, 'login', '2024-10-20 11:50:14'),
(402, 1, 'login', '2024-10-20 11:52:00'),
(403, 1, 'logout', '2024-10-20 11:53:37'),
(404, 1, 'login', '2024-10-20 11:54:56'),
(405, 1, 'logout', '2024-10-20 12:01:06'),
(406, 1, 'login', '2024-10-20 12:02:48'),
(407, 1, 'logout', '2024-10-20 13:28:19'),
(408, 1, 'login', '2024-10-20 13:30:06'),
(409, 1, 'logout', '2024-10-20 13:36:40'),
(410, 1, 'login', '2024-10-20 13:38:46'),
(411, 1, 'logout', '2024-10-20 13:44:09'),
(412, 1, 'login', '2024-10-20 13:46:02'),
(413, 1, 'logout', '2024-10-20 13:51:29'),
(414, 1, 'login', '2024-10-20 13:55:23'),
(415, 1, 'logout', '2024-10-20 14:02:09'),
(416, 1, 'login', '2024-10-20 14:02:13'),
(417, 1, 'logout', '2024-10-20 14:07:08'),
(418, 1, 'login', '2024-10-20 14:52:15'),
(419, 1, 'login', '2024-10-20 16:56:54'),
(420, 1, 'logout', '2024-10-20 17:28:18'),
(421, 1, 'login', '2024-10-20 17:28:24'),
(422, 1, 'logout', '2024-10-20 17:48:06'),
(423, 1, 'login', '2024-10-20 17:48:28'),
(424, 1, 'logout', '2024-10-20 17:59:57'),
(425, 1, 'login', '2024-10-20 18:00:26'),
(426, 1, 'logout', '2024-10-20 18:42:46'),
(427, 1, 'login', '2024-10-20 18:43:59'),
(428, 1, 'logout', '2024-10-20 19:21:10'),
(429, 1, 'login', '2024-10-20 19:21:39'),
(430, 1, 'logout', '2024-10-20 19:27:45'),
(431, 1, 'login', '2024-10-20 19:28:53'),
(432, 1, 'logout', '2024-10-20 20:33:19'),
(433, 1, 'login', '2024-10-20 20:33:49'),
(434, 1, 'login', '2024-10-20 21:20:57'),
(435, 1, 'login', '2024-10-21 07:48:27'),
(436, 1, 'logout', '2024-10-21 09:01:12'),
(437, 1, 'login', '2024-10-21 09:01:42'),
(438, 1, 'logout', '2024-10-21 09:22:07'),
(442, 1, 'login', '2024-10-21 10:47:17'),
(443, 1, 'logout', '2024-10-21 10:48:52'),
(444, 86, 'login', '2024-10-21 15:44:39'),
(445, 86, 'logout', '2024-10-21 15:45:01'),
(446, 1, 'login', '2024-10-21 15:45:13'),
(447, 1, 'logout', '2024-10-21 17:32:54'),
(448, 1, 'login', '2024-10-21 18:08:37'),
(449, 1, 'logout', '2024-10-21 18:13:42');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria_accesos_archivo`
--

CREATE TABLE `auditoria_accesos_archivo` (
  `idAcceso` int(11) NOT NULL,
  `idUser` int(11) NOT NULL,
  `accion` varchar(255) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `auditoria_accesos_archivo`
--

INSERT INTO `auditoria_accesos_archivo` (`idAcceso`, `idUser`, `accion`, `fecha`) VALUES
(1, 32, 'DELETE', '2024-10-20 09:24:17'),
(2, 71, 'DELETE', '2024-10-20 21:34:55'),
(3, 68, 'DELETE', '2024-10-20 21:35:09'),
(4, 83, 'DELETE', '2024-10-21 15:45:25'),
(5, 84, 'DELETE', '2024-10-21 15:45:33');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `idCategoria` int(11) NOT NULL,
  `nombreCategoria` varchar(100) NOT NULL,
  `creado_por` int(11) DEFAULT NULL,
  `estado_categoria` enum('activo','inactivo') DEFAULT 'activo',
  `tipo_categoria` enum('gasto','ingreso') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`idCategoria`, `nombreCategoria`, `creado_por`, `estado_categoria`, `tipo_categoria`) VALUES
(23, 'Compra', 2, 'activo', 'gasto'),
(24, 'Ocio', 2, 'activo', 'gasto'),
(25, 'Luz', 2, 'activo', 'gasto'),
(26, 'Agua', 2, 'activo', 'gasto'),
(29, 'Salario', 2, 'activo', 'ingreso'),
(30, 'Paga', 2, 'activo', 'ingreso'),
(31, 'Otros', 2, 'activo', 'ingreso'),
(32, 'Ventas', 2, 'activo', 'ingreso'),
(35, 'Supermercado', 1, 'activo', 'gasto'),
(36, 'Ingresos inesperados', 1, 'activo', 'ingreso'),
(37, 'Ingresos esperados', 1, 'activo', 'ingreso'),
(38, 'Gastos presupuestados', 1, 'activo', 'gasto'),
(39, 'Gastos imprevistos', 1, 'activo', 'gasto'),
(41, 'Nuevitas', 26, 'activo', 'ingreso');

--
-- Disparadores `categorias`
--
DELIMITER $$
CREATE TRIGGER `auditar_categoria_insert` AFTER INSERT ON `categorias` FOR EACH ROW BEGIN
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario, fecha)
  VALUES ('INSERT', 'categorias', NEW.idCategoria, NEW.creado_por, NOW());
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuraciones`
--

CREATE TABLE `configuraciones` (
  `idConfig` int(11) NOT NULL,
  `clave_config` varchar(100) DEFAULT NULL,
  `valor_config` text DEFAULT NULL,
  `idUser` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `envio_refranes`
--

CREATE TABLE `envio_refranes` (
  `idEnvio` int(11) NOT NULL,
  `idRefran` int(11) NOT NULL,
  `idUser` int(11) NOT NULL,
  `fecha_envio` date NOT NULL,
  `momento` enum('mañana','tarde') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `familias`
--

CREATE TABLE `familias` (
  `idFamilia` int(11) NOT NULL,
  `nombre_familia` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `familias`
--

INSERT INTO `familias` (`idFamilia`, `nombre_familia`, `password`, `estado`) VALUES
(1, 'Familia1', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', 'activo'),
(16, 'Familia juan', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', 'activo'),
(22, 'mmmmmm', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', 'activo'),
(23, 'vvvvv', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', 'activo'),
(24, 'buena', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', 'activo'),
(30, '27Fam', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', 'activo'),
(31, 'SebasFamilia', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', 'activo'),
(32, 'PaulaFamilia', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', 'activo'),
(33, 'SilviaFamilia', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', 'activo'),
(34, 'JuanFamilia', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', 'activo'),
(35, 'LucioFamilia', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', 'activo'),
(36, 'FamiliaNueva1', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', 'activo'),
(37, 'FamiliaNueva2', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', 'activo'),
(38, 'RobertoF1', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', 'activo'),
(39, 'RobertoF2', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', 'activo'),
(40, 'RobertoF3', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', 'activo'),
(41, 'RobertoF4', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', 'activo'),
(43, 'LosGuayonesF1', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', 'activo'),
(44, 'LosGuayonesF2', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', 'activo'),
(45, 'MolonaFamilia', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', 'activo'),
(46, 'LaMejorDelMundo', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', 'activo'),
(47, 'Pruebita11', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', 'activo'),
(48, 'LosBola', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', 'activo'),
(50, 'BolincheFamilia', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', 'activo'),
(51, 'AverSi', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', 'activo'),
(53, 'GonzalezFamilia', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', 'activo'),
(55, 'PucholFamila', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', 'activo'),
(56, 'FamiliaPrueba', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', 'activo'),
(57, 'FamiliaPruebitaDefinitiva', '$2y$10$rhmmZ60UIU5LMHyX/DDU7uN9nO.HBAKVGb7urbhVIQApCtMjVGtPS', 'activo'),
(58, 'Rafalito2Familia', '$2y$10$CX00..3SPRUZj2xgvdKl2O5ji4UXUF5llRhxq9rgXYuW4OTxkw/ve', 'activo'),
(59, 'MuretiFamilia', '$2y$10$WRhA2Qo5xuar6n/TY5kUxestSpXbn5uXtbXA0stoPagngbgCRpcHq', 'activo'),
(60, 'BeitaJFamilia', '$2y$10$1CVjJJ6W63OBd.z2BAWdf.xZIkjEuppZGgorDh3d9tsiECnZ7XLuW', 'activo'),
(61, '61F', '$2y$10$sHyQ7vOzvZgdcKfJROinQezqA3rHpWOz9UQCkvJNPkuLRv0N.Y78W', 'activo'),
(62, 'Familia62', '$2y$10$.23Q7rMGm4Tjr9ramCjw3.YD.h7UdZI379Q9OmbwAmVl/MpiYU.e.', 'activo'),
(63, 'BobiotoFamilia', '$2y$10$h4Y.v0jNUsZ89NcryPOci.KzFSLc/6BzkFuzU47c6zYUpcTRJJnXS', 'activo');

--
-- Disparadores `familias`
--
DELIMITER $$
CREATE TRIGGER `auditar_familias` AFTER INSERT ON `familias` FOR EACH ROW BEGIN
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario, fecha)
  VALUES ('INSERT', 'familias', NEW.idFamilia, 'sistema', NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `auditar_familias_delete` AFTER DELETE ON `familias` FOR EACH ROW BEGIN
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, fecha) 
  VALUES ('delete', 'familias', OLD.idFamilia, NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `auditar_familias_delete_archivo` AFTER DELETE ON `familias` FOR EACH ROW BEGIN
  INSERT INTO auditoria_accesos_archivo (idUser, accion, fecha)
  VALUES (NULL, 'DELETE_FAMILIA_' || OLD.idFamilia, NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `auditar_familias_update` AFTER UPDATE ON `familias` FOR EACH ROW BEGIN
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario, fecha)
  VALUES ('UPDATE', 'familias', NEW.idFamilia, 'sistema', NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `eliminar_gastos_huerfanos` AFTER DELETE ON `familias` FOR EACH ROW BEGIN
  DELETE FROM gastos WHERE idFamilia = OLD.idFamilia;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `eliminar_ingresos_huerfanos` AFTER DELETE ON `familias` FOR EACH ROW BEGIN
  DELETE FROM ingresos WHERE idFamilia = OLD.idFamilia;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gastos`
--

CREATE TABLE `gastos` (
  `idGasto` int(11) NOT NULL,
  `idUser` int(11) NOT NULL,
  `importe` decimal(10,2) NOT NULL,
  `idCategoria` int(11) NOT NULL,
  `origen` enum('banco','efectivo','tarjeta','transferencia','otro') NOT NULL,
  `concepto` varchar(100) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `idGrupo` int(11) DEFAULT NULL,
  `idFamilia` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `gastos`
--

INSERT INTO `gastos` (`idGasto`, `idUser`, `importe`, `idCategoria`, `origen`, `concepto`, `fecha`, `idGrupo`, `idFamilia`) VALUES
(18, 2, 120.50, 23, 'banco', 'Mercadona', '2024-10-07 22:00:00', 1, 1),
(19, 2, 48.25, 24, 'efectivo', 'Cena', '2024-10-07 22:00:00', 1, 1),
(20, 2, 80.23, 25, 'banco', 'Factura Luz', '2024-10-07 22:00:00', 1, 1),
(21, 2, 80.00, 26, 'banco', 'Factura Agua', '2024-10-07 22:00:00', 1, 1),
(22, 27, 150.00, 24, 'banco', 'otrossss', '2024-10-12 22:00:00', NULL, 30),
(23, 27, 8.00, 24, 'efectivo', 'Cervecitas', '2024-10-12 22:00:00', NULL, 30),
(24, 27, 80.00, 26, 'banco', 'Factura Agua', '2024-10-12 22:00:00', NULL, 30),
(25, 27, 300.00, 38, 'banco', 'Microondas', '2024-10-12 22:00:00', NULL, 30),
(26, 27, 26.00, 23, 'banco', 'farmacia', '2024-10-12 22:00:00', NULL, 30),
(27, 27, 8.00, 35, 'banco', 'Leche', '2024-10-12 22:00:00', NULL, 30),
(28, 27, 123.00, 23, 'banco', 'Mercadona', '2024-10-12 22:00:00', NULL, 30),
(29, 31, 25.23, 24, 'banco', 'Comida amigas', '2024-10-13 22:00:00', NULL, 30),
(30, 31, 4.00, 24, 'efectivo', 'bono Bus', '2024-10-13 22:00:00', NULL, 30);

--
-- Disparadores `gastos`
--
DELIMITER $$
CREATE TRIGGER `auditar_creacion_gasto` AFTER INSERT ON `gastos` FOR EACH ROW BEGIN
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario)
  VALUES ('INSERT', 'gastos', NEW.idGasto, NEW.idUser);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `auditar_gastos_delete` AFTER DELETE ON `gastos` FOR EACH ROW BEGIN
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario, fecha)
  VALUES ('DELETE', 'gastos', OLD.idGasto, OLD.idUser, NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `auditar_gastos_update` AFTER UPDATE ON `gastos` FOR EACH ROW BEGIN
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario, fecha)
  VALUES ('UPDATE', 'gastos', NEW.idGasto, NEW.idUser, NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `check_gasto_asignacion` BEFORE INSERT ON `gastos` FOR EACH ROW BEGIN
  IF NEW.idFamilia IS NULL AND NEW.idGrupo IS NULL THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Un gasto debe pertenecer a una familia, a un grupo o ser personal.';
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `validar_consistencia_gasto` BEFORE INSERT ON `gastos` FOR EACH ROW BEGIN
  DECLARE v_familia_exist INT;
  DECLARE v_grupo_exist INT;
  
  -- Validar que el usuario pertenece a la familia, si está asignada
  IF NEW.idFamilia IS NOT NULL THEN
    SELECT COUNT(*) INTO v_familia_exist 
    FROM usuarios 
    WHERE idUser = NEW.idUser AND idFamilia = NEW.idFamilia;
    
    IF v_familia_exist = 0 THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El usuario no pertenece a la familia asignada';
    END IF;
  END IF;
  
  -- Validar que el usuario pertenece al grupo, si está asignado
  IF NEW.idGrupo IS NOT NULL THEN
    SELECT COUNT(*) INTO v_grupo_exist 
    FROM usuarios 
    WHERE idUser = NEW.idUser AND idGrupo = NEW.idGrupo;
    
    IF v_grupo_exist = 0 THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El usuario no pertenece al grupo asignado';
    END IF;
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `verificar_consistencia_gasto` BEFORE INSERT ON `gastos` FOR EACH ROW BEGIN
    -- Verificar que el gasto no sea negativo
    IF NEW.importe < 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El importe del gasto no puede ser negativo';
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `verificar_gasto_familia_grupo` BEFORE INSERT ON `gastos` FOR EACH ROW BEGIN
  DECLARE familia_grupo_valido BOOLEAN;
  
  -- Verificar si el usuario pertenece a la familia o grupo indicado
  SELECT COUNT(*) INTO familia_grupo_valido
  FROM usuarios
  WHERE idUser = NEW.idUser
    AND (idFamilia = NEW.idFamilia OR idGrupo = NEW.idGrupo);
  
  IF familia_grupo_valido = 0 THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'El usuario no pertenece a la familia o grupo indicado';
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupos`
--

CREATE TABLE `grupos` (
  `idGrupo` int(11) NOT NULL,
  `nombre_grupo` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `grupos`
--

INSERT INTO `grupos` (`idGrupo`, `nombre_grupo`, `password`) VALUES
(1, 'Grupo1', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq'),
(9, 'Grupitoo lllll', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq'),
(11, 'B3', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq'),
(14, 'AndrésGrupo', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq'),
(15, 'PaulaGrupo', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq'),
(16, 'SilviaGrupo', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq'),
(17, 'JuanGrupo', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq'),
(18, 'LucioGrupo', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq'),
(19, 'GrupoNuevo1', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq'),
(20, 'GrupoNuevo2', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq'),
(21, 'RafitaGrupo', '$2y$10$omVFUYHV0gIl98a3IboAR.2d0nDOUZqcxcKQ.3duLxkwURTahJeo.'),
(22, 'MuretiGrupo', '$2y$10$5rjlBLJVC2j4QtwfV4hQW./D0o1fv3Xd2y3cxkcQeLndBZ4UFQVQe'),
(23, 'BeitaJGrupo', '$2y$10$ZYiazRGE.Na7FblA/aEOCuQszJ.xTr40/MrSAxiRXmMqGEy3xWooK'),
(24, '24Grupo', '$2y$10$A.UDKWNBoIiBVBpFbej/YevY5wMm1D8fZ94aAf0RaNwkgZnj/L1bi'),
(25, 'Grupo25', '$2y$10$LzRUCeMoJwKO1.B/pDJF5uA55ljHUbRaypc3S3AoMS1op0NWt8AVS'),
(26, 'BobitoGrupo', '$2y$10$ZEMaH0SRDrNAasAQt5ANauvCU7ksVakOm2OVb.TlNyThQBGeIZuxW');

--
-- Disparadores `grupos`
--
DELIMITER $$
CREATE TRIGGER `auditar_grupos_delete` AFTER DELETE ON `grupos` FOR EACH ROW BEGIN
    INSERT INTO auditoria (accion, tabla_afectada, idRegistro, fecha)
    VALUES ('DELETE', 'grupos', OLD.idGrupo, NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `auditar_grupos_delete_archivo` AFTER DELETE ON `grupos` FOR EACH ROW BEGIN
  INSERT INTO auditoria_accesos_archivo (idUser, accion, fecha)
  VALUES (NULL, 'DELETE_GRUPO_' || OLD.idGrupo, NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `auditar_grupos_insert` AFTER INSERT ON `grupos` FOR EACH ROW BEGIN
    INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario, fecha)
    VALUES ('INSERT', 'grupos', NEW.idGrupo, 'Sistema', NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `auditar_grupos_update` AFTER UPDATE ON `grupos` FOR EACH ROW BEGIN
    INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario, fecha)
    VALUES ('UPDATE', 'grupos', NEW.idGrupo, 'Sistema', NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `before_insert_grupos` BEFORE INSERT ON `grupos` FOR EACH ROW BEGIN
    DECLARE v_grupo_exist INT;

    -- Verificar si ya existe un grupo con el mismo nombre
    SELECT COUNT(*) INTO v_grupo_exist FROM grupos WHERE nombre_grupo = NEW.nombre_grupo;
    IF v_grupo_exist > 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El grupo ya existe.';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingresos`
--

CREATE TABLE `ingresos` (
  `idIngreso` int(11) NOT NULL,
  `idUser` int(11) DEFAULT NULL,
  `importe` decimal(10,2) NOT NULL,
  `idCategoria` int(11) DEFAULT NULL,
  `origen` enum('banco','efectivo','tarjeta','transferencia','otro') NOT NULL,
  `concepto` varchar(100) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `idGrupo` int(11) DEFAULT NULL,
  `idFamilia` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ingresos`
--

INSERT INTO `ingresos` (`idIngreso`, `idUser`, `importe`, `idCategoria`, `origen`, `concepto`, `fecha`, `idGrupo`, `idFamilia`) VALUES
(17, 2, 1500.00, 29, 'banco', 'Sueldo Rafa', '2024-10-07 22:00:00', 1, 1),
(18, 2, 50.00, 30, 'efectivo', 'Cumple Rafa', '2024-10-07 22:00:00', 1, 1),
(19, 2, 15.00, 31, 'efectivo', 'Lotería', '2024-10-07 22:00:00', 1, 1),
(20, 2, 350.00, 32, 'efectivo', 'Venta móvil', '2024-10-07 22:00:00', 1, 1),
(23, 27, 15.00, 31, 'efectivo', 'Lotería', '2024-10-12 22:00:00', NULL, 30),
(24, 27, 1500.00, 29, 'banco', 'Septiembre', '2024-10-12 22:00:00', NULL, 30),
(25, 31, 1600.00, 29, 'banco', 'Agosto', '2024-10-13 22:00:00', NULL, 30),
(26, 31, 15.00, 32, 'efectivo', 'Venta libros', '2024-10-13 22:00:00', NULL, 30);

--
-- Disparadores `ingresos`
--
DELIMITER $$
CREATE TRIGGER `auditar_creacion_ingreso` AFTER INSERT ON `ingresos` FOR EACH ROW BEGIN
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario)
  VALUES ('INSERT', 'ingresos', NEW.idIngreso, NEW.idUser);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `auditar_ingresos_delete` AFTER DELETE ON `ingresos` FOR EACH ROW BEGIN
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario, fecha)
  VALUES ('DELETE', 'ingresos', OLD.idIngreso, OLD.idUser, NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `auditar_ingresos_update` AFTER UPDATE ON `ingresos` FOR EACH ROW BEGIN
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario, fecha)
  VALUES ('UPDATE', 'ingresos', NEW.idIngreso, NEW.idUser, NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `check_ingreso_asignacion` BEFORE INSERT ON `ingresos` FOR EACH ROW BEGIN
  IF NEW.idFamilia IS NOT NULL OR NEW.idGrupo IS NOT NULL THEN
    SET @dummy = 1;
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `menuadmin`
--

CREATE TABLE `menuadmin` (
  `idMenu` int(11) NOT NULL,
  `idRol` int(11) DEFAULT NULL,
  `nombreItem` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `news_letter_envios`
--

CREATE TABLE `news_letter_envios` (
  `idEnvio` int(11) NOT NULL,
  `idUser` int(11) NOT NULL,
  `fecha_envio` timestamp NOT NULL DEFAULT current_timestamp(),
  `idRefran` int(11) NOT NULL,
  `saldo_total` decimal(10,2) NOT NULL,
  `gastos_totales` decimal(10,2) NOT NULL,
  `ingresos_totales` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

CREATE TABLE `notificaciones` (
  `idNotificacion` int(11) NOT NULL,
  `idUser` int(11) DEFAULT NULL,
  `mensaje` text DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `leido` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `notificaciones`
--

INSERT INTO `notificaciones` (`idNotificacion`, `idUser`, `mensaje`, `fecha`, `leido`) VALUES
(1, 1, 'Se ha agregado un nuevo gasto.', '2024-10-15 18:01:23', 1),
(2, 1, 'Esta es una nueva notificación.', '2024-10-15 18:03:48', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos_cache`
--

CREATE TABLE `permisos_cache` (
  `idUser` int(11) NOT NULL,
  `nombrePermiso` varchar(255) NOT NULL,
  `tipoPermiso` varchar(255) NOT NULL,
  `tiene_permiso` tinyint(4) DEFAULT 0,
  `fecha_cache` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `preferencias_usuarios`
--

CREATE TABLE `preferencias_usuarios` (
  `idPreferencia` int(11) NOT NULL,
  `idUser` int(11) NOT NULL,
  `clave` varchar(255) NOT NULL,
  `valor` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `preferencias_usuarios`
--

INSERT INTO `preferencias_usuarios` (`idPreferencia`, `idUser`, `clave`, `valor`) VALUES
(1, 1, 'notificaciones', 'activado'),
(2, 1, 'dashboard_layout', 'modo_simple');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `refranes`
--

CREATE TABLE `refranes` (
  `idRefran` int(11) NOT NULL,
  `refran` text NOT NULL,
  `autor` varchar(255) DEFAULT NULL,
  `pais` varchar(100) DEFAULT NULL,
  `fecha_ultimo_uso` timestamp NULL DEFAULT NULL,
  `texto_refran` varchar(255) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `refranes`
--

INSERT INTO `refranes` (`idRefran`, `refran`, `autor`, `pais`, `fecha_ultimo_uso`, `texto_refran`, `fecha_creacion`) VALUES
(1, 'A caballo regalado no le mires el diente', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(2, 'A quien madruga, Dios le ayuda', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(3, 'Más vale tarde que nunca', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(4, 'No por mucho madrugar, amanece más temprano', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(5, 'A buen entendedor, pocas palabras bastan', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(6, 'En boca cerrada no entran moscas', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(7, 'Perro ladrador, poco mordedor', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(8, 'Camarón que se duerme, se lo lleva la corriente', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(9, 'Al mal tiempo, buena cara', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(10, 'Ojos que no ven, corazón que no siente', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(11, 'Más vale pájaro en mano que ciento volando', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(12, 'Dime con quién andas y te diré quién eres', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(13, 'El que mucho abarca, poco aprieta', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(14, 'Cría cuervos y te sacarán los ojos', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(15, 'A quien buen árbol se arrima, buena sombra le cobija', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(16, 'Agua que no has de beber, déjala correr', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(17, 'Quien siembra vientos, recoge tempestades', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(18, 'En casa del herrero, cuchillo de palo', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(19, 'Matar dos pájaros de un tiro', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(20, 'No hay mal que por bien no venga', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(21, 'Más vale prevenir que curar', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(22, 'Zapatero a tus zapatos', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(23, 'Quien tiene boca, se equivoca', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(24, 'El hábito no hace al monje', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(25, 'Lo que no mata, engorda', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(26, 'Roma no se hizo en un día', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(27, 'No hay mal que dure cien años', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(28, 'Más sabe el diablo por viejo que por diablo', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(29, 'De tal palo, tal astilla', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(30, 'A falta de pan, buenas son tortas', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(31, 'Al que buen árbol se arrima, buena sombra le cobija', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(32, 'Cuando el río suena, agua lleva', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(33, 'Cada oveja con su pareja', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(34, 'Barriga llena, corazón contento', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(35, 'Al pan, pan, y al vino, vino', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(36, 'No hay peor sordo que el que no quiere oír', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(37, 'Quien mucho abarca, poco aprieta', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(38, 'Más vale tarde que nunca', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(39, 'Crea fama y échate a dormir', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(40, 'No hay mal que por bien no venga', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(41, 'Aunque la mona se vista de seda, mona se queda', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(42, 'Dime de qué presumes y te diré de qué careces', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(43, 'El que la sigue, la consigue', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(44, 'Más vale solo que mal acompañado', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(45, 'En la variedad está el gusto', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(46, 'Cuando una puerta se cierra, otra se abre', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(47, 'No es oro todo lo que reluce', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(48, 'El que no arriesga, no gana', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(49, 'El tiempo lo cura todo', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(50, 'La avaricia rompe el saco', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(51, 'A quien Dios se la dé, San Pedro se la bendiga', 'Desconocido', 'España', '2024-10-04 12:25:37', NULL, '2024-10-04 12:25:37'),
(52, 'El viaje de mil millas comienza con un solo paso', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(53, 'El que pregunta es tonto por cinco minutos, pero el que no pregunta sigue siendo tonto siempre', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(54, 'Si quieres que algo se haga, encárgalo a una persona ocupada', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(55, 'Saber y no hacer es no saber', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(56, 'Antes de iniciar la labor de cambiar el mundo, da tres vueltas por tu propia casa', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(57, 'No temas crecer lentamente, teme solo quedarte quieto', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(58, 'El agua demasiado pura no tiene peces', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(59, 'No importa lo lento que vayas, siempre y cuando no te detengas', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(60, 'Los grandes árboles nacen de semillas pequeñas', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(61, 'Un hombre sabio se adapta a las circunstancias como el agua se adapta a la forma del recipiente que la contiene', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(62, 'No puedes evitar que el pájaro de la tristeza vuele sobre tu cabeza, pero sí puedes evitar que anide en tu cabellera', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(63, 'El sabio no dice lo que sabe, y el necio no sabe lo que dice', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(64, 'El bambú que se dobla es más fuerte que el roble que resiste', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(65, 'Quien teme sufrir, ya sufre el temor', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(66, 'Las grandes almas tienen voluntades; las débiles solo deseos', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(67, 'El que estudia diez años en la oscuridad será universalmente conocido como alguien que ha logrado el éxito en una sola vez', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(68, 'Un hombre sin sonrisa no debe abrir una tienda', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(69, 'Caer no es una derrota, rendirse sí lo es', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(70, 'Si te caes siete veces, levántate ocho', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(71, 'La enseñanza es el oficio del alma', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(72, 'Es fácil ser valiente desde una distancia segura', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(73, 'La paciencia es una flor que no crece en todos los jardines', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(74, 'Un problema no es para siempre', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(75, 'La puerta mejor cerrada es aquella que puede dejarse abierta', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(76, 'El que domina a los otros es fuerte; el que se domina a sí mismo es poderoso', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(77, 'Si das pescado a un hombre hambriento, le nutres una vez. Si le enseñas a pescar, le nutrirás toda su vida', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(78, 'El que no tiene enemigos, no tiene amigos', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(79, 'El tiempo es un río que arrastra los días y las horas', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(80, 'Mejor ser un perro en tiempos de paz, que un hombre en tiempos de guerra', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(81, 'No busques la verdad fuera de ti, está en tu interior', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(82, 'Una palabra amable no cuesta nada, pero vale mucho', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(83, 'La vida es realmente simple, pero insistimos en hacerla complicada', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(84, 'El jade necesita ser tallado antes de ser una joya', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(85, 'Es mejor encender una vela que maldecir la oscuridad', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(86, 'Los sabios no necesitan una larga explicación, los tontos no comprenderán por más que se les explique', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(87, 'La perseverancia supera la inteligencia', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(88, 'Si caminas solo, irás más rápido; si caminas acompañado, llegarás más lejos', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(89, 'Cada sonrisa que se da a los demás, vuelve a ti', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(90, 'El hombre superior es modesto en su discurso, pero excede en sus acciones', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(91, 'Escucha el viento, y sabrás la dirección de tu destino', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(92, 'Aquel que sabe cuándo ha tenido suficiente es rico', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(93, 'Es mejor ser engañado que desconfiar de todos', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(94, 'El que se aparta de la sabiduría envejece rápidamente', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(95, 'Quien no comprende una mirada, tampoco comprenderá una larga explicación', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(96, 'El silencio es un amigo que nunca traiciona', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(97, 'Es más fácil saber hacer una cosa que hacerla', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(98, 'Con una mentira se puede ir muy lejos, pero sin esperanzas de volver', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(99, 'El hombre que ha cometido un error y no lo corrige, comete otro error mayor', 'Desconocido', 'China', '2024-10-04 12:26:13', NULL, '2024-10-04 12:26:13'),
(100, 'A palabras necias, oídos sordos', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(101, 'Al que no le sobra pan, no críe can', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(102, 'Amor con amor se paga', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(103, 'Ande yo caliente y ríase la gente', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(104, 'A todo cerdo le llega su San Martín', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(105, 'Burro grande, ande o no ande', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(106, 'Cada loco con su tema', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(107, 'Coge la ocasión por los pelos', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(108, 'Con pan y vino se anda el camino', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(109, 'Cuentas claras, amistades largas', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(110, 'Dar en el clavo', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(111, 'De noche todos los gatos son pardos', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(112, 'Del dicho al hecho hay mucho trecho', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(113, 'Dios los cría y ellos se juntan', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(114, 'Donde hay patrón no manda marinero', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(115, 'El buen paño en el arca se vende', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(116, 'El que no corre, vuela', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(117, 'El que no llora, no mama', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(118, 'El que tiene padrino, se bautiza', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(119, 'El que vive de ilusiones, muere de desengaños', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(120, 'En abril, aguas mil', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(121, 'En martes, ni te cases ni te embarques', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(122, 'En tiempos de guerra, cualquier agujero es trinchera', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(123, 'Entre broma y broma, la verdad asoma', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(124, 'Gato con guantes no caza ratones', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(125, 'Haz bien y no mires a quién', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(126, 'Hombre prevenido vale por dos', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(127, 'La cabra siempre tira al monte', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(128, 'La dicha de la fea, la hermosa la desea', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(129, 'La mujer del César no solo debe ser honrada, sino parecerlo', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(130, 'Las apariencias engañan', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(131, 'Lo cortés no quita lo valiente', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(132, 'Lo prometido es deuda', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(133, 'Loro viejo no aprende a hablar', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(134, 'Mal de muchos, consuelo de tontos', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(135, 'Más vale maña que fuerza', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(136, 'Más vale un toma que dos te daré', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(137, 'Nadie es profeta en su tierra', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(138, 'No hay enemigo pequeño', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(139, 'No hay que vender la piel del oso antes de cazarlo', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(140, 'No se ganó Zamora en una hora', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(141, 'Nunca digas de esta agua no beberé', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(142, 'Perro que ladra no muerde', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(143, 'Quien algo quiere, algo le cuesta', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(144, 'Quien mucho habla, mucho yerra', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(145, 'Quien ríe último, ríe mejor', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(146, 'Sarna con gusto no pica', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(147, 'Soldado avisado no muere en guerra', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39'),
(148, 'Sobre gustos no hay nada escrito', 'Desconocido', 'España', '2024-10-04 12:26:39', NULL, '2024-10-04 12:26:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `idRol` int(11) NOT NULL,
  `nombreRol` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`idRol`, `nombreRol`) VALUES
(1, 'superadmin'),
(2, 'admin'),
(3, 'usuario'),
(4, 'registro');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles_permisos`
--

CREATE TABLE `roles_permisos` (
  `idPermiso` int(11) NOT NULL,
  `idRol` int(11) DEFAULT NULL,
  `nombrePermiso` varchar(255) DEFAULT NULL,
  `tipoPermiso` enum('leer','escribir','eliminar') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles_permisos`
--

INSERT INTO `roles_permisos` (`idPermiso`, `idRol`, `nombrePermiso`, `tipoPermiso`) VALUES
(7, 1, 'gastos', 'leer'),
(8, 1, 'gastos', 'escribir'),
(9, 1, 'gastos', 'eliminar'),
(10, 2, 'gastos', 'leer'),
(11, 3, 'gastos', 'leer'),
(12, 3, 'ingresos', 'leer'),
(13, 1, 'usuarios', 'leer'),
(14, 1, 'usuarios', 'escribir'),
(15, 1, 'usuarios', 'eliminar'),
(16, 1, 'familias', 'leer'),
(17, 1, 'familias', 'escribir'),
(18, 1, 'familias', 'eliminar'),
(19, 1, 'grupos', 'leer'),
(20, 1, 'grupos', 'escribir'),
(21, 1, 'grupos', 'eliminar'),
(22, 1, 'gastos', 'leer'),
(23, 1, 'gastos', 'escribir'),
(24, 1, 'gastos', 'eliminar'),
(25, 1, 'ingresos', 'leer'),
(26, 1, 'ingresos', 'escribir'),
(27, 1, 'ingresos', 'eliminar'),
(28, 1, 'presupuestos', 'leer'),
(29, 1, 'presupuestos', 'escribir'),
(30, 1, 'presupuestos', 'eliminar'),
(31, 1, 'metas', 'leer'),
(32, 1, 'metas', 'escribir'),
(33, 1, 'metas', 'eliminar'),
(34, 2, 'usuarios', 'leer'),
(35, 2, 'usuarios', 'escribir'),
(36, 2, 'usuarios', 'eliminar'),
(37, 2, 'familias', 'leer'),
(38, 2, 'familias', 'escribir'),
(39, 2, 'familias', 'eliminar'),
(40, 2, 'grupos', 'leer'),
(41, 2, 'grupos', 'escribir'),
(42, 2, 'grupos', 'eliminar'),
(43, 2, 'gastos', 'leer'),
(44, 2, 'gastos', 'escribir'),
(45, 2, 'gastos', 'eliminar'),
(46, 2, 'ingresos', 'leer'),
(47, 2, 'ingresos', 'escribir'),
(48, 2, 'ingresos', 'eliminar'),
(49, 2, 'presupuestos', 'leer'),
(50, 2, 'presupuestos', 'escribir'),
(51, 2, 'metas', 'leer'),
(52, 2, 'metas', 'escribir'),
(53, 3, 'gastos', 'leer'),
(54, 3, 'gastos', 'escribir'),
(55, 3, 'gastos', 'eliminar'),
(56, 3, 'ingresos', 'leer'),
(57, 3, 'ingresos', 'escribir'),
(58, 3, 'ingresos', 'eliminar'),
(59, 3, 'presupuestos', 'leer'),
(60, 3, 'presupuestos', 'escribir'),
(61, 3, 'metas', 'leer'),
(62, 3, 'metas', 'escribir'),
(63, 3, 'gastos', 'leer'),
(64, 3, 'ingresos', 'leer'),
(65, 3, 'gastos', 'leer'),
(66, 3, 'gastos', 'escribir'),
(67, 3, 'gastos', 'eliminar'),
(68, 3, 'ingresos', 'leer'),
(69, 3, 'ingresos', 'escribir'),
(70, 3, 'ingresos', 'eliminar'),
(71, 3, 'presupuestos', 'leer'),
(72, 3, 'presupuestos', 'escribir'),
(73, 3, 'metas', 'leer'),
(74, 3, 'metas', 'escribir'),
(75, 4, 'usuarios', 'leer'),
(76, 4, 'usuarios', 'escribir'),
(77, 4, 'familias', 'leer'),
(78, 4, 'familias', 'escribir'),
(79, 4, 'grupos', 'leer'),
(80, 4, 'grupos', 'escribir');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `situacion`
--

CREATE TABLE `situacion` (
  `idSituacion` int(11) NOT NULL,
  `idUser` int(11) DEFAULT NULL,
  `rango_fechas` enum('mes','semana','año','intervalo') DEFAULT NULL,
  `saldo` decimal(10,2) NOT NULL,
  `total_gastos` decimal(10,2) NOT NULL,
  `total_ingresos` decimal(10,2) NOT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `idUser` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `alias` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contrasenya` varchar(255) DEFAULT NULL,
  `fecha_nacimiento` date NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `nivel_usuario` enum('superadmin','admin','usuario','registro') DEFAULT 'usuario',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado_usuario` enum('activo','inactivo') DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`idUser`, `nombre`, `apellido`, `alias`, `email`, `contrasenya`, `fecha_nacimiento`, `telefono`, `nivel_usuario`, `fecha_registro`, `estado_usuario`) VALUES
(1, 'Super', 'Admin', 'superadmin', 'superadmin@example.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '1970-01-01', '625418965', 'superadmin', '2024-10-04 10:03:48', 'activo'),
(2, 'Admin1', 'Family1', 'admin1', 'admin1@example.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '1980-01-01', '625418965', 'admin', '2024-10-04 10:04:06', 'activo'),
(5, 'Pareja1', 'Family1', 'pareja1', 'pareja1@example.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '1982-02-02', '625418965', 'usuario', '2024-10-04 10:05:28', 'activo'),
(6, 'Hijo1', 'Family1', 'hijo1', 'hijo1@example.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '2005-01-01', '625418965', 'usuario', '2024-10-04 10:05:28', 'activo'),
(7, 'Hijo2', 'Family1', 'hijo2', 'hijo2@example.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '2007-01-01', '625418965', 'usuario', '2024-10-04 10:05:28', 'activo'),
(8, 'Hijo3', 'Family1', 'hijo3', 'hijo3@example.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '2010-01-01', '625418965', 'usuario', '2024-10-04 10:05:28', 'activo'),
(9, 'Amigo1', 'Grupo1', 'amigo1', 'amigo1@example.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '1990-03-03', '625418965', 'usuario', '2024-10-04 10:05:49', 'activo'),
(10, 'Amigo2', 'Grupo1', 'amigo2', 'amigo2@example.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '1991-03-03', '625418965', 'usuario', '2024-10-04 10:05:49', 'activo'),
(11, 'Amigo3', 'Grupo1', 'amigo3', 'amigo3@example.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '1992-03-03', '625418965', 'admin', '2024-10-04 10:05:49', 'activo'),
(16, 'kk', 'kkkk', 'kk', 'kk@kk.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '2004-04-04', '455545545', 'admin', '2024-10-10 16:31:32', 'activo'),
(18, 'Lucía', 'Gómez', 'Lucy', 'lucy@lucy.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '1968-02-02', '123456789', 'usuario', '2024-10-12 17:42:45', 'activo'),
(19, 'Rafa', 'Gómez', 'RafaGomez', 'RafaGomez@RafaGomez.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '1982-02-02', '336265984', 'usuario', '2024-10-12 18:28:50', 'activo'),
(20, 'xxxxxx', 'xxxxxx', 'xxxxxx', 'xxx@xxx.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '2003-03-03', '777777777', 'admin', '2024-10-13 08:28:06', 'activo'),
(21, 'mmmmmm', 'mmmmmm', 'mmmmmm', 'mmmmmm@mmmmmm.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '1999-12-18', '222555555', 'usuario', '2024-10-13 08:34:52', 'activo'),
(26, '26Usu', '26Usu', '26Usu', '26Usu@26Usu.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '1980-06-03', '262662266', 'usuario', '2024-10-13 17:39:57', 'activo'),
(27, '27Usu', '27Usu', '27Usu', '27Usu@27Usu.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '2001-01-01', '222333333', 'admin', '2024-10-13 18:28:47', 'activo'),
(28, '28Usu', '28Usu', '28Usu', '28Usu@28Usu.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '2004-04-04', '626222222', 'usuario', '2024-10-14 16:28:08', 'activo'),
(29, '29Usu', '29Usu', '29Usu', '29Usu@29Usu.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '2001-03-03', '555222222', 'usuario', '2024-10-14 16:30:53', 'activo'),
(30, '50Usu', '50Usu', '50Usu', '50Usu@50Usu.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '1966-12-22', '639584745', 'usuario', '2024-10-14 17:24:50', 'activo'),
(31, '31Usu', '31Usu', '31Usu', '31Usu@31Usu.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '2008-08-08', '987454545', 'usuario', '2024-10-14 18:11:36', 'activo'),
(33, 'Euclides', 'Euclides', 'Euclides', 'Euclides@Euclides.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '1987-03-02', '582331145', 'usuario', '2024-10-20 09:25:11', 'activo'),
(34, 'Sebas', 'Sebas', 'Sebas', 'Sebas@Sebas.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '1999-03-02', '245636985', 'admin', '2024-10-20 09:27:59', 'activo'),
(35, 'Carlitos', 'Carlitos', 'Carlitos', 'Carlitos@Carlitos.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '1975-01-01', '458623515', 'usuario', '2024-10-20 09:29:23', 'activo'),
(36, 'Ramón', 'Ramón', 'Ramón', 'Ramon@Ramon.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '1999-02-03', '789512445', 'usuario', '2024-10-20 09:31:36', 'activo'),
(37, 'Andrés', 'Andrés', 'Andrés', 'Andres@Andres.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '2003-03-03', '526323214', 'admin', '2024-10-20 09:34:04', 'activo'),
(38, 'Sebastián', 'Sebastián', 'Sebastián', 'Sebas@123.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '2004-04-04', '754232659', 'admin', '2024-10-20 09:36:05', 'activo'),
(39, 'Tian', 'Tian', 'Tian', 'Tian@Tian.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '2000-03-03', '256336699', 'admin', '2024-10-20 09:40:26', 'activo'),
(40, 'Goro', 'Goro', 'Goro', 'Goro@Goro.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '1976-06-30', '568221144', 'usuario', '2024-10-20 09:42:58', 'activo'),
(41, 'Eduardito', 'Eduardito', 'Eduardito', 'Eduardito@Eduardito.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '1987-03-02', '351226699', 'admin', '2024-10-20 09:56:31', 'activo'),
(42, 'Nolo', 'Nolo', 'Nolo', 'Nolo@Nolo.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '2000-02-03', '321565854', 'admin', '2024-10-20 10:10:40', 'activo'),
(43, 'Externo', 'Externo', 'Externo', 'Externo@Externo.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '2000-12-02', '856232124', 'usuario', '2024-10-20 10:14:01', 'activo'),
(44, 'Eduardo', 'Eduardo', 'Eduardo', 'Eduardo@Eduardo.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '1973-01-01', '358652147', 'usuario', '2024-10-20 10:20:19', 'activo'),
(45, 'Esteban', 'Esteban', 'Esteban', 'Esteban@Esteban.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '1973-02-02', '352669988', 'usuario', '2024-10-20 10:47:20', 'activo'),
(46, 'Franjo', 'Franjo', 'Franjo', 'Franjo@Franjo.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '1973-05-01', '352487489', 'usuario', '2024-10-20 10:56:38', 'activo'),
(47, 'Luis', 'Luis', 'Luis', 'Luis@Luis.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '1973-05-05', '695332211', 'usuario', '2024-10-20 11:10:56', 'activo'),
(48, 'JavierI', 'JavierI', 'JavierI', 'JavierI@JavierI.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '1973-06-06', '695847574', 'usuario', '2024-10-20 11:17:56', 'activo'),
(49, 'Mónica', 'Mónica', 'Mónica', 'Monica@Monica.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '2000-02-03', '352669944', 'usuario', '2024-10-20 11:24:30', 'activo'),
(50, 'Mery', 'Mery', 'Mery', 'Mery@Mery.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '1979-09-09', '985624763', 'usuario', '2024-10-20 11:35:17', 'activo'),
(51, 'Luisote', 'Luisote', 'Luisote', 'Luisote@Luisote.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '1973-03-03', '698574868', 'usuario', '2024-10-20 11:49:59', 'activo'),
(52, 'Estela', 'Estela', 'Estela', 'Estela@Estela.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '1979-09-09', '325456987', 'usuario', '2024-10-20 11:51:53', 'activo'),
(53, 'Susanita', 'Susanita', 'Susanita', 'Susanita@Susanita.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '1974-06-02', '357986512', 'usuario', '2024-10-20 12:02:41', 'activo'),
(54, 'Natalia', 'Natalia', 'Natalia', 'Natalia@Natalia.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '1998-08-08', '586241458', 'usuario', '2024-10-20 13:29:53', 'activo'),
(55, 'Paula', 'Paula', 'Paula', 'Paula@Paula.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '1997-09-26', '789562519', 'usuario', '2024-10-20 13:33:15', 'activo'),
(56, 'Fran', 'Fran', 'Fran', 'Fran@Fran.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '1966-04-15', '245968547', 'usuario', '2024-10-20 13:38:38', 'activo'),
(57, 'Mamen', 'Mamen', 'Mamen', 'Mamen@Mamen.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '1973-04-04', '586958475', 'usuario', '2024-10-20 13:45:49', 'activo'),
(58, 'Laura', 'Laura', 'Laura', 'Laura@Laura.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '1984-01-06', '753264875', 'usuario', '2024-10-20 13:55:11', 'activo'),
(59, 'Silvia', 'Silvia', 'Silvia', 'Silvia@Silvia.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '1999-03-22', '351265847', 'usuario', '2024-10-20 13:58:07', 'activo'),
(60, 'Manolo', 'Manolo', 'Manolo', 'Manolo@Manolo.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '1995-05-05', '265323536', 'admin', '2024-10-20 14:03:29', 'activo'),
(61, 'Juan', 'Juan', 'Juan', 'Juan@Juan.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '1992-03-03', '785246859', 'admin', '2024-10-20 14:05:27', 'activo'),
(62, 'Mica', 'Mica', 'Mica', 'Mica@Mica.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '1971-09-01', '159698423', 'usuario', '2024-10-20 14:08:36', 'activo'),
(63, 'Lucio', 'Lucio', 'Lucio', 'Lucio@Lucio.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '2005-05-04', '265323254', 'admin', '2024-10-20 14:55:14', 'activo'),
(64, 'NuevoAdmin', 'ApellidoAdmin', 'nuevoadmin', 'nuevoadmin@example.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '1990-01-01', '123456789', 'admin', '2024-10-20 15:27:05', 'activo'),
(65, 'NuevoAdmin', 'ApellidoAdmin', 'nuevoadmin', 'nuevoadmin2@example.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '1990-01-01', '123456789', 'admin', '2024-10-20 15:28:56', 'activo'),
(66, 'Marianico', 'Marianico', 'Marianico', 'Marianico@Marianico.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '1999-12-22', '562336699', 'usuario', '2024-10-20 19:26:49', 'activo'),
(67, 'Mariana', 'Mariana', 'Mariana', 'Mariana@Mariana.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '2000-12-13', '625474142', 'usuario', '2024-10-20 19:28:40', 'activo'),
(70, 'UsuarioPrueba', 'ApellidoPrueba', 'AliasPrueba', 'nuevoemail@example.com', '$2y$10$WpcxKZnOTuV0R8WiW62rM.iw1n/JA09gjBu52cTYG.mbLdkBA5zrq', '1990-01-01', '123456789', 'usuario', '2024-10-20 21:08:55', 'activo'),
(72, 'PruebitaFinal', 'PruebitaFinal', 'PruebitaFinal', 'PruebitaFinal@PruebitaFinal.com', '$2y$10$.i0w23GsLlK9DMawpTJqdu5Vi2lOakC0RD3Lf97a4KdmaOBUuivzq', '1999-02-03', '622335588', 'admin', '2024-10-20 21:25:22', 'activo'),
(73, 'Rafalito', 'Rafalito', 'Rafalito', 'Rafalito@Rafalito.com', '$2y$10$B1v79/yJVqJrWDO84O9U5eldzUI0XBuxnXCzyav9dXIjrofetIskq', '1991-10-10', '524440033', 'admin', '2024-10-21 07:42:36', 'activo'),
(74, 'Mureti', 'Mureti', 'Mureti', 'Mureti@Mureti.com', '$2y$10$0Y1MG16R8jybQrhJcVSz7OKmXzDfUyLoCbGkQEevu/x17bm13aBmW', '1997-01-01', '624586235', 'admin', '2024-10-21 08:02:32', 'activo'),
(75, 'BeitaJ', 'BeitaJ', 'BeitaJ', 'BeitaJ@BeitaJ.com', '$2y$10$2rxhoTHFyAIbWVMWAwZ3iO54Axf9XPQYcZmpYJSPG9iMHgZ559RcC', '2002-01-01', '654321478', 'admin', '2024-10-21 09:03:38', 'activo'),
(76, 'MariCarla', 'MariCarla', 'MariCarla', 'MariCarla@MariCarla.com', '$2y$10$unawTivnQEP/1DPUzo64VOCZO9fxc3hic2sco97oiQWTeIVo/Zi.C', '2002-02-02', '633842651', 'usuario', '2024-10-21 10:45:37', 'activo'),
(77, 'MonoMonito', 'MonoMonito', 'MonoMonito', 'MonoMonito@MonoMonito.com', '$2y$10$CtU3Y5Re/u71HYvW7.kfzOTKb7p6SNca0JeSxWmY2xjCKEMmu/N/C', '2000-01-01', '685447788', 'usuario', '2024-10-21 10:51:30', 'activo'),
(78, 'Pichurritos', 'Pichurritos', 'Pichurritos', 'Pichurritos@Pichurritos.com', '$2y$10$YRf6QoeYKIXXLg4Gv.tL9unsRtikMz5jI920HrF4jka/pcqUzJVti', '2003-03-03', '698447852', 'admin', '2024-10-21 11:20:39', 'activo'),
(79, 'Usu79', 'Usu79', 'Usu79', 'Usu79@Usu79.com', '$2y$10$.QvDRl2dinjvg.blXeHGl.xIdXkjTsGtNfvgR6vGH4E4b2vnHFicO', '1979-09-09', '635221144', 'admin', '2024-10-21 11:42:34', 'activo'),
(80, 'Jaime80', 'Jaime80', 'Jaime80', 'Jaime80@Jaime80.com', '$2y$10$/ZCc7HbKMqV77DmbKOZ/0eaM6DxVKXN8vmzrgSRWusH2v9dPo8Gw.', '2001-01-01', '633254869', 'admin', '2024-10-21 12:07:21', 'activo'),
(81, 'Usu81', 'Usu81', 'Usu81', 'Usu81@Usu81.com', '$2y$10$11aqzD1DWS1.10Nyw87MGurjCKiyPCZJpJlq6eZR.F.eMgqv61Lq6', '1997-07-07', '633847595', 'admin', '2024-10-21 12:18:43', 'activo'),
(82, 'Usu82', 'Usu82', 'Usu82', 'Usu82@Usu82.com', '$2y$10$w8WbSl63N1N6puE9PKHzHOTemthr9ccBVSzgpwfxalH0CjRzqsW1K', '1991-10-10', '658998877', 'admin', '2024-10-21 12:36:35', 'activo'),
(85, 'OchentaYcinco', 'OchentaYcinco', 'OchentaYcinco', 'OchentaYcinco@OchentaYcinco.com', '$2y$10$zeLnKVeT06Z9vSPDhwxcueP9FNzSOEjPraAV18qpuDj8JdM3NJm7e', '1996-06-06', '655388475', 'registro', '2024-10-21 15:27:11', 'activo'),
(86, 'OchentaYSeis', 'OchentaYSeis', 'OchentaYSeis', 'OchentaYSeis@OchentaYSeis.com', '$2y$10$.VooByngCMZBWO.6Z1H7TeKB5JWd/YAJ2EpJg76cLQ94hzmDXyV7a', '1999-05-02', '654445566', 'admin', '2024-10-21 15:42:33', 'activo'),
(87, 'OchentaYsiete', 'OchentaYsiete', 'OchentaYsiete', 'OchentaYsiete@OchentaYsiete.com', '$2y$10$s4W47MQzn65gmjwvRAZUMOmRd5BarDvmoeHLbrNxW6YRWII0tkwqO', '1976-05-06', '652489563', 'admin', '2024-10-21 15:59:34', 'activo'),
(88, 'Calimero', 'Calimero', 'Calimero', 'Calimero@Calimero.com', '$2y$10$P0K9Hi0F4Ml/pk/GN/Gvbe/MEvNS8/cB0onOq7JHay.koEUEwb25K', '2003-03-03', '625333333', 'admin', '2024-10-21 18:07:12', 'activo'),
(89, 'Bobito', 'Bobito', 'Bobito', 'Bobito@Bobito.com', '$2y$10$cVtqNaP8rG0OdfGL7msOmeprvtLDeFoeGGafrLSBz99CsBg9RFFNS', '1973-06-06', '658585858', 'admin', '2024-10-21 18:15:54', 'activo'),
(90, 'Bobito90', 'Bobito90', 'Bobito90', 'Bobito90@Bobito90.com', '$2y$10$nJGEdhc9rmRAw6V5NUAjwu5m51.AK8Sh3rf67.JHWwDAShpQiRPVW', '1995-09-09', '958652545', 'admin', '2024-10-21 18:21:43', 'activo'),
(91, 'Bobito91', 'Bobito91', 'Bobito91', 'Bobito91@Bobito91.com', '$2y$10$CDQpi5LOsjuYIKaxKR/dzuD0n531zhkHQwqKcsPmDWOKKFqKglwsm', '2000-12-13', '584625847', 'usuario', '2024-10-21 18:24:58', 'activo');

--
-- Disparadores `usuarios`
--
DELIMITER $$
CREATE TRIGGER `auditar_creacion_usuario` AFTER INSERT ON `usuarios` FOR EACH ROW BEGIN
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario)
  VALUES ('INSERT', 'usuarios', NEW.idUser, NEW.idUser);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `auditar_usuarios` AFTER INSERT ON `usuarios` FOR EACH ROW BEGIN
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario, fecha)
  VALUES ('INSERT', 'usuarios', NEW.idUser, 'sistema', NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `auditar_usuarios_delete` AFTER DELETE ON `usuarios` FOR EACH ROW BEGIN
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario, fecha)
  VALUES ('DELETE', 'usuarios', OLD.idUser, OLD.idUser, NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `auditar_usuarios_delete_archivo` AFTER DELETE ON `usuarios` FOR EACH ROW BEGIN
  INSERT INTO auditoria_accesos_archivo (idUser, accion, fecha)
  VALUES (OLD.idUser, 'DELETE', NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `auditar_usuarios_insert` AFTER INSERT ON `usuarios` FOR EACH ROW BEGIN
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario, fecha)
  VALUES ('INSERT', 'usuarios', NEW.idUser, NEW.idUser, NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `auditar_usuarios_update` AFTER UPDATE ON `usuarios` FOR EACH ROW BEGIN
  INSERT INTO auditoria (accion, tabla_afectada, idRegistro, usuario, fecha)
  VALUES ('UPDATE', 'usuarios', NEW.idUser, NEW.idUser, NOW());
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios_familias`
--

CREATE TABLE `usuarios_familias` (
  `id` int(11) NOT NULL,
  `idUser` int(11) NOT NULL,
  `idFamilia` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios_familias`
--

INSERT INTO `usuarios_familias` (`id`, `idUser`, `idFamilia`) VALUES
(1, 1, 1),
(2, 2, 1),
(3, 5, 1),
(4, 6, 1),
(5, 7, 1),
(6, 8, 1),
(8, 27, 30),
(9, 31, 30),
(10, 16, 23),
(12, 20, 1),
(13, 19, 22),
(14, 19, 23),
(15, 33, 30),
(16, 34, 31),
(17, 41, 31),
(18, 42, 31),
(19, 44, 31),
(20, 51, 31),
(21, 55, 32),
(22, 59, 33),
(23, 60, 33),
(24, 61, 34),
(25, 63, 35),
(26, 63, 34),
(27, 65, 34),
(28, 65, 35),
(29, 65, 36),
(30, 65, 37),
(31, 38, 50),
(32, 27, 51),
(33, 41, 53),
(34, 61, 55),
(35, 72, 1),
(36, 72, 57),
(37, 73, 58),
(38, 74, 59),
(39, 74, 58),
(40, 75, 60),
(41, 75, 59),
(42, 86, 61),
(43, 87, 62),
(44, 87, 60),
(45, 89, 63);

--
-- Disparadores `usuarios_familias`
--
DELIMITER $$
CREATE TRIGGER `before_insert_usuarios_familias` BEFORE INSERT ON `usuarios_familias` FOR EACH ROW BEGIN
  IF (SELECT COUNT(*) FROM familias WHERE idFamilia = NEW.idFamilia) = 0 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La familia no existe';
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios_grupos`
--

CREATE TABLE `usuarios_grupos` (
  `idUser` int(11) NOT NULL,
  `idGrupo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios_grupos`
--

INSERT INTO `usuarios_grupos` (`idUser`, `idGrupo`) VALUES
(1, 1),
(2, 1),
(9, 1),
(10, 1),
(11, 1),
(11, 11),
(18, 11),
(19, 9),
(20, 1),
(20, 9),
(20, 11),
(36, 1),
(37, 14),
(41, 14),
(42, 14),
(43, 14),
(46, 14),
(55, 15),
(59, 16),
(60, 16),
(61, 17),
(63, 17),
(63, 18),
(65, 17),
(65, 18),
(65, 19),
(65, 20),
(72, 15),
(73, 21),
(74, 21),
(74, 22),
(75, 22),
(75, 23),
(86, 24),
(87, 23),
(87, 25),
(89, 26);

--
-- Disparadores `usuarios_grupos`
--
DELIMITER $$
CREATE TRIGGER `before_insert_usuarios_grupos` BEFORE INSERT ON `usuarios_grupos` FOR EACH ROW BEGIN
  IF (SELECT COUNT(*) FROM grupos WHERE idGrupo = NEW.idGrupo) = 0 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El grupo no existe';
  END IF;
END
$$
DELIMITER ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `administradores_familias`
--
ALTER TABLE `administradores_familias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idAdmin` (`idAdmin`,`idFamilia`),
  ADD KEY `fk_familia` (`idFamilia`);

--
-- Indices de la tabla `administradores_grupos`
--
ALTER TABLE `administradores_grupos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idGrupo` (`idGrupo`),
  ADD KEY `fk_admin_grupo` (`idAdmin`);

--
-- Indices de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD PRIMARY KEY (`idAuditoria`);

--
-- Indices de la tabla `auditoria_accesos`
--
ALTER TABLE `auditoria_accesos`
  ADD PRIMARY KEY (`idAcceso`),
  ADD KEY `idx_acceso_user_fecha` (`idUser`,`fecha`);

--
-- Indices de la tabla `auditoria_accesos_archivo`
--
ALTER TABLE `auditoria_accesos_archivo`
  ADD PRIMARY KEY (`idAcceso`),
  ADD KEY `idx_acceso_user_fecha` (`idUser`,`fecha`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`idCategoria`),
  ADD UNIQUE KEY `nombreCategoria` (`nombreCategoria`,`creado_por`,`tipo_categoria`),
  ADD KEY `creado_por` (`creado_por`);

--
-- Indices de la tabla `configuraciones`
--
ALTER TABLE `configuraciones`
  ADD PRIMARY KEY (`idConfig`),
  ADD UNIQUE KEY `clave_config` (`clave_config`);

--
-- Indices de la tabla `envio_refranes`
--
ALTER TABLE `envio_refranes`
  ADD PRIMARY KEY (`idEnvio`),
  ADD UNIQUE KEY `unique_envio` (`idUser`,`fecha_envio`,`momento`),
  ADD KEY `idRefran` (`idRefran`);

--
-- Indices de la tabla `familias`
--
ALTER TABLE `familias`
  ADD PRIMARY KEY (`idFamilia`),
  ADD UNIQUE KEY `nombre_familia` (`nombre_familia`),
  ADD KEY `idx_nombre_familia` (`nombre_familia`),
  ADD KEY `idx_familias_password` (`password`);

--
-- Indices de la tabla `gastos`
--
ALTER TABLE `gastos`
  ADD PRIMARY KEY (`idGasto`),
  ADD KEY `idUser` (`idUser`),
  ADD KEY `idCategoria` (`idCategoria`),
  ADD KEY `fk_gasto_familia` (`idFamilia`),
  ADD KEY `fk_gasto_grupo` (`idGrupo`),
  ADD KEY `idx_gastos_fecha_importe_concepto` (`fecha`,`importe`,`concepto`);

--
-- Indices de la tabla `grupos`
--
ALTER TABLE `grupos`
  ADD PRIMARY KEY (`idGrupo`),
  ADD UNIQUE KEY `nombre_grupo` (`nombre_grupo`),
  ADD KEY `idx_nombre_grupo` (`nombre_grupo`),
  ADD KEY `idx_grupos_password` (`password`),
  ADD KEY `idx_grupos_nombre` (`nombre_grupo`);

--
-- Indices de la tabla `ingresos`
--
ALTER TABLE `ingresos`
  ADD PRIMARY KEY (`idIngreso`),
  ADD KEY `idUser` (`idUser`),
  ADD KEY `idCategoria` (`idCategoria`),
  ADD KEY `idx_ingresos_fecha_importe` (`fecha`,`importe`),
  ADD KEY `idx_ingresos_concepto` (`concepto`),
  ADD KEY `idx_ingresos_fecha` (`fecha`),
  ADD KEY `idx_ingresos_importe_fecha` (`importe`,`fecha`),
  ADD KEY `fk_ingreso_familia` (`idFamilia`),
  ADD KEY `fk_ingreso_grupo` (`idGrupo`),
  ADD KEY `idx_ingresos_fecha_importe_concepto` (`fecha`,`importe`,`concepto`);

--
-- Indices de la tabla `menuadmin`
--
ALTER TABLE `menuadmin`
  ADD PRIMARY KEY (`idMenu`);

--
-- Indices de la tabla `news_letter_envios`
--
ALTER TABLE `news_letter_envios`
  ADD PRIMARY KEY (`idEnvio`),
  ADD KEY `idUser` (`idUser`),
  ADD KEY `idRefran` (`idRefran`);

--
-- Indices de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`idNotificacion`);

--
-- Indices de la tabla `permisos_cache`
--
ALTER TABLE `permisos_cache`
  ADD PRIMARY KEY (`idUser`,`nombrePermiso`,`tipoPermiso`);

--
-- Indices de la tabla `preferencias_usuarios`
--
ALTER TABLE `preferencias_usuarios`
  ADD PRIMARY KEY (`idPreferencia`),
  ADD KEY `idUser` (`idUser`);

--
-- Indices de la tabla `refranes`
--
ALTER TABLE `refranes`
  ADD PRIMARY KEY (`idRefran`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`idRol`);

--
-- Indices de la tabla `roles_permisos`
--
ALTER TABLE `roles_permisos`
  ADD PRIMARY KEY (`idPermiso`),
  ADD KEY `fk_rol` (`idRol`);

--
-- Indices de la tabla `situacion`
--
ALTER TABLE `situacion`
  ADD PRIMARY KEY (`idSituacion`),
  ADD KEY `idUser` (`idUser`),
  ADD KEY `idx_situacion_user_fechas` (`idUser`,`fecha_inicio`,`fecha_fin`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`idUser`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `unique_alias_email` (`alias`,`email`),
  ADD KEY `idx_usuario_nivel` (`nivel_usuario`),
  ADD KEY `idx_usuarios_nivel_familia_grupo` (`nivel_usuario`),
  ADD KEY `idx_usuarios_nivel_fecha` (`nivel_usuario`,`fecha_registro`),
  ADD KEY `idx_usuarios_fecha_registro` (`fecha_registro`),
  ADD KEY `idx_usuarios_email` (`email`),
  ADD KEY `idx_usuarios_nombre` (`nombre`),
  ADD KEY `idx_usuarios_apellido` (`apellido`),
  ADD KEY `idx_idUser` (`idUser`);

--
-- Indices de la tabla `usuarios_familias`
--
ALTER TABLE `usuarios_familias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idUser` (`idUser`),
  ADD KEY `idFamilia` (`idFamilia`);

--
-- Indices de la tabla `usuarios_grupos`
--
ALTER TABLE `usuarios_grupos`
  ADD PRIMARY KEY (`idUser`,`idGrupo`),
  ADD KEY `fk_usuario_grupo_grupo` (`idGrupo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `administradores_familias`
--
ALTER TABLE `administradores_familias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT de la tabla `administradores_grupos`
--
ALTER TABLE `administradores_grupos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  MODIFY `idAuditoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=985;

--
-- AUTO_INCREMENT de la tabla `auditoria_accesos`
--
ALTER TABLE `auditoria_accesos`
  MODIFY `idAcceso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=450;

--
-- AUTO_INCREMENT de la tabla `auditoria_accesos_archivo`
--
ALTER TABLE `auditoria_accesos_archivo`
  MODIFY `idAcceso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `idCategoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT de la tabla `configuraciones`
--
ALTER TABLE `configuraciones`
  MODIFY `idConfig` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `envio_refranes`
--
ALTER TABLE `envio_refranes`
  MODIFY `idEnvio` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `familias`
--
ALTER TABLE `familias`
  MODIFY `idFamilia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT de la tabla `gastos`
--
ALTER TABLE `gastos`
  MODIFY `idGasto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `grupos`
--
ALTER TABLE `grupos`
  MODIFY `idGrupo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `ingresos`
--
ALTER TABLE `ingresos`
  MODIFY `idIngreso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `menuadmin`
--
ALTER TABLE `menuadmin`
  MODIFY `idMenu` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `news_letter_envios`
--
ALTER TABLE `news_letter_envios`
  MODIFY `idEnvio` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `idNotificacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `preferencias_usuarios`
--
ALTER TABLE `preferencias_usuarios`
  MODIFY `idPreferencia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `refranes`
--
ALTER TABLE `refranes`
  MODIFY `idRefran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=149;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `idRol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `roles_permisos`
--
ALTER TABLE `roles_permisos`
  MODIFY `idPermiso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT de la tabla `situacion`
--
ALTER TABLE `situacion`
  MODIFY `idSituacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `idUser` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT de la tabla `usuarios_familias`
--
ALTER TABLE `usuarios_familias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `administradores_familias`
--
ALTER TABLE `administradores_familias`
  ADD CONSTRAINT `fk_admin` FOREIGN KEY (`idAdmin`) REFERENCES `usuarios` (`idUser`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_familia` FOREIGN KEY (`idFamilia`) REFERENCES `familias` (`idFamilia`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `administradores_grupos`
--
ALTER TABLE `administradores_grupos`
  ADD CONSTRAINT `fk_admin_grupo` FOREIGN KEY (`idAdmin`) REFERENCES `usuarios` (`idUser`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_admin_grupo_admin` FOREIGN KEY (`idAdmin`) REFERENCES `usuarios` (`idUser`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_admin_grupo_grupo` FOREIGN KEY (`idGrupo`) REFERENCES `grupos` (`idGrupo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_grupo` FOREIGN KEY (`idGrupo`) REFERENCES `grupos` (`idGrupo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `auditoria_accesos`
--
ALTER TABLE `auditoria_accesos`
  ADD CONSTRAINT `fk_user_acceso` FOREIGN KEY (`idUser`) REFERENCES `usuarios` (`idUser`) ON DELETE CASCADE;

--
-- Filtros para la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD CONSTRAINT `categorias_ibfk_1` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`idUser`);

--
-- Filtros para la tabla `envio_refranes`
--
ALTER TABLE `envio_refranes`
  ADD CONSTRAINT `envio_refranes_ibfk_1` FOREIGN KEY (`idRefran`) REFERENCES `refranes` (`idRefran`),
  ADD CONSTRAINT `envio_refranes_ibfk_2` FOREIGN KEY (`idUser`) REFERENCES `usuarios` (`idUser`);

--
-- Filtros para la tabla `gastos`
--
ALTER TABLE `gastos`
  ADD CONSTRAINT `fk_gasto_familia` FOREIGN KEY (`idFamilia`) REFERENCES `familias` (`idFamilia`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_gasto_grupo` FOREIGN KEY (`idGrupo`) REFERENCES `grupos` (`idGrupo`) ON DELETE CASCADE;

--
-- Filtros para la tabla `ingresos`
--
ALTER TABLE `ingresos`
  ADD CONSTRAINT `fk_ingreso_familia` FOREIGN KEY (`idFamilia`) REFERENCES `familias` (`idFamilia`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ingreso_grupo` FOREIGN KEY (`idGrupo`) REFERENCES `grupos` (`idGrupo`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ingresos_categoria` FOREIGN KEY (`idCategoria`) REFERENCES `categorias` (`idCategoria`) ON DELETE SET NULL,
  ADD CONSTRAINT `ingresos_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `usuarios` (`idUser`) ON DELETE SET NULL;

--
-- Filtros para la tabla `news_letter_envios`
--
ALTER TABLE `news_letter_envios`
  ADD CONSTRAINT `news_letter_envios_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `usuarios` (`idUser`) ON DELETE CASCADE,
  ADD CONSTRAINT `news_letter_envios_ibfk_2` FOREIGN KEY (`idRefran`) REFERENCES `refranes` (`idRefran`);

--
-- Filtros para la tabla `preferencias_usuarios`
--
ALTER TABLE `preferencias_usuarios`
  ADD CONSTRAINT `preferencias_usuarios_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `usuarios` (`idUser`) ON DELETE CASCADE;

--
-- Filtros para la tabla `roles_permisos`
--
ALTER TABLE `roles_permisos`
  ADD CONSTRAINT `fk_rol` FOREIGN KEY (`idRol`) REFERENCES `roles` (`idRol`) ON DELETE CASCADE;

--
-- Filtros para la tabla `situacion`
--
ALTER TABLE `situacion`
  ADD CONSTRAINT `situacion_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `usuarios` (`idUser`);

--
-- Filtros para la tabla `usuarios_familias`
--
ALTER TABLE `usuarios_familias`
  ADD CONSTRAINT `usuarios_familias_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `usuarios` (`idUser`) ON DELETE CASCADE,
  ADD CONSTRAINT `usuarios_familias_ibfk_2` FOREIGN KEY (`idFamilia`) REFERENCES `familias` (`idFamilia`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuarios_grupos`
--
ALTER TABLE `usuarios_grupos`
  ADD CONSTRAINT `fk_usuario_grupo_grupo` FOREIGN KEY (`idGrupo`) REFERENCES `grupos` (`idGrupo`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_usuario_grupo_usuario` FOREIGN KEY (`idUser`) REFERENCES `usuarios` (`idUser`) ON DELETE CASCADE;

DELIMITER $$
--
-- Eventos
--
CREATE DEFINER=`root`@`localhost` EVENT `limpiar_cache_permisos` ON SCHEDULE EVERY 1 HOUR STARTS '2024-10-16 09:29:52' ON COMPLETION NOT PRESERVE ENABLE DO DELETE FROM permisos_cache WHERE TIMESTAMPDIFF(HOUR, fecha_cache, NOW()) > 1$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
