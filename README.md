# VIOLET — Plataforma Educativa sobre Prevención de la Violencia contra la Mujer

**Repositorio oficial:** [https://github.com/RicaldiEdson/VIOLET](https://github.com/RicaldiEdson/VIOLET)  
**Autor:** Edson Ricaldi ([@RicaldiEdson](https://github.com/RicaldiEdson))  
**Gestión:** 2026  

---

## 🌸 1. Descripción General del Proyecto

**VIOLET** es una plataforma web formativa, interactiva y de concientización diseñada para brindar información clara, herramientas de prevención y orientación inmediata sobre los diferentes tipos de violencia contra las mujeres, sustentada en el marco legal boliviano (**Ley N.° 348** — *Ley Integral para Garantizar a las Mujeres una Vida Libre de Violencia*).

### Funcionalidades principales implementadas en el código:
- **Módulo Informativo:** Presentación didáctica sobre los tipos de violencia (psicológica, física, sexual, económica/patrimonial, digital, simbólica), señales de alerta temprana, mitos y realidades, derechos fundamentales y rutas de actuación ante emergencias.
- **Quiz de Autoevaluación y Reflexión:** Evaluación interactiva en tiempo real con preguntas aleatorias extraídas dinámicamente desde la base de datos MySQL, validación en servidor, cálculo de puntaje, análisis por categoría y retroalimentación pedagógica contextualizada.
- **Botón de Salida Rápida (Protección al Usuario):** Mecanismo de seguridad para personas que consulten el portal en situaciones de riesgo o entornos vigilados, accesible mediante un botón visible en la cabecera o mediante el atajo de teclado (`Doble pulsación de Escape` o `Alt + Q`), que redirige inmediatamente a un sitio neutral y borra el rastro de navegación reciente.
- **Directorio de Recursos y Asistencia:** Catálogo estructurado de instituciones de auxilio y orientación (SLIM, SEPDAVI, Fiscalía General del Estado, Defensoría del Pueblo, Ministerio de Justicia, ONU Mujeres Bolivia).
- **Panel Administrativo Protegido:** Módulo de gestión interna (`/admin/`) con autenticación mediante sesiones seguras y contraseñas cifradas con BCRYPT, panel de control con métricas clave, gestión (CRUD) de preguntas pedagógicas, categorías temáticas, directorio de recursos y auditoría de resultados de los intentos del quiz.
- **Sección de Desarrolladores:** Ficha técnica y créditos institucionales de desarrollo.

---

## 🛠️ 2. Tecnologías Utilizadas

- **Lenguaje Backend:** PHP 8.1+ (arquitectura modular, tipado estricto en funciones clave y separación clara de responsabilidades).
- **Base de Datos:** MySQL 5.7+ / MariaDB 10.4+ con motor `InnoDB` y codificación `utf8mb4_unicode_ci`.
- **Acceso a Datos:** Conexión centralizada mediante **PDO** con sentencias preparadas (Prepared Statements) para prevenir inyecciones SQL.
- **Frontend y Diseño:**
  - HTML5 semántico accesible.
  - CSS3 puro con sistema de diseño modular basado en variables CSS (`assets/css/estilos.css`).
  - Framework Bootstrap 5.3.3 y librería de iconografía Bootstrap Icons 1.11.3 (vía CDN seguro).
- **JavaScript del Cliente:** Vanilla JavaScript moderno (`assets/js/app.js`), sin dependencias pesadas, encargado de atajos de teclado, interacciones fluidas y seguridad del usuario.
- **Seguridad Aplicada:**
  - Protección contra CSRF mediante generación y validación de tokens criptográficos aleatorios (`random_bytes(32)`) por sesión.
  - Protección contra XSS mediante función de sanitización y escapado contextual `e()`.
  - Hashing seguro de contraseñas con `password_hash()` y algoritmo BCRYPT.
  - Manejo seguro de sesiones HTTP (`httponly`, `samesite = Lax`, regeneración periódica de identificador de sesión).

---

## 📋 3. Requisitos del Sistema

Para desplegar y ejecutar VIOLET localmente se requiere:
1. Servidor web local con soporte para PHP 8.1 o superior (por ejemplo, **XAMPP**, **WampServer**, **Laragon** o servidor embebido de PHP).
2. Servidor de bases de datos **MySQL 5.7+** o **MariaDB 10.4+**.
3. Extensiones de PHP activadas:
   - `pdo_mysql`
   - `session`
   - `json`
   - `mbstring`
4. Navegador web moderno (Google Chrome, Mozilla Firefox, Microsoft Edge, Safari o navegadores basados en Chromium).

---

## 🗄️ 4. Instalación de la Base de Datos

El script de la base de datos se encuentra en el repositorio en:
```text
database/violeta_db.sql
```

### Pasos para importar mediante phpMyAdmin:
1. Inicie los módulos de **Apache** y **MySQL** desde el Panel de Control de XAMPP.
2. Abra su navegador web y acceda a [http://localhost/phpmyadmin](http://localhost/phpmyadmin).
3. En la barra superior, haga clic en la pestaña **Importar**.
4. En la opción **Seleccionar archivo**, busque y elija el archivo `database/violeta_db.sql` ubicado en la carpeta del proyecto.
5. Verifique que el juego de caracteres esté en **utf-8** y presione el botón **Importar** (o **Continuar**) al final de la página.
6. El script creará automáticamente la base de datos `violeta_db`, definirá las tablas con sus respectivas restricciones de clave foránea e índices, y precargará las categorías formativas, 25 preguntas educativas con sus opciones y el directorio de instituciones de ayuda.

---

## ⚙️ 5. Configuración de la Conexión a la Base de Datos

La configuración de conexión se encuentra centralizada en el archivo `config/conexion.php`:

```php
define('DB_HOST', '127.0.0.1');  // Dirección del servidor MySQL (localhost o 127.0.0.1)
define('DB_PORT', '3307');       // Puerto de MySQL (ajustar a 3306 si usa el puerto estándar de XAMPP)
define('DB_NAME', 'violeta_db'); // Nombre de la base de datos
define('DB_USER', 'root');       // Usuario de MySQL (por defecto 'root' en XAMPP)
define('DB_PASS', '');           // Contraseña de MySQL (vacío por defecto en XAMPP)
define('DB_CHARSET', 'utf8mb4');
```

> **Nota para la configuración local:**  
> Si su instalación de XAMPP ejecuta MySQL en el puerto por defecto `3306`, modifique la línea 9 de `config/conexion.php` cambiando `'3307'` por `'3306'`.

En `config/configuracion.php` se definen las rutas base y variables de entorno:
```php
define('VIOLETA_ENV', 'development'); // 'development' para depuración o 'production'
define('BASE_URL', '/violeta');       // Ruta relativa desde htdocs
```

---

## 🚀 6. Pasos para Ejecutar el Proyecto con XAMPP

1. Copie o clone la carpeta del proyecto dentro del directorio raíz de su servidor XAMPP:
   ```text
   C:\xampp\htdocs\VIOLETA
   ```
2. Inicie los servicios **Apache** y **MySQL** desde el XAMPP Control Panel.
3. Asegúrese de haber importado la base de datos `database/violeta_db.sql` tal como se describe en el Paso 4.
4. Abra su navegador web y diríjase a la URL local de la aplicación:
   ```text
   http://localhost/VIOLETA/
   ```

### Acceso a las secciones principales:
- **Página de inicio:** `http://localhost/VIOLETA/index.php`
- **Módulo formativo:** `http://localhost/VIOLETA/informacion.php`
- **Quiz pedagógico:** `http://localhost/VIOLETA/quiz.php`
- **Ficha de desarrolladores:** `http://localhost/VIOLETA/desarrolladores.php`
- **Panel de administración:** `http://localhost/VIOLETA/admin/login.php`

---

## 🔐 7. Acceso al Panel Administrativo

El panel administrativo permite gestionar los contenidos del portal y visualizar auditorías de las evaluaciones.

- **URL de acceso:** `http://localhost/VIOLETA/admin/login.php`
- **Autenticación:** El sistema valida contra la tabla `administradores`.
- Para crear un usuario administrador inicial con contraseña cifrada BCRYPT, puede ejecutar en phpMyAdmin la siguiente consulta SQL (reemplazando el correo y generando el hash seguro):

```sql
-- Ejemplo de inserción de administrador seguro:
-- La contraseña de ejemplo es: VioletaAdmin2026!
INSERT INTO `administradores` (`nombre`, `email`, `password_hash`, `activo`) 
VALUES (
  'Administrador VIOLET',
  'admin@violeta.org',
  '$2y$10$wJtK5s7i1O6R5rM4yQn9le5aKz0O.jQkU2d3uF8a9g0e1H2i3J4K5', -- Hash BCRYPT generado con password_hash()
  1
) ON DUPLICATE KEY UPDATE `activo` = 1;
```

---

## 📂 8. Estructura General del Proyecto

```text
VIOLETA/
├── .gitignore                    # Reglas de exclusión de Git para entorno PHP/XAMPP
├── README.md                     # Documentación completa del proyecto
├── desarrolladores.php           # Ficha técnica institucional del equipo de desarrollo
├── index.php                     # Portada principal y presentación del portal
├── informacion.php               # Guía educativa completa sobre prevención y Ley 348
├── quiz.php                      # Quiz interactivo aleatorio con validación en servidor
├── resultado.php                 # Visualización de puntaje, análisis pedagógico y recomendaciones
├── salida.php                    # Redireccionamiento seguro de emergencia
│
├── admin/                        # Panel de administración interno
│   ├── categorias.php            # Gestión y listado de categorías temáticas
│   ├── dashboard.php             # Métricas clave, estadísticas y accesos directos
│   ├── login.php                 # Autenticación administrativa segura con CSRF y BCRYPT
│   ├── logout.php                # Cierre de sesión y revocación segura de credenciales
│   ├── pregunta_form.php         # Formulario de alta y edición de preguntas y opciones
│   ├── preguntas.php             # Catálogo y filtro de preguntas educativas
│   ├── recursos.php              # Administración del directorio de instituciones de apoyo
│   ├── resultados.php            # Auditoría y seguimiento de evaluaciones realizadas
│   └── includes/
│       ├── auth_guard.php        # Middleware guardián de verificación de sesión activa
│       ├── header_admin.php      # Cabecera y navegación del panel administrativo
│       └── footer_admin.php      # Pie de página del panel administrativo
│
├── assets/                       # Recursos estáticos del cliente
│   ├── css/
│   │   └── estilos.css           # Hoja de estilos con variables de color, tipografía y componentes
│   └── js/
│       └── app.js                # Lógica de salida rápida, atajos y comportamiento dinámico
│
├── config/                       # Archivos de configuración del sistema
│   ├── conexion.php              # Conexión centralizada a MySQL con PDO y manejo de errores
│   └── configuracion.php         # Constantes de entorno, rutas base y directivas de reporte
│
├── database/                     # Almacenamiento del esquema y datos iniciales
│   └── violeta_db.sql            # Script SQL oficial con DDL (tablas) y DML (datos pedagógicos)
│
└── includes/                     # Componentes y utilidades reutilizables
    ├── footer.php                # Pie de página público institucional
    ├── funciones.php             # Librería central de validaciones, sanitización, CSRF y consultas
    └── header.php                # Cabecera pública, menú de navegación y botón de salida rápida
```

---

## 🛡️ 9. Privacidad y Seguridad

- El proyecto **no recopila datos personales sensibles** de los usuarios que realizan el quiz formativo; las evaluaciones se asocian a códigos de sesión anónimos para propósitos estrictamente pedagógicos y estadísticos.
- El repositorio está configurado para omitir archivos temporales, logs de servidor y volcados de datos privados mediante el archivo `.gitignore`.
- Los datos de contacto institucionales corresponden a servicios oficiales de atención y asistencia legal del Estado Plurinacional de Bolivia.

---

## 📜 10. Licencia y Créditos

Proyecto desarrollado con fines educativos y de impacto social para la prevención de la violencia contra las mujeres.  
Cochabamba, Bolivia — Gestión 2026.
