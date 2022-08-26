-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 26-08-2022 a las 22:31:01
-- Versión del servidor: 5.7.36
-- Versión de PHP: 7.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sac_app`
--
CREATE DATABASE IF NOT EXISTS `sac_app` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `sac_app`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_articulos_favoritos`
--

DROP TABLE IF EXISTS `tbl_articulos_favoritos`;
CREATE TABLE IF NOT EXISTS `tbl_articulos_favoritos` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `Articulo` varchar(15) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=43 DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tbl_articulos_favoritos`
--

INSERT INTO `tbl_articulos_favoritos` (`id`, `Articulo`, `created_at`, `updated_at`) VALUES
(41, '13705012', '2022-08-26 16:47:29', '2022-08-26 16:47:29'),
(42, '13401013', '2022-08-26 16:47:48', '2022-08-26 16:47:48'),
(40, '11720011', '2022-08-26 16:47:11', '2022-08-26 16:47:11'),
(35, '17303021', '2022-08-25 22:30:37', '2022-08-25 22:30:37'),
(39, '13705105', '2022-08-26 16:46:37', '2022-08-26 16:46:37');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_rutas_asginadas`
--

DROP TABLE IF EXISTS `tbl_rutas_asginadas`;
CREATE TABLE IF NOT EXISTS `tbl_rutas_asginadas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `RUTA` varchar(10) DEFAULT NULL,
  `id_usuario` int(10) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tbl_rutas_asginadas`
--

INSERT INTO `tbl_rutas_asginadas` (`id`, `RUTA`, `id_usuario`, `created_at`, `updated_at`) VALUES
(13, 'F06', 9, '2022-08-25 22:16:14', '2022-08-25 22:16:14'),
(12, 'F08', 7, '2022-08-25 22:10:32', '2022-08-25 22:10:32'),
(10, 'F05', 7, '2022-08-25 22:10:22', '2022-08-25 22:10:22'),
(11, 'F09', 7, '2022-08-25 22:10:27', '2022-08-25 22:10:27'),
(14, 'F14', 9, '2022-08-25 22:16:22', '2022-08-25 22:16:22'),
(15, 'F19', 9, '2022-08-25 22:16:29', '2022-08-25 22:16:29'),
(16, 'F04', 10, '2022-08-25 22:17:15', '2022-08-25 22:17:15'),
(17, 'F18', 10, '2022-08-25 22:17:22', '2022-08-25 22:17:22'),
(18, 'F11', 12, '2022-08-25 22:17:55', '2022-08-25 22:17:55'),
(19, 'F20', 12, '2022-08-25 22:18:01', '2022-08-25 22:18:01'),
(20, 'F07', 13, '2022-08-25 22:18:09', '2022-08-25 22:18:09'),
(21, 'F08', 13, '2022-08-25 22:18:14', '2022-08-25 22:18:14'),
(22, 'F23', 13, '2022-08-25 22:18:19', '2022-08-25 22:18:19'),
(23, 'F06', 13, '2022-08-25 22:18:25', '2022-08-25 22:18:25'),
(24, 'F14', 13, '2022-08-25 22:18:31', '2022-08-25 22:18:31'),
(25, 'F19', 13, '2022-08-25 22:18:37', '2022-08-25 22:18:37'),
(26, 'F05', 14, '2022-08-25 22:18:50', '2022-08-25 22:18:50'),
(27, 'F13', 14, '2022-08-25 22:18:56', '2022-08-25 22:18:56'),
(28, 'F03', 14, '2022-08-25 22:19:04', '2022-08-25 22:19:04'),
(29, 'F21', 14, '2022-08-25 22:19:15', '2022-08-25 22:19:15'),
(30, 'F10', 15, '2022-08-25 22:19:29', '2022-08-25 22:19:29'),
(31, 'F09', 15, '2022-08-25 22:19:34', '2022-08-25 22:19:34'),
(32, 'F22', 15, '2022-08-25 22:19:39', '2022-08-25 22:19:39'),
(33, 'F04', 15, '2022-08-25 22:19:45', '2022-08-25 22:19:45');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_usuario`
--

DROP TABLE IF EXISTS `tbl_usuario`;
CREATE TABLE IF NOT EXISTS `tbl_usuario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Usuario` varchar(255) DEFAULT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `activo` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tbl_usuario`
--

