## Meriendas Waldorf

Aplicación Laravel para la autogestión de meriendas (agenda, asignaciones, familias, recordatorios).

### Entorno de desarrollo con Docker

Este proyecto suele utilizarse dentro de un entorno Docker (compose en el directorio padre). Requisitos: Docker Desktop o Docker Engine.

**Comandos `php artisan` y `composer`** se ejecutan dentro del contenedor, por ejemplo desde el directorio padre:

```bash
docker compose run --rm app php artisan migrate
docker compose run --rm app composer require paquete/nombre
```

### Accesos típicos (con Docker)

- Aplicación web: http://localhost:8080
- Panel de administración (Filament): http://localhost:8080/admin
- Mailhog (correos de prueba): http://localhost:8025
- Documentación API REST: http://localhost:8080/docs

**Usuarios para entrar al admin:** al ejecutar `php artisan db:seed` se crea un usuario por familia. El **email** y la **contraseña** son el nombre del primer niño de esa familia, sin tildes y en minúsculas (ej.: niño "Demián" → email `demian@meriendas.local`, contraseña `demian`).

---

## Configuración a nivel de servidor

### 1. Variables de entorno (`.env`)

El archivo `.env` en la raíz del proyecto define la configuración. Además de las [variables estándar de Laravel](https://laravel.com/docs/configuration#environment-configuration), el proyecto usa las siguientes específicas.

#### Base de datos (con Docker)

| Variable       | Descripción                    | Ejemplo (Docker) |
|----------------|--------------------------------|------------------|
| `DB_CONNECTION`| Driver de base de datos         | `mysql`          |
| `DB_HOST`      | Host del servidor de BD        | `db`             |
| `DB_PORT`      | Puerto                          | `3306`           |
| `DB_DATABASE`  | Nombre de la base              | `meriendas`      |
| `DB_USERNAME`  | Usuario                         | `meriendas`      |
| `DB_PASSWORD`  | Contraseña                      | `secret`         |

#### Aplicación

| Variable        | Descripción                          | Valor por defecto |
|-----------------|--------------------------------------|-------------------|
| `APP_NAME`      | Nombre de la aplicación              | Laravel           |
| `APP_URL`       | URL pública (para enlaces y mails)   | http://localhost  |
| `APP_DEBUG`     | Modo depuración (en producción: `false`) | true         |
| `APP_ENV`       | Entorno (`local`, `production`, etc.) | local            |

#### Meriendas – Regalo / colecta

| Variable                         | Descripción                                           | Valor por defecto |
|----------------------------------|-------------------------------------------------------|-------------------|
| `MERIENDAS_MONTO_APORTAR_REGALO` | Monto sugerido para aportar a la colecta (número)     | `300`             |

#### Meriendas – Asignaciones

| Variable                            | Descripción                                                                 | Valor por defecto |
|-------------------------------------|-----------------------------------------------------------------------------|-------------------|
| `MERIENDAS_DIA_RECALCULO_ASIGNACIONES` | Día del mes (1–28) en que se recalculan las asignaciones del mes siguiente | `25`              |
| `MERIENDAS_MISMO_ALUMNO_FRUTA_ELAB`    | Permitir mismo alumno fruta y elaboración el mismo día (raro)               | `false`           |

#### Meriendas – Notificaciones por correo

| Variable                    | Descripción                                              | Valor por defecto |
|-----------------------------|----------------------------------------------------------|-------------------|
| `MERIENDAS_NOTIFICAR_DIAS_ANTES` | Días de antelación para el recordatorio de merienda      | `1`               |
| `MERIENDAS_NOTIFICAR_HORA`  | Hora del día para enviar recordatorios (formato 24h)      | `08:00`           |

#### Recordatorios (cron)

| Variable             | Descripción                                           | Valor por defecto |
|----------------------|-------------------------------------------------------|-------------------|
| `RECORDATORIO_HORA`  | Hora a la que se ejecuta el job de recordatorios      | `18:00`           |

#### Correo (producción)

Para que los correos (recordatorios, notificaciones de intercambio) se envíen de verdad, configurar el mailer en `.env` (SMTP, Mailgun, etc.) y al menos:

- `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`
- `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME`

En desarrollo con Docker se usa Mailhog; los correos quedan en http://localhost:8025.

---

### 2. Cron (programador de Laravel)

La aplicación usa el [programador de Laravel](https://laravel.com/docs/scheduling). En el servidor debe existir **un único cron** que ejecute el scheduler cada minuto:

```cron
* * * * * cd /ruta/al/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

Ajustar `/ruta/al/proyecto` a la raíz de esta aplicación.

#### Tareas programadas

| Tarea                         | Frecuencia        | Descripción |
|------------------------------|-------------------|-------------|
| Recordatorios de merienda    | Diario a la hora configurada (`RECORDATORIO_HORA`) | Envía recordatorios a las familias que tienen merienda al día siguiente. |
| Generar mes siguiente        | Un día al mes     | El día configurado (`MERIENDAS_DIA_RECALCULO_ASIGNACIONES`, por defecto **25**) a las 00:00 se recalculan y generan las asignaciones del **mes siguiente**. |

Si no se ejecuta el cron, no se enviarán recordatorios ni se generarán automáticamente las asignaciones del mes siguiente.

---

### 3. Cola de trabajos (queue)

Los recordatorios se envían mediante el job `EnviarRecordatoriosMerienda`, encolado. Por defecto la cola usa la base de datos (`QUEUE_CONNECTION=database`).

Para procesar la cola, en el servidor debe estar corriendo un worker:

```bash
php artisan queue:work
```

En producción suele ejecutarse con un gestor de procesos (Supervisor, systemd, etc.). Con Docker, el worker puede ser un proceso adicional dentro del contenedor `app` o un contenedor dedicado.

---

### 4. Resumen de archivos de configuración

| Archivo      | Uso |
|--------------|-----|
| `.env`       | Variables de entorno (no versionado). Copiar de `.env.example` y ajustar. |
| `config/meriendas.php`   | Regalo, asignaciones, notificaciones (leen de `.env`). |
| `config/recordatorio.php`| Canales y hora de recordatorios. |
| `routes/console.php`     | Definición del schedule (recordatorios y generación del mes siguiente). |

Para cambiar el día de recálculo de asignaciones, la hora de recordatorios o el monto de la colecta, basta con ajustar las variables correspondientes en `.env` y reiniciar/recargar la aplicación (y el worker de cola si aplica).
