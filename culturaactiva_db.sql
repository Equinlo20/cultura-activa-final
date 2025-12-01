-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 30-11-2025 a las 02:30:13
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
-- Base de datos: `culturaactiva_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `certificados`
--

CREATE TABLE `certificados` (
  `id_certificado` int(11) NOT NULL,
  `id_ticket` int(11) DEFAULT NULL,
  `codigo_verificacion` varchar(100) NOT NULL,
  `url_pdf` varchar(255) NOT NULL,
  `fecha_emision` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `certificados`
--

INSERT INTO `certificados` (`id_certificado`, `id_ticket`, `codigo_verificacion`, `url_pdf`, `fecha_emision`) VALUES
(1, 1, 'CERT-1-68FDD1F2B5DEB', 'generado_en_vivo.pdf', '2025-10-26 07:46:58'),
(2, 2, 'CERT-2-68FEDB48777E1', 'generado_en_vivo.pdf', '2025-10-27 02:39:04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `eventos`
--

CREATE TABLE `eventos` (
  `id_evento` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_evento` datetime NOT NULL,
  `lugar` varchar(255) NOT NULL,
  `id_organizador` int(11) DEFAULT NULL,
  `fecha_publicacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` varchar(50) DEFAULT 'Borrador',
  `capacidad` int(11) DEFAULT 500,
  `visibilidad` varchar(50) DEFAULT 'Público'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `eventos`
--

INSERT INTO `eventos` (`id_evento`, `nombre`, `descripcion`, `fecha_evento`, `lugar`, `id_organizador`, `fecha_publicacion`, `estado`, `capacidad`, `visibilidad`) VALUES
(1, 'Festival de Prueba', '', '2025-10-03 14:30:00', 'Salon de Eventos', 1, '2025-10-26 07:01:32', 'Publicado', 500, 'Público'),
(2, 'Concierto de Gala 2025', '', '2025-10-24 15:00:00', 'Teatro Municipal', 2, '2025-10-27 02:27:37', 'Publicado', 500, 'Público');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id_pago` int(11) NOT NULL,
  `id_ticket` int(11) DEFAULT NULL,
  `monto` decimal(10,2) NOT NULL,
  `metodo` varchar(50) DEFAULT NULL COMMENT 'Credit card, Bank transfer (de gestion-de-pagos-*.jpg)',
  `estado` varchar(50) DEFAULT 'Pendiente' COMMENT 'Completado, Pendiente, Reembolsado',
  `fecha_pago` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_transaccion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pagos`
--

