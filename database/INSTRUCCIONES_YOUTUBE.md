# Integración con YouTube - Instrucciones

## 📋 Descripción General

La integración con YouTube permite sincronizar automáticamente los videos de tu canal de YouTube con la sección de podcasts de RadioRías. Los videos se importan con toda su información (título, descripción, miniatura, duración, etc.) y se muestran como podcasts en tu sitio web.

## 🔧 Configuración Inicial

### Paso 1: Actualizar la Base de Datos

**IMPORTANTE:** Debes ejecutar este paso ANTES de usar la sincronización.

1. Abre **phpMyAdmin** desde XAMPP
2. Selecciona la base de datos `radiomorrazo`
3. Ve a la pestaña **SQL**
4. Abre el archivo `database/update_podcasts_youtube.sql`
5. Copia todo el contenido del archivo
6. Pégalo en el editor SQL de phpMyAdmin
7. Haz clic en **Continuar** o **Go** para ejecutar el script

Este script agregará las columnas necesarias:
- `youtube_url`: URL completa del video en YouTube
- `youtube_id`: ID único del video de YouTube
- Modificará la columna `archivo` para que sea opcional (NULL)

### Paso 2: Verificar Configuración de YouTube

La configuración ya está lista en `config/config.php`:

```php
// Configuración de YouTube API
define('YOUTUBE_API_KEY', 'AIzaSyCKAGjR1iit7bcu2D5qBUh0-tO0bscr-og');
define('YOUTUBE_CHANNEL_ID', 'UCHLIFb1mo7Jf6yl2g2Gkbjg');
```

## 📺 Cómo Usar la Sincronización

### Opción 1: Desde el Panel de Administración

1. Inicia sesión en el panel de administración: `http://localhost/Radio_Rias/login.php`
2. Ve a **Gestión de Podcasts**
3. Haz clic en el botón rojo **"Sincronizar YouTube"** en la parte superior
4. Haz clic en **"Sincronizar Ahora"**
5. Espera a que el proceso termine (puede tardar varios minutos)
6. Verás un resumen con:
   - **Nuevos**: Videos que se agregaron por primera vez
   - **Actualizados**: Videos que ya existían y se actualizaron
   - **Total Procesados**: Cantidad total de videos procesados

### Opción 2: Acceso Directo

También puedes acceder directamente a:
`http://localhost/Radio_Rias/admin/youtube_sync.php`

## ✨ Características

### Lo que se Sincroniza Automáticamente:

- ✅ **Título del video** (en español y gallego)
- ✅ **Descripción completa**
- ✅ **Miniatura en alta calidad** (se descarga automáticamente)
- ✅ **Fecha de publicación**
- ✅ **Duración del video** (formato HH:MM:SS o MM:SS)
- ✅ **Cantidad de reproducciones** (views)
- ✅ **URL del video** y **ID de YouTube**

### Comportamiento de la Sincronización:

- 📌 **Videos nuevos**: Se crean como podcasts con estado "Activo"
- 🔄 **Videos existentes**: Se actualizan solo los datos (títulos, descripciones, stats)
- 🖼️ **Miniaturas**: Se descargan en `uploads/podcasts/images/`
- ⚠️ **Videos duplicados**: No se crean, se detectan por el `youtube_id`
- 🔢 **Límite de sincronización**: 50 videos por ejecución (últimos videos del canal)

## 🎬 Visualización en el Sitio Web

Los podcasts de YouTube se muestran automáticamente en la página de podcasts con:

1. **Botón "Ver Video"**: Abre un modal con el reproductor embebido de YouTube
2. **Botón de YouTube**: Abre el video directamente en YouTube
3. **Diseño especial**: Los podcasts de YouTube mantienen el mismo diseño elegante que los demás

### Ejemplo de Código en la Vista:

```php
<?php if (!empty($podcast['youtube_id'])): ?>
    <!-- Botón ver video -->
    <button onclick="showVideoModal('<?php echo $podcast['youtube_id']; ?>', ...)">
        <i class="fab fa-youtube mr-2"></i>Ver Video
    </button>
    
    <!-- Botón abrir en YouTube -->
    <a href="<?php echo $podcast['youtube_url']; ?>" target="_blank">
        <i class="fab fa-youtube"></i>
    </a>
<?php endif; ?>
```

## 🔄 Recomendaciones de Uso

### Frecuencia de Sincronización:

