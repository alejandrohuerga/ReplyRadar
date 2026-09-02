<div align="center">

# 🎯 ReplyRadar

**Convierte conversaciones de Reddit en oportunidades de negocio**

ReplyRadar monitoriza Reddit en tiempo real, detecta usuarios con intención real de compra y te entrega oportunidades clasificadas y accionables antes que tu competencia.

[![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?style=flat&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat&logo=php&logoColor=white)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](#-licencia)
[![Status](https://img.shields.io/badge/status-en%20desarrollo-yellow)]()

</div>

---

## 📋 Tabla de contenidos

- [🎯 ReplyRadar](#-replyradar)
  - [📋 Tabla de contenidos](#-tabla-de-contenidos)
  - [🚀 ¿Qué es ReplyRadar?](#-qué-es-replyradar)
  - [✨ Funcionalidades](#-funcionalidades)
  - [📸 Capturas de pantalla](#-capturas-de-pantalla)
  - [🧱 Stack tecnológico](#-stack-tecnológico)
  - [✅ Requisitos previos](#-requisitos-previos)
  - [⚙️ Instalación](#️-instalación)
  - [🔑 Variables de entorno](#-variables-de-entorno)
  - [▶️ Puesta en marcha](#️-puesta-en-marcha)
  - [⏱️ Programador de tareas (scanner de Reddit)](#️-programador-de-tareas-scanner-de-reddit)
  - [📁 Estructura del proyecto](#-estructura-del-proyecto)
  - [🗺️ Roadmap](#️-roadmap)
  - [🤝 Contribuir](#-contribuir)
  - [👤 Autor](#-autor)
  - [📄 Licencia](#-licencia)

---

## 🚀 ¿Qué es ReplyRadar?

**ReplyRadar** es una aplicación SaaS construida en Laravel que resuelve un problema muy concreto: encontrar, entre miles de publicaciones de Reddit, las conversaciones donde alguien está buscando activamente una solución a un problema — no solo curioseando.

Está pensado para **indie hackers, founders de SaaS, creators y marketers** que quieren encontrar a su próximo cliente en el sitio donde ya está hablando de su problema, en lugar de esperar a que llegue a ellos.

El usuario crea un proyecto, define una o varias palabras clave, y ReplyRadar escanea Reddit automáticamente, puntúa cada publicación según su intención de compra y relevancia, y presenta un dashboard ordenado por prioridad.

---

## ✨ Funcionalidades

- 🔍 **Búsqueda multi-palabra clave** — crea proyectos con varias keywords y monitoriza todas a la vez.
- ⚡ **Actualización automática** — escanea Reddit cada 30 minutos sin intervención manual.
- 🎯 **Opportunity Score (0-100)** — cada publicación recibe una puntuación combinando intención de compra, relevancia y nivel de interacción.
- 🔥 **Clasificación por temperatura** — oportunidades marcadas como *Hot* o *Warm* según su potencial.
- 📊 **Panel de oportunidades** — vista centralizada con total detectado, oportunidades "hot" y subreddit más activo.
- 📥 **Exportación a CSV** — para llevar los datos a tu CRM, Notion o donde los necesites.
- 🔐 **Autenticación con verificación de email**.
- 💳 **Planes por suscripción** — Free, Pro y Business, con límites diferenciados de proyectos, keywords e historial.
- 🌐 **Interfaz en inglés y español**.
- 🛠️ **Sin configuración técnica para el usuario final** — registro, primera keyword y resultados en minutos.

---

## 📸 Capturas de pantalla

<div align="center">

| Landing | Dashboard de oportunidades |
|---|---|
| ![Landing de ReplyRadar](./docs/screenshots/landing.png) | ![Panel de oportunidades](./docs/screenshots/dashboard.png) |

</div>

> 💡 Sustituye las imágenes de `docs/screenshots/` por tus propias capturas antes de publicar el repositorio.

---

## 🧱 Stack tecnológico

| Capa | Tecnología |
|---|---|
| Backend | [Laravel 11](https://laravel.com) (PHP 8.2+) |
| Base de datos | MySQL / MariaDB |
| Frontend | Blade + Tailwind CSS |
| Build de assets | Vite |
| Autenticación | Laravel Breeze / Fortify |
| Tareas programadas | Laravel Scheduler + Queues |
| Origen de datos | API de Reddit |

> ⚠️ Ajusta esta tabla si alguna tecnología difiere de tu implementación real (por ejemplo, si usas Livewire, Inertia+React, PostgreSQL, o un proveedor de pagos como Stripe para los planes Pro/Business).

---

## ✅ Requisitos previos

Antes de instalar el proyecto, asegúrate de tener:

- PHP >= 8.2
- Composer 2.x
- Node.js >= 18 y npm
- MySQL 8 (o MariaDB equivalente)
- Una cuenta de desarrollador de Reddit para obtener credenciales de su API ([reddit.com/prefs/apps](https://www.reddit.com/prefs/apps))
- (Opcional) Un servidor de correo (Mailtrap, SMTP, etc.) para probar la verificación de email en local

---

## ⚙️ Instalación

```bash
# 1. Clona el repositorio
git clone https://github.com/alejandrohuerga/ReplyRadar.git
cd ReplyRadar

# 2. Instala las dependencias de PHP
composer install

# 3. Instala las dependencias de JavaScript
npm install

# 4. Copia el archivo de entorno y genera la clave de la aplicación
cp .env.example .env
php artisan key:generate

# 5. Configura tu base de datos y credenciales en el archivo .env
#    (ver sección "Variables de entorno" más abajo)

# 6. Ejecuta las migraciones
php artisan migrate

# 7. (Opcional) Carga datos de ejemplo
php artisan db:seed

# 8. Compila los assets del frontend
npm run build
```

---

## 🔑 Variables de entorno

Configura estas claves en tu archivo `.env`:

```env
APP_NAME=ReplyRadar
APP_ENV=local
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=replyradar
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=2525
MAIL_USERNAME=
MAIL_PASSWORD=

REDDIT_CLIENT_ID=tu_client_id
REDDIT_CLIENT_SECRET=tu_client_secret
REDDIT_USER_AGENT=ReplyRadar/1.0
```

> 📝 Revisa tu `.env.example` real y completa aquí cualquier clave adicional que use tu proyecto (por ejemplo, `STRIPE_KEY` si tienes pagos implementados).

---

## ▶️ Puesta en marcha

Con todo instalado y configurado, levanta el entorno de desarrollo en dos terminales:

```bash
# Terminal 1 — servidor de Laravel
php artisan serve

# Terminal 2 — compilación de assets en modo desarrollo
npm run dev
```

La aplicación estará disponible en `http://localhost:8000`.

---

## ⏱️ Programador de tareas (scanner de Reddit)

ReplyRadar depende del **scheduler de Laravel** para escanear Reddit cada 30 minutos y del sistema de **colas** para procesar los resultados sin bloquear la aplicación.

En **desarrollo**, puedes simular el scheduler ejecutando:

```bash
php artisan schedule:work
```

Y, en otra terminal, procesa las colas con:

```bash
php artisan queue:work
```

En **producción**, añade esta entrada a cron en tu servidor:

```
* * * * * cd /ruta/a/tu/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

Y gestiona la cola con un supervisor de procesos (por ejemplo, [Supervisor](https://laravel.com/docs/queues#supervisor-configuration)) para que `queue:work` se mantenga siempre activo.

---

## 📁 Estructura del proyecto

```
ReplyRadar/
├── app/
│   ├── Http/Controllers/     # Controladores (proyectos, keywords, oportunidades...)
│   ├── Models/                # Modelos Eloquent
│   ├── Services/              # Lógica de scraping/scoring de Reddit
│   └── Console/Commands/      # Comandos artisan (scanner, etc.)
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── views/                 # Vistas Blade
│   └── css / js               # Assets del frontend
├── routes/
│   └── web.php
└── .env.example
```

> Ajusta este árbol a la estructura real de tu proyecto si difiere.

---

## 🗺️ Roadmap

- [ ] Soporte para más fuentes además de Reddit (Twitter/X, foros)
- [ ] Notificaciones por email/Slack cuando aparece una oportunidad "hot"
- [ ] Integración con CRMs (HubSpot, Notion)
- [ ] API pública para consumir las oportunidades desde otras herramientas

---

## 🤝 Contribuir

Este es actualmente un proyecto personal, pero las sugerencias son bienvenidas:

1. Haz un fork del repositorio
2. Crea una rama para tu cambio (`git checkout -b feature/mi-mejora`)
3. Haz commit de tus cambios (`git commit -m 'Añade mi mejora'`)
4. Haz push a tu rama (`git push origin feature/mi-mejora`)
5. Abre un Pull Request

---

## 👤 Autor

**Alejandro de la Huerga Fernández**
Desarrollador Web Full Stack

- GitHub: [@alejandrohuerga](https://github.com/alejandrohuerga)
- LinkedIn: [Alejandro de la Huerga Fernández](https://www.linkedin.com/in/alejandroalejandrodelahuergafernandez)
- Email: alejandrohuerga.dev@gmail.com

---

## 📄 Licencia

Este proyecto está bajo la licencia MIT. Consulta el archivo [`LICENSE`](./LICENSE) para más detalles.

<div align="center">

Hecho con ☕ y Laravel

</div>