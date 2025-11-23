# 📋 Sistema de Historial de Actividades

## 🎯 Descripción

Sistema completo de auditoría e historial que registra **todas las acciones importantes** del sitio web. El historial es **inmutable** (no se puede borrar) y está diseñado solo para visualización.

## 🗄️ Base de Datos

### Crear la Tabla

Ejecuta el siguiente archivo SQL en tu base de datos:

```sql
database/create_historial_table.sql
```

O ejecuta en phpMyAdmin o tu gestor de base de datos preferido.

## 📊 Eventos Registrados

El sistema registra automáticamente:

### 1. **Autenticación**
- ✅ Login de usuarios
- ✅ Logout de usuarios
- 🔐 Registra IP, navegador, fecha/hora

### 2. **Podcasts**
- ✅ Reproducción de podcasts
- ✅ Descarga de podcasts
- ✅ Visualización de videos de YouTube
- ✅ Click en botones de acción

### 3. **Contacto**
- ✅ Envío de formularios de contacto
- 📝 Registra nombre, email, asunto

### 4. **Administración** (Próximamente)
- Crear, editar, eliminar usuarios
- Crear, editar, eliminar podcasts
- Crear, editar, eliminar noticias
- Crear, editar, eliminar programas

## 📁 Archivos del Sistema

### 1. **Clase Principal**
- `includes/Historial.php` - Clase para manejar el historial

### 2. **Interfaz de Administración**
- `admin/historial.php` - Página para visualizar el historial

### 3. **Integraciones**
- `includes/inicio.php` - Registra login
- `includes/salir.php` - Registra logout
- `pages/podcasts.php` - Registra acciones de podcasts
- `api/contact_form.php` - Registra contactos

## 🚀 Uso

### Visualizar el Historial

1. Inicia sesión como administrador
2. Ve a: `admin/historial.php`
3. Usa los filtros para buscar eventos específicos

### Filtros Disponibles

- **Tipo de Evento**: login, logout, create, update, delete, view, download, play, contact, click
- **Categoría**: usuario, podcast, noticia, programa, contacto
- **Usuario**: Filtrar por usuario específico
- **Fecha**: Próximamente

## 🎨 Características de la Interfaz

### Estadísticas
- Dashboard con conteos de cada tipo de evento
- Iconos y colores diferenciados por tipo
- Actualización en tiempo real

### Tabla de Eventos
- Muestra fecha/hora, evento, categoría, usuario, entidad, acción e IP
- Paginación automática (50 registros por página)
- Ordenado por fecha/hora (más recientes primero)

### Información Detallada
- IP del visitante
- Navegador y sistema operativo
- ID y nombre de entidad afectada
- Detalles adicionales en JSON

## 📝 Registro Manual de Eventos

Si necesitas registrar eventos manualmente en tu código:

```php
require_once('includes/Historial.php');
$historial = new Historial($con);

$historial->registrar(
    'tipo_evento',      // Tipo: login, create, update, delete, etc
    'categoria',        // Categoría: usuario, podcast, noticia, etc
    'descripcion',      // Descripción de la acción
    [
        'usuario_id' => 1,              // ID del usuario
        'usuario_nombre' => 'Admin',    // Nombre del usuario
        'entidad_id' => 5,              // ID de la entidad
        'entidad_nombre' => 'Podcast',  // Nombre de la entidad
        'detalles' => [                 // Detalles adicionales
            'campo1' => 'valor1',
            'campo2' => 'valor2'
        ]
    ]
);
```

### Tipos de Eventos Comunes

- `login` - Inicio de sesión
- `logout` - Cierre de sesión
- `create` - Crear/agregar
- `update` - Editar/modificar
- `delete` - Eliminar
- `view` - Visualizar
- `download` - Descargar
- `play` - Reproducir
- `contact` - Enviar contacto
- `click` - Click en botón/enlace

### Categorías Disponibles

- `usuario` - Usuarios del sistema
- `podcast` - Podcasts y audios
- `noticia` - Noticias
- `programa` - Programas de radio
- `contacto` - Formularios de contacto

## 🔒 Seguridad

- ✅ Solo lectura (no se puede borrar)
- ✅ Registro de IP y navegador
- ✅ Trazabilidad completa de acciones
- ✅ Logs de errores no interrumpen el flujo

## 📈 Estadísticas

```php
// Obtener estadísticas por tipo de evento
$estadisticas = $historial->obtenerEstadisticas();
```

## 🔍 Consultas Avanzadas

### Buscar por Usuario
```php
$eventos = $historial->obtenerHistorial(['usuario_id' => 1]);
```

### Buscar por Tipo
```php
$eventos = $historial->obtenerHistorial(['tipo_evento' => 'login']);
```

### Buscar por Fecha
```php
$eventos = $historial->obtenerHistorial([
    'fecha_desde' => '2024-01-01',
    'fecha_hasta' => '2024-12-31'
]);
```

## ⚙️ Configuración

### Cambiar Registros por Página

Edita `admin/historial.php` línea ~18:

```php
$registros_por_pagina = 50; // Cambia este valor
```

### Deshabilitar Registro (No Recomendado)

Si necesitas deshabilitar el historial temporalmente, comenta la línea donde se incluye:

```php
// require_once('../includes/Historial.php');
```

## 📝 Notas Importantes

1. **No borrar**: El historial está diseñado para no poder borrarse
2. **Automático**: Se registra automáticamente en todas las acciones importantes
3. **Rendimiento**: No afecta el rendimiento del sitio
4. **Privacidad**: Respeta la privacidad y no almacena datos sensibles

## 🐛 Troubleshooting

### No se registran eventos

Verifica que:
1. La tabla `historial` existe en la base de datos
2. La clase `Historial.php` está incluida
3. Los permisos de la base de datos son correctos

### Error al abrir admin/historial.php

Verifica que:
1. Estás logueado como administrador
2. El archivo `includes/validarsesion.php` existe
3. Tienes permisos de administrador

### Historial muy grande

Considera:
1. Archivar registros antiguos periódicamente
2. Ajustar `$registros_por_pagina`
3. Usar filtros para ver eventos específicos

## 🎯 Próximas Mejoras

- [ ] Registro automático de ediciones/eliminaciones en admin
- [ ] Filtros por fecha
- [ ] Exportar historial a CSV/Excel
- [ ] Gráficas y reportes visuales
- [ ] Notificaciones de eventos críticos
- [ ] Limpieza automática de eventos antiguos

