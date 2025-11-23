# 📺 Instrucciones para Sincronizar Videos de YouTube

## ✅ Configuración Lista

Tu sistema ya está configurado para sincronizar videos de YouTube:

- **API Key**: AIzaSyCKAGjR1iit7bcu2D5qBUh0-tO0bscr-og
- **Channel ID**: UCHLIFb1mo7Jf6yl2g2Gkbjg
- **Canal**: Miguelángel Parcero
- **Videos disponibles**: 18

## 🚀 Cómo Sincronizar los Videos

### Paso 1: Iniciar Sesión
1. Abre tu navegador y ve a: `http://localhost/Radio_Rias/login.php`
2. Inicia sesión con tus credenciales de administrador

### Paso 2: Ir a Gestión de Podcasts
1. Desde el dashboard, haz clic en **"Gestión de Podcasts"**
2. O ve directamente a: `http://localhost/Radio_Rias/admin/podcasts.php`

### Paso 3: Sincronizar con YouTube
1. En la parte superior de la página, encontrarás un botón rojo **"Sincronizar YouTube"**
2. Haz clic en ese botón
3. Se abrirá la página de sincronización con información del canal
4. Haz clic en el botón **"Sincronizar Ahora"**

### Paso 4: Esperar el Proceso
- El proceso puede tardar varios minutos (dependiendo de la cantidad de videos)
- Verás un indicador de carga mientras se sincronizan los videos
- **NO CIERRES** la ventana hasta que termine

### Paso 5: Ver los Resultados
Al finalizar, verás un resumen con:
- ✅ **Nuevos**: Videos que se agregaron por primera vez
- 🔄 **Actualizados**: Videos que ya existían y se actualizaron
- 📊 **Total Procesados**: Cantidad total de videos sincronizados

## 🎯 Características del Sistema

### ✨ Todos los Videos Quedarán Activos
- Los videos nuevos se insertan con `activo = 1`
- Los videos existentes se actualizan a `activo = 1`
- **Todos los videos sincronizados serán visibles automáticamente en el sitio web**

### 📥 Información que se Sincroniza
- ✅ Título del video
- ✅ Descripción completa
- ✅ Fecha de publicación
- ✅ Duración del video
- ✅ Miniatura en alta calidad (se descarga automáticamente)
- ✅ Contador de reproducciones de YouTube
- ✅ URL completa del video
- ✅ ID único del video de YouTube

### 🔄 Sistema Inteligente
- Detecta videos duplicados automáticamente
- NO crea videos repetidos
- Actualiza videos existentes con información nueva
- Descarga solo las miniaturas de videos nuevos

## 🎬 Después de la Sincronización

Una vez sincronizados, los videos aparecerán en la página de podcasts:

**URL**: `http://localhost/Radio_Rias/?page=podcasts&lang=es`

### Botones Disponibles
- 🎥 **Ver Video**: Abre un modal con el reproductor de YouTube
- 🔗 **Abrir en YouTube**: Enlace directo al video en YouTube

## 📝 Notas Importantes

1. **Conexión a Internet**: Necesitas conexión a internet para sincronizar
2. **Tiempo de Sincronización**: Puede tardar varios minutos (aprox. 30 segundos por video)
3. **Cuota de API**: YouTube permite hasta 10,000 unidades diarias (suficiente para tu canal)
4. **Espacio en Disco**: Las miniaturas ocupan espacio en `uploads/podcasts/images/`
5. **Privacidad**: Solo se sincronizan videos públicos del canal

## 🔧 Si Tienes Problemas

### Error: "Error al conectar con la API de YouTube"
- Verifica tu conexión a internet
- Asegúrate de que el API Key sea válido

### Error: "Error de API: Quota exceeded"
- Has excedido el límite diario de solicitudes
- Espera 24 horas o aumenta la cuota en Google Cloud Console

### No se Descargaron las Miniaturas
- Verifica permisos de escritura en `uploads/podcasts/images/`
- Asegúrate de que la carpeta existe

## ✅ Verificación Final

Después de sincronizar, verifica que todo funcionó correctamente:

1. Ve a `http://localhost/Radio_Rias/admin/podcasts.php`
2. Deberías ver todos los videos de YouTube en la lista
3. Todos deberían estar con estado "Activo" (verde)
4. Ve a `http://localhost/Radio_Rias/?page=podcasts&lang=es`
5. Deberías ver todos los videos disponibles para reproducir

---

**¡Listo para sincronizar!** 🎉

Solo sigue los pasos anteriores y todos tus videos de YouTube aparecerán automáticamente en la sección de podcasts.
