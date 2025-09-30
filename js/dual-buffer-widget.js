/**
 * RadioRías Simple Widget
 * Widget de radio simplificado sin sistema de buffer dual
 * Versión: 2.0 - Simplificado
 */

class RadioWidget {
  constructor() {
    // Estado del widget
    this.isPlaying = false
    this.isVisible = false
    this.volume = 50
    
    // Audio simple
    this.audio = null
    this.streamUrl = "https://ec7.yesstreaming.net:2325/stream"
    
    // Drag & drop
    this.isDragging = false
    this.dragOffset = { x: 0, y: 0 }
    this.position = { x: 20, y: 20 }
    
    // Logs y debugging
    this.debugMode = true
    
    this.log("Radio Widget inicializado")
    this.init()
  }

  init() {
    this.createWidget()
    this.bindEvents()
    this.setupDrag()
    this.initAudio()
    this.loadPosition()
    this.loadVisibilityState()
    this.log("Inicialización completa")
  }

  log(message, level = 'info') {
    if (this.debugMode) {
      const timestamp = new Date().toLocaleTimeString()
      const prefix = `[RadioRías ${timestamp}]`
      
      switch(level) {
        case 'error':
          console.error(`${prefix} ERROR: ${message}`)
          break
        case 'warn':
          console.warn(`${prefix} WARN: ${message}`)
          break
        default:
          console.log(`${prefix} ${message}`)
      }
    }
  }

  createWidget() {
    const widget = document.createElement("div")
    widget.id = "radio-widget"
    widget.style.cssText = `
      position: fixed;
      bottom: 20px;
      right: 20px;
      background: white;
      border-radius: 15px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.2);
      padding: 18px;
      z-index: 10000;
      display: none;
      min-width: 280px;
      max-width: 320px;
    `

    widget.innerHTML = `
      <div id="drag-header" style="
        display: flex; 
        align-items: center; 
        justify-content: space-between; 
        margin-bottom: 10px;
        cursor: move;
        padding: 5px 0;
        user-select: none;
      ">
        <div style="display: flex; align-items: center;">
          <i class="fas fa-broadcast-tower" style="color: #20B2AA; margin-right: 10px; font-size: 18px;"></i>
          <div>
            <span style="font-weight: bold; color: #333;">RadioRías</span>
            <div style="font-size: 10px; color: #666; margin-top: 2px;">
              En vivo
            </div>
          </div>
        </div>
        <button id="close-btn" style="
          background: none; 
          border: none; 
          color: #666; 
          cursor: pointer; 
          font-size: 16px;
          padding: 8px;
          min-width: 40px;
          min-height: 40px;
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
          transition: all 0.2s ease;
        " 
        onmouseover="this.style.backgroundColor='#f0f0f0'" 
        onmouseout="this.style.backgroundColor='transparent'"
        ontouchstart="this.style.backgroundColor='#f0f0f0'" 
        ontouchend="this.style.backgroundColor='transparent'">
          <i class="fas fa-times"></i>
        </button>
      </div>
      
      <div style="display: flex; align-items: center; gap: 15px;">
        <button id="play-btn" style="
          background: #20B2AA;
          border: none;
          border-radius: 50%;
          width: 50px;
          height: 50px;
          min-width: 50px;
          min-height: 50px;
          color: white;
          cursor: pointer;
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 18px;
          transition: all 0.2s ease;
          touch-action: manipulation;
        "
        onmouseover="this.style.transform='scale(1.05)'" 
        onmouseout="this.style.transform='scale(1)'"
        ontouchstart="this.style.transform='scale(1.05)'" 
        ontouchend="this.style.transform='scale(1)'">
          <i class="fas fa-play"></i>
        </button>
        
        <div style="display: flex; align-items: center; gap: 8px; flex: 1;">
          <i class="fas fa-volume-up" style="color: #666; font-size: 14px;"></i>
          <input type="range" id="volume-slider" min="0" max="100" value="50" style="
            flex: 1;
            height: 6px;
            border-radius: 3px;
            background: #ddd;
            outline: none;
            cursor: pointer;
            touch-action: manipulation;
            -webkit-appearance: none;
            appearance: none;
          ">
        </div>
      </div>
      
      <div id="status" style="
        margin-top: 10px;
        font-size: 12px;
        color: #666;
        text-align: center;
      ">Listo para reproducir</div>
    `

    document.body.appendChild(widget)
    this.widget = widget
    this.log("Widget creado")
  }

