-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 11-07-2023 a las 23:26:28
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `criptomonedas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `criptos`
--

CREATE TABLE `criptos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `precio` int(11) NOT NULL,
  `nacionalidad` varchar(50) NOT NULL,
  `foto` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `criptos`
--

INSERT INTO `criptos` (`id`, `nombre`, `precio`, `nacionalidad`, `foto`) VALUES
(1, 'dogecoin', 20, 'yankee', './Fotos/dogecoin.jpg'),
(2, 'eterium', 200, 'china', './Fotos/eterium.jpg'),
(3, 'bitcoin', 2000, 'yankee', './Fotos/bitcoin.jpg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logs`
--

CREATE TABLE `logs` (
  `id_usuario` int(11) NOT NULL,
  `id_cripto` int(11) NOT NULL,
  `accion` text NOT NULL,
  `fecha_accion` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `logs`
--

INSERT INTO `logs` (`id_usuario`, `id_cripto`, `accion`, `fecha_accion`) VALUES
(2, 5, 'Borrado', '2023-07-11');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `mail` varchar(50) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `clave` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `mail`, `tipo`, `clave`) VALUES
(1, 'marco@mail.com', 'cliente', 'cliente1'),
(2, 'nico@mail.com', 'admin', 'admin'),
(3, 'mati@mail.com', 'cliente', 'cliente2'),
(6, 'mateo@mail.com', 'admin', 'clave2');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `nombre_cripto` varchar(50) NOT NULL,
  `id_cripto` int(11) NOT NULL,
  `mail_comprador` varchar(50) NOT NULL,
  `fecha` date NOT NULL,
  `cantidad` int(11) NOT NULL,
  `pago` int(11) NOT NULL,
  `foto` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `nombre_cripto`, `id_cripto`, `mail_comprador`, `fecha`, `cantidad`, `pago`, `foto`) VALUES
(1, 'eterium', 2, 'nico@mail.com', '2023-07-03', 5, 160, './FotosCripto2023/eterium_2023-07-03_nico.jpg'),
(2, 'eterium', 2, 'nico@mail.com', '2023-07-03', 5, 160, './FotosCripto2023/eterium_2023-07-03_nico.jpg'),
(3, 'Bitcoin', 3, 'nico@mail.com', '2023-07-10', 3, 15000, './FotosCripto2023/Bitcoin_2023-07-10_nico.jpg'),
(4, 'doge', 1, 'nico@mail.com', '2023-07-10', 3, 45, './FotosCripto2023/doge_2023-07-10_nico.jpg');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `criptos`
--
ALTER TABLE `criptos`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `mail` (`mail`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `criptos`
--
ALTER TABLE `criptos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
