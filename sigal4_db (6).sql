-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 13-02-2026 a las 13:01:47
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
-- Base de datos: `sigal4_db`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `calcular_promedio_periodo` (IN `p_id_usuario` INT, IN `p_id_materia` INT, IN `p_id_periodo` INT)   BEGIN
    DECLARE v_promedio DECIMAL(5,2);
    DECLARE v_total_peso DECIMAL(5,2);
    
    SELECT 
        SUM(c.valor * c.peso) / SUM(c.peso),
        SUM(c.peso)
    INTO v_promedio, v_total_peso
    FROM calificacion c
    WHERE c.id_usuario = p_id_usuario
      AND c.id_materia = p_id_materia
      AND c.id_periodo = p_id_periodo;
    
    IF v_total_peso > 0 THEN
        INSERT INTO promedio_periodo (id_usuario, id_materia, id_periodo, promedio, porcentaje_completado)
        VALUES (p_id_usuario, p_id_materia, p_id_periodo, v_promedio, v_total_peso)
        ON DUPLICATE KEY UPDATE 
            promedio = v_promedio,
            porcentaje_completado = v_total_peso;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `promover_estudiantes` (IN `p_id_ano_viejo` INT, IN `p_id_ano_nuevo` INT)   BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_id_usuario INT;
    DECLARE v_id_grado_sec INT;
    DECLARE v_nivel VARCHAR(20);
    DECLARE v_nuevo_grado VARCHAR(50);
    
    DECLARE cur CURSOR FOR 
        SELECT i.id_usuario, i.id_grado_sec, gs.nivel
        FROM inscripcion i
        JOIN grado_seccion gs ON i.id_grado_sec = gs.id_grado_sec
        WHERE i.id_ano_escolar = p_id_ano_viejo 
          AND i.estado = 'ACTIVO';
    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    OPEN cur;
    
    read_loop: LOOP
        FETCH cur INTO v_id_usuario, v_id_grado_sec, v_nivel;
        IF done THEN
            LEAVE read_loop;
        END IF;
        
        -- Lógica para determinar nuevo grado (ej: "1ro A" -> "2do A")
        SET v_nuevo_grado = CONCAT(SUBSTRING_INDEX((SELECT nombre FROM grado_seccion WHERE id_grado_sec = v_id_grado_sec), ' ', 1) + 1, 
                                  ' ', 
                                  SUBSTRING_INDEX((SELECT nombre FROM grado_seccion WHERE id_grado_sec = v_id_grado_sec), ' ', -1));
        
        -- Buscar o crear nuevo grado
        INSERT INTO inscripcion (id_usuario, id_grado_sec, id_ano_escolar, fecha_inscripcion, estado)
        SELECT v_id_usuario, gs.id_grado_sec, p_id_ano_nuevo, CURDATE(), 'ACTIVO'
        FROM grado_seccion gs
        WHERE gs.nombre = v_nuevo_grado 
          AND gs.id_ano_escolar = p_id_ano_nuevo;
        
        -- Actualizar estado anterior
        UPDATE inscripcion 
        SET estado = 'PROMOVIDO'
        WHERE id_usuario = v_id_usuario 
          AND id_ano_escolar = p_id_ano_viejo;
    END LOOP;
    
    CLOSE cur;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ano_escolar`
--

CREATE TABLE `ano_escolar` (
  `id_ano` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `activo` tinyint(1) DEFAULT 0,
  `cerrado` tinyint(1) DEFAULT 0,
  `fecha_cierre` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ano_escolar`
--

