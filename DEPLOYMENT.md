# Guía de Despliegue en InfinityFree — Proyecto VIOLET

Esta guía detalla el procedimiento oficial y paso a paso para publicar el proyecto **VIOLET** de forma gratuita en el servicio de hosting **InfinityFree**, obteniendo una URL pública accesible desde cualquier dispositivo conectado a internet.

---

## 📌 1. Información Técnica del Entorno InfinityFree

- **Servidor Web:** Apache con soporte de `.htaccess`.
- **Intérprete PHP:** PHP 8.2 activo (compatible con la arquitectura PHP 8.1+ de VIOLET).
- **Extensiones requeridas y disponibles:** `pdo_mysql`, `session`, `mbstring`, `json`.
- **Base de Datos:** MySQL / MariaDB compatible con `InnoDB` y `utf8mb4_unicode_ci`.
- **Directorio Raíz Público:** La carpeta pública de InfinityFree es obligatoriamente **`htdocs/`**.
- **Acceso a Base de Datos:** Mediante **phpMyAdmin** integrado en el panel de control.
- **Acceso a Archivos:** Administrador de archivos web (*Monsta File Manager*) y cliente FTP (*FileZilla*).

> [!IMPORTANT]
> **Diferencia entre Local y Remoto:**
> - En tu computadora local (XAMPP), la aplicación utiliza `127.0.0.1`, puerto `3307`, usuario `root` y base de datos `violeta_db`.
> - En **InfinityFree**, el servidor MySQL es un host remoto independiente (por ejemplo `sql123.infinityfree.com` o `sqlXXX.epizy.com`), puerto `3306`, y la base de datos y usuario llevan un prefijo asignado (por ejemplo `if0_38000000_violeta` e `if0_38000000`).
> - **El proyecto conserva ambas configuraciones separadas sin interferir entre sí.**

---

## 📂 2. Archivos a Subir al Directorio Público (`htdocs/`)

Al conectarse al servidor de InfinityFree, todos los archivos del proyecto deben ubicarse directamente dentro del directorio **`htdocs/`**:

```text
htdocs/
├── .htaccess                     # Reglas de seguridad y protección de archivos
├── desarrolladores.php           # Página de créditos y ficha técnica
├── index.php                     # Portada principal del portal
├── informacion.php               # Guía educativa y normativa (Ley 348)
├── quiz.php                      # Quiz interactivo aleatorio
├── resultado.php                 # Evaluación pedagógica en servidor
├── salida.php                    # Redirección inmediata de seguridad
├── admin/                        # Panel administrativo completo
│   ├── categorias.php
│   ├── dashboard.php
│   ├── login.php
│   ├── logout.php
│   ├── pregunta_form.php
│   ├── preguntas.php
│   ├── recursos.php
│   ├── resultados.php
│   └── includes/
│       ├── auth_guard.php
│       ├── footer_admin.php
│       └── header_admin.php
├── assets/                       # Hojas de estilo y scripts cliente
│   ├── css/
│   │   └── estilos.css
│   └── js/
│       └── app.js
├── config/                       # Configuraciones del sistema
│   ├── conexion.php              # Gestor central de conexión PDO
│   ├── conexion.local.php        # [SE CREA EN EL HOSTING] Credenciales reales de InfinityFree
│   └── configuracion.php         # Rutas, sesiones y metadatos
├── database/                     # Scripts SQL
│   ├── violeta_db.sql            # Script original (entorno local XAMPP)
│   └── violeta_db_infinityfree.sql # Script adaptado para phpMyAdmin de InfinityFree
└── includes/                     # Componentes compartidos
    ├── footer.php
    ├── funciones.php
    └── header.php
```

> [!CAUTION]
> **Archivos que NO deben subirse ni exponerse:**
> - No subas copias de seguridad personales, volcados temporales (`*.bak`, `*.tmp`) ni carpetas de Git (`.git/`).
> - El archivo `.htaccess` configurado en el proyecto bloquea automáticamente cualquier intento de descarga externa directa de archivos `.sql`, `.env` o archivos locales de configuración.

---

## 🚀 3. Procedimiento de Publicación Paso a Paso

---

### Paso 1: Crear la Cuenta y el Sitio Web en InfinityFree

