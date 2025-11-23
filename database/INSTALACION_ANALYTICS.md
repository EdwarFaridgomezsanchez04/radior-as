# 📊 Sistema de Analytics Real - Instalación

## 🎯 Descripción

Sistema completo de analytics que captura datos **reales** de tu sitio web:
- ✅ Visitas y visitantes únicos
- ✅ Reproducciones de podcasts
- ✅ Descargas de archivos
- ✅ Eventos del historial (login, creación, edición, etc.)
- ✅ Datos de dispositivos y navegadores
- ✅ Compatible con Hostinger y cualquier hosting PHP

## 📦 Instalación

### Paso 1: Ejecutar SQL en la Base de Datos

Ejecuta este archivo en phpMyAdmin o tu gestor de base de datos:

```bash
database/create_analytics_table.sql
```

O copia y pega este SQL:

```sql
-- Tabla para estadísticas de visitas y páginas
CREATE TABLE IF NOT EXISTS analytics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT,
    page_url VARCHAR(500) NOT NULL,
    page_title VARCHAR(255),
    referer VARCHAR(500),
    visit_date DATE NOT NULL,
    visit_time TIME NOT NULL,
    visit_datetime DATETIME NOT NULL,
    country VARCHAR(100),
    device_type VARCHAR(50),
    browser VARCHAR(100),
    os VARCHAR(100),
    is_bot TINYINT(1) DEFAULT 0,
    user_id INT DEFAULT NULL,
    time_on_page INT DEFAULT 0 COMMENT 'Tiempo en segundos',
    INDEX idx_session_id (session_id),
    INDEX idx_visit_date (visit_date),
    INDEX idx_ip_address (ip_address),
    INDEX idx_user_id (user_id),
    INDEX idx_is_bot (is_bot)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Analytics de visitas al sitio';

-- Tabla para contadores de reproducciones
CREATE TABLE IF NOT EXISTS play_counts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    podcast_id INT DEFAULT NULL,
    program_id INT DEFAULT NULL,
    youtube_id VARCHAR(100) DEFAULT NULL,
    play_date DATE NOT NULL,
    plays INT DEFAULT 1,
    unique_plays INT DEFAULT 0,
    INDEX idx_podcast_id (podcast_id),
    INDEX idx_play_date (play_date),
    INDEX idx_youtube_id (youtube_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Contadores de reproducciones por fecha';
```

### Paso 2: Activar Tracking Automático

Agrega al inicio del archivo `index.php` (después de session_start):

```php
require_once('includes/tracking.php');
```

### Paso 3: Verificar Instalación

1. Accede al dashboard: `admin/dashboard.php`
2. Deberías ver estadísticas reales en lugar de "0"
3. Las estadísticas se actualizan automáticamente

## 📈 Datos que se Capturan

### Automáticamente:
- ✅ Todas las visitas a cada página
- ✅ Visitantes únicos (por sesión)
- ✅ IP y navegador del visitante
- ✅ Tipo de dispositivo (Desktop/Mobile/Tablet)
- ✅ Sistema operativo
- ✅ Filtra bots automáticamente
- ✅ Tiempo en cada página

### Del Historial:
- ✅ Logins/Logouts
- ✅ Creación/Edición/Eliminación de contenidos
- ✅ Reproducciones de podcasts
- ✅ Descargas
- ✅ Envíos de formularios
- ✅ Todos los eventos del sistema

### De la Base de Datos:
- ✅ Total de usuarios registrados
- ✅ Total de programas activos
- ✅ Total de podcasts publicados
- ✅ Reproducciones totales de podcasts
- ✅ Descargas totales

## 🎨 Interfaz del Dashboard

El dashboard ahora muestra:
1. **Usuarios totales** - Con contador de activos
2. **Programas activos** - Total de programas en el sistema
3. **Podcasts publicados** - Con contador de reproducciones
4. **Eventos en historial** - De los últimos 30 días

Todas las estadísticas son **real-time** y se actualizan automáticamente.

## 🔧 Funciones Disponibles

### Clase Tracker

```php
$tracker = new Tracker($conexion);

// Registrar visita manualmente
$tracker->trackVisit($page_url, $page_title);

// Obtener estadísticas
$stats = $tracker->getStats($days = 30);
// Retorna: total_visits, unique_visits, top_pages, devices, browsers

// Obtener estadísticas del historial
$history_stats = $tracker->getHistoryStats();
// Retorna: total_events, unique_users, logins, plays, downloads, etc.

// Obtener estadísticas de podcasts
$podcast_stats = $tracker->getPodcastStats();
// Retorna: total_podcasts, total_plays, total_downloads, top_podcasts
```

## 🌐 Compatibilidad con Hosting

### Funciona en:
- ✅ **Hostinger** - Totalmente compatible
- ✅ **cPanel** - Compatible
- ✅ **Plesk** - Compatible
- ✅ **XAMPP/WAMP/MAMP** - Para desarrollo local
- ✅ Cualquier hosting con PHP 7.4+ y MySQL 5.7+

### Requisitos:
- PHP 7.4 o superior
- MySQL 5.7 o superior
- Extensiones PHP: PDO, JSON
- 50MB de espacio en base de datos

## 📊 Visualización de Datos

### Dashboard Principal (`admin/dashboard.php`)
- Tarjetas con estadísticas principales
- Datos en tiempo real
- Actualización automática

### Historial (`admin/historial.php`)
- Todos los eventos del sistema
- Filtros por tipo y categoría
- Paginación automática

## 🔒 Privacidad

- ✅ No se almacenan datos sensibles
- ✅ IPs se registran de forma anónima
- ✅ Cumple con GDPR (datos anónimos)
- ✅ Bots se filtran automáticamente

## ⚡ Rendimiento

- ✅ Tracking asíncrono (no ralentiza el sitio)
- ✅ Índices optimizados en base de datos
- ✅ Consultas eficientes
- ✅ Sin carga adicional para el usuario

## 🎯 Próximas Mejoras

- [ ] Gráficas visuales de tendencias
- [ ] Exportación a Excel/CSV
- [ ] Reportes por email automáticos
- [ ] Integración con Google Analytics
- [ ] Mapas de calor de clicks

## 📝 Notas

1. **Primera vez**: Puede tardar unos minutos en mostrar estadísticas
2. **Bots**: Automáticamente se filtran crawlers
3. **Sesiones**: Visitantes únicos se determinan por session_id
4. **Historial**: Data de los últimos 30 días por defecto

## 🆘 Troubleshooting

### No se muestran estadísticas
- Verifica que las tablas existen en la base de datos
- Revisa que tracking.php esté incluido en index.php
- Verifica permisos de la base de datos

### Estadísticas en 0
- Es normal si el sitio es nuevo
- Genera tráfico visitando diferentes páginas
- Verifica en phpMyAdmin que se inserten datos en la tabla `analytics`

### Error de conexión
- Verifica config/conexion.php
- Verifica credenciales de la base de datos
- Verifica que PDO esté habilitado

## ✅ Resultado Final

Tu dashboard ahora muestra:
- ✅ Usuarios reales del sistema
- ✅ Programas reales publicados
- ✅ Podcasts reales con reproducciones reales
- ✅ Eventos reales del historial

**¡Todo es 100% real, capturado automáticamente de tu sitio!**