INSERT INTO `tbl_usuario` (`id`, `Usuario`, `nombre`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'Marangelo', 'Maryan Espinoza Barrera', 'N', '2022-08-25 20:11:39', '2022-08-25 22:08:08'),
(2, 'Marangelo', 'Maryan Espinoza Barrera', 'N', '2022-08-25 20:12:01', '2022-08-25 22:08:04'),
(3, 'Marangelo', 'Maryan Espinoza Barrera', 'N', '2022-08-25 20:12:13', '2022-08-25 22:08:00'),
(4, 'Marangelo02', 'Maryan Espinoza Barrera', 'SS', '2022-08-25 20:17:54', '2022-08-25 21:26:56'),
(5, 'marangelo02', 'marangelo02', 'N', '2022-08-25 21:14:59', '2022-08-25 22:07:57'),
(6, '02', '02', 'N', '2022-08-25 21:17:00', '2022-08-25 21:23:48'),
(7, 'marangelo', 'maryan adan espinoza', 'N', '2022-08-25 22:10:00', '2022-08-25 22:11:55'),
(8, 'SAC01', 'SAC01 | Alejandra', 'S', '2022-08-25 22:12:42', '2022-08-25 22:13:55'),
(9, 'SAC08', 'SAC08 | NADIESKA URBINA', 'S', '2022-08-25 22:13:06', '2022-08-25 22:13:06'),
(10, 'SAC07', 'SAC07 | Marisela Sevilla', 'S', '2022-08-25 22:13:17', '2022-08-25 22:17:09'),
(11, 'SAC06', 'SAC6 | Maria Alejandra', 'N', '2022-08-25 22:13:36', '2022-08-25 22:20:21'),
(12, 'SAC05', 'SAC05 | Yorleni Urbina', 'S', '2022-08-25 22:14:12', '2022-08-25 22:14:12'),
(13, 'SAC04', 'SAC04 | JESSICA LUCILA', 'S', '2022-08-25 22:14:22', '2022-08-26 17:51:48'),
(14, 'SAC03', 'SAC03 | AURA CAROLINA', 'S', '2022-08-25 22:14:44', '2022-08-25 22:14:44'),
(15, 'SAC02', 'SAC02 | REYNA ALVAREZ', 'S', '2022-08-25 22:14:56', '2022-08-25 22:14:56'),
(16, 'NOMBRE DE USUARIO', 'NOMBRE COMPLE', 'N', '2022-08-25 22:45:52', '2022-08-26 17:51:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `activo` varchar(5) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `nombre`, `username`, `password`, `activo`, `created_at`, `updated_at`) VALUES
(4, 'Maryan Espinoza Barrera', 'Marangelo02', '$2y$10$EYvTJl76gqho3CXSUAyDXum.KcX1qOT4qO87.bhZrGSJDmdafdyge', 'N', NULL, NULL),
(3, 'Maryan Espinoza Barrera', 'Marangelo', '$2y$10$EYvTJl76gqho3CXSUAyDXum.KcX1qOT4qO87.bhZrGSJDmdafdyge', 'N', NULL, NULL),
(1, 'Maryan Espinoza Barrera', 'Marangelo', '$2y$10$EYvTJl76gqho3CXSUAyDXum.KcX1qOT4qO87.bhZrGSJDmdafdyge', 'N', NULL, NULL),
(2, 'Maryan Espinoza Barrera', 'Marangelo', '$2y$10$EYvTJl76gqho3CXSUAyDXum.KcX1qOT4qO87.bhZrGSJDmdafdyge', 'S', NULL, NULL),
(5, 'marangelo02', 'marangelo02', '$2y$10$EYvTJl76gqho3CXSUAyDXum.KcX1qOT4qO87.bhZrGSJDmdafdyge', 'N', NULL, NULL),
(6, '02', '02', '$2y$10$EYvTJl76gqho3CXSUAyDXum.KcX1qOT4qO87.bhZrGSJDmdafdyge', 'N', NULL, NULL),
(7, 'maryan adan espinoza', 'marangelo', '$2y$10$EYvTJl76gqho3CXSUAyDXum.KcX1qOT4qO87.bhZrGSJDmdafdyge', 'N', NULL, NULL),
(8, 'SAC01 | Alejandra', 'SAC01', '$2y$10$EYvTJl76gqho3CXSUAyDXum.KcX1qOT4qO87.bhZrGSJDmdafdyge', 'S', NULL, NULL),
(9, 'SAC08 | NADIESKA URBINA', 'SAC08', '$2y$10$EYvTJl76gqho3CXSUAyDXum.KcX1qOT4qO87.bhZrGSJDmdafdyge', 'S', NULL, NULL),
(10, 'SAC07 | Marisela Sevilla', 'SAC07', '$2y$10$EYvTJl76gqho3CXSUAyDXum.KcX1qOT4qO87.bhZrGSJDmdafdyge', 'S', NULL, NULL),
(11, 'SAC6 | Maria Alejandra', 'SAC06', '$2y$10$EYvTJl76gqho3CXSUAyDXum.KcX1qOT4qO87.bhZrGSJDmdafdyge', 'N', NULL, NULL),
(12, 'SAC05 | Yorleni Urbina', 'SAC05', '$2y$10$EYvTJl76gqho3CXSUAyDXum.KcX1qOT4qO87.bhZrGSJDmdafdyge', 'S', NULL, NULL),
(13, 'SAC04 | JESSICA LUCILA', 'SAC04', '$2y$10$EYvTJl76gqho3CXSUAyDXum.KcX1qOT4qO87.bhZrGSJDmdafdyge', 'S', NULL, NULL),
(14, 'SAC03 | AURA CAROLINA', 'SAC03', '$2y$10$EYvTJl76gqho3CXSUAyDXum.KcX1qOT4qO87.bhZrGSJDmdafdyge', 'S', NULL, NULL),
(15, 'SAC02 | REYNA ALVAREZ', 'SAC02', '$2y$10$EYvTJl76gqho3CXSUAyDXum.KcX1qOT4qO87.bhZrGSJDmdafdyge', 'S', NULL, NULL),
(16, 'NOMBRE COMPLE', 'NOMBRE DE USUARIO', '$2y$10$EYvTJl76gqho3CXSUAyDXum.KcX1qOT4qO87.bhZrGSJDmdafdyge', 'N', NULL, NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