  bindEvents() {
    // Botón de play/pause - con prevención de drag
    const playBtn = document.getElementById("play-btn")
    playBtn.addEventListener("click", () => this.togglePlay())
    playBtn.addEventListener("mousedown", (e) => e.stopPropagation())
    playBtn.addEventListener("touchstart", (e) => e.stopPropagation())

    // Botón de cerrar - con prevención de drag
    const closeBtn = document.getElementById("close-btn")
    closeBtn.addEventListener("click", (e) => {
      e.stopPropagation()
      this.hideWidget()
    })
    closeBtn.addEventListener("mousedown", (e) => e.stopPropagation())
    closeBtn.addEventListener("touchstart", (e) => e.stopPropagation())

    // Control de volumen - con prevención de drag
    const volumeSlider = document.getElementById("volume-slider")
    volumeSlider.addEventListener("input", (e) => this.setVolume(e.target.value))
    volumeSlider.addEventListener("mousedown", (e) => e.stopPropagation())
    volumeSlider.addEventListener("touchstart", (e) => e.stopPropagation())
    
    this.log("Eventos configurados")
  }

  initAudio() {
    this.log("Inicializando audio...")
    
    this.audio = document.createElement('audio')
    this.audio.crossOrigin = "anonymous"
    this.audio.volume = this.volume / 100
    this.audio.preload = "none"
    
    // Eventos del audio
    this.audio.addEventListener('loadstart', () => {
      this.log("Iniciando carga del stream")
      this.updateStatus("Conectando...")
    })
    
    this.audio.addEventListener('canplay', () => {
      this.log("Listo para reproducir")
    })
    
    this.audio.addEventListener('playing', () => {
      this.log("Reproduciendo")
      this.updateStatus("Reproduciendo en vivo")
    })
    
    this.audio.addEventListener('pause', () => {
      this.log("Pausado")
      this.updateStatus("Pausado")
    })
    
    this.audio.addEventListener('error', (e) => {
      this.log(`Error de audio: ${e.message}`, 'error')
      this.updateStatus("Error de conexión")
      this.isPlaying = false
      this.updatePlayButton()
    })
    
    this.audio.addEventListener('stalled', () => {
      this.log("Stream interrumpido", 'warn')
      this.updateStatus("Reconectando...")
    })
    
    this.audio.addEventListener('waiting', () => {
      this.log("Esperando datos del stream")
      this.updateStatus("Cargando...")
    })

    document.body.appendChild(this.audio)
    this.log("Audio inicializado")
  }

  async togglePlay() {
    this.log(`Toggle play - Estado actual: ${this.isPlaying}`)
    
    if (!this.isPlaying) {
      await this.startStreaming()
    } else {
      this.stopStreaming()
    }
  }

  async startStreaming() {
    this.log("Iniciando streaming")
    this.isPlaying = true
    this.updatePlayButton()
    this.updateStatus("Conectando...")
    
    try {
      const timestamp = Date.now()
      this.audio.src = `${this.streamUrl}?t=${timestamp}`
      this.audio.load()
      
      await this.audio.play()
      this.log("Streaming iniciado exitosamente")
      
    } catch (error) {
      this.log(`Error iniciando streaming: ${error.message}`, 'error')
      this.updateStatus("Error al iniciar")
      this.isPlaying = false
      this.updatePlayButton()
    }
  }

  stopStreaming() {
    this.log("Deteniendo streaming")
    
    this.isPlaying = false
    this.updatePlayButton()
    this.updateStatus("Pausado")
    
    if (this.audio) {
      this.audio.pause()
    }
    
    this.log("Streaming detenido")
  }

