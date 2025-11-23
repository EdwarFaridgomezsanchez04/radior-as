# 🔧 Solución de Problemas - Integración YouTube

## Error: "No se pudo conectar con el servidor: Unexpected token '<', ... is not valid JSON"

### ✅ **SOLUCIONADO**

Este error ocurría porque el servidor estaba devolviendo HTML en lugar de JSON. Se ha corregido agregando control de output buffering en el archivo `admin/youtube_sync.php`.

### Qué se hizo:
1. ✅ Agregado output buffering para solicitudes POST
2. ✅ Mejorado el manejo de errores
3. ✅ Limpieza del buffer antes de devolver JSON
4. ✅ Creado script de prueba de API

---

## 🧪 Cómo Probar que Funciona

### Opción 1: Script de Prueba

Accede a este script para verificar que la API de YouTube funciona correctamente:

```
http://localhost/Radio_Rias/admin/test_youtube_api.php
```

Este script te mostrará:
- ✅ Información del canal
- ✅ Estadísticas (suscriptores, videos, vistas)
- ✅ Lista de los últimos 5 videos
- ❌ Cualquier error de configuración

### Opción 2: Probar Sincronización Directamente

1. Ve a: `http://localhost/Radio_Rias/admin/youtube_sync.php`
2. Haz clic en **"Sincronizar Ahora"**
3. Deberías ver:
   - Un loader animado mientras sincroniza
   - Estadísticas de videos sincronizados
   - Mensaje de éxito

---

## 🐛 Otros Problemas Comunes

### Problema 1: Error 400 - Bad Request

**Síntoma:**
```json
{
  "error": {
    "code": 400,
    "message": "API key not valid"
  }
}
```

**Solución:**
- Verifica que la API Key en `config/config.php` sea correcta
- Asegúrate de que no haya espacios extra al inicio o final
- La API Key debe tener exactamente 39 caracteres

**Verificar:**
```php
// config/config.php
define('YOUTUBE_API_KEY', 'AIzaSyCKAGjR1iit7bcu2D5qBUh0-tO0bscr-og');
```

---

### Problema 2: Error 403 - Forbidden

**Síntoma:**
```json
{
  "error": {
    "code": 403,
    "message": "The request cannot be completed because you have exceeded your quota"
  }
}
```

**Causa:** Has excedido la cuota diaria de la API de YouTube (10,000 unidades/día por defecto)