1. Ingrese a [https://www.infinityfree.com](https://www.infinityfree.com) y cree una cuenta con su correo electrónico (o inicie sesión).
2. En el panel principal (*Dashboard*), haga clic en el botón **+ Create Account**.
3. **Paso 1 del asistente (Domain Selection):**
   - Seleccione la pestaña **Free Subdomain** (Subdominio gratuito).
   - Escriba el nombre deseado para su portal (por ejemplo: `violeta-web` o `violeta-bolivia`).
   - Elija una extensión de dominio disponible (por ejemplo: `infinityfreeapp.com` o `epizy.com`).
   - Haga clic en **Check Availability**.
4. **Paso 2 del asistente (Account Information):**
   - Deje la opción de contraseña en automático o defina una contraseña segura.
   - Presione **Create Account**.
5. Su cuenta de hosting quedará creada. El panel le mostrará un mensaje de confirmación y su nombre de usuario de hosting (con formato `if0_XXXXXXXX`).

---

### Paso 2: Crear la Base de Datos Remota en el Panel

1. En el panel de su cuenta en InfinityFree, haga clic en el botón verde **Control Panel** (o *Open Control Panel*).
   *(Si es la primera vez que ingresa, le pedirá aceptar los términos haciendo clic en "I Approve").*
2. Busque la sección **Databases** y haga clic en **MySQL Databases**.
3. En el campo **Create New Database**:
   - Escriba el nombre de la base de datos (por ejemplo: `violeta`).
   - Haga clic en el botón **Create Database**.
4. En la tabla inferior verá su base de datos creada. **Copie y guarde los siguientes 4 datos que muestra la página:**
   - **MySQL Host / Server:** (Suele ser `sql123.infinityfree.com` o similar; varía según su servidor).
   - **MySQL Database Name:** (Tendrá el formato `if0_XXXXXXXX_violeta`).
   - **MySQL Username:** (Tendrá el formato `if0_XXXXXXXX`).
   - **MySQL Password:** (La contraseña de su cuenta de hosting, visible en el panel del cliente en *Account Details > Password*).
   - **Port:** `3306` (puerto estándar de MySQL en InfinityFree).

---

### Paso 3: Importar la Base de Datos con phpMyAdmin

1. En la misma página de **MySQL Databases**, en la fila de su base de datos recién creada, haga clic en el botón **Admin** (o **phpMyAdmin**).
2. Se abrirá la interfaz web de phpMyAdmin conectada a su base de datos remota.
3. En la barra lateral izquierda, haga clic en el nombre de su base de datos (`if0_XXXXXXXX_violeta`) para asegurarse de que esté seleccionada.
4. En la barra superior, haga clic en la pestaña **Importar** (*Import*).
5. En **Seleccionar archivo**, elija el archivo adaptado del proyecto:
   ```text
   database/violeta_db_infinityfree.sql
   ```
   *(Este archivo ya viene adaptado específicamente para InfinityFree: omite sentencias restringidas de creación global y crea todas las tablas, 25 preguntas educativas, categorías y recursos directamente en su base de datos).*
6. Deje las opciones en formato SQL y codificación UTF-8, y haga clic en **Continuar** / **Importar** al final de la página.
7. Compruebe que aparezca el mensaje de éxito en verde y que en el panel izquierdo figuren las 7 tablas del sistema:
   - `administradores`
   - `categorias`
   - `preguntas`
   - `opciones`
   - `intentos`
   - `respuestas_usuario`
   - `recursos_ayuda`

---

### Paso 4: Subir los Archivos de la Aplicación

Puede utilizar cualquiera de las dos opciones siguientes:

#### Opción A: Mediante el Administrador de Archivos Web (Recomendada para rapidez)
1. En el panel de su cuenta de InfinityFree, haga clic en **File Manager**.
2. Haga doble clic para ingresar a la carpeta **`htdocs/`**.
3. Si existe algún archivo por defecto (como `index2.html` o `DO NOT UPLOAD FILES HERE`), elimínelo.
4. Para subir todo rápidamente:
   - En su computadora, comprima todo el contenido de la carpeta `VIOLETA` en un archivo `.zip`.
   - En el File Manager web, haga clic en el icono de subida (**Upload**), seleccione el archivo `.zip`.
   - Haga clic derecho sobre el archivo subido y elija **Extract / Descomprimir**.
   - Verifique que los archivos (`index.php`, `informacion.php`, carpetas `admin`, `assets`, `config`, etc.) queden directamente dentro de `htdocs/`.

#### Opción B: Mediante Cliente FTP (FileZilla)
1. En los detalles de su cuenta en InfinityFree, copie los datos de acceso FTP:
   - **FTP Host:** (ej. `ftpupload.net`)
   - **FTP Username:** (ej. `if0_XXXXXXXX`)
   - **FTP Password:** (su contraseña de cuenta)
   - **FTP Port:** `21`
2. Abra FileZilla, ingrese los datos y presione **Conexión rápida**.
3. En el panel remoto (derecha), abra la carpeta **`htdocs/`**.
4. En el panel local (izquierda), seleccione todos los archivos de `VIOLETA` y arrástrelos a `htdocs/`.

---

### Paso 5: Configurar las Credenciales en el Servidor Remoto

Una vez subidos los archivos:

1. En el File Manager de InfinityFree, ingrese a la carpeta **`htdocs/config/`**.
2. Haga clic en **New File** (Nuevo archivo) y nómbrelo exactamente:
   ```text
   conexion.local.php
   ```
3. Abra el archivo para editarlo y pegue el siguiente código con los datos reales que obtuvo en el **Paso 2**:

```php
<?php
/**
 * Credenciales de conexión remota en InfinityFree
 * Este archivo permanece privado en el servidor y no está en GitHub.
 */
define('DB_HOST', 'sqlXXX.infinityfree.com'); // Su MySQL Host
define('DB_PORT', '3306');                    // Puerto estándar de InfinityFree
define('DB_NAME', 'if0_XXXXXXXX_violeta');    // Su MySQL Database Name
define('DB_USER', 'if0_XXXXXXXX');            // Su MySQL Username
define('DB_PASS', 'SuContraseñaDeHosting');   // Su MySQL Password
define('DB_CHARSET', 'utf8mb4');

// En InfinityFree, la aplicación corre en la raíz de htdocs/:
define('BASE_URL', '');
```

4. Guarde los cambios (**Save & Close**).

---

### Paso 6: Crear el Usuario Administrador en la Base de Datos Remota

Para poder iniciar sesión en el panel administrativo (`/admin/login.php`):

1. Regrese a **phpMyAdmin** en InfinityFree.
2. Haga clic en la pestaña **SQL**.
3. Pegue y ejecute la siguiente sentencia para registrar al administrador inicial:

```sql
INSERT INTO `administradores` (`nombre`, `email`, `password_hash`, `activo`) 
VALUES (
  'Administrador VIOLET',
  'admin@violeta.org',
  -- Contraseña: VioletaAdmin2026! (cifrada con BCRYPT)
  '$2y$10$7zB3cW9Ww74Dqf8oJ6d3tOqX1.bX1R1V5Fq2qY1J4K7L0M3N6P9Ri',
  1
) ON DUPLICATE KEY UPDATE `activo` = 1;
```

---

### Paso 7: Activar Certificado SSL Gratuito (HTTPS)

1. En el panel de InfinityFree (*Client Area*), diríjase a la sección **Free SSL Certificates**.
2. Haga clic en **New SSL Certificate**.
3. Seleccione el dominio/subdominio que creó para VIOLET.
4. Elija el proveedor recomendado (**Let's Encrypt** o **ZeroSSL**).
5. Siga las instrucciones en pantalla para configurar el registro CNAME automáticamente (InfinityFree tiene un botón llamado **Setup CNAME Records Automatically**).
6. Tras unos minutos de validación, presione **Install SSL Certificate Automatically**.
7. Su sitio web quedará disponible con candado verde de seguridad mediante `https://`.

---

## 🔍 4. Verificación y Pruebas de la Aplicación Publicada

Abra su navegador e ingrese a su URL pública:  
`https://su-subdominio.infinityfreeapp.com/`

Verifique los siguientes puntos para confirmar el correcto funcionamiento:
- [ ] **Página de Inicio (`index.php`):** Carga con su diseño violeta, tipografías e imágenes vectoriales.
- [ ] **Módulo de Información (`informacion.php`):** Muestra las categorías educativas y la normativa de la Ley 348.
- [ ] **Quiz Interactivo (`quiz.php`):** Carga 10 preguntas aleatorias desde la base de datos MySQL remota, permite seleccionar respuestas y calcula el puntaje final en servidor con retroalimentación en `resultado.php`.
- [ ] **Ficha de Desarrolladores (`desarrolladores.php`):** Carga la información del equipo y la institución.
- [ ] **Botón de Salida Rápida:** Redirige inmediatamente a Google (`salida.php`) y atajo de teclado `Esc x2`.
- [ ] **Panel Administrativo (`admin/login.php`):** Permite iniciar sesión con `admin@violeta.org` y `VioletaAdmin2026!`, visualizando métricas y gestión de preguntas.
