-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 06-11-2024 a las 23:45:12
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
-- Base de datos: `lascuentasclaras`
--

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
(4, 2, 97),
(7, 2, 98),
(12, 2, 101),
(5, 3, 2),
(22, 3, 106),
(19, 140, 104),
(20, 143, 105),
(23, 145, 107),
(25, 148, 105),
(24, 148, 108),
(26, 149, 108),
(27, 150, 109),
(28, 150, 110);

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
(2, 61, 3),
(2, 65, 4),
(2, 67, 6),
(2, 74, 7),
(2, 77, 8),
(2, 73, 9),
(137, 79, 12),
(2, 72, 13),
(137, 83, 16),
(2, 86, 19),
(2, 71, 24),
(138, 69, 25),
(138, 91, 26),
(140, 92, 27),
(143, 93, 28),
(3, 94, 29),
(145, 95, 30),
(148, 96, 31),
(148, 95, 32),
(149, 96, 33),
(150, 97, 34),
(150, 98, 35);

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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `definiciones_financieras`
--

CREATE TABLE `definiciones_financieras` (
  `idDeffin` int(11) NOT NULL,
  `concepto_eco` text NOT NULL,
  `fecha_ultimo_uso` timestamp NULL DEFAULT NULL,
  `texto_concepto_eco` varchar(255) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `definiciones_financieras`
--

INSERT INTO `definiciones_financieras` (`idDeffin`, `concepto_eco`, `fecha_ultimo_uso`, `texto_concepto_eco`, `fecha_creacion`) VALUES
(1, 'Inflación', NULL, 'La inflación es el aumento generalizado y sostenido de los precios de bienes y servicios en un país durante un período de tiempo.', '2024-10-25 14:38:31');

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
(1, 'Familia1', 'Temp@1234ComplexLa7890@2', 'activo'),
(2, 'Familia22', 'Temp@1234ComplexLa7890@2', 'activo'),
(97, 'Familia97', '$2y$10$73idQDfSf..vDnc5aGeA3ueUfnDQHFDfhN8vr98Smawp5yZUJ6Mg.', 'activo'),
(98, 'Familias98', '$2y$10$eOwV0HWJx66KsjUtpBFMdelvWcJnQdSzBnalbP5aBgOBJ7uNpTVIa', 'activo'),
(101, 'Familia101', '$2y$10$/pguidulkTM15xD1Ho6NqurjZBaqEVzQe6JlRQy/KQE7FyFMFjtqC', 'activo'),
(104, 'Familia104', '$2y$10$.WWRvlpSCBmarFcv.hA/eeavANFx5Uwr2.fvtNffnXMbXWXQeziEy', 'activo'),
(105, 'Familia105', '$2y$10$miAbsjV9EGkPu89xA1nz5.I9zjiwN18q8YqPFlrxyydXcamAZ5toC', 'activo'),
(106, 'Familia106', '$2y$10$4PbQRMQBjuTFIWZmqT9W6u9eduBxzBHKPpbf.ixm2K26eN0v9561S', 'activo'),
(107, 'Familia107', '$2y$10$KzQvqjrbW8EjQcwja.P90uPJk3SwIoXYNuT2xgYz7wp/h/pHbqD6e', 'activo'),
(108, 'Familia108', '$2y$10$QsEbK79gqi7kD7egjJbPxujItLfUcx/Nc9KhbqVJ7NKmQUhQDBp1O', 'activo'),
(109, 'Familia109', '$2y$10$RLDpjINuTN0FBBqOlZxrGuQWw4AMQJJZBchd7WtmMG/BSaqt7QhSi', 'activo'),
(110, 'Familia110', '$2y$10$Iy93Ota.86Nt2/bTjHzPSOWkU2k.dzwRikwvJaKD4/JC/CbKz/3Y6', 'activo');

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
(1, 'Grupo1', 'Temp@1234ComplexLa7890@2'),
(2, 'Grupo2', 'Temp@1234ComplexLa7890@2'),
(61, 'Grupos61', '$2y$10$45KqXAoYaSOosoQeI0/D4eZlxp0oDnrvETxdYRe9w9dpZExz7TWse'),
(62, 'Grupo62', '$2y$10$v6maTJF6.4qkEC05sDbvAuAX3UOfvF8IfaBV9cur7ngmu1FZh247e'),
(63, 'Grupo63', '$2y$10$jDuqC05udrXYlewkhZnCO.Dgmv039.RsL1iP7fRW5Cz0wla3zopP6'),
(65, 'Grupo65', '$2y$10$sToUra/DXfh3mvry.Ue7MeeDP2ryb.c2iitVMUdONDVhsRvLMfqM.'),
(67, 'Grupo67', '$2y$10$zdeOQAYkWsv9Yty6OLLRxe.luxgpjRGkHHDQ5aAH2SZiT5wFQxwb6'),
(69, 'Grupo69', '$2y$10$2HmRWdajUyqxsxyJepJTDOa4X7UV6rOQEC5CEkx0ir5TZ8lvZRmq.'),
(71, 'Grupo71', '$2y$10$AJVvclw3YLmvJ4OyLouyiOKJpK0NWYktyuBEf01e3eR/9x4n5Wk6u'),
(72, 'Grupo72', '$2y$10$11QVhj8jl6ZmCOKW1ubqKOjtK2JgfYBxEK9PC1kuvvXr9fiENwWL2'),
(73, 'Grupo73', '$2y$10$K1dRTI50c29jj3EOzEZULOB5RLhKRQr0c1X14i.IHFnvaTu7kCc02'),
(74, 'Grupo74', '$2y$10$Euli4uNarkuwAkK.YPhh7OLBo28JvwHBsGaze433jyL86D2d8juvO'),
(75, 'Grupo75', '$2y$10$dbU/4G0vwu/b3mpFflvtAO2ikjw.PVz0M6DaA77X9WVvh2gs6G6Oi'),
(77, 'Grupo77', '$2y$10$k6NsDHO7ocebhjqmi6NINO/anfFB.rCUfEOZufvSJ9z7QHrMfl3M2'),
(79, 'Grupo79', '$2y$10$WdiuJdbiWQ.g9WzDnG3FyeyFcHPTeMFDyCwu/jaIq4Ws/Ybxqf0DS'),
(80, 'Grupo80', '$2y$10$K70g8VtVavPnh.0c8cIwKeJEeFjdn6dU4N4ynVokcmWKFMVIliwr2'),
(81, 'Grupo81', '$2y$10$.F9FmKDwx6Qy3bHQnhY5Jun5SsSls.O5h5pUw2NYs9SLVZwj72rHi'),
(82, 'Grupo82', '$2y$10$XsUE8F3wmDCB1RDl5J0xMeTOITOKg3OvhjPUNuvqxLwvV1Drg5Yxq'),
(83, 'Grupo83', '$2y$10$cZ7hHLwdg5cOXWew0cnQYeUpv2ZAx.GY5QpEPjYRM5DXXgZyDfn3e'),
(86, 'Grupo86', '$2y$10$wJQ1x34xZMm9wAJy1DSTeOV8.XKsTQZNdI82yZH2KNBrSWDvNH5/2'),
(91, 'Grupo91', '$2y$10$lJbexs0QywvviDaJNhiw4./lEvkHFt/HIbMKBhfSbVS5lgLk73GIm'),
(92, 'Grupo92', '$2y$10$t11OrhYvztnvey.KvOusJe.XCsC1XVLu5GHvXhaw87AoelikNacvK'),
(93, 'Grupo93', '$2y$10$31e2h1Dje7uAXADaqBHEWODdft8UWOrYnzla6hrUwQ2L9yFO1j4bm'),
(94, 'Grupo94', '$2y$10$GPZz/MdzK/7fZvQooDeksue8gjazb0bY2wncvVM8K10wQyqogiurW'),
(95, 'Grupo95', '$2y$10$4GI3oX45nZx7EkgsDo2fXezRh7SKW2itUzVE/g.69LknB0TpRDXa6'),
(96, 'Grupo96', '$2y$10$MDpAi6IvkgnHhwtA34HZReRZUfQnstEKRgNV3kZKmlUziRd1qyJ7C'),
(97, 'Grupo97', '$2y$10$ef2gyHhHW/WHwB95ZQE9yeHlgEwuIWmigc/C9pL6c8JIWPt.r2Aly'),
(98, 'Grupo98', '$2y$10$OQfVo8uqMf4Gp44JOM92yuu9JH5ZBJX7YsXcLas3XT6ZP5RKLkuBW');

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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `news_letter_envios`
--

CREATE TABLE `news_letter_envios` (
  `idEnvio` int(11) NOT NULL,
  `idUser` int(11) NOT NULL,
  `fecha_envio` timestamp NOT NULL DEFAULT current_timestamp(),
  `idDeffin` int(11) DEFAULT NULL,
  `saldo_total` decimal(10,2) NOT NULL,
  `gastos_totales` decimal(10,2) NOT NULL,
  `ingresos_totales` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `news_letter_envios`
--

INSERT INTO `news_letter_envios` (`idEnvio`, `idUser`, `fecha_envio`, `idDeffin`, `saldo_total`, `gastos_totales`, `ingresos_totales`) VALUES
(1, 1, '2024-10-25 14:42:51', 1, 5000.00, 1500.00, 2000.00);

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
(81, 1, 'usuarios', 'leer'),
(82, 1, 'usuarios', 'escribir'),
(83, 1, 'usuarios', 'eliminar'),
(84, 1, 'familias', 'leer'),
(85, 1, 'familias', 'escribir'),
(86, 1, 'familias', 'eliminar'),
(87, 1, 'grupos', 'leer'),
(88, 1, 'grupos', 'escribir'),
(89, 1, 'grupos', 'eliminar'),
(90, 1, 'gastos', 'leer'),
(91, 1, 'gastos', 'escribir'),
(92, 1, 'gastos', 'eliminar'),
(93, 1, 'ingresos', 'leer'),
(94, 1, 'ingresos', 'escribir'),
(95, 1, 'ingresos', 'eliminar'),
(96, 1, 'metas', 'leer'),
(97, 1, 'metas', 'escribir'),
(98, 1, 'metas', 'eliminar'),
(99, 1, 'presupuestos', 'leer'),
(100, 1, 'presupuestos', 'escribir'),
(101, 1, 'presupuestos', 'eliminar'),
(102, 2, 'usuarios', 'leer'),
(103, 2, 'usuarios', 'escribir'),
(104, 2, 'usuarios', 'eliminar'),
(105, 2, 'familias', 'leer'),
(106, 2, 'familias', 'escribir'),
(107, 2, 'familias', 'eliminar'),
(108, 2, 'grupos', 'leer'),
(109, 2, 'grupos', 'escribir'),
(110, 2, 'grupos', 'eliminar'),
(111, 2, 'gastos', 'leer'),
(112, 2, 'gastos', 'escribir'),
(113, 2, 'gastos', 'eliminar'),
(114, 2, 'ingresos', 'leer'),
(115, 2, 'ingresos', 'escribir'),
(116, 2, 'ingresos', 'eliminar'),
(117, 2, 'metas', 'leer'),
(118, 2, 'metas', 'escribir'),
(119, 2, 'presupuestos', 'leer'),
(120, 2, 'presupuestos', 'escribir'),
(121, 3, 'usuarios', 'leer'),
(122, 3, 'familias', 'leer'),
(123, 3, 'grupos', 'leer'),
(124, 3, 'gastos', 'leer'),
(125, 3, 'gastos', 'escribir'),
(126, 3, 'gastos', 'eliminar'),
(127, 3, 'ingresos', 'leer'),
(128, 3, 'ingresos', 'escribir'),
(129, 3, 'ingresos', 'eliminar'),
(130, 3, 'metas', 'leer'),
(131, 3, 'metas', 'escribir'),
(132, 3, 'presupuestos', 'leer'),
(133, 3, 'presupuestos', 'escribir'),
(134, 4, 'familias', 'leer'),
(135, 4, 'familias', 'escribir'),
(136, 4, 'grupos', 'leer'),
(137, 4, 'grupos', 'escribir');

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
  `contrasenya` varchar(255) NOT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `nivel_usuario` enum('superadmin','admin','usuario','registro') NOT NULL DEFAULT 'usuario',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado_usuario` enum('activo','inactivo') NOT NULL DEFAULT 'activo',
  `password_premium` varchar(255) DEFAULT NULL,
  `es_premium` varchar(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`idUser`, `nombre`, `apellido`, `alias`, `email`, `contrasenya`, `fecha_nacimiento`, `telefono`, `nivel_usuario`, `fecha_registro`, `estado_usuario`, `password_premium`, `es_premium`) VALUES
(1, 'Usuario1', 'Usuario1', 'Usuario1', 'Usuario1@Usuario1.com', 'Temp@1234ComplexLa7890@2', NULL, NULL, 'usuario', '2024-10-25 12:04:06', 'activo', 'Temp@1234ComplexLa7890@2', NULL),
(2, 'Admin1', 'Admin1', 'admin1', 'admin1@admin1.com', 'Temp@1234ComplexLa7890@2', NULL, NULL, 'admin', '2024-10-25 12:08:10', 'activo', 'Temp@1234ComplexLa7890@2', NULL),
(3, 'Tres', 'Tres', 'Tres', 'Tres@Tres.com', 'Temp@1234ComplexLa7890@2', NULL, '235698474', 'admin', '2024-10-25 12:11:32', 'activo', NULL, NULL),
(136, 'Cuatro', 'Cuatro', 'Cuatro', 'Cuatro@Cuatro.com', '$2y$10$89wDQq4KWrLG4eOWEdOTwuabgZQICWv3bYRhchAM5pxMOYf568Icy', NULL, NULL, 'superadmin', '2024-11-04 14:50:31', 'activo', '$2y$10$8KE1p41mQ713SQ/zhm/UXu.iRUF6GNlheCCdE0OAI3Vmhrh.dzeOe', NULL),
(137, 'User4', 'User4', 'User4', 'User4@User4.com', '$2y$10$8AlF11hYIlDtoCqvWbIbXeP4JTdK02/xL.KWirPDPFdUNXArDeC2u', NULL, NULL, 'admin', '2024-11-04 15:46:04', 'activo', '$2y$10$qgFQ5Udo7M6rTeCyExsJ5eQTotOPS6zvGDiuliP2vYkm7zcD3jkqC', NULL),
(138, 'CientoTreintayOcho', 'CientoTreintayOcho', 'CientoTreintayOcho', 'CientoTreintayOcho@CientoTreintayOcho.com', '$2y$10$PKW6wuNCo6PW/DTuDBvkd.kb9jxTPKnA9l5mri.Kf79gViaesG08a', NULL, '698547423', 'admin', '2024-11-04 18:02:59', 'activo', '$2y$10$OzqReCNLneSfFh0CAN1mEu34FKu3WR9/PLB3A6XCpLXNUHWbXizTu', NULL),
(139, 'CientoTreintayNueve', 'CientoTreintayNueveve', 'CientoTreintayNueve', 'CientoTreintayNueve@CientoTreintayNueve.com', '$2y$10$6rBJI9fjbZT4pe9jHYSER.upIr6IXH4yqRbxI9wdQCNRhj6G2tSuC', '2005-02-03', '695887744', 'admin', '2024-11-05 19:27:33', 'activo', NULL, NULL),
(140, 'CientoCuarenta', 'CientoCuarenta', 'CientoCuarenta', 'CientoCuarenta@CientoCuarenta.com', '$2y$10$PXn0H9lvBeHGQdLxFzI7zehgNPn18LUUSGCe/Hdg/R1ztMgVeF2H2', '1993-03-03', '635889945', 'admin', '2024-11-05 19:30:46', 'activo', NULL, NULL),
(141, 'CientoCuarentayUno', 'CientoCuarentayUno', 'CientoCuarentayUno', 'CientoCuarentayUno@CientoCuarentayUno.com', '$2y$10$7ol02Xnzx7hz19eVvQ4aYOsQRGtCspkmzKr2cg9kYKkLqGEz3Mbb2', '1975-02-02', '659332211', 'admin', '2024-11-06 11:41:34', 'activo', NULL, NULL),
(143, 'Prueba', 'Prueba', 'Prueba', 'Prueba@Prueba.com', '$2y$10$8EGiZyodnkyWTSR8kohOHOzUV6.25W22ah/Y4IadiYR96WgQ6Yc9S', '2000-06-03', '362756215', 'admin', '2024-11-06 14:01:07', 'activo', NULL, NULL),
(145, 'CientoCuarentayCinco', 'CientoCuarentayCinco', 'CientoCuarentayCinco', 'CientoCuarentayCinco@CientoCuarentayCinco.com', '$2y$10$3NdeoNQVbCrrBevtMjL5G.8ed2LePXHvUvpgsXn0RSFrUD.3t7Gxy', NULL, '', 'admin', '2024-11-06 17:50:06', 'activo', '$2y$10$1FP9SZIlzvvVRSsfYIQEfezQ.zpOiJMuO3Z.GeGNXxBZ1XKoYJuY2', NULL),
(146, 'CientoCuaretaySeis', 'CientoCuaretaySeis', 'CientoCuaretaySeis', 'CientoCuaretaySeis@CientoCuaretaySeis.com', '$2y$10$b3z0LSsLhpFszJCnf7OmF.qJFv//7/ChWyD6rQ/tt.d76H1dbaxlS', NULL, '', 'admin', '2024-11-06 18:43:52', 'activo', '$2y$10$Imc420AXI/GEzVpyyOUYxu6p3KoCCfSQGR4eXanI9.dtiwhBLnbly', NULL),
(148, 'CientoCuarentayOcho', 'CientoCuarentayOcho', 'CientoCuarentayOcho', 'CientoCuarentayOcho@CientoCuarentayOcho.com', '$2y$10$uSecX2Y82HNzjdwrXZZOWudubp79ZYz55UmUdeo//hrYZsS4.YxuG', NULL, NULL, 'admin', '2024-11-06 22:18:15', 'activo', '$2y$10$eD4S9k20yMRYUQJnI.5zz.CJtgEtOrcj817/iZ2hxsbcbf/r5IuEW', NULL),
(149, 'CientoCuarentayNueve', 'CientoCuarentayNueve', 'CientoCuarentayNueve', 'CientoCuarentayNueve@CientoCuarentayNueve.com', '$2y$10$b1NF19ywzpCAmkWRlmTnWe40qlCF284cDm.s5h1xy6TlqbyogZxXC', '2001-03-02', '968562314', 'admin', '2024-11-06 22:29:23', 'activo', NULL, NULL),
(150, 'CientoCincuenta', 'CientoCincuenta', 'CientoCincuenta', 'CientoCincuenta@CientoCincuenta.com', '$2y$10$oabsKi5pemjIS7GurbzEOO/g89XVFzeX.9pFDyeUdGQWH8KDEbf82', '2001-05-04', '695321458', 'admin', '2024-11-06 22:37:08', 'activo', NULL, NULL);

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
(1, 137, 97),
(2, 140, 104),
(3, 143, 105),
(4, 141, 105),
(5, 138, 105),
(6, 145, 107),
(7, 146, 107),
(9, 148, 108),
(10, 148, 105),
(11, 149, 108),
(12, 150, 109);

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
(2, 62),
(2, 63),
(2, 65),
(2, 67),
(2, 69),
(2, 71),
(2, 72),
(2, 73),
(2, 74),
(2, 75),
(2, 77),
(2, 79),
(2, 80),
(2, 81),
(2, 82),
(137, 61),
(138, 93),
(140, 92),
(141, 93),
(143, 93),
(145, 95),
(146, 95),
(148, 95),
(148, 96),
(149, 96),
(150, 97);

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
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`idCategoria`),
  ADD UNIQUE KEY `nombreCategoria` (`nombreCategoria`,`creado_por`,`tipo_categoria`),
  ADD KEY `creado_por` (`creado_por`);

--
-- Indices de la tabla `definiciones_financieras`
--
ALTER TABLE `definiciones_financieras`
  ADD PRIMARY KEY (`idDeffin`);

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
  ADD KEY `fecha` (`fecha`),
  ADD KEY `idGrupo` (`idGrupo`),
  ADD KEY `idFamilia` (`idFamilia`);

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
-- Indices de la tabla `news_letter_envios`
--
ALTER TABLE `news_letter_envios`
  ADD PRIMARY KEY (`idEnvio`),
  ADD KEY `idUser` (`idUser`),
  ADD KEY `fk_definicion_financiera` (`idDeffin`);

--
-- Indices de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`idNotificacion`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `administradores_grupos`
--
ALTER TABLE `administradores_grupos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `idCategoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT de la tabla `familias`
--
ALTER TABLE `familias`
  MODIFY `idFamilia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT de la tabla `gastos`
--
ALTER TABLE `gastos`
  MODIFY `idGasto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `grupos`
--
ALTER TABLE `grupos`
  MODIFY `idGrupo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT de la tabla `ingresos`
--
ALTER TABLE `ingresos`
  MODIFY `idIngreso` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `news_letter_envios`
--
ALTER TABLE `news_letter_envios`
  MODIFY `idEnvio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `idNotificacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `idRol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `roles_permisos`
--
ALTER TABLE `roles_permisos`
  MODIFY `idPermiso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=138;

--
-- AUTO_INCREMENT de la tabla `situacion`
--
ALTER TABLE `situacion`
  MODIFY `idSituacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `idUser` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=151;

--
-- AUTO_INCREMENT de la tabla `usuarios_familias`
--
ALTER TABLE `usuarios_familias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

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
-- Filtros para la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD CONSTRAINT `categorias_ibfk_1` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`idUser`);

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
  ADD CONSTRAINT `fk_definicion_financiera` FOREIGN KEY (`idDeffin`) REFERENCES `definiciones_financieras` (`idDeffin`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `news_letter_envios_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `usuarios` (`idUser`) ON DELETE CASCADE;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