INSERT INTO `ano_escolar` (`id_ano`, `nombre`, `fecha_inicio`, `fecha_fin`, `activo`, `cerrado`, `fecha_cierre`) VALUES
(1, '2024-2025', '2026-02-11', '2026-02-11', 0, 0, NULL),
(2, '2025-20226', '2026-02-17', '2026-02-19', 1, 0, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignacion_docente`
--

CREATE TABLE `asignacion_docente` (
  `id_asignacion` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_grado_sec` int(11) NOT NULL,
  `id_materia` int(11) NOT NULL,
  `id_ano_escolar` int(11) NOT NULL,
  `horario` text DEFAULT NULL,
  `carga_horaria` int(11) DEFAULT 0,
  `titular` tinyint(1) DEFAULT 1,
  `activo` tinyint(1) DEFAULT 1,
  `fecha_asignacion` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `asignacion_docente`
--

INSERT INTO `asignacion_docente` (`id_asignacion`, `id_usuario`, `id_grado_sec`, `id_materia`, `id_ano_escolar`, `horario`, `carga_horaria`, `titular`, `activo`, `fecha_asignacion`) VALUES
(1, 2, 1, 1, 2, NULL, 0, 1, 1, '2026-02-11'),
(2, 2, 2, 1, 2, NULL, 0, 1, 1, '2026-02-12');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `backup_log`
--

CREATE TABLE `backup_log` (
  `id_backup` int(11) NOT NULL,
  `nombre_archivo` varchar(255) NOT NULL,
  `ruta` varchar(500) NOT NULL,
  `tamaño_bytes` bigint(20) DEFAULT NULL,
  `fecha_backup` datetime DEFAULT current_timestamp(),
  `realizado_por` int(11) DEFAULT NULL,
  `tipo` enum('COMPLETO','INCREMENTAL','DIFERENCIAL') DEFAULT 'COMPLETO',
  `estado` enum('EXITOSO','FALLIDO','EN_PROGRESO') DEFAULT 'EN_PROGRESO',
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `backup_log`
--

INSERT INTO `backup_log` (`id_backup`, `nombre_archivo`, `ruta`, `tamaño_bytes`, `fecha_backup`, `realizado_por`, `tipo`, `estado`, `observaciones`) VALUES
(1, 'sigal_db_2026-02-12_051905.sql', 'backups/sigal_db_2026-02-12_051905.sql', 49909, '2026-02-12 01:19:07', 7, 'COMPLETO', 'EXITOSO', 'Respaldo manual exitoso'),
(2, 'sigal_db_2026-02-12_052629.sql', 'backups/sigal_db_2026-02-12_052629.sql', 50115, '2026-02-12 01:26:29', 7, 'COMPLETO', 'EXITOSO', 'Respaldo manual exitoso');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calificacion`
--

CREATE TABLE `calificacion` (
  `id_calificacion` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_materia` int(11) NOT NULL,
  `id_periodo` int(11) NOT NULL,
  `id_asignacion` int(11) NOT NULL,
  `valor` decimal(5,2) NOT NULL,
  `tipo_evaluacion` enum('EXAMEN','TAREA','PROYECTO','PARTICIPACION','LABORATORIO','OTRO') NOT NULL,
  `peso` decimal(3,2) DEFAULT 1.00,
  `descripcion` varchar(200) DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `fecha_modificacion` datetime DEFAULT NULL,
  `modificado_por` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Disparadores `calificacion`
--
DELIMITER $$
CREATE TRIGGER `trg_calificacion_update` BEFORE UPDATE ON `calificacion` FOR EACH ROW BEGIN
    SET NEW.fecha_modificacion = NOW();
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `citacion`
--

CREATE TABLE `citacion` (
  `id_citacion` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `fecha_citacion` datetime NOT NULL,
  `motivo` text NOT NULL,
  `lugar` varchar(100) DEFAULT NULL,
  `estado` enum('PENDIENTE','CONFIRMADA','REALIZADA','CANCELADA') DEFAULT 'PENDIENTE',
  `id_incidencia` int(11) DEFAULT NULL,
  `fecha_confirmacion` datetime DEFAULT NULL,
  `confirmada_por` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comunicado`
--

CREATE TABLE `comunicado` (
  `id_comunicado` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `contenido` text NOT NULL,
  `tipo` enum('GENERAL','ACADEMICO','DISCIPLINARIO','ADMINISTRATIVO','EVENTO') NOT NULL,
  `prioridad` enum('BAJA','MEDIA','ALTA','URGENTE') DEFAULT 'MEDIA',
  `id_usuario_emisor` int(11) NOT NULL,
  `fecha_publicacion` datetime DEFAULT current_timestamp(),
  `fecha_expiracion` date DEFAULT NULL,
  `destinatarios` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`destinatarios`)),
  `adjuntos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`adjuntos`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion_sistema`
--

CREATE TABLE `configuracion_sistema` (
  `id_config` int(11) NOT NULL,
  `clave` varchar(100) NOT NULL,
  `valor` text DEFAULT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `modulo` varchar(50) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `editable` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `configuracion_sistema`
--

INSERT INTO `configuracion_sistema` (`id_config`, `clave`, `valor`, `tipo`, `modulo`, `descripcion`, `editable`) VALUES
(1, 'INSTITUCION_NOMBRE', '\"Liceo Nacional Ejemplo\"', 'STRING', 'GENERAL', 'Nombre de la institución', 1),
(2, 'INSTITUCION_DIRECCION', '\"Av. Principal #123\"', 'STRING', 'GENERAL', 'Dirección de la institución', 1),
(3, 'INSTITUCION_TELEFONO', '\"+58-212-5555555\"', 'STRING', 'GENERAL', 'Teléfono de la institución', 1),
(4, 'ANIO_ESCOLAR_ACTUAL', '1', 'INT', 'ACADEMICO', 'ID del año escolar activo', 1),
(5, 'PORCENTAJE_APROBACION', '60.00', 'DECIMAL', 'ACADEMICO', 'Porcentaje mínimo para aprobar', 1),
(6, 'DIAS_JUSTIFICACION_ASISTENCIA', '3', 'INT', 'ASISTENCIA', 'Días para justificar asistencia', 1),
(7, 'HORA_LIMITE_TARDANZA', '\"07:30:00\"', 'TIME', 'ASISTENCIA', 'Hora límite para considerar tardanza', 1),
(8, 'NOTIFICACIONES_ACTIVAS', 'true', 'BOOLEAN', 'COMUNICACION', 'Activar/desactivar notificaciones', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `constancia`
--

CREATE TABLE `constancia` (
  `id_constancia` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `contenido` text NOT NULL,
  `fecha_emision` date NOT NULL,
  `emitida_por` int(11) NOT NULL,
  `codigo_verificacion` varchar(50) DEFAULT NULL,
  `descargada` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contenido_educativo`
--

CREATE TABLE `contenido_educativo` (
  `id_contenido` int(11) NOT NULL,
  `id_asignacion` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `tipo` enum('MATERIAL','TAREA','ANUNCIO','EXAMEN','RECURSO') NOT NULL,
  `archivos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`archivos`)),
  `fecha_publicacion` datetime DEFAULT current_timestamp(),
  `fecha_entrega` date DEFAULT NULL,
  `instrucciones` text DEFAULT NULL,
  `puntos` decimal(5,2) DEFAULT NULL,
  `visible` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `escala_calificacion`
--

CREATE TABLE `escala_calificacion` (
  `id_escala` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `tipo` enum('NUMERICA','LITERAL','PORCENTUAL') NOT NULL,
  `rango_min` decimal(5,2) NOT NULL,
  `rango_max` decimal(5,2) NOT NULL,
  `equivalencia` varchar(20) DEFAULT NULL,
  `aprobatorio` tinyint(1) DEFAULT 0,
  `activa` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `escala_calificacion`
--

INSERT INTO `escala_calificacion` (`id_escala`, `nombre`, `tipo`, `rango_min`, `rango_max`, `equivalencia`, `aprobatorio`, `activa`) VALUES
(1, 'Escala 0-20', 'NUMERICA', 0.00, 20.00, NULL, 0, 1),
(2, 'Escala Aprobatoria 0-20', 'NUMERICA', 10.00, 20.00, NULL, 1, 1),
(3, 'Escala Literal', 'LITERAL', 0.00, 100.00, 'A,B,C,D,F', 0, 1),
(4, 'Escala Porcentual', 'PORCENTUAL', 0.00, 100.00, NULL, 0, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grado_seccion`
--

CREATE TABLE `grado_seccion` (
  `id_grado_sec` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `nivel` varchar(20) NOT NULL,
  `turno` enum('MATUTINO','VESPERTINO','NOCTURNO') NOT NULL,
  `capacidad_max` int(11) DEFAULT 30,
  `id_ano_escolar` int(11) NOT NULL,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `grado_seccion`
--

INSERT INTO `grado_seccion` (`id_grado_sec`, `nombre`, `nivel`, `turno`, `capacidad_max`, `id_ano_escolar`, `activo`) VALUES
(1, '1ro \"A\"', 'media general', 'MATUTINO', 30, 2, 1),
(2, '1 \"B\"', 'media general', 'MATUTINO', 30, 2, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `incidencia_disciplina`
--

CREATE TABLE `incidencia_disciplina` (
  `id_incidencia` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_usuario_reporta` int(11) NOT NULL,
  `id_grado_sec` int(11) NOT NULL,
  `fecha_incidencia` datetime DEFAULT current_timestamp(),
  `tipo` enum('POSITIVA','NEGATIVA','NEUTRAL') NOT NULL,
  `categoria` varchar(50) DEFAULT NULL,
  `descripcion` text NOT NULL,
  `gravedad` enum('LEVE','MODERADA','GRAVE') DEFAULT 'LEVE',
  `sancion` text DEFAULT NULL,
  `estado` enum('REPORTADA','EN_REVISION','RESUELTA','ARCHIVADA') DEFAULT 'REPORTADA',
  `fecha_resolucion` datetime DEFAULT NULL,
  `resuelta_por` int(11) DEFAULT NULL,
  `observaciones_resolucion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inscripcion`
--

CREATE TABLE `inscripcion` (
  `id_inscripcion` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_grado_sec` int(11) NOT NULL,
  `id_ano_escolar` int(11) NOT NULL,
  `fecha_inscripcion` date NOT NULL,
  `estado` enum('ACTIVO','PROMOVIDO','REPITIENTE','RETIRADO','EGRESADO') DEFAULT 'ACTIVO',
  `numero_lista` int(11) DEFAULT NULL,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `inscripcion`
--

INSERT INTO `inscripcion` (`id_inscripcion`, `id_usuario`, `id_grado_sec`, `id_ano_escolar`, `fecha_inscripcion`, `estado`, `numero_lista`, `observaciones`) VALUES
(1, 8, 1, 1, '2026-02-12', 'ACTIVO', 1, NULL),
(2, 9, 1, 1, '2026-02-12', 'ACTIVO', 2, NULL);

--
-- Disparadores `inscripcion`
--
DELIMITER $$
CREATE TRIGGER `trg_inscripcion_capacidad` BEFORE INSERT ON `inscripcion` FOR EACH ROW BEGIN
    DECLARE v_capacidad INT;
    DECLARE v_inscritos INT;
    
    SELECT capacidad_max INTO v_capacidad
    FROM grado_seccion
    WHERE id_grado_sec = NEW.id_grado_sec;
    
    SELECT COUNT(*) INTO v_inscritos
    FROM inscripcion
    WHERE id_grado_sec = NEW.id_grado_sec
      AND id_ano_escolar = NEW.id_ano_escolar
      AND estado = 'ACTIVO';
    
    IF v_inscritos >= v_capacidad THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La sección ha alcanzado su capacidad máxima';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `log_sistema`
--

CREATE TABLE `log_sistema` (
  `id_log` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `accion` varchar(100) NOT NULL,
  `modulo` varchar(50) NOT NULL,
  `detalles` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `fecha_log` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materia`
--

CREATE TABLE `materia` (
  `id_materia` int(11) NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `creditos` int(11) DEFAULT 1,
  `horas_semana` int(11) DEFAULT 4,
  `area` varchar(50) DEFAULT NULL,
  `nivel` varchar(20) NOT NULL,
  `activa` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materia`
--

INSERT INTO `materia` (`id_materia`, `codigo`, `nombre`, `descripcion`, `creditos`, `horas_semana`, `area`, `nivel`, `activa`) VALUES
(1, 'MAT-01', 'Matemáticas', 'Cátedra de formación matemática', 1, 4, NULL, '', 1),
(2, 'CAS-01', 'Castellano', 'Lengua y literatura', 1, 4, NULL, '', 1),
(3, 'ING-01', 'Inglés', 'Idioma extranjero', 1, 4, NULL, '', 1),
(4, 'BIO-01', 'Biología', 'Ciencias biológicas', 1, 4, NULL, '', 1),
(5, 'QUI-01', 'Química', 'Ciencias químicas', 1, 4, NULL, '', 1),
(6, 'FIS-01', 'Física', 'Ciencias físicas', 1, 4, NULL, '', 1),
(7, 'HIS-01', 'Historia de Venezuela', 'Historia nacional', 1, 4, NULL, '', 1),
(8, 'GEO-01', 'Geografía', 'Geografía general', 1, 4, NULL, '', 1),
(9, 'EDF-01', 'Educación Física', 'Actividad física y salud', 1, 4, NULL, '', 1),
(10, 'AYP-01', 'Arte y Patrimonio', 'Educación artística', 1, 4, NULL, '', 1),
(11, 'FPS-01', 'Formación para la Soberanía', 'Instrucción premilitar', 1, 4, NULL, '', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensaje`
--

CREATE TABLE `mensaje` (
  `id_mensaje` int(11) NOT NULL,
  `id_usuario_remitente` int(11) NOT NULL,
  `id_usuario_destinatario` int(11) NOT NULL,
  `asunto` varchar(200) DEFAULT NULL,
  `contenido` text NOT NULL,
  `leido` tinyint(1) DEFAULT 0,
  `fecha_envio` datetime DEFAULT current_timestamp(),
  `fecha_lectura` datetime DEFAULT NULL,
  `adjuntos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`adjuntos`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pago`
--

CREATE TABLE `pago` (
  `id_pago` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `concepto` varchar(100) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `fecha_emision` date NOT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `fecha_pago` date DEFAULT NULL,
  `estado` enum('PENDIENTE','PAGADO','VENCIDO','CANCELADO') DEFAULT 'PENDIENTE',
  `metodo_pago` varchar(50) DEFAULT NULL,
  `referencia` varchar(100) DEFAULT NULL,
  `comprobante_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `periodo_evaluacion`
--

CREATE TABLE `periodo_evaluacion` (
  `id_periodo` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `peso_porcentaje` decimal(5,2) DEFAULT 0.00,
  `tipo` enum('LAPSO','TRIMESTRE','SEMESTRE','ANUAL') NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `id_ano_escolar` int(11) NOT NULL,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `periodo_evaluacion`
--

INSERT INTO `periodo_evaluacion` (`id_periodo`, `nombre`, `peso_porcentaje`, `tipo`, `fecha_inicio`, `fecha_fin`, `id_ano_escolar`, `activo`) VALUES
(1, 'primer momento', 0.01, 'LAPSO', '2026-01-04', '2026-04-16', 2, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permiso`
--

CREATE TABLE `permiso` (
  `id_permiso` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `modulo` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `planificacion_clase`
--

CREATE TABLE `planificacion_clase` (
  `id_planificacion` int(11) NOT NULL,
  `id_asignacion` int(11) NOT NULL,
  `unidad` varchar(100) DEFAULT NULL,
  `tema` varchar(200) NOT NULL,
  `objetivos` text DEFAULT NULL,
  `actividades` text DEFAULT NULL,
  `recursos` text DEFAULT NULL,
  `fecha_planificada` date DEFAULT NULL,
  `duracion_horas` int(11) DEFAULT NULL,
  `realizada` tinyint(1) DEFAULT 0,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `plan_estudio`
--

CREATE TABLE `plan_estudio` (
  `id_plan` int(11) NOT NULL,
  `id_grado_sec` int(11) NOT NULL,
  `id_materia` int(11) NOT NULL,
  `horas_semanales` int(11) DEFAULT 0,
  `orden` int(11) DEFAULT 1,
  `obligatoria` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `plan_estudio`
--

INSERT INTO `plan_estudio` (`id_plan`, `id_grado_sec`, `id_materia`, `horas_semanales`, `orden`, `obligatoria`) VALUES
(1, 1, 6, 4, 1, 1),
(2, 1, 1, 4, 1, 1),
(3, 1, 11, 4, 1, 1),
(4, 2, 1, 4, 1, 1),
(5, 2, 2, 4, 1, 1),
(6, 2, 3, 4, 1, 1),
(7, 2, 4, 4, 1, 1),
(8, 2, 10, 4, 1, 1),
(9, 2, 9, 4, 1, 1),
(10, 2, 8, 4, 1, 1),
(11, 2, 11, 4, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `promedio_periodo`
--

CREATE TABLE `promedio_periodo` (
  `id_promedio` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_materia` int(11) NOT NULL,
  `id_periodo` int(11) NOT NULL,
  `promedio` decimal(5,2) NOT NULL,
  `porcentaje_completado` decimal(5,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registro_asistencia`
--

CREATE TABLE `registro_asistencia` (
  `id_asistencia` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_grado_sec` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `hora` time DEFAULT NULL,
  `estado` enum('PRESENTE','AUSENTE','TARDANZA','JUSTIFICADO','LICENCIA') NOT NULL,
  `justificacion` text DEFAULT NULL,
  `id_usuario_registro` int(11) DEFAULT NULL,
  `dispositivo` varchar(50) DEFAULT NULL,
  `latitud` decimal(10,8) DEFAULT NULL,
  `longitud` decimal(11,8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `representante_estudiante`
--

CREATE TABLE `representante_estudiante` (
  `id_representante` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `parentesco` enum('PADRE','MADRE','TUTOR','HERMANO','OTRO') NOT NULL,
  `principal` tinyint(1) DEFAULT 1,
  `autorizado_retirar` tinyint(1) DEFAULT 1,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `id_rol` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `nivel_acceso` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`id_rol`, `nombre`, `descripcion`, `nivel_acceso`) VALUES
(1, 'ADMINISTRADOR', 'Acceso completo a todos los módulos del sistema', 100),
(2, 'DIRECTOR', 'Acceso a reportes y gestión administrativa', 90),
(3, 'COORDINADOR', 'Gestión académica y supervisión docente', 80),
(4, 'DOCENTE', 'Gestión de cursos, calificaciones y asistencia', 70),
(5, 'REPRESENTANTE', 'Consulta de información de estudiantes', 50),
(6, 'ESTUDIANTE', 'Consulta de información personal', 40),
(7, 'SECRETARIO', 'Gestión administrativa básica', 60);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol_permiso`
--

CREATE TABLE `rol_permiso` (
  `id_rol` int(11) NOT NULL,
  `id_permiso` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `cedula` varchar(20) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  `tipo_usuario` enum('ESTUDIANTE','DOCENTE','REPRESENTANTE','ADMINISTRATIVO','COORDINADOR','DIRECTOR') NOT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `ultimo_login` datetime DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `reset_token` varchar(100) DEFAULT NULL,
  `token_expira` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `cedula`, `nombre`, `apellido`, `email`, `telefono`, `direccion`, `fecha_nacimiento`, `foto_perfil`, `tipo_usuario`, `activo`, `fecha_registro`, `ultimo_login`, `password_hash`, `reset_token`, `token_expira`) VALUES
(2, '30249203', 'Jhean', 'Vera', 'jheanvera911@gmail.com', '04128749394', 'gfdgfdgdfg', '2004-02-04', 'profiles/FFoE65Ro8CKLsawd3JhCacESEPHTkjOpOkiZiF88.jpg', 'DOCENTE', 1, '2026-02-10 00:45:35', NULL, '$2y$12$C73LUtPNiULGDZvRAZBWwemMpOmCYmU95q5WioAGvHbGZZ6tEezhy', NULL, NULL),
(5, '12123123', 'Admin1', 'principal', 'admin1911@gmail.com', '04165756765', 'bolivar', '1980-06-20', NULL, 'ADMINISTRATIVO', 1, '2026-02-11 00:14:44', NULL, '$2y$12$G2R01oqdtdQ.04sVuOXgH.2auMAxW/XDG/ra8LR40L4clzq5kyZTO', NULL, NULL),
(6, '13259350', 'admin', '2', 'admin22@gmail.com', '04128559619', 'mi casa', '2000-07-13', NULL, 'ADMINISTRATIVO', 1, '2026-02-11 01:03:04', NULL, '$2y$12$VthI/A2hiK7Tk9uDqutLP.HxLNbHkG3Sko0FJV3dKdurYaO/PmD2a', NULL, NULL),
(7, '30249204', 'Lius', 'López', 'liuslpz22@gmail.com', '04128742123', 'soledad', '1984-02-29', 'profiles/42snc842JReQq6pnHybzNUK68XV1FOpBQWKcm34c.jpg', 'ADMINISTRATIVO', 1, '2026-02-11 18:55:25', NULL, '$2y$12$RihPG2xMeg02Klzo7ffIM.oJMQOIpczSHwRmBZAah23w/9l2SQ36W', NULL, NULL),
(8, '40000001', 'Estudiante', 'Prueba Uno', 'alumno1@gmail.com', '04120000001', 'Ciudad Bolivar', '2010-05-15', NULL, 'ESTUDIANTE', 1, '2026-02-12 23:18:22', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL),
(9, '40000002', 'Estudiante', 'Prueba Dos', 'alumno2@gmail.com', '04120000002', 'Soledad', '2010-08-20', NULL, 'ESTUDIANTE', 1, '2026-02-12 23:18:22', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL);

--
-- Disparadores `usuario`
--
DELIMITER $$
CREATE TRIGGER `trg_log_usuario_login` AFTER UPDATE ON `usuario` FOR EACH ROW BEGIN
    IF NEW.ultimo_login IS NOT NULL AND OLD.ultimo_login != NEW.ultimo_login THEN
        INSERT INTO log_sistema (id_usuario, accion, modulo, detalles)
        VALUES (NEW.id_usuario, 'LOGIN', 'AUTENTICACION', 
                CONCAT('Usuario ', NEW.email, ' inició sesión'));
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_rol`
--

CREATE TABLE `usuario_rol` (
  `id_usuario` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_calificaciones_estudiante`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_calificaciones_estudiante` (
`id_usuario` int(11)
,`materia` varchar(100)
,`periodo` varchar(50)
,`valor` decimal(5,2)
,`tipo_evaluacion` enum('EXAMEN','TAREA','PROYECTO','PARTICIPACION','LABORATORIO','OTRO')
,`peso` decimal(3,2)
,`promedio_periodo` decimal(5,2)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_docentes_asignaciones`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_docentes_asignaciones` (
`id_usuario` int(11)
,`nombre` varchar(50)
,`apellido` varchar(50)
,`grado_seccion` varchar(50)
,`materia` varchar(100)
,`horario` text
,`carga_horaria` int(11)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_estudiantes_activos`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_estudiantes_activos` (
`id_usuario` int(11)
,`cedula` varchar(20)
,`nombre` varchar(50)
,`apellido` varchar(50)
,`email` varchar(100)
,`grado_seccion` varchar(50)
,`turno` enum('MATUTINO','VESPERTINO','NOCTURNO')
,`id_ano_escolar` int(11)
,`numero_lista` int(11)
);

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_calificaciones_estudiante`
--
DROP TABLE IF EXISTS `vista_calificaciones_estudiante`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_calificaciones_estudiante`  AS SELECT `c`.`id_usuario` AS `id_usuario`, `m`.`nombre` AS `materia`, `p`.`nombre` AS `periodo`, `c`.`valor` AS `valor`, `c`.`tipo_evaluacion` AS `tipo_evaluacion`, `c`.`peso` AS `peso`, `pp`.`promedio` AS `promedio_periodo` FROM (((`calificacion` `c` join `materia` `m` on(`c`.`id_materia` = `m`.`id_materia`)) join `periodo_evaluacion` `p` on(`c`.`id_periodo` = `p`.`id_periodo`)) left join `promedio_periodo` `pp` on(`c`.`id_usuario` = `pp`.`id_usuario` and `c`.`id_materia` = `pp`.`id_materia` and `c`.`id_periodo` = `pp`.`id_periodo`)) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_docentes_asignaciones`
--
DROP TABLE IF EXISTS `vista_docentes_asignaciones`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_docentes_asignaciones`  AS SELECT `u`.`id_usuario` AS `id_usuario`, `u`.`nombre` AS `nombre`, `u`.`apellido` AS `apellido`, `gs`.`nombre` AS `grado_seccion`, `m`.`nombre` AS `materia`, `ad`.`horario` AS `horario`, `ad`.`carga_horaria` AS `carga_horaria` FROM (((`usuario` `u` join `asignacion_docente` `ad` on(`u`.`id_usuario` = `ad`.`id_usuario`)) join `grado_seccion` `gs` on(`ad`.`id_grado_sec` = `gs`.`id_grado_sec`)) join `materia` `m` on(`ad`.`id_materia` = `m`.`id_materia`)) WHERE `u`.`tipo_usuario` = 'DOCENTE' AND `u`.`activo` = 1 AND `ad`.`activo` = 1 ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_estudiantes_activos`
--
DROP TABLE IF EXISTS `vista_estudiantes_activos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_estudiantes_activos`  AS SELECT `u`.`id_usuario` AS `id_usuario`, `u`.`cedula` AS `cedula`, `u`.`nombre` AS `nombre`, `u`.`apellido` AS `apellido`, `u`.`email` AS `email`, `gs`.`nombre` AS `grado_seccion`, `gs`.`turno` AS `turno`, `i`.`id_ano_escolar` AS `id_ano_escolar`, `i`.`numero_lista` AS `numero_lista` FROM ((`usuario` `u` join `inscripcion` `i` on(`u`.`id_usuario` = `i`.`id_usuario`)) join `grado_seccion` `gs` on(`i`.`id_grado_sec` = `gs`.`id_grado_sec`)) WHERE `u`.`tipo_usuario` = 'ESTUDIANTE' AND `u`.`activo` = 1 AND `i`.`estado` = 'ACTIVO' ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `ano_escolar`
--
ALTER TABLE `ano_escolar`
  ADD PRIMARY KEY (`id_ano`);

--
-- Indices de la tabla `asignacion_docente`
--
ALTER TABLE `asignacion_docente`
  ADD PRIMARY KEY (`id_asignacion`),
  ADD UNIQUE KEY `uk_docente_materia_grado` (`id_usuario`,`id_grado_sec`,`id_materia`,`id_ano_escolar`),
  ADD KEY `id_grado_sec` (`id_grado_sec`),
  ADD KEY `id_materia` (`id_materia`),
  ADD KEY `id_ano_escolar` (`id_ano_escolar`);

--
-- Indices de la tabla `backup_log`
--
ALTER TABLE `backup_log`
  ADD PRIMARY KEY (`id_backup`),
  ADD KEY `realizado_por` (`realizado_por`);

--
-- Indices de la tabla `calificacion`
--
ALTER TABLE `calificacion`
  ADD PRIMARY KEY (`id_calificacion`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_materia` (`id_materia`),
  ADD KEY `id_asignacion` (`id_asignacion`),
  ADD KEY `modificado_por` (`modificado_por`),
  ADD KEY `idx_calificacion_periodo` (`id_periodo`,`id_materia`);

--
-- Indices de la tabla `citacion`
--
ALTER TABLE `citacion`
  ADD PRIMARY KEY (`id_citacion`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_estudiante` (`id_estudiante`),
  ADD KEY `id_incidencia` (`id_incidencia`),
  ADD KEY `confirmada_por` (`confirmada_por`);

--
-- Indices de la tabla `comunicado`
--
ALTER TABLE `comunicado`
  ADD PRIMARY KEY (`id_comunicado`),
  ADD KEY `id_usuario_emisor` (`id_usuario_emisor`),
  ADD KEY `idx_comunicado_fecha` (`fecha_publicacion`,`tipo`);

--
-- Indices de la tabla `configuracion_sistema`
--
ALTER TABLE `configuracion_sistema`
  ADD PRIMARY KEY (`id_config`),
  ADD UNIQUE KEY `clave` (`clave`);

--
-- Indices de la tabla `constancia`
--
ALTER TABLE `constancia`
  ADD PRIMARY KEY (`id_constancia`),
  ADD UNIQUE KEY `codigo_verificacion` (`codigo_verificacion`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `emitida_por` (`emitida_por`);

--
-- Indices de la tabla `contenido_educativo`
--
ALTER TABLE `contenido_educativo`
  ADD PRIMARY KEY (`id_contenido`),
  ADD KEY `idx_contenido_asignacion` (`id_asignacion`,`tipo`);

--
-- Indices de la tabla `escala_calificacion`
--
ALTER TABLE `escala_calificacion`
  ADD PRIMARY KEY (`id_escala`);

--
-- Indices de la tabla `grado_seccion`
--
ALTER TABLE `grado_seccion`
  ADD PRIMARY KEY (`id_grado_sec`),
  ADD KEY `id_ano_escolar` (`id_ano_escolar`);

--
-- Indices de la tabla `incidencia_disciplina`
--
ALTER TABLE `incidencia_disciplina`
  ADD PRIMARY KEY (`id_incidencia`),
  ADD KEY `id_usuario_reporta` (`id_usuario_reporta`),
  ADD KEY `id_grado_sec` (`id_grado_sec`),
  ADD KEY `resuelta_por` (`resuelta_por`),
  ADD KEY `idx_incidencia_estudiante` (`id_usuario`,`fecha_incidencia`);

--
-- Indices de la tabla `inscripcion`
--
ALTER TABLE `inscripcion`
  ADD PRIMARY KEY (`id_inscripcion`),
  ADD UNIQUE KEY `uk_estudiante_ano` (`id_usuario`,`id_ano_escolar`),
  ADD KEY `id_grado_sec` (`id_grado_sec`),
  ADD KEY `id_ano_escolar` (`id_ano_escolar`),
  ADD KEY `idx_inscripcion_estado` (`estado`,`id_ano_escolar`);

--
-- Indices de la tabla `log_sistema`
--
ALTER TABLE `log_sistema`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `materia`
--
ALTER TABLE `materia`
  ADD PRIMARY KEY (`id_materia`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Indices de la tabla `mensaje`
--
ALTER TABLE `mensaje`
  ADD PRIMARY KEY (`id_mensaje`),
  ADD KEY `id_usuario_remitente` (`id_usuario_remitente`),
  ADD KEY `id_usuario_destinatario` (`id_usuario_destinatario`);

--
-- Indices de la tabla `pago`
--
ALTER TABLE `pago`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `periodo_evaluacion`
--
ALTER TABLE `periodo_evaluacion`
  ADD PRIMARY KEY (`id_periodo`),
  ADD KEY `id_ano_escolar` (`id_ano_escolar`);

--
-- Indices de la tabla `permiso`
--
ALTER TABLE `permiso`
  ADD PRIMARY KEY (`id_permiso`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `planificacion_clase`
--
ALTER TABLE `planificacion_clase`
  ADD PRIMARY KEY (`id_planificacion`),
  ADD KEY `id_asignacion` (`id_asignacion`);

--
-- Indices de la tabla `plan_estudio`
--
ALTER TABLE `plan_estudio`
  ADD PRIMARY KEY (`id_plan`),
  ADD UNIQUE KEY `uk_grado_materia` (`id_grado_sec`,`id_materia`),
  ADD KEY `id_materia` (`id_materia`);

--
-- Indices de la tabla `promedio_periodo`
--
ALTER TABLE `promedio_periodo`
  ADD PRIMARY KEY (`id_promedio`),
  ADD UNIQUE KEY `uk_estudiante_materia_periodo` (`id_usuario`,`id_materia`,`id_periodo`),
  ADD KEY `id_materia` (`id_materia`),
  ADD KEY `id_periodo` (`id_periodo`);

--
-- Indices de la tabla `registro_asistencia`
--
ALTER TABLE `registro_asistencia`
  ADD PRIMARY KEY (`id_asistencia`),
  ADD UNIQUE KEY `uk_estudiante_fecha` (`id_usuario`,`fecha`),
  ADD KEY `id_grado_sec` (`id_grado_sec`),
  ADD KEY `id_usuario_registro` (`id_usuario_registro`),
  ADD KEY `idx_asistencia_fecha` (`fecha`,`id_grado_sec`);

--
-- Indices de la tabla `representante_estudiante`
--
ALTER TABLE `representante_estudiante`
  ADD PRIMARY KEY (`id_representante`,`id_estudiante`),
  ADD KEY `id_estudiante` (`id_estudiante`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`id_rol`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `rol_permiso`
--
ALTER TABLE `rol_permiso`
  ADD PRIMARY KEY (`id_rol`,`id_permiso`),
  ADD KEY `id_permiso` (`id_permiso`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `cedula` (`cedula`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_usuario_tipo` (`tipo_usuario`,`activo`),
  ADD KEY `idx_usuario_email` (`email`);

--
-- Indices de la tabla `usuario_rol`
--
ALTER TABLE `usuario_rol`
  ADD PRIMARY KEY (`id_usuario`,`id_rol`),
  ADD KEY `id_rol` (`id_rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `ano_escolar`
--
ALTER TABLE `ano_escolar`
  MODIFY `id_ano` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `asignacion_docente`
--
ALTER TABLE `asignacion_docente`
  MODIFY `id_asignacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `backup_log`
--
ALTER TABLE `backup_log`
  MODIFY `id_backup` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `calificacion`
--
ALTER TABLE `calificacion`
  MODIFY `id_calificacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `citacion`
--
ALTER TABLE `citacion`
  MODIFY `id_citacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `comunicado`
--
ALTER TABLE `comunicado`
  MODIFY `id_comunicado` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `configuracion_sistema`
--
ALTER TABLE `configuracion_sistema`
  MODIFY `id_config` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `constancia`
--
ALTER TABLE `constancia`
  MODIFY `id_constancia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `contenido_educativo`
--
ALTER TABLE `contenido_educativo`
  MODIFY `id_contenido` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `escala_calificacion`
--
ALTER TABLE `escala_calificacion`
  MODIFY `id_escala` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `grado_seccion`
--
ALTER TABLE `grado_seccion`
  MODIFY `id_grado_sec` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `incidencia_disciplina`
--
ALTER TABLE `incidencia_disciplina`
  MODIFY `id_incidencia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `inscripcion`
--
ALTER TABLE `inscripcion`
  MODIFY `id_inscripcion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `log_sistema`
--
ALTER TABLE `log_sistema`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `materia`
--
ALTER TABLE `materia`
  MODIFY `id_materia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `mensaje`
--
ALTER TABLE `mensaje`
  MODIFY `id_mensaje` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pago`
--
ALTER TABLE `pago`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `periodo_evaluacion`
--
ALTER TABLE `periodo_evaluacion`
  MODIFY `id_periodo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `permiso`
--
ALTER TABLE `permiso`
  MODIFY `id_permiso` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `planificacion_clase`
--
ALTER TABLE `planificacion_clase`
  MODIFY `id_planificacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `plan_estudio`
--
ALTER TABLE `plan_estudio`
  MODIFY `id_plan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `promedio_periodo`
--
ALTER TABLE `promedio_periodo`
  MODIFY `id_promedio` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `registro_asistencia`
--
ALTER TABLE `registro_asistencia`
  MODIFY `id_asistencia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `asignacion_docente`
--
ALTER TABLE `asignacion_docente`
  ADD CONSTRAINT `asignacion_docente_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `asignacion_docente_ibfk_2` FOREIGN KEY (`id_grado_sec`) REFERENCES `grado_seccion` (`id_grado_sec`),
  ADD CONSTRAINT `asignacion_docente_ibfk_3` FOREIGN KEY (`id_materia`) REFERENCES `materia` (`id_materia`),
  ADD CONSTRAINT `asignacion_docente_ibfk_4` FOREIGN KEY (`id_ano_escolar`) REFERENCES `ano_escolar` (`id_ano`);

--
-- Filtros para la tabla `backup_log`
--
ALTER TABLE `backup_log`
  ADD CONSTRAINT `backup_log_ibfk_1` FOREIGN KEY (`realizado_por`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `calificacion`
--
ALTER TABLE `calificacion`
  ADD CONSTRAINT `calificacion_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `calificacion_ibfk_2` FOREIGN KEY (`id_materia`) REFERENCES `materia` (`id_materia`),
  ADD CONSTRAINT `calificacion_ibfk_3` FOREIGN KEY (`id_periodo`) REFERENCES `periodo_evaluacion` (`id_periodo`),
  ADD CONSTRAINT `calificacion_ibfk_4` FOREIGN KEY (`id_asignacion`) REFERENCES `asignacion_docente` (`id_asignacion`),
  ADD CONSTRAINT `calificacion_ibfk_5` FOREIGN KEY (`modificado_por`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `citacion`
--
ALTER TABLE `citacion`
  ADD CONSTRAINT `citacion_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `citacion_ibfk_2` FOREIGN KEY (`id_estudiante`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `citacion_ibfk_3` FOREIGN KEY (`id_incidencia`) REFERENCES `incidencia_disciplina` (`id_incidencia`),
  ADD CONSTRAINT `citacion_ibfk_4` FOREIGN KEY (`confirmada_por`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `comunicado`
--
ALTER TABLE `comunicado`
  ADD CONSTRAINT `comunicado_ibfk_1` FOREIGN KEY (`id_usuario_emisor`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `constancia`
--
ALTER TABLE `constancia`
  ADD CONSTRAINT `constancia_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `constancia_ibfk_2` FOREIGN KEY (`emitida_por`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `contenido_educativo`
--
ALTER TABLE `contenido_educativo`
  ADD CONSTRAINT `contenido_educativo_ibfk_1` FOREIGN KEY (`id_asignacion`) REFERENCES `asignacion_docente` (`id_asignacion`);

--
-- Filtros para la tabla `grado_seccion`
--
ALTER TABLE `grado_seccion`
  ADD CONSTRAINT `grado_seccion_ibfk_1` FOREIGN KEY (`id_ano_escolar`) REFERENCES `ano_escolar` (`id_ano`);

--
-- Filtros para la tabla `incidencia_disciplina`
--
ALTER TABLE `incidencia_disciplina`
  ADD CONSTRAINT `incidencia_disciplina_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `incidencia_disciplina_ibfk_2` FOREIGN KEY (`id_usuario_reporta`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `incidencia_disciplina_ibfk_3` FOREIGN KEY (`id_grado_sec`) REFERENCES `grado_seccion` (`id_grado_sec`),
  ADD CONSTRAINT `incidencia_disciplina_ibfk_4` FOREIGN KEY (`resuelta_por`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `inscripcion`
--
ALTER TABLE `inscripcion`
  ADD CONSTRAINT `inscripcion_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `inscripcion_ibfk_2` FOREIGN KEY (`id_grado_sec`) REFERENCES `grado_seccion` (`id_grado_sec`),
  ADD CONSTRAINT `inscripcion_ibfk_3` FOREIGN KEY (`id_ano_escolar`) REFERENCES `ano_escolar` (`id_ano`);

--
-- Filtros para la tabla `log_sistema`
--
ALTER TABLE `log_sistema`
  ADD CONSTRAINT `log_sistema_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `mensaje`
--
ALTER TABLE `mensaje`
  ADD CONSTRAINT `mensaje_ibfk_1` FOREIGN KEY (`id_usuario_remitente`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `mensaje_ibfk_2` FOREIGN KEY (`id_usuario_destinatario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `pago`
--
ALTER TABLE `pago`
  ADD CONSTRAINT `pago_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `periodo_evaluacion`
--
ALTER TABLE `periodo_evaluacion`
  ADD CONSTRAINT `periodo_evaluacion_ibfk_1` FOREIGN KEY (`id_ano_escolar`) REFERENCES `ano_escolar` (`id_ano`);

--
-- Filtros para la tabla `planificacion_clase`
--
ALTER TABLE `planificacion_clase`
  ADD CONSTRAINT `planificacion_clase_ibfk_1` FOREIGN KEY (`id_asignacion`) REFERENCES `asignacion_docente` (`id_asignacion`);

--
-- Filtros para la tabla `plan_estudio`
--
ALTER TABLE `plan_estudio`
  ADD CONSTRAINT `plan_estudio_ibfk_1` FOREIGN KEY (`id_grado_sec`) REFERENCES `grado_seccion` (`id_grado_sec`),
  ADD CONSTRAINT `plan_estudio_ibfk_2` FOREIGN KEY (`id_materia`) REFERENCES `materia` (`id_materia`);

--
-- Filtros para la tabla `promedio_periodo`
--
ALTER TABLE `promedio_periodo`
  ADD CONSTRAINT `promedio_periodo_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `promedio_periodo_ibfk_2` FOREIGN KEY (`id_materia`) REFERENCES `materia` (`id_materia`),
  ADD CONSTRAINT `promedio_periodo_ibfk_3` FOREIGN KEY (`id_periodo`) REFERENCES `periodo_evaluacion` (`id_periodo`);

--
-- Filtros para la tabla `registro_asistencia`
--
ALTER TABLE `registro_asistencia`
  ADD CONSTRAINT `registro_asistencia_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `registro_asistencia_ibfk_2` FOREIGN KEY (`id_grado_sec`) REFERENCES `grado_seccion` (`id_grado_sec`),
  ADD CONSTRAINT `registro_asistencia_ibfk_3` FOREIGN KEY (`id_usuario_registro`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `representante_estudiante`
--
ALTER TABLE `representante_estudiante`
  ADD CONSTRAINT `representante_estudiante_ibfk_1` FOREIGN KEY (`id_representante`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `representante_estudiante_ibfk_2` FOREIGN KEY (`id_estudiante`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `rol_permiso`
--
ALTER TABLE `rol_permiso`
  ADD CONSTRAINT `rol_permiso_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id_rol`),
  ADD CONSTRAINT `rol_permiso_ibfk_2` FOREIGN KEY (`id_permiso`) REFERENCES `permiso` (`id_permiso`);

--
-- Filtros para la tabla `usuario_rol`
--
ALTER TABLE `usuario_rol`
  ADD CONSTRAINT `usuario_rol_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `usuario_rol_ibfk_2` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id_rol`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
