# Guía de Publicación y Despliegue de VIOLET en Internet

Esta guía describe el procedimiento detallado para publicar el proyecto web **VIOLET** en un servicio de alojamiento web (hosting) público con soporte para **PHP 8.1+** y **MySQL / MariaDB**, permitiendo que cualquier usuario en internet pueda acceder e interactuar con la aplicación.

---

## 📌 1. Aclaración Importante sobre GitHub Pages

> [!WARNING]
> **GitHub Pages NO es compatible con este proyecto.**  
> GitHub Pages únicamente aloja sitios web estáticos (HTML, CSS y JavaScript del lado del cliente). **VIOLET requiere un servidor con intérprete PHP y un motor de base de datos MySQL / MariaDB** para evaluar las respuestas del quiz, gestionar sesiones seguras, registrar intentos y administrar los contenidos en el panel administrativo.

Para obtener una URL pública funcional se requiere un hosting compatible con la pila **LAMP / WAMP** (Linux/Apache/MySQL/PHP).

---

## ⚙️ 2. Requisitos Técnicos del Servidor de Hosting

- **PHP:** Versión 8.1 o superior (recomendado PHP 8.2+).
- **Extensiones de PHP requeridas:**
  - `pdo_mysql` (Conexión centralizada a base de datos mediante PDO)
  - `session` (Gestión de sesiones de usuario y administrador)
  - `mbstring` (Manipulación segura de texto UTF-8)
  - `json` (Procesamiento de datos estructurados)
- **Base de Datos:** MySQL 5.7+ o MariaDB 10.4+ con soporte para `utf8mb4_unicode_ci` y motor `InnoDB`.
- **Servidor Web:** Apache (con soporte de `.htaccess`) o Nginx.
- **Directorio de publicación (DocumentRoot):** Debe apuntar a la raíz del proyecto.

---

## 🏆 3. Hosting Recomendado para VIOLET

Para este proyecto existen dos alternativas recomendadas según el tipo de entorno deseado:

### Opción A (Recomendada - Gratuita y sin tarjeta de crédito): **InfinityFree**
- **Ventajas:** 100% gratuito, incluye PHP 8.2, bases de datos MySQL, acceso a **phpMyAdmin**, certificados SSL (HTTPS) gratuitos y subdominios gratuitos (ej: `https://violeta.infinityfreeapp.com`).
- **Ideal para:** Demostraciones académicas, concursos, evaluaciones y proyectos web PHP clásicos.

### Opción B (Alternativa Gratuita Avanzada): **Alwaysdata**
- **Ventajas:** Permite hasta 100 MB de almacenamiento gratuito, soporte de PHP moderno, MySQL, phpMyAdmin, acceso SSH y subdominio gratuito (ej: `https://violeta.alwaysdata.net`).

---

## 🚀 4. Procedimiento de Despliegue Paso a Paso (Ejemplo con InfinityFree)