- **Diario**: Si subes videos frecuentemente
- **Semanal**: Si subes videos ocasionalmente
- **Manual**: Después de subir un nuevo video importante

### Mejores Prácticas:

1. ✅ Ejecuta la sincronización en horarios de bajo tráfico
2. ✅ Verifica que los videos se importaron correctamente después de cada sincronización
3. ✅ Puedes editar manualmente cualquier podcast después de importarlo (cambiar categoría, destacar, etc.)
4. ✅ Los videos se marcan como "Activos" por defecto, puedes desactivar los que no quieras mostrar

## 🎨 Personalización

### Categorías:

Por defecto, los videos de YouTube se importan sin categoría asignada. Puedes:

1. Asignar categorías manualmente después de la importación
2. Modificar el script `admin/youtube_sync.php` para asignar categorías automáticamente basándose en:
   - Palabras clave en el título
   - Tags del video
   - Playlists de YouTube

### Programas:

Similar a las categorías, puedes asociar videos a programas específicos:

1. Manualmente desde el panel de administración
2. Modificando el script para asignar automáticamente

## 🐛 Solución de Problemas

### Error: "Error al conectar con la API de YouTube"

**Causa**: Problema de conexión o API Key incorrecta

**Solución**:
1. Verifica que tienes conexión a internet
2. Comprueba que la API Key sea correcta en `config/config.php`
3. Verifica que la API Key no haya alcanzado su límite de cuota diaria

### Error: "Error de API: ..."

**Causa**: Problema con la API de YouTube (cuota excedida, permisos, etc.)

**Solución**:
1. Verifica el límite de cuota de tu API Key en Google Cloud Console
2. La cuota se restablece diariamente
3. Si es necesario, solicita un aumento de cuota

### Los videos no aparecen en la página

**Causa**: Los podcasts están desactivados o hay un problema con la base de datos

**Solución**:
1. Verifica en el panel de administración que los podcasts estén marcados como "Activos"
2. Comprueba que ejecutaste el script SQL de actualización correctamente
3. Revisa que las columnas `youtube_url` y `youtube_id` existan en la tabla

### Las miniaturas no se descargan

**Causa**: Permisos de escritura en el directorio

**Solución**:
1. Verifica que el directorio `uploads/podcasts/images/` exista
2. Asegúrate de que tenga permisos de escritura (777 en desarrollo)
3. En Windows con XAMPP, normalmente no es un problema

## 📊 Estadísticas y Métricas

La sincronización también importa las estadísticas de YouTube:

- **Reproducciones**: Se sincronizan con los "views" del video
- **Duración**: Se convierte automáticamente al formato legible (HH:MM:SS)

Estas estadísticas se pueden ver en:
- Panel de administración de podcasts
- Página pública de podcasts

## 🔐 Seguridad

- ✅ Solo los administradores pueden ejecutar la sincronización
- ✅ La API Key se almacena en el servidor (no es visible en el navegador)
- ✅ Los archivos subidos se validan antes de guardarse
- ✅ Las consultas SQL usan prepared statements para prevenir inyección SQL

## 📝 Notas Adicionales

### Limitaciones:

- La API de YouTube tiene un límite de cuota diario (10,000 unidades por defecto)
- Cada sincronización consume aproximadamente:
  - 100 unidades por búsqueda de videos
  - 1 unidad por cada video al obtener detalles
  - Total: ~150 unidades por sincronización de 50 videos

### Ventajas:

- ✅ Sincronización automática y rápida
- ✅ No necesitas subir videos manualmente
- ✅ Las miniaturas se descargan automáticamente
- ✅ Los videos se actualizan automáticamente (estadísticas, títulos, etc.)
- ✅ Compatible con el sistema actual de podcasts

## 💡 Sugerencias de Mejora

Posibles mejoras futuras:

1. **Sincronización automática programada** (cron job)
2. **Webhooks de YouTube** para sincronización en tiempo real
3. **Filtrado por playlist** específica
4. **Asignación automática de categorías** basada en tags
5. **Importación de comentarios** de YouTube
6. **Sincronización de likes y estadísticas** avanzadas

---

## 🆘 Soporte

Si tienes problemas o preguntas:

1. Revisa esta documentación
2. Verifica los logs en la consola del navegador (F12)
3. Comprueba los errores en el script de sincronización

---

**¡Disfruta de tu nueva integración con YouTube! 🎉**