INSERT INTO `pagos` (`id_pago`, `id_ticket`, `monto`, `metodo`, `estado`, `fecha_pago`, `id_transaccion`) VALUES
(1, 1, 0.00, 'Gratuito', 'Completado', '2025-10-26 07:15:39', NULL),
(2, 2, 50000.00, 'Bank transfer', 'Completado', '2025-10-27 02:33:17', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `patrocinadores`
--

CREATE TABLE `patrocinadores` (
  `id_patrocinador` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `id_evento` int(11) DEFAULT NULL,
  `nivel` varchar(50) DEFAULT NULL COMMENT 'Platinum, Gold, Silver (de la imagen)',
  `contribucion` decimal(10,2) DEFAULT NULL,
  `contacto_nombre` varchar(255) DEFAULT NULL,
  `contacto_email` varchar(255) DEFAULT NULL,
  `destacado` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `id_permiso` int(11) NOT NULL,
  `nombre_permiso` varchar(100) NOT NULL COMMENT 'Ej: ver_eventos, crear_usuarios'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `permisos` (`id_permiso`, `nombre_permiso`) VALUES
(1, 'ver_dashboard'),
(2, 'ver_usuarios'),
(3, 'crear_usuarios'),
(4, 'editar_usuarios'),
(5, 'eliminar_usuarios'),
(6, 'ver_asistentes'),
(7, 'ver_roles'),
(8, 'crear_roles'),
(9, 'editar_roles'),
(10, 'eliminar_roles'),
(11, 'ver_eventos'),
(12, 'crear_eventos'),
(13, 'editar_eventos'),
(14, 'eliminar_eventos'),
(15, 'ver_tickets'),
(16, 'crear_tickets'),
(17, 'editar_tickets'),
(18, 'eliminar_tickets'),
(19, 'ver_patrocinadores'),
(20, 'crear_patrocinadores'),
(21, 'editar_patrocinadores'),
(22, 'eliminar_patrocinadores'),
(23, 'ver_pagos'),
(24, 'editar_pagos'),
(25, 'ver_estadisticas'),
(26, 'ver_tipos_ticket'),
(27, 'crear_tipos_ticket'),
(28, 'editar_tipos_ticket'),
(29, 'eliminar_tipos_ticket'),
(30, 'ver_control_asistencia'),
(31, 'ejecutar_control_asistencia');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id_rol` int(11) NOT NULL,
  `nombre_rol` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_rol`, `nombre_rol`) VALUES
(1, 'Administrador'),
(3, 'Asistente'),
(2, 'Organizador');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol_permisos`
--

CREATE TABLE `rol_permisos` (
  `id_rol` int(11) NOT NULL,
  `id_permiso` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rol_permisos`
--

INSERT INTO `rol_permisos` (`id_rol`, `id_permiso`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(1, 13),
(1, 14),
(1, 15),
(1, 16),
(1, 17),
(1, 18),
(1, 19),
(1, 20),
(1, 21),
(1, 22),
(1, 23),
(1, 24),
(1, 25),
(1, 26),
(1, 27),
(1, 28),
(1, 29),
(1, 30),
(1, 31),
(2, 1),
(2, 11),
(2, 12),
(2, 13),
(2, 25),
(2, 26),
(2, 27),
(2, 28),
(2, 29),
(2, 30),
(2, 31);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tickets`
--

CREATE TABLE `tickets` (
  `id_ticket` int(11) NOT NULL,
  `numero_ticket` varchar(20) NOT NULL COMMENT 'Ej: CON-59552',
  `id_evento` int(11) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL COMMENT 'El asistente que compró',
  `id_tipo_ticket` int(11) DEFAULT NULL,
  `estado` varchar(50) DEFAULT 'Activo' COMMENT 'Activo, Usado (de tikets-*.png)',
  `escaneado` tinyint(1) DEFAULT 0,
  `fecha_compra` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tickets`
--

INSERT INTO `tickets` (`id_ticket`, `numero_ticket`, `id_evento`, `id_usuario`, `id_tipo_ticket`, `estado`, `escaneado`, `fecha_compra`) VALUES
(1, 'TIC-68FDCA9BA47E5', 1, 3, 1, 'Usado', 1, '2025-10-26 07:15:39'),
(2, 'TIC-68FED9EDD7A05', 2, 4, 3, 'Usado', 1, '2025-10-27 02:33:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_ticket`
--

CREATE TABLE `tipos_ticket` (
  `id_tipo_ticket` int(11) NOT NULL,
  `id_evento` int(11) DEFAULT NULL,
  `nombre` varchar(100) NOT NULL COMMENT 'Ej: Regular, VIP, Estudiante',
  `precio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `cantidad_disponible` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipos_ticket`
--

INSERT INTO `tipos_ticket` (`id_tipo_ticket`, `id_evento`, `nombre`, `precio`, `cantidad_disponible`) VALUES
(1, 1, 'Entrada Gratuita', 0.00, 100),
(2, 1, 'Entrada VIP', 25000.00, 50),
(3, 2, 'Entrada VIP', 50000.00, 100),
(4, 2, 'Entrada Gratuita', 0.00, 200);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre_completo` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `id_rol` int(11) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` varchar(20) DEFAULT 'Activo',
  `telefono` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre_completo`, `email`, `password_hash`, `id_rol`, `fecha_creacion`, `estado`, `telefono`) VALUES
(1, 'Administrador Principal', 'admin@correo.com', '$2b$12$WK0NfBwd9We0MNmAMlvvcuSIClJgfahbGxiDGJHRoaMaF4kSJNi0O', 1, '2025-10-26 06:32:51', 'Activo', NULL),
(2, 'Mario Suarez', 'organizador@test.com', '$2b$12$LULx0oRcIcUfBsUmDVWFKuzMNkfWx7D1K5z59ib0pEaQRjrQY80aW', 2, '2025-10-26 06:59:45', 'Activo', NULL),
(3, 'Juan Luna', 'participante@test.com', '$2y$10$tj43OA2D0aTiybNI8vgawOUl5o2NUgR84E/tIHIj9SAyx45RTZG4K', 3, '2025-10-26 07:07:07', 'Activo', '3157459641'),
(4, 'Sebastian Ramos', 'participante1@test.com', '$2b$12$SlEjqAiJhAQTYwXWIa/IVOGrMg9C4Yf0qeoUVwvn3Q5saZP53JBZ2', 3, '2025-10-27 02:32:35', 'Activo', '3127454687');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `certificados`
--
ALTER TABLE `certificados`
  ADD PRIMARY KEY (`id_certificado`),
  ADD UNIQUE KEY `codigo_verificacion` (`codigo_verificacion`),
  ADD KEY `id_ticket` (`id_ticket`);

--
-- Indices de la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD PRIMARY KEY (`id_evento`),
  ADD KEY `id_organizador` (`id_organizador`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `id_ticket` (`id_ticket`);

--
-- Indices de la tabla `patrocinadores`
--
ALTER TABLE `patrocinadores`
  ADD PRIMARY KEY (`id_patrocinador`),
  ADD KEY `id_evento` (`id_evento`);

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`id_permiso`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_rol`),
  ADD UNIQUE KEY `nombre_rol` (`nombre_rol`);

--
-- Indices de la tabla `rol_permisos`
--
ALTER TABLE `rol_permisos`
  ADD PRIMARY KEY (`id_rol`,`id_permiso`),
  ADD KEY `id_permiso` (`id_permiso`);

--
-- Indices de la tabla `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id_ticket`),
  ADD UNIQUE KEY `numero_ticket` (`numero_ticket`),
  ADD KEY `id_evento` (`id_evento`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_tipo_ticket` (`id_tipo_ticket`);

--
-- Indices de la tabla `tipos_ticket`
--
ALTER TABLE `tipos_ticket`
  ADD PRIMARY KEY (`id_tipo_ticket`),
  ADD KEY `id_evento` (`id_evento`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `id_rol` (`id_rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `certificados`
--
ALTER TABLE `certificados`
  MODIFY `id_certificado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `eventos`
--
ALTER TABLE `eventos`
  MODIFY `id_evento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `patrocinadores`
--
ALTER TABLE `patrocinadores`
  MODIFY `id_patrocinador` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id_permiso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id_ticket` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tipos_ticket`
--
ALTER TABLE `tipos_ticket`
  MODIFY `id_tipo_ticket` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `certificados`
--
ALTER TABLE `certificados`
  ADD CONSTRAINT `certificados_ibfk_1` FOREIGN KEY (`id_ticket`) REFERENCES `tickets` (`id_ticket`);

--
-- Filtros para la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD CONSTRAINT `eventos_ibfk_1` FOREIGN KEY (`id_organizador`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`id_ticket`) REFERENCES `tickets` (`id_ticket`);

--
-- Filtros para la tabla `patrocinadores`
--
ALTER TABLE `patrocinadores`
  ADD CONSTRAINT `patrocinadores_ibfk_1` FOREIGN KEY (`id_evento`) REFERENCES `eventos` (`id_evento`);

--
-- Filtros para la tabla `rol_permisos`
--
ALTER TABLE `rol_permisos`
  ADD CONSTRAINT `rol_permisos_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON DELETE CASCADE,
  ADD CONSTRAINT `rol_permisos_ibfk_2` FOREIGN KEY (`id_permiso`) REFERENCES `permisos` (`id_permiso`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`id_evento`) REFERENCES `eventos` (`id_evento`),
  ADD CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `tickets_ibfk_3` FOREIGN KEY (`id_tipo_ticket`) REFERENCES `tipos_ticket` (`id_tipo_ticket`);

--
-- Filtros para la tabla `tipos_ticket`
--
ALTER TABLE `tipos_ticket`
  ADD CONSTRAINT `tipos_ticket_ibfk_1` FOREIGN KEY (`id_evento`) REFERENCES `eventos` (`id_evento`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
