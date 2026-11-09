# Vantrovia

Proyecto SaaS desarrollado con Laravel, Blade, Tabler, Vite y PostgreSQL.

## Requisitos

Tener instalado:

- PHP 8.4
- Composer
- Node.js
- NPM
- Git
- PostgreSQL

Comprobar:

```powershell
php -v
composer --version
node -v
npm -v
git --version
psql --version
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

El archivo `.env` contiene configuración privada y no debe subirse a GitHub.

### 4. Crear la base de datos PostgreSQL

Entrar a PostgreSQL:

```powershell
psql -U postgres
```

Crear usuario y base de datos:

```sql
CREATE USER vantrovia_user WITH PASSWORD 'TU_PASSWORD';

CREATE DATABASE vantrovia
    WITH OWNER = vantrovia_user
    ENCODING = 'UTF8';

GRANT ALL PRIVILEGES ON DATABASE vantrovia TO vantrovia_user;
```

Salir:

```sql
\q
```

Configurar `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=vantrovia
DB_USERNAME=vantrovia_user
DB_PASSWORD=TU_PASSWORD
```

### 5. Crear las tablas

```powershell
php artisan migrate --seed
```

### 6. Limpiar caché

```powershell
php artisan optimize:clear
```

## Ejecutar Vantrovia

Se necesitan dos terminales abiertas dentro del proyecto.

### Terminal 1 — Frontend

```powershell
npm run dev
```

### Terminal 2 — Laravel

```powershell
php artisan serve
```

Abrir:

```text
http://127.0.0.1:8000
```

## Las siguientes veces

Normalmente solo necesitas:

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

Configurar en `.env`:

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

> Este comando elimina todos los datos de la base de datos configurada.

## Si PowerShell bloquea NPM

Ejecutar una sola vez:

```powershell
Set-ExecutionPolicy -Scope CurrentUser RemoteSigned
```

Luego cerrar y volver a abrir PowerShell.