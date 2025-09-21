// Configuración de RadioRías para servidor Icecast
window.RadioRiasConfig = {
  // Configuración del stream
  stream: {
    // URL principal usando proxy PHP (evita CORS)
    primary: "proxy.php",
    
    // URL de respaldo directo (puede tener CORS)
    fallback: "http://88.150.230.110:8950/stream",
    
    // Formato del stream
    format: "mp3",
    
    // Bitrate
    bitrate: "128kbps"
  },
  
  // Configuración de metadatos para Icecast
  metadata: {
    // URL para obtener metadatos de Icecast (formato JSON)
    apiUrl: "http://88.150.230.110:8950/status-json.xsl",
    
    // Intervalo de actualización en milisegundos (15 segundos para Icecast)
    updateInterval: 15000,
    
    // Habilitar metadatos de Icecast
    enabled: true
  },
  
  // Información de la radio
  radioInfo: {
    name: "RadioRías",
    description: "La voz del corazón de Galicia",
    website: "https://radiorias.com",
    email: "contacto@radiorias.com"
  },
  
  // Configuración del widget
  widget: {
    // Posición inicial
    defaultPosition: {
      x: window.innerWidth - 320,
      y: window.innerHeight - 120
    },
    
    // Colores
    colors: {
      primary: "#20B2AA",
      secondary: "#00CED1",
      accent: "#fde047"
    }
  }
}

console.log("RadioRías Config cargado:", window.RadioRiasConfig)
