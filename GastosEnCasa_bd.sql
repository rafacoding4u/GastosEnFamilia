-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 18-10-2024 a las 18:59:28
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
(5, 2, 10),
(6, 2, 11),
(7, 2, 12),
(9, 2, 14),
(10, 2, 15),
(4, 16, 9),
(8, 16, 13),
(11, 16, 16),
(12, 16, 19),
(13, 16, 20),
(14, 20, 23),
(15, 20, 24),
(18, 27, 30);

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
(2, 1, 1);

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
(455, 'DELETE', 'usuarios', 17, '17', '2024-10-18 16:53:32');

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
(355, 1, 'login', '2024-10-18 16:34:34');

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
(1, 'Familia1', '$2y$10$YE/fFb8WWDsnPmpGhDg.iuvFb1aHYQiyBM6oxl9Dmj8ER.iMgh0yG', 'activo'),
(16, 'Familia juan', '$2y$10$YE/fFb8WWDsnPmpGhDg.iuvFb1aHYQiyBM6oxl9Dmj8ER.iMgh0yG', 'activo'),
(22, 'mmmmmm', '$2y$10$YE/fFb8WWDsnPmpGhDg.iuvFb1aHYQiyBM6oxl9Dmj8ER.iMgh0yG', 'activo'),
(23, 'vvvvv', '$2y$10$YE/fFb8WWDsnPmpGhDg.iuvFb1aHYQiyBM6oxl9Dmj8ER.iMgh0yG', 'activo'),
(24, 'buena', '$2y$10$YE/fFb8WWDsnPmpGhDg.iuvFb1aHYQiyBM6oxl9Dmj8ER.iMgh0yG', 'activo'),
(30, '27Fam', '$2y$10$YE/fFb8WWDsnPmpGhDg.iuvFb1aHYQiyBM6oxl9Dmj8ER.iMgh0yG', 'activo');

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
(1, 'Grupo1', '$2y$10$YE/fFb8WWDsnPmpGhDg.iuvFb1aHYQiyBM6oxl9Dmj8ER.iMgh0yG'),
(9, 'Grupitoo lllll', '$2y$10$YE/fFb8WWDsnPmpGhDg.iuvFb1aHYQiyBM6oxl9Dmj8ER.iMgh0yG'),
(11, 'B3', '$2y$10$YE/fFb8WWDsnPmpGhDg.iuvFb1aHYQiyBM6oxl9Dmj8ER.iMgh0yG');

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
(3, 'usuario');

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
(12, 3, 'ingresos', 'leer');

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
  `nivel_usuario` enum('superadmin','admin','usuario') DEFAULT 'usuario',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado_usuario` enum('activo','inactivo') DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`idUser`, `nombre`, `apellido`, `alias`, `email`, `contrasenya`, `fecha_nacimiento`, `telefono`, `nivel_usuario`, `fecha_registro`, `estado_usuario`) VALUES
(1, 'Super', 'Admin', 'superadmin', 'superadmin@example.com', '$2y$10$YE/fFb8WWDsnPmpGhDg.iuvFb1aHYQiyBM6oxl9Dmj8ER.iMgh0yG', '1970-01-01', '625418965', 'superadmin', '2024-10-04 10:03:48', 'activo'),
(2, 'Admin1', 'Family1', 'admin1', 'admin1@example.com', '$2y$10$YE/fFb8WWDsnPmpGhDg.iuvFb1aHYQiyBM6oxl9Dmj8ER.iMgh0yG', '1980-01-01', '625418965', 'admin', '2024-10-04 10:04:06', 'activo'),
(5, 'Pareja1', 'Family1', 'pareja1', 'pareja1@example.com', '$2y$10$YE/fFb8WWDsnPmpGhDg.iuvFb1aHYQiyBM6oxl9Dmj8ER.iMgh0yG', '1982-02-02', '625418965', 'usuario', '2024-10-04 10:05:28', 'activo'),
(6, 'Hijo1', 'Family1', 'hijo1', 'hijo1@example.com', '$2y$10$YE/fFb8WWDsnPmpGhDg.iuvFb1aHYQiyBM6oxl9Dmj8ER.iMgh0yG', '2005-01-01', '625418965', 'usuario', '2024-10-04 10:05:28', 'activo'),
(7, 'Hijo2', 'Family1', 'hijo2', 'hijo2@example.com', '$2y$10$YE/fFb8WWDsnPmpGhDg.iuvFb1aHYQiyBM6oxl9Dmj8ER.iMgh0yG', '2007-01-01', '625418965', 'usuario', '2024-10-04 10:05:28', 'activo'),
(8, 'Hijo3', 'Family1', 'hijo3', 'hijo3@example.com', '$2y$10$YE/fFb8WWDsnPmpGhDg.iuvFb1aHYQiyBM6oxl9Dmj8ER.iMgh0yG', '2010-01-01', '625418965', 'usuario', '2024-10-04 10:05:28', 'activo'),
(9, 'Amigo1', 'Grupo1', 'amigo1', 'amigo1@example.com', '$2y$10$YE/fFb8WWDsnPmpGhDg.iuvFb1aHYQiyBM6oxl9Dmj8ER.iMgh0yG', '1990-03-03', '625418965', 'usuario', '2024-10-04 10:05:49', 'activo'),
(10, 'Amigo2', 'Grupo1', 'amigo2', 'amigo2@example.com', '$2y$10$YE/fFb8WWDsnPmpGhDg.iuvFb1aHYQiyBM6oxl9Dmj8ER.iMgh0yG', '1991-03-03', '625418965', 'usuario', '2024-10-04 10:05:49', 'activo'),
(11, 'Amigo3', 'Grupo1', 'amigo3', 'amigo3@example.com', '$2y$10$YE/fFb8WWDsnPmpGhDg.iuvFb1aHYQiyBM6oxl9Dmj8ER.iMgh0yG', '1992-03-03', '625418965', 'admin', '2024-10-04 10:05:49', 'activo'),
(16, 'kk', 'kkkk', 'kk', 'kk@kk.com', '$2y$10$YE/fFb8WWDsnPmpGhDg.iuvFb1aHYQiyBM6oxl9Dmj8ER.iMgh0yG', '2004-04-04', '455545545', 'admin', '2024-10-10 16:31:32', 'activo'),
(18, 'Lucía', 'Gómez', 'Lucy', 'lucy@lucy.com', '$2y$10$YE/fFb8WWDsnPmpGhDg.iuvFb1aHYQiyBM6oxl9Dmj8ER.iMgh0yG', '1968-02-02', '123456789', 'usuario', '2024-10-12 17:42:45', 'activo'),
(19, 'Rafa', 'Gómez', 'RafaGomez', 'RafaGomez@RafaGomez.com', '$2y$10$YE/fFb8WWDsnPmpGhDg.iuvFb1aHYQiyBM6oxl9Dmj8ER.iMgh0yG', '1982-02-02', '336265984', 'usuario', '2024-10-12 18:28:50', 'activo'),
(20, 'xxxxxx', 'xxxxxx', 'xxxxxx', 'xxx@xxx.com', '$2y$10$YE/fFb8WWDsnPmpGhDg.iuvFb1aHYQiyBM6oxl9Dmj8ER.iMgh0yG', '2003-03-03', '777777777', 'admin', '2024-10-13 08:28:06', 'activo'),
(21, 'mmmmmm', 'mmmmmm', 'mmmmmm', 'mmmmmm@mmmmmm.com', '$2y$10$YE/fFb8WWDsnPmpGhDg.iuvFb1aHYQiyBM6oxl9Dmj8ER.iMgh0yG', '1999-12-18', '222555555', 'usuario', '2024-10-13 08:34:52', 'activo'),
(26, '26Usu', '26Usu', '26Usu', '26Usu@26Usu.com', '$2y$10$YE/fFb8WWDsnPmpGhDg.iuvFb1aHYQiyBM6oxl9Dmj8ER.iMgh0yG', '1980-06-03', '262662266', 'usuario', '2024-10-13 17:39:57', 'activo'),
(27, '27Usu', '27Usu', '27Usu', '27Usu@27Usu.com', '$2y$10$YE/fFb8WWDsnPmpGhDg.iuvFb1aHYQiyBM6oxl9Dmj8ER.iMgh0yG', '2001-01-01', '222333333', 'admin', '2024-10-13 18:28:47', 'activo'),
(28, '28Usu', '28Usu', '28Usu', '28Usu@28Usu.com', '$2y$10$YE/fFb8WWDsnPmpGhDg.iuvFb1aHYQiyBM6oxl9Dmj8ER.iMgh0yG', '2004-04-04', '626222222', 'usuario', '2024-10-14 16:28:08', 'activo'),
(29, '29Usu', '29Usu', '29Usu', '29Usu@29Usu.com', '$2y$10$Jce6chf5g155y5siM3HVkupRg8iJoC4rhMaxFHg.UO9ryNITo6pqy', '2001-03-03', '555222222', 'usuario', '2024-10-14 16:30:53', 'activo'),
(30, '50Usu', '50Usu', '50Usu', '50Usu@50Usu.com', '$2y$10$B0QWKa2P1dWoI0tlPFY5j.P.cKZSqokCN5E0PlSkLUy1kjwNtBU66', '1966-12-22', '639584745', 'usuario', '2024-10-14 17:24:50', 'activo'),
(31, '31Usu', '31Usu', '31Usu', '31Usu@31Usu.com', '$2y$10$3xGESvqM7kMyOQF3Y4vvE.vjoKtcp3FKHT.IpQSkjVVLi/rhgZfgq', '2008-08-08', '987454545', 'usuario', '2024-10-14 18:11:36', 'activo');

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
DELIMITER $$
CREATE TRIGGER `before_insert_usuario` BEFORE INSERT ON `usuarios` FOR EACH ROW BEGIN
  -- Validar si la familia existe
  IF NEW.idFamilia IS NOT NULL AND 
     (SELECT COUNT(*) FROM familias WHERE idFamilia = NEW.idFamilia) = 0 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La familia no existe';
  END IF;

  -- Validar si el grupo existe
  IF NEW.idGrupo IS NOT NULL AND 
     (SELECT COUNT(*) FROM grupos WHERE idGrupo = NEW.idGrupo) = 0 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El grupo no existe';
  END IF;
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
(12, 20, 1);

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
(20, 11);

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
  ADD UNIQUE KEY `idAdmin` (`idAdmin`,`idFamilia`);

--
-- Indices de la tabla `administradores_grupos`
--
ALTER TABLE `administradores_grupos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idGrupo` (`idGrupo`),
  ADD KEY `fk_admin_grupo_admin` (`idAdmin`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `administradores_grupos`
--
ALTER TABLE `administradores_grupos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  MODIFY `idAuditoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=456;

--
-- AUTO_INCREMENT de la tabla `auditoria_accesos`
--
ALTER TABLE `auditoria_accesos`
  MODIFY `idAcceso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=356;

--
-- AUTO_INCREMENT de la tabla `auditoria_accesos_archivo`
--
ALTER TABLE `auditoria_accesos_archivo`
  MODIFY `idAcceso` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `idFamilia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `gastos`
--
ALTER TABLE `gastos`
  MODIFY `idGasto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `grupos`
--
ALTER TABLE `grupos`
  MODIFY `idGrupo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

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
  MODIFY `idPreferencia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `refranes`
--
ALTER TABLE `refranes`
  MODIFY `idRefran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=149;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `idRol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `roles_permisos`
--
ALTER TABLE `roles_permisos`
  MODIFY `idPermiso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `situacion`
--
ALTER TABLE `situacion`
  MODIFY `idSituacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `idUser` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de la tabla `usuarios_familias`
--
ALTER TABLE `usuarios_familias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `administradores_grupos`
--
ALTER TABLE `administradores_grupos`
  ADD CONSTRAINT `fk_admin_grupo_admin` FOREIGN KEY (`idAdmin`) REFERENCES `usuarios` (`idUser`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_admin_grupo_grupo` FOREIGN KEY (`idGrupo`) REFERENCES `grupos` (`idGrupo`) ON DELETE CASCADE ON UPDATE CASCADE;

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