  // Drag & Drop
  setupDrag() {
    const dragHeader = document.getElementById("drag-header")
    if (!dragHeader) return

    // Eventos de mouse
    dragHeader.addEventListener("mousedown", (e) => this.startDrag(e))
    document.addEventListener("mousemove", (e) => this.drag(e))
    document.addEventListener("mouseup", () => this.stopDrag())

    // Eventos de touch para móviles
    dragHeader.addEventListener("touchstart", (e) => this.startDrag(e), { passive: false })
    document.addEventListener("touchmove", (e) => this.drag(e), { passive: false })
    document.addEventListener("touchend", () => this.stopDrag())

    this.log("Drag & drop configurado")
  }

  startDrag(e) {
    e.preventDefault()
    this.isDragging = true

    const clientX = e.clientX || (e.touches && e.touches[0] ? e.touches[0].clientX : 0)
    const clientY = e.clientY || (e.touches && e.touches[0] ? e.touches[0].clientY : 0)

    const rect = this.widget.getBoundingClientRect()
    this.dragOffset.x = clientX - rect.left
    this.dragOffset.y = clientY - rect.top

    document.body.style.cursor = "grabbing"
    this.log("Iniciando drag")
  }

  drag(e) {
    if (!this.isDragging) return
    e.preventDefault()

    const clientX = e.clientX || (e.touches && e.touches[0] ? e.touches[0].clientX : 0)
    const clientY = e.clientY || (e.touches && e.touches[0] ? e.touches[0].clientY : 0)

    this.position.x = clientX - this.dragOffset.x
    this.position.y = clientY - this.dragOffset.y

    const maxX = window.innerWidth - this.widget.offsetWidth
    const maxY = window.innerHeight - this.widget.offsetHeight

    this.position.x = Math.max(0, Math.min(this.position.x, maxX))
    this.position.y = Math.max(0, Math.min(this.position.y, maxY))

    this.updatePosition()
  }

  stopDrag() {
    if (this.isDragging) {
      this.isDragging = false
      document.body.style.cursor = "default"
      this.savePosition()
      this.log("Drag finalizado")
    }
  }

  updatePosition() {
    if (this.widget) {
      this.widget.style.left = this.position.x + "px"
      this.widget.style.top = this.position.y + "px"
      this.widget.style.right = "auto"
      this.widget.style.bottom = "auto"
    }
  }

  setVolume(value) {
    this.volume = parseInt(value)
    if (this.audio) {
      this.audio.volume = this.volume / 100
    }
    this.log(`Volumen ajustado: ${this.volume}%`)
  }

  updatePlayButton() {
    const playIcon = document.querySelector("#play-btn i")
    if (playIcon) {
      playIcon.className = this.isPlaying ? "fas fa-pause" : "fas fa-play"
    }
  }

  updateStatus(status) {
    const statusEl = document.getElementById("status")
    if (statusEl) {
      statusEl.textContent = status
    }
  }

  showWidget() {
    if (this.widget) {
      this.widget.style.display = "block"
      this.isVisible = true
      this.saveVisibilityState()
      this.log("Widget mostrado")
    }
  }

  hideWidget() {
    if (this.widget) {
      this.widget.style.display = "none"
      this.isVisible = false
      this.saveVisibilityState()
      
      // Detener streaming
      this.stopStreaming()
      
      this.log("Widget ocultado")
    }
  }

  loadPosition() {
    const saved = localStorage.getItem("radio-widget-position")
    if (saved) {
      try {
        this.position = JSON.parse(saved)
        this.updatePosition()
        this.log("Posición cargada")
      } catch (e) {
        this.log("Error cargando posición", 'warn')
      }
    }
  }

  savePosition() {
    localStorage.setItem("radio-widget-position", JSON.stringify(this.position))
  }

  loadVisibilityState() {
    const saved = localStorage.getItem("radio-widget-visible")
    if (saved === "true") {
      this.showWidget()
    }
  }

  saveVisibilityState() {
    localStorage.setItem("radio-widget-visible", this.isVisible.toString())
  }
}

// Inicializar widget cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
  console.log("[RadioRías] DOM cargado, creando widget de radio...")
  window.radioWidget = new RadioWidget()
})

// Función global para mostrar el widget
function showRadioWidget() {
  if (window.radioWidget) {
    window.radioWidget.showWidget()
  }
}

// Mantener compatibilidad con función anterior
function showDualBufferRadioWidget() {
  showRadioWidget()
}