### Paso 1: Crear la cuenta en el Hosting
1. Ingrese a [https://www.infinityfree.com](https://www.infinityfree.com) y cree una cuenta gratuita.
2. En el panel de control, haga clic en **Create Account**.
3. Elija un subdominio gratuito (por ejemplo: `violeta-bolivia.infinityfreeapp.com`).
4. Complete la creación y espere unos minutos a que el panel active la cuenta.

---

### Paso 2: Crear la Base de Datos MySQL
1. En el panel de su cuenta de hosting, diríjase a la sección **MySQL Databases**.
2. Ingrese un nombre para la base de datos (por ejemplo `db_violeta`) y presione **Create Database**.
3. El sistema le mostrará los datos de conexión asignados por el proveedor. **Copie y guarde estos datos:**
   - **MySQL Host / Server:** (Ejemplo: `sql123.infinityfree.com` o similar)
   - **MySQL Database Name:** (Ejemplo: `if0_38000000_db_violeta`)
   - **MySQL Username:** (Ejemplo: `if0_38000000`)
   - **MySQL Password:** (La contraseña de su cuenta de hosting)
   - **MySQL Port:** `3306` (puerto estándar)

---

### Paso 3: Importar el Script de Base de Datos `database/violeta_db.sql`
1. En la sección **MySQL Databases** del panel, haga clic en el botón **phpMyAdmin** junto a su base de datos.
2. Una vez dentro de phpMyAdmin, asegúrese de hacer clic en el nombre de su base de datos en la barra lateral izquierda (para que quede seleccionada).
3. Diríjase a la pestaña superior **Importar** (Import).
4. En **Seleccionar archivo**, elija el archivo [database/violeta_db.sql](file:///C:/xampp/htdocs/VIOLETA/database/violeta_db.sql) del proyecto.
5. > [!NOTE]
   > En hostings compartidos donde el panel ya creó y seleccionó la base de datos, si el servidor le arroja un error relativo a `CREATE DATABASE`, abra el archivo SQL en un editor de texto y comente las siguientes dos líneas antes de importar:
   > ```sql
   > -- CREATE DATABASE IF NOT EXISTS `violeta_db` ...;
   > -- USE `violeta_db`;
   > ```
6. Haga clic en el botón **Continuar** / **Importar**.
7. Verifique que se hayan creado las tablas: `administradores`, `categorias`, `preguntas`, `opciones`, `intentos`, `respuestas_usuario`, `recursos_ayuda`.

---

### Paso 4: Subir los Archivos de la Aplicación
Existen dos formas de subir el código al servidor:

#### Método 1: Mediante el Administrador de Archivos Web (File Manager)
1. En el panel del hosting, abra el **Online File Manager**.
2. Ingrese a la carpeta raíz pública: en InfinityFree es la carpeta **`htdocs/`**.
3. Elimine cualquier archivo de bienvenida predeterminado (como `index2.html` o similar).
4. Suba todos los archivos y carpetas del proyecto VIOLET dentro de `htdocs/`:
   ```text
   htdocs/
   ├── admin/
   ├── assets/
   ├── config/
   ├── database/
   ├── includes/
   ├── desarrolladores.php
   ├── index.php
   ├── informacion.php
   ├── quiz.php
   ├── resultado.php
   ├── salida.php
   └── ...
   ```
   *(Consejo: Para mayor velocidad, comprima el contenido de VIOLET en un archivo `.zip`, súbalo a `htdocs/` y use la función **Extract / Descomprimir** del File Manager).*

#### Método 2: Mediante Cliente FTP (FileZilla)
1. Descargue y abra **FileZilla Client**.
2. Conéctese con los datos FTP proporcionados por su hosting (Host FTP, Usuario FTP, Contraseña y Puerto 21).
3. Transfiera los archivos a la carpeta `htdocs/` del servidor remoto.

---

### Paso 5: Configurar las Credenciales de la Base de Datos en el Hosting

El proyecto VIOLET fue actualizado con una arquitectura de conexión flexible. Para conectar la aplicación a la base de datos del hosting sin exponer credenciales en GitHub:

1. En el File Manager del hosting, ingrese a la carpeta `config/`.
2. Cree un nuevo archivo llamado **`conexion.local.php`** (puede guiarse de `conexion.local.php.example`).
3. Pegue el siguiente contenido con los datos reales que anotó en el **Paso 2**:

```php
<?php
/**
 * Configuración de conexión en hosting de producción
 * Este archivo permanece privado en el servidor y nunca se publica en GitHub.
 */
define('DB_HOST', 'sql123.infinityfree.com'); // Su MySQL Host
define('DB_PORT', '3306');                    // Puerto estándar
define('DB_NAME', 'if0_38000000_db_violeta'); // Nombre asignado de la base de datos
define('DB_USER', 'if0_38000000');            // Usuario asignado
define('DB_PASS', 'SuPasswordDelHosting');    // Contraseña
define('DB_CHARSET', 'utf8mb4');

// En hostings donde el proyecto está en la raíz de htdocs, la URL base es vacía:
define('BASE_URL', '');
```

4. Guarde el archivo.

*(Nota: En plataformas PaaS como Render o Railway, en lugar de un archivo PHP puede ingresar estas variables directamente en la sección **Environment Variables** del panel web: `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS` o `DATABASE_URL`).*

---

### Paso 6: Configurar un Administrador Inicial

Para ingresar al panel de administración (`/admin/login.php`) en su hosting:
1. Abra **phpMyAdmin** en el panel de su hosting.
2. Diríjase a la pestaña **SQL** y ejecute la siguiente sentencia para crear su usuario administrador:

```sql
INSERT INTO `administradores` (`nombre`, `email`, `password_hash`, `activo`) 
VALUES (
  'Administrador VIOLET',
  'admin@violeta.org',
  -- Contraseña: VioletaAdmin2026! (cifrada con BCRYPT)
  '$2y$10$7zB3cW9Ww74Dqf8oJ6d3tOqX1.bX1R1V5Fq2qY1J4K7L0M3N6P9Ri',
  1
);
```

---

## ✅ 5. Verificación de la Aplicación en Internet

Una vez completados los pasos anteriores:

1. Abra su navegador web e ingrese a su URL pública:  
   `https://su-subdominio.infinityfreeapp.com/`
2. **Lista de comprobación (Checklist):**
   - [ ] La portada de bienvenida carga correctamente con estilos y tipografías.
   - [ ] La navegación entre páginas (`Información`, `Quiz`, `Desarrolladores`) funciona sin errores 404.
   - [ ] El **Quiz interactivo** permite responder las preguntas pedagógicas y genera el resultado en servidor.
   - [ ] El botón de **Salida rápida** redirige inmediatamente a un portal neutral.
   - [ ] El panel administrativo en `/admin/login.php` permite iniciar sesión y visualizar las estadísticas de los intentos.
