/**
 * Widget Simplificado de RadioRías - Solo funciones esenciales
 */

class SimpleRadioWidget {
  constructor() {
    this.isPlaying = false
    this.audio = null
    this.streamUrl = "proxy.php"
    this.volume = 50
    this.isVisible = false
    
    // Variables para drag & drop
    this.isDragging = false
    this.dragOffset = { x: 0, y: 0 }
    this.position = { x: 20, y: 20 }
    
    console.log("[RadioRías] Widget Simple inicializado")
    this.init()
  }

  init() {
    this.createWidget()
    this.bindEvents()
    this.setupDrag()
    this.initAudio()
    this.loadPosition()
    this.loadVisibilityState()
  }

  createWidget() {
    const widget = document.createElement("div")
    widget.id = "simple-radio-widget"
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
      <div id="simple-drag-header" style="
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
          <span style="font-weight: bold; color: #333;">RadioRías</span>
        </div>
        <button id="simple-close-btn" style="
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
        <button id="simple-play-btn" style="
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
          <input type="range" id="simple-volume-slider" min="0" max="100" value="50" style="
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
      
      <div id="simple-status" style="
        margin-top: 10px;
        font-size: 12px;
        color: #666;
        text-align: center;
      ">Listo para reproducir</div>
    `

    document.body.appendChild(widget)
    this.widget = widget
  }

  bindEvents() {
    // Botón de play/pause - con prevención de drag
    const playBtn = document.getElementById("simple-play-btn")
    playBtn.addEventListener("click", () => this.togglePlay())
    playBtn.addEventListener("mousedown", (e) => e.stopPropagation())
    playBtn.addEventListener("touchstart", (e) => e.stopPropagation())

    // Botón de cerrar - con prevención de drag
    const closeBtn = document.getElementById("simple-close-btn")
    closeBtn.addEventListener("click", (e) => {
      e.stopPropagation()
      this.hideWidget()
    })
    
    // Evitar que el drag se active en el botón de cerrar
    closeBtn.addEventListener("mousedown", (e) => e.stopPropagation())
    closeBtn.addEventListener("touchstart", (e) => e.stopPropagation())

    // Control de volumen - con prevención de drag
    const volumeSlider = document.getElementById("simple-volume-slider")
    volumeSlider.addEventListener("input", (e) => this.setVolume(e.target.value))
    volumeSlider.addEventListener("mousedown", (e) => e.stopPropagation())
    volumeSlider.addEventListener("touchstart", (e) => e.stopPropagation())
  }

  setupDrag() {
    const dragHeader = document.getElementById("simple-drag-header")
    if (!dragHeader) return

    // Eventos de mouse
    dragHeader.addEventListener("mousedown", (e) => this.startDrag(e))
    document.addEventListener("mousemove", (e) => this.drag(e))
    document.addEventListener("mouseup", () => this.stopDrag())

    // Eventos de touch para móviles
    dragHeader.addEventListener("touchstart", (e) => this.startDrag(e), { passive: false })
    document.addEventListener("touchmove", (e) => this.drag(e), { passive: false })
    document.addEventListener("touchend", () => this.stopDrag())

    console.log("[RadioRías] Drag & drop configurado")
  }

  startDrag(e) {
    e.preventDefault()
    this.isDragging = true

    // Obtener coordenadas del evento (mouse o touch)
    const clientX = e.clientX || (e.touches && e.touches[0] ? e.touches[0].clientX : 0)
    const clientY = e.clientY || (e.touches && e.touches[0] ? e.touches[0].clientY : 0)

    // Calcular offset desde el punto de click hasta la esquina del widget
    const rect = this.widget.getBoundingClientRect()
    this.dragOffset.x = clientX - rect.left
    this.dragOffset.y = clientY - rect.top

    // Cambiar cursor
    document.body.style.cursor = "grabbing"
    console.log("[RadioRías] Iniciando drag")
  }

  drag(e) {
    if (!this.isDragging) return
    e.preventDefault()

    // Obtener coordenadas del evento (mouse o touch)
    const clientX = e.clientX || (e.touches && e.touches[0] ? e.touches[0].clientX : 0)
    const clientY = e.clientY || (e.touches && e.touches[0] ? e.touches[0].clientY : 0)

    // Calcular nueva posición
    this.position.x = clientX - this.dragOffset.x
    this.position.y = clientY - this.dragOffset.y

    // Limitar a los bordes de la pantalla
    const maxX = window.innerWidth - this.widget.offsetWidth
    const maxY = window.innerHeight - this.widget.offsetHeight

    this.position.x = Math.max(0, Math.min(this.position.x, maxX))
    this.position.y = Math.max(0, Math.min(this.position.y, maxY))

    // Aplicar posición
    this.updatePosition()
  }

  stopDrag() {
    if (this.isDragging) {
      this.isDragging = false
      document.body.style.cursor = "default"
      this.savePosition()
      console.log("[RadioRías] Drag finalizado")
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

  initAudio() {
    this.audio = document.createElement('audio')
    this.audio.crossOrigin = "anonymous"
    this.audio.volume = this.volume / 100
    this.audio.preload = "none"
    
    // Eventos de audio
    this.audio.addEventListener('loadstart', () => {
      this.updateStatus("Conectando...")
      this.setLoadingState(true)
    })
    
    this.audio.addEventListener('canplay', () => {
      this.updateStatus("Listo")
      this.setLoadingState(false)
    })
    
    this.audio.addEventListener('playing', () => {
      this.updateStatus("Reproduciendo")
      this.setLoadingState(false)
    })
    
    this.audio.addEventListener('pause', () => {
      this.updateStatus("Pausado")
    })
    
    this.audio.addEventListener('error', (e) => {
      console.error("[RadioRías] Error de audio:", e)
      this.updateStatus("Error de conexión")
      this.setLoadingState(false)
    })

    document.body.appendChild(this.audio)
    console.log("[RadioRías] Audio inicializado")
  }

  async togglePlay() {
    console.log("[RadioRías] Toggle play - Estado actual:", this.isPlaying)
    
    if (!this.isPlaying) {
      try {
        this.audio.src = this.streamUrl
        this.setLoadingState(true)
        this.updateStatus("Conectando...")
        
        await this.audio.play()
        this.isPlaying = true
        this.updatePlayButton()
        console.log("[RadioRías] Reproducción iniciada")
        
      } catch (error) {
        console.error("[RadioRías] Error al reproducir:", error)
        this.updateStatus("Error al reproducir")
        this.setLoadingState(false)
      }
    } else {
      this.audio.pause()
      this.isPlaying = false
      this.updatePlayButton()
      this.updateStatus("Pausado")
      console.log("[RadioRías] Reproducción pausada")
    }
  }

  setLoadingState(isLoading) {
    const playBtn = document.getElementById("simple-play-btn")
    const playIcon = playBtn.querySelector("i")
    
    if (isLoading) {
      playIcon.className = "fas fa-spinner fa-spin"
      playBtn.disabled = true
      playBtn.style.opacity = "0.6"
    } else {
      playIcon.className = this.isPlaying ? "fas fa-pause" : "fas fa-play"
      playBtn.disabled = false
      playBtn.style.opacity = "1"
    }
    
    console.log(`[RadioRías] Loading state: ${isLoading}`)
  }

  updatePlayButton() {
    const playIcon = document.querySelector("#simple-play-btn i")
    if (playIcon) {
      playIcon.className = this.isPlaying ? "fas fa-pause" : "fas fa-play"
    }
  }

  updateStatus(status) {
    const statusEl = document.getElementById("simple-status")
    if (statusEl) {
      statusEl.textContent = status
    }
  }

  setVolume(value) {
    this.volume = parseInt(value)
    if (this.audio) {
      this.audio.volume = this.volume / 100
    }
    console.log(`[RadioRías] Volumen: ${this.volume}%`)
  }

  showWidget() {
    if (this.widget) {
      this.widget.style.display = "block"
      this.isVisible = true
      this.saveVisibilityState()
      console.log("[RadioRías] Widget mostrado")
    }
  }

  hideWidget() {
    if (this.widget) {
      this.widget.style.display = "none"
      this.isVisible = false
      this.saveVisibilityState()
      
      // Pausar audio si está reproduciendo
      if (this.isPlaying) {
        this.audio.pause()
        this.isPlaying = false
      }
      
      console.log("[RadioRías] Widget ocultado")
    }
  }

  loadPosition() {
    const saved = localStorage.getItem("simple-radio-widget-position")
    if (saved) {
      try {
        this.position = JSON.parse(saved)
        this.updatePosition()
        console.log("[RadioRías] Posición cargada:", this.position)
      } catch (e) {
        console.log("[RadioRías] Error cargando posición, usando por defecto")
      }
    }
  }

  savePosition() {
    localStorage.setItem("simple-radio-widget-position", JSON.stringify(this.position))
    console.log("[RadioRías] Posición guardada:", this.position)
  }

  loadVisibilityState() {
    const saved = localStorage.getItem("simple-radio-widget-visible")
    if (saved === "true") {
      this.showWidget()
    }
  }

  saveVisibilityState() {
    localStorage.setItem("simple-radio-widget-visible", this.isVisible.toString())
  }
}

// Inicializar widget cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
  console.log("[RadioRías] DOM cargado, creando widget simple...")
  window.simpleRadioWidget = new SimpleRadioWidget()
})

// Función global para mostrar el widget (llamada desde botones)
function showSimpleRadioWidget() {
  if (window.simpleRadioWidget) {
    window.simpleRadioWidget.showWidget()
  }
}