**Soluciones:**
1. ⏰ **Esperar:** La cuota se restablece a medianoche (hora del Pacífico, EE.UU.)
2. 📊 **Verificar uso:** Ve a [Google Cloud Console](https://console.cloud.google.com/)
3. 📈 **Solicitar aumento:** Puedes solicitar más cuota en Google Cloud Console
4. ⚡ **Optimizar:** Ejecuta la sincronización solo cuando sea necesario (no cada hora)

**Consumo aproximado por sincronización:**
- Búsqueda de videos: ~100 unidades
- Detalles de videos: ~1 unidad por video
- **Total:** ~150 unidades por sincronización de 50 videos

---

### Problema 3: Error 404 - Not Found

**Síntoma:**
```json
{
  "error": {
    "code": 404,
    "message": "Channel not found"
  }
}
```

**Solución:**
- Verifica que el Channel ID sea correcto
- El Channel ID debe empezar con "UC" (User Channel)

**Cómo obtener el Channel ID correcto:**
1. Ve a tu canal de YouTube
2. Haz clic en tu avatar → "Tu canal"
3. La URL será: `youtube.com/channel/UCxxxxxxxxx`
4. Copia la parte después de `/channel/`

---

### Problema 4: allow_url_fopen deshabilitado

**Síntoma:**
```
Warning: file_get_contents(): https:// wrapper is disabled
```

**Causa:** La configuración de PHP no permite abrir URLs remotas

**Solución:**

1. Abre el archivo `php.ini` (en XAMPP: `C:\xampp\php\php.ini`)
2. Busca la línea: `allow_url_fopen`
3. Asegúrate de que esté así:
   ```ini
   allow_url_fopen = On
   ```
4. Reinicia Apache desde el panel de XAMPP
5. Verifica con `test_youtube_api.php`

---

### Problema 5: Las miniaturas no se descargan

**Síntoma:** Los podcasts se crean pero sin imagen

**Causas posibles:**
1. Permisos de escritura en el directorio
2. Directorio no existe

**Solución:**

1. **Verificar que el directorio existe:**
   ```
   uploads/podcasts/images/
   ```

2. **En Windows (XAMPP):** Normalmente no hay problema de permisos

3. **En Linux/Mac:**
   ```bash
   chmod -R 777 uploads/podcasts/images/
   ```

4. **Verificar manualmente:**
   - Ve a `uploads/podcasts/images/`
   - Debe haber archivos `.jpg` con nombres como `67a3f8b4c12e9.jpg`
   - Si no hay archivos, hay un problema de permisos

---

### Problema 6: Los videos no aparecen en la página

**Síntoma:** La sincronización funciona pero los videos no se muestran en `/podcasts`

**Soluciones:**

1. **Verificar que estén activos:**
   - Ve a `admin/podcasts.php`
   - Verifica que los podcasts tengan estado "Activo" (verde)
   - Si están "Inactivos" (rojo), haz clic en el botón de toggle

2. **Verificar en la base de datos:**
   ```sql
   SELECT id, titulo_es, youtube_id, youtube_url, activo 
   FROM podcasts 
   WHERE youtube_id IS NOT NULL
   LIMIT 10;
   ```

3. **Limpiar caché del navegador:**
   - Presiona `Ctrl + Shift + R` (o `Cmd + Shift + R` en Mac)
   - Esto fuerza una recarga sin caché

---

### Problema 7: Error de base de datos - Columnas no existen

**Síntoma:**
```
Unknown column 'youtube_id' in 'field list'
```

**Causa:** No se ejecutó el script SQL de actualización

**Solución:**

1. Ve a phpMyAdmin: `http://localhost/phpmyadmin`
2. Selecciona la base de datos `radiomorrazo`
3. Ve a la pestaña **SQL**
4. Copia el contenido de `database/update_podcasts_youtube.sql`
5. Pégalo en el editor SQL
6. Haz clic en **Continuar**

**Verificar que funcionó:**
```sql
DESCRIBE podcasts;
```

Debes ver las columnas:
- `youtube_url`
- `youtube_id`
- `archivo` (debe ser NULL, no NOT NULL)

---

### Problema 8: El modal de video no se abre

**Síntoma:** Al hacer clic en "Ver Video" no pasa nada

**Soluciones:**

1. **Verificar la consola del navegador:**
   - Presiona `F12`
   - Ve a la pestaña "Console"
   - Busca errores en rojo

2. **Verificar que jQuery/JavaScript está cargado:**
   - En la consola, escribe: `typeof $`
   - Debe mostrar `"function"` o similar

3. **Limpiar caché:**
   - `Ctrl + Shift + Delete`
   - Selecciona "Caché" y "Cookies"
   - Limpia

4. **Verificar que el video tiene youtube_id:**
   ```sql
   SELECT titulo_es, youtube_id, youtube_url 
   FROM podcasts 
   WHERE youtube_id IS NOT NULL 
   LIMIT 5;
   ```

---

## 📞 Checklist de Diagnóstico Rápido

Cuando tengas un problema, verifica en orden:

- [ ] ✅ Ejecutaste el script SQL: `database/update_podcasts_youtube.sql`
- [ ] ✅ La API Key y Channel ID están correctos en `config/config.php`
- [ ] ✅ El script de prueba funciona: `admin/test_youtube_api.php`
- [ ] ✅ El directorio existe: `uploads/podcasts/images/`
- [ ] ✅ Apache está corriendo en XAMPP
- [ ] ✅ MySQL está corriendo en XAMPP
- [ ] ✅ No tienes errores en la consola del navegador (F12)
- [ ] ✅ Tienes conexión a internet
- [ ] ✅ `allow_url_fopen` está habilitado en php.ini

---

## 🔍 Cómo Obtener Más Información

### Ver logs de errores de PHP:

1. **En XAMPP:**
   ```
   C:\xampp\apache\logs\error.log
   ```

2. **En tu script, agregar temporalmente:**
   ```php
   error_reporting(E_ALL);
   ini_set('display_errors', 1);
   ```

### Ver respuesta completa de la API:

En `admin/youtube_sync.php`, temporalmente puedes agregar:

```php
// Después de $response = @file_get_contents($url);
file_put_contents('debug_youtube_response.txt', $response);
```

Esto guardará la respuesta completa en un archivo que puedes revisar.

---

## ✅ Todo Funciona Correctamente Si:

- ✅ El script de prueba muestra: "¡Todo está funcionando correctamente!"
- ✅ La sincronización muestra estadísticas (X nuevos, Y actualizados)
- ✅ Los podcasts aparecen en `admin/podcasts.php`
- ✅ Los videos se muestran en la página `/podcasts`
- ✅ El modal se abre al hacer clic en "Ver Video"
- ✅ Las miniaturas se cargan correctamente

---

## 💡 Tips Adicionales

### Evitar problemas de cuota:

- ⏰ Sincroniza una vez al día (no cada hora)
- 📅 Mejor en horas de bajo tráfico (madrugada)
- 🎯 Usa la sincronización solo cuando subes videos nuevos

### Optimizar el rendimiento:

- 🖼️ Las miniaturas se descargan solo una vez
- 🔄 Los videos existentes solo se actualizan (no se duplican)
- 📊 Las estadísticas (views) se actualizan en cada sincronización

### Mantenimiento:

- 🧹 Limpia periódicamente las miniaturas de videos eliminados
- 📈 Revisa las estadísticas en `admin/estadisticas-simple.php`
- 🔍 Verifica la base de datos periódicamente

---

**¿Aún tienes problemas?**

1. Ejecuta el script de prueba: `admin/test_youtube_api.php`
2. Revisa los logs de Apache: `C:\xampp\apache\logs\error.log`
3. Verifica la consola del navegador (F12)
4. Comprueba que ejecutaste el script SQL

---

Última actualización: Octubre 2025

