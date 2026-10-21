-- ============================================================
-- VIOLETA - Base de datos educativa sobre prevención de
-- violencia contra la mujer
-- Proyecto académico - Diseño y Programación Web III - 2026
-- Instituto Técnico Nacional de Comercio "Federico Álvarez Plata"
-- ============================================================

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET time_zone = '-04:00';
SET foreign_key_checks = 0;

-- ============================================================
-- CREACIÓN DE BASE DE DATOS:
-- En entorno local (XAMPP / root) crea la base de datos 'violeta_db'.
-- En hostings compartidos (InfinityFree, cPanel, Alwaysdata) donde
-- la base de datos ya fue creada desde el panel, seleccione su base
-- de datos antes de importar y comente las siguientes 2 sentencias
-- si su proveedor restringe CREATE DATABASE / USE:
-- ============================================================
CREATE DATABASE IF NOT EXISTS `violeta_db`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `violeta_db`;

-- ============================================================
-- TABLA: administradores
-- ============================================================
CREATE TABLE IF NOT EXISTS `administradores` (
  `id_admin`       INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `nombre`         VARCHAR(100)     NOT NULL,
  `email`          VARCHAR(150)     NOT NULL,
  `password_hash`  VARCHAR(255)     NOT NULL,
  `activo`         TINYINT(1)       NOT NULL DEFAULT 1,
  `ultimo_acceso`  DATETIME                  DEFAULT NULL,
  `fecha_creacion` DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_admin`),
  UNIQUE KEY `uq_admin_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: categorias
-- ============================================================
CREATE TABLE IF NOT EXISTS `categorias` (
  `id_categoria`   INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `nombre`         VARCHAR(100)     NOT NULL,
  `slug`           VARCHAR(100)     NOT NULL,
  `descripcion`    TEXT                      DEFAULT NULL,
  `icono`          VARCHAR(80)               DEFAULT NULL,
  `color`          VARCHAR(20)               DEFAULT '#5636C9',
  `estado`         TINYINT(1)       NOT NULL DEFAULT 1,
  `orden`          TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `fecha_creacion` DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_categoria`),
  UNIQUE KEY `uq_cat_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: preguntas
-- ============================================================
CREATE TABLE IF NOT EXISTS `preguntas` (
  `id_pregunta`    INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `id_categoria`   INT UNSIGNED     NOT NULL,
  `enunciado`      TEXT             NOT NULL,
  `explicacion`    TEXT                      DEFAULT NULL,
  `dificultad`     ENUM('basico','intermedio','avanzado') NOT NULL DEFAULT 'basico',
  `estado`         TINYINT(1)       NOT NULL DEFAULT 1,
  `fecha_creacion` DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pregunta`),
  KEY `idx_preg_categoria` (`id_categoria`),
  KEY `idx_preg_estado`    (`estado`),
  CONSTRAINT `fk_preg_cat` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: opciones
-- ============================================================
CREATE TABLE IF NOT EXISTS `opciones` (
  `id_opcion`    INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_pregunta`  INT UNSIGNED NOT NULL,
  `texto_opcion` TEXT         NOT NULL,
  `es_correcta`  TINYINT(1)   NOT NULL DEFAULT 0,
  `orden`        TINYINT      NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_opcion`),
  KEY `idx_opc_pregunta` (`id_pregunta`),
  CONSTRAINT `fk_opc_preg` FOREIGN KEY (`id_pregunta`) REFERENCES `preguntas` (`id_pregunta`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: intentos
-- ============================================================
CREATE TABLE IF NOT EXISTS `intentos` (
  `id_intento`          INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  `codigo_sesion`       VARCHAR(64)    NOT NULL,
  `total_preguntas`     TINYINT        NOT NULL DEFAULT 0,
  `respuestas_correctas`TINYINT        NOT NULL DEFAULT 0,
  `respuestas_incorrectas` TINYINT     NOT NULL DEFAULT 0,
  `puntuacion_porcentaje` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  `completado`          TINYINT(1)     NOT NULL DEFAULT 0,
  `fecha_inicio`        DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_fin`           DATETIME                DEFAULT NULL,
  PRIMARY KEY (`id_intento`),
  KEY `idx_int_sesion` (`codigo_sesion`),
  KEY `idx_int_completado` (`completado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: respuestas_usuario
-- ============================================================
CREATE TABLE IF NOT EXISTS `respuestas_usuario` (
  `id_respuesta`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_intento`           INT UNSIGNED NOT NULL,
  `id_pregunta`          INT UNSIGNED NOT NULL,
  `id_opcion_seleccionada` INT UNSIGNED NOT NULL,
  `era_correcta`         TINYINT(1)   NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_respuesta`),
  UNIQUE KEY `uq_resp_intento_pregunta` (`id_intento`, `id_pregunta`),
  KEY `idx_resp_intento`  (`id_intento`),
  KEY `idx_resp_pregunta` (`id_pregunta`),
  CONSTRAINT `fk_resp_intento`  FOREIGN KEY (`id_intento`)  REFERENCES `intentos`  (`id_intento`) ON DELETE CASCADE,
  CONSTRAINT `fk_resp_pregunta` FOREIGN KEY (`id_pregunta`) REFERENCES `preguntas`  (`id_pregunta`),
  CONSTRAINT `fk_resp_opcion`   FOREIGN KEY (`id_opcion_seleccionada`) REFERENCES `opciones` (`id_opcion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: recursos_ayuda
-- ============================================================
CREATE TABLE IF NOT EXISTS `recursos_ayuda` (
  `id_recurso`     INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre`         VARCHAR(200) NOT NULL,
  `descripcion`    TEXT                  DEFAULT NULL,
  `tipo_recurso`   VARCHAR(80)           DEFAULT NULL,
  `contacto`       VARCHAR(200)          DEFAULT NULL,
  `url_oficial`    VARCHAR(300)          DEFAULT NULL,
  `nota_verificacion` TEXT              DEFAULT NULL,
  `estado`         TINYINT(1)   NOT NULL DEFAULT 1,
  `fecha_creacion` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_recurso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET foreign_key_checks = 1;

-- ============================================================
-- DATOS INICIALES: categorias
-- ============================================================
INSERT INTO `categorias` (`id_categoria`, `nombre`, `slug`, `descripcion`, `icono`, `color`, `estado`, `orden`) VALUES
(1, 'Tipos de violencia',       'tipos-violencia',    'Conoce las distintas formas en que se manifiesta la violencia contra la mujer.',                             'shield-exclamation', '#5636C9', 1, 1),
(2, 'Señales de alerta',        'senales-alerta',     'Conductas y situaciones que pueden indicar una relación o entorno de riesgo.',                               'exclamation-triangle','#D94FC1', 1, 2),
(3, 'Mitos y realidades',       'mitos-realidades',   'Desmonta ideas erróneas que normalizan o minimizan la violencia.',                                           'chat-square-text',   '#A96913', 1, 3),
(4, 'Derechos de las mujeres',  'derechos',           'Conoce los derechos reconocidos y los marcos legales vigentes en Bolivia.',                                  'file-earmark-text',  '#21865A', 1, 4),
(5, 'Prevención y actuación',   'prevencion',         'Estrategias, recursos y orientación para prevenir y responder ante situaciones de violencia.',               'heart',              '#5636C9', 1, 5),
(6, 'Recursos de ayuda',        'recursos-ayuda',     'Instituciones y servicios de orientación disponibles.',                                                      'telephone',          '#281653', 1, 6);

-- ============================================================
-- DATOS INICIALES: preguntas (20 preguntas educativas)
-- ============================================================
INSERT INTO `preguntas` (`id_pregunta`, `id_categoria`, `enunciado`, `explicacion`, `dificultad`, `estado`) VALUES
-- Cat 1: Tipos de violencia
(1,  1, '¿Cuál de las siguientes situaciones es un ejemplo de violencia psicológica?',
     'La violencia psicológica incluye insultos, humillaciones, amenazas y manipulación emocional que dañan la autoestima y la salud mental de la persona afectada. No requiere contacto físico para causar daño.',
     'basico', 1),

(2,  1, '¿Qué es la violencia económica o patrimonial?',
     'La violencia económica consiste en controlar, limitar o negar el acceso a recursos económicos, bienes o trabajo para generar dependencia y control sobre la víctima.',
     'basico', 1),

(3,  1, '¿Cuál de estas conductas puede considerarse violencia digital?',
     'La violencia digital incluye acosar, vigilar, amenazar o difundir contenido íntimo sin consentimiento mediante medios tecnológicos. Es una forma de violencia reconocida legalmente en muchos países.',
     'intermedio', 1),

(4,  1, '¿Qué tipo de violencia se manifiesta a través de golpes, empujones o agresiones físicas?',
     'La violencia física incluye cualquier acción que cause daño corporal, como golpes, empujones, patadas o bofetadas. Es la forma más visible, pero no la única ni necesariamente la más dañina.',
     'basico', 1),

(5,  1, '¿Cuál de las siguientes afirmaciones describe mejor la violencia simbólica?',
     'La violencia simbólica se reproduce mediante discursos, imágenes, estereotipos y normas culturales que subordinan o degradan a las mujeres. Aunque no es directamente física, refuerza las desigualdades de género.',
     'avanzado', 1),

-- Cat 2: Señales de alerta
(6,  2, '¿Cuál de las siguientes conductas puede ser una señal de alerta en una relación?',
     'El control excesivo sobre las actividades, amistades, ropa o comunicaciones de la pareja es una señal de alerta importante. Este comportamiento busca aislar y generar dependencia.',
     'basico', 1),

(7,  2, '¿Qué significa el aislamiento social en el contexto de la violencia contra la mujer?',
     'El aislamiento social es una táctica de control que busca separar a la persona de su red de apoyo —familia, amistades, compañeros de trabajo— para aumentar su dependencia hacia quien ejerce violencia.',
     'intermedio', 1),

(8,  2, '¿Cuál de las siguientes situaciones puede indicar una relación potencialmente dañina?',
     'Las humillaciones frecuentes, especialmente en público, erosionan la autoestima y la confianza de la persona afectada. Junto con el control y el aislamiento, forman un patrón de relación preocupante.',
     'basico', 1),

-- Cat 3: Mitos y realidades
(9,  3, '¿Es correcto afirmar que "si no hay golpes, no hay violencia"?',
     'Esta afirmación es un mito. La violencia puede ser psicológica, económica, sexual, digital o simbólica. No requiere daño físico visible para ser real o dañina.',
     'basico', 1),

(10, 3, 'Sobre la afirmación "los celos son una prueba de amor", ¿qué es correcto?',
     'Los celos patológicos no son una prueba de amor, sino una forma de control. El amor saludable se construye sobre la confianza mutua, el respeto y la autonomía de cada persona.',
     'basico', 1),

(11, 3, '¿Es verdad que la violencia ocurre principalmente en familias de bajos recursos económicos?',
     'Este es un mito. La violencia contra la mujer ocurre en todos los contextos sociales, económicos y culturales. Las creencias que la limitan a ciertos grupos invisibilizan muchos casos.',
     'intermedio', 1),

(12, 3, '¿Quién es responsable de la violencia ejercida en una relación?',
     'La responsabilidad de ejercer violencia corresponde siempre a quien la ejerce. Ninguna conducta, forma de vestir, decisión o característica de la víctima justifica ni provoca la violencia.',
     'basico', 1),

-- Cat 4: Derechos
(13, 4, '¿Qué establece la Ley N.° 348 de Bolivia respecto a la violencia hacia las mujeres?',
     'La Ley N.° 348, "Ley Integral para Garantizar a las Mujeres una Vida Libre de Violencia", promulgada en Bolivia en 2013, reconoce 16 formas de violencia contra las mujeres y establece mecanismos de protección, atención y sanción.',
     'intermedio', 1),

(14, 4, '¿Qué derecho fundamental incluye vivir sin violencia?',
     'El derecho a vivir libre de violencia es un derecho humano fundamental, reconocido en la Constitución Política del Estado de Bolivia, en la Convención sobre la Eliminación de Todas las Formas de Discriminación contra la Mujer (CEDAW) y en la Convención de Belém do Pará.',
     'basico', 1),

(15, 4, '¿Puede una mujer en Bolivia solicitar una medida de protección sin presentar pruebas físicas de violencia?',
     'En Bolivia, la Ley N.° 348 permite solicitar medidas de protección basándose en la declaración de la víctima, sin necesidad de presentar pruebas físicas. El principio de creencia y el enfoque de protección son centrales en esta ley.',
     'avanzado', 1),

-- Cat 5: Prevención y actuación
(16, 5, '¿Cuál es una estrategia adecuada si alguien cercano parece estar viviendo una situación de violencia?',
     'Escuchar sin juzgar, validar los sentimientos de la persona y ofrecer acompañamiento sin presionarla a tomar decisiones inmediatas es una forma de apoyo importante. No se debe presionar, exponer información o actuar sin su consentimiento.',
     'intermedio', 1),

(17, 5, '¿Por qué puede resultar difícil para una persona salir de una relación violenta?',
     'Existen múltiples factores: dependencia económica, miedo, vínculos afectivos, aislamiento social, falta de información sobre recursos y, en ocasiones, normalización de la situación. No salir inmediatamente no significa que la persona acepte la violencia.',
     'intermedio', 1),

(18, 5, '¿Qué puede hacer una persona que presenció una situación de violencia en un espacio público?',
     'En situaciones de riesgo visible, se puede alertar a la persona afectada discretamente, acompañarla si acepta, llamar a servicios de emergencia cuando haya riesgo inmediato y no confrontar directamente a quien ejerce violencia si hay riesgo de escalada.',
     'avanzado', 1),

-- Cat 2: señales + Cat 5: prevención
(19, 2, '¿Cuál de los siguientes comportamientos es una forma de control en una relación?',
     'Revisar el teléfono, las redes sociales o los mensajes de la pareja sin su consentimiento es una forma de control y vigilancia. Aunque a veces se justifica como preocupación, forma parte de un patrón de desconfianza y control.',
     'basico', 1),

(20, 5, '¿Qué significa buscar orientación ante una situación de violencia?',
     'Buscar orientación es una decisión informada y valiente. Implica acudir a personas de confianza, organizaciones especializadas o servicios institucionales para obtener información, acompañamiento o protección. No implica debilidad.',
     'basico', 1),

-- Preguntas adicionales (21-25) para mayor variedad
(21, 1, '¿Cuál de las siguientes situaciones describe violencia sexual?',
     'La violencia sexual incluye cualquier acto sexual realizado sin consentimiento libre e informado. El consentimiento puede retirarse en cualquier momento, incluso dentro de una relación de pareja.',
     'intermedio', 1),

(22, 3, 'Sobre el mito "pedir ayuda es señal de debilidad", ¿qué es correcto?',
     'Buscar ayuda ante una situación difícil requiere reconocer la situación y dar pasos para protegerse. Es una decisión que demuestra fortaleza, no debilidad.',
     'basico', 1),

(23, 4, '¿Qué organismos internacionales han reconocido el derecho de las mujeres a vivir sin violencia?',
     'La CEDAW (Convención sobre la Eliminación de Todas las Formas de Discriminación contra la Mujer), la Convención de Belém do Pará y la Declaración Universal de Derechos Humanos son marcos internacionales clave en la protección de los derechos de las mujeres.',
     'intermedio', 1),

(24, 2, '¿Cuál de las siguientes puede ser una consecuencia del aislamiento prolongado en una relación?',
     'El aislamiento prolongado puede reducir el acceso de la persona a su red de apoyo, dificultar la identificación de la situación como problemática y aumentar la dependencia emocional o económica hacia quien ejerce violencia.',
     'intermedio', 1),

(25, 1, '¿Cómo se define la violencia patrimonial?',
     'La violencia patrimonial implica la destrucción, retención, sustracción o deterioro de bienes, recursos económicos o documentos de la víctima con el fin de generar control o dependencia.',
     'avanzado', 1);

-- ============================================================
-- DATOS INICIALES: opciones (4 por pregunta, 1 correcta)
-- ============================================================
INSERT INTO `opciones` (`id_pregunta`, `texto_opcion`, `es_correcta`, `orden`) VALUES
-- P1: violencia psicológica
(1, 'Insultar y humillar a la pareja constantemente',                                        1, 1),
(1, 'Discutir por diferencias de opinión de forma puntual',                                  0, 2),
(1, 'Pedir explicaciones sobre un retraso inesperado',                                       0, 3),
(1, 'Expresar molestia por una decisión de la pareja',                                       0, 4),
-- P2: violencia económica
(2, 'Controlar o negar el acceso de la mujer a dinero o bienes para generar dependencia',    1, 1),
(2, 'Repartir responsabilidades económicas de forma equitativa en el hogar',                 0, 2),
(2, 'Proponer un presupuesto familiar compartido',                                           0, 3),
(2, 'Gestionar los ahorros con acuerdo de ambas partes',                                     0, 4),
-- P3: violencia digital
(3, 'Publicar imágenes íntimas de alguien sin su consentimiento',                            1, 1),
(3, 'Compartir publicaciones de una persona en redes sociales',                              0, 2),
(3, 'Enviar un mensaje de texto para saludar',                                               0, 3),
(3, 'Etiquetar a alguien en una fotografía pública con su permiso',                         0, 4),
-- P4: violencia física
(4, 'Violencia física',                                                                      1, 1),
(4, 'Violencia simbólica',                                                                   0, 2),
(4, 'Violencia económica',                                                                   0, 3),
(4, 'Violencia digital',                                                                     0, 4),
-- P5: violencia simbólica
(5, 'Es la que se transmite mediante estereotipos, imágenes y discursos que subordinan a las mujeres', 1, 1),
(5, 'Es únicamente la que ocurre en campañas publicitarias',                                0, 2),
(5, 'Es la que se ejerce solo a través de palabras directas',                               0, 3),
(5, 'Es la que se produce exclusivamente en el ámbito laboral',                             0, 4),
-- P6: señal de alerta
(6, 'Controlar la ropa, amistades y comunicaciones de la pareja',                           1, 1),
(6, 'Expresar preferencias sobre cómo vestir de forma respetuosa',                          0, 2),
(6, 'Preguntar cómo le fue a la pareja durante el día',                                     0, 3),
(6, 'Planificar actividades juntos con acuerdo mutuo',                                      0, 4),
-- P7: aislamiento social
(7, 'Separar a la persona de su red de apoyo para aumentar su dependencia',                 1, 1),
(7, 'Disfrutar de actividades en pareja con frecuencia',                                    0, 2),
(7, 'Preferir pasar tiempo a solas de forma libre y elegida',                               0, 3),
(7, 'Mantener una rutina de pareja estable',                                                0, 4),
-- P8: relación dañina
(8, 'Humillaciones frecuentes que erosionan la autoestima',                                 1, 1),
(8, 'Discutir sobre el destino de unas vacaciones',                                         0, 2),
(8, 'Tener diferencias de personalidad en algunos aspectos',                                0, 3),
(8, 'Pasar períodos de menor comunicación por trabajo',                                     0, 4),
-- P9: mito golpes
(9, 'No, es un mito. La violencia puede ser psicológica, económica, sexual o digital',      1, 1),
(9, 'Sí, solo cuenta como violencia lo que deja marcas físicas visibles',                   0, 2),
(9, 'Sí, porque la ley solo reconoce la violencia física',                                  0, 3),
(9, 'No, pero la violencia psicológica no causa daños reales',                              0, 4),
-- P10: celos
(10, 'Es un mito. Los celos patológicos son una forma de control, no de amor',              1, 1),
(10, 'Es verdad, los celos demuestran que la pareja se preocupa',                           0, 2),
(10, 'Depende del nivel de celos, algunos siempre son aceptables',                          0, 3),
(10, 'Es verdad si la relación es reciente',                                                0, 4),
-- P11: mito nivel socioeconómico
(11, 'No, la violencia ocurre en todos los contextos sociales y económicos',                1, 1),
(11, 'Sí, los estudios confirman que solo ocurre en contextos de pobreza',                  0, 2),
(11, 'Sí, en familias con educación universitaria no existe este problema',                 0, 3),
(11, 'No, pero sí es más frecuente en zonas rurales',                                       0, 4),
-- P12: responsabilidad
(12, 'De quien ejerce la violencia, sin excepción',                                         1, 1),
(12, 'De ambas partes por igual, en toda relación conflictiva',                             0, 2),
(12, 'De la víctima, si provocó la situación con su conducta',                              0, 3),
(12, 'Del entorno familiar que no intervino a tiempo',                                      0, 4),
-- P13: Ley 348
(13, 'Reconoce 16 formas de violencia y establece mecanismos de protección y sanción',      1, 1),
(13, 'Regula únicamente la violencia física en el ámbito doméstico',                        0, 2),
(13, 'Establece penas solo para agresiones con armas',                                      0, 3),
(13, 'Se aplica exclusivamente en el municipio de La Paz',                                  0, 4),
-- P14: derecho a vivir sin violencia
(14, 'El derecho a vivir libre de violencia, reconocido como derecho humano fundamental',   1, 1),
(14, 'El derecho a la propiedad privada',                                                   0, 2),
(14, 'El derecho al sufragio',                                                              0, 3),
(14, 'El derecho a la educación básica',                                                    0, 4),
-- P15: medida de protección
(15, 'Sí, la Ley N.° 348 permite solicitarlas basándose en la declaración de la víctima',  1, 1),
(15, 'No, siempre se requieren pruebas físicas o testimonios de terceros',                  0, 2),
(15, 'Solo en casos de violencia física con lesiones documentadas',                         0, 3),
(15, 'Únicamente cuando existe una denuncia policial previa',                               0, 4),
-- P16: apoyo a alguien cercano
(16, 'Escuchar sin juzgar y ofrecer acompañamiento respetando su decisión',                 1, 1),
(16, 'Confrontar directamente al agresor para defender a la persona',                       0, 2),
(16, 'Obligarla a dejar la relación de inmediato por su propio bien',                       0, 3),
(16, 'Publicar la situación en redes sociales para generar presión',                        0, 4),
-- P17: dificultad para salir
(17, 'Por múltiples factores: económicos, emocionales, miedo y falta de red de apoyo',      1, 1),
(17, 'Porque en realidad no quieren salir de la situación',                                 0, 2),
(17, 'Porque la violencia no es tan grave como parece',                                     0, 3),
(17, 'Porque las leyes no permiten salir de una relación de pareja',                        0, 4),
-- P18: testigo en espacio público
(18, 'Alertar discretamente a la persona afectada o llamar a servicios de emergencia si hay riesgo', 1, 1),
(18, 'Ignorar la situación para no involucrarse en problemas ajenos',                       0, 2),
(18, 'Confrontar físicamente al agresor para detenerlo',                                    0, 3),
(18, 'Grabar la situación y publicarla sin autorización de la víctima',                     0, 4),
-- P19: control en relación
(19, 'Revisar el teléfono o las redes sociales de la pareja sin su consentimiento',         1, 1),
(19, 'Compartir contraseñas de forma voluntaria y acordada entre ambos',                    0, 2),
(19, 'Preguntar si la pareja llegó bien a casa después de un viaje',                        0, 3),
(19, 'Recordar compromisos importantes en la agenda compartida',                            0, 4),
-- P20: buscar orientación
(20, 'Una decisión informada y valiente para obtener información y acompañamiento',         1, 1),
(20, 'Una señal de que la situación es demasiado grave para resolverse',                    0, 2),
(20, 'Una acción que puede empeorar la situación en la mayoría de los casos',               0, 3),
(20, 'Una obligación legal que implica denunciar formalmente',                              0, 4),
-- P21: violencia sexual
(21, 'Cualquier acto sexual realizado sin consentimiento libre e informado',                1, 1),
(21, 'Solo las agresiones cometidas por desconocidos fuera del hogar',                     0, 2),
(21, 'Únicamente los actos que dejan lesiones físicas documentadas',                       0, 3),
(21, 'Solo lo que ocurre dentro del matrimonio y es reportado',                            0, 4),
-- P22: mito pedir ayuda
(22, 'Es un mito. Buscar ayuda demuestra fortaleza y capacidad de reconocer la situación', 1, 1),
(22, 'Es verdad, las personas fuertes resuelven sus problemas solas',                       0, 2),
(22, 'Depende del tipo de ayuda que se busque',                                             0, 3),
(22, 'Es verdad en contextos culturales donde se valora la privacidad familiar',            0, 4),
-- P23: organismos internacionales
(23, 'La CEDAW, la Convención de Belém do Pará y la Declaración Universal de DDHH',        1, 1),
(23, 'Únicamente la ONU, mediante resoluciones no vinculantes',                             0, 2),
(23, 'Solo el Tribunal Penal Internacional en casos de guerra',                             0, 3),
(23, 'Exclusivamente los gobiernos de cada país mediante legislación nacional',             0, 4),
-- P24: consecuencia del aislamiento
(24, 'Reduce el acceso a redes de apoyo y aumenta la dependencia hacia el agresor',        1, 1),
(24, 'Fortalece los vínculos afectivos dentro de la pareja',                               0, 2),
(24, 'Permite a la pareja conocerse mejor sin influencias externas',                       0, 3),
(24, 'Mejora la comunicación al eliminar distracciones sociales',                          0, 4),
-- P25: violencia patrimonial
(25, 'Destrucción, retención o deterioro de bienes y recursos para generar dependencia',   1, 1),
(25, 'Cualquier conflicto sobre herencias familiares entre parientes',                     0, 2),
(25, 'La negativa a compartir gastos del hogar de forma equitativa',                      0, 3),
(25, 'Los problemas económicos derivados de una crisis financiera',                        0, 4);

-- ============================================================
-- DATOS INICIALES: recursos_ayuda
-- Nota: solo se incluyen recursos verificados con datos generales.
-- No se agregan números de teléfono específicos sin verificación.
-- ============================================================
INSERT INTO `recursos_ayuda` (`nombre`, `descripcion`, `tipo_recurso`, `contacto`, `url_oficial`, `nota_verificacion`, `estado`) VALUES
(
  'SLIM - Servicio Legal Integral Municipal',
  'El SLIM brinda orientación legal y psicológica gratuita a mujeres en situación de violencia, así como a sus familias. Está presente en los municipios de Bolivia.',
  'Servicio legal y psicológico',
  'Acuda a la alcaldía o gobierno municipal de su ciudad para conocer la ubicación del SLIM más cercano.',
  'https://www.mujeres.gob.bo',
  'Recurso verificado. Los datos de contacto específicos varían por municipio. Consulte la alcaldía local.',
  1
),
(
  'SEPDAVI - Servicio Plurinacional de Defensa de la Víctima',
  'Institución boliviana que brinda asistencia legal gratuita, orientación y representación a víctimas de violencia y delitos.',
  'Asistencia legal gratuita',
  'Consulte la sede regional más cercana en su departamento.',
  'https://www.sepdavi.gob.bo',
  'Recurso verificado a nivel institucional. Contactos locales varían por departamento.',
  1
),
(
  'Fiscalía del Estado Plurinacional de Bolivia',
  'Institución responsable de investigar delitos. Puede presentar denuncias por violencia de género ante la fiscalía más cercana.',
  'Denuncia y proceso penal',
  'Acuda a la Fiscalía de su ciudad o municipio.',
  'https://www.fiscalia.gob.bo',
  'Recurso institucional verificado.',
  1
),
(
  'Defensoría del Pueblo de Bolivia',
  'Vela por la vigencia, promoción y difusión de los derechos humanos. Puede recibir quejas sobre vulneración de derechos.',
  'Defensa de derechos humanos',
  'Consulte las oficinas departamentales o regionales de la Defensoría del Pueblo.',
  'https://www.defensoria.gob.bo',
  'Recurso institucional verificado.',
  1
),
(
  'Ministerio de Justicia y Transparencia Institucional',
  'Entidad gubernamental que coordina políticas de prevención de violencia contra las mujeres en Bolivia.',
  'Política pública y coordinación',
  NULL,
  'https://www.justicia.gob.bo',
  'Recurso institucional verificado. No se publica número de contacto específico sin verificación actualizada.',
  1
),
(
  'ONU Mujeres Bolivia',
  'Organismo de las Naciones Unidas dedicado a la igualdad de género y el empoderamiento de las mujeres, con presencia en Bolivia.',
  'Organismo internacional',
  NULL,
  'https://lac.unwomen.org/es',
  'Recurso verificado a nivel internacional.',
  1
);
