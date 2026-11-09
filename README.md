# Vantrovia

Proyecto SaaS desarrollado con Laravel, Blade, Tabler y Vite.

## Requisitos

Tener instalado:

- PHP 8.4
- Composer
- Node.js
- NPM
- Git

Comprobar:

```powershell
php -v
composer --version
node -v
npm -v
git --version
```

## Levantar el proyecto por primera vez

### 1. Clonar el repositorio

```powershell
cd C:\Users\TU_USUARIO\Documents
git clone URL_DEL_REPOSITORIO
cd Vantrovia
```

### 2. Instalar dependencias

```powershell
composer install
npm install
```

### 3. Crear `.env`

```powershell
Copy-Item .env.example .env
php artisan key:generate
```

El archivo `.env` contiene la configuración local y no debe subirse a GitHub.

### 4. Crear la base de datos

Actualmente en desarrollo se utiliza SQLite.

Verifica en `.env`:

```env
DB_CONNECTION=sqlite
```

Crea el archivo:

```powershell
New-Item database\database.sqlite -ItemType File -Force
```

Ejecuta migraciones y seeders:

```powershell
php artisan migrate --seed
```

### 5. Limpiar caché

```powershell
php artisan optimize:clear
```

## Ejecutar Vantrovia

Se necesitan dos terminales abiertas dentro de la carpeta del proyecto.

### Terminal 1 — Frontend

```powershell
npm run dev
```

### Terminal 2 — Laravel

```powershell
php artisan serve
```

Abrir en el navegador:

```text
http://127.0.0.1:8000
```

## Las siguientes veces

Una vez instalado y configurado, normalmente solo necesitas:

**Terminal 1**

```powershell
npm run dev
```

**Terminal 2**

```powershell
php artisan serve
```

## Configuración de correo

La recuperación de contraseña utiliza SMTP.

Configurar las credenciales en `.env`, por ejemplo:

```env
MAIL_MAILER=smtp
MAIL_SCHEME=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=TU_CORREO
MAIL_PASSWORD="TU_PASSWORD_DE_APLICACION"
MAIL_FROM_ADDRESS=TU_CORREO
MAIL_FROM_NAME="Vantrovia"
```

Después:

```powershell
php artisan optimize:clear
```

Nunca subir `.env` ni contraseñas reales a GitHub.

## Comandos útiles

Ejecutar migraciones pendientes:

```powershell
php artisan migrate
```

Limpiar caché:

```powershell
php artisan optimize:clear
```

Ver rutas:

```powershell
php artisan route:list
```

Abrir Tinker:

```powershell
php artisan tinker
```

Recrear completamente la BD local:

```powershell
php artisan migrate:fresh --seed
```

> Este último comando elimina todos los datos locales.

## Si PowerShell bloquea NPM

Ejecutar una sola vez:

```powershell
Set-ExecutionPolicy -Scope CurrentUser RemoteSigned
```

Luego cerrar y volver a abrir PowerShell.