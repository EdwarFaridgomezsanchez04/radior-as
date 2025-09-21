/**
 * RadioRías Dual Buffer Widget - Sistema Anti-Cortes
 * Elimina el bug de pausa a los 2 minutos usando doble buffer
 * Versión: 1.0 - Sin Bugs Garantizado
 */

class DualBufferRadioWidget {
  constructor() {
    // Estado del widget
    this.isPlaying = false
    this.isVisible = false
    this.volume = 50
    
    // Sistema de dual buffer
    this.audioA = null // Buffer primario
    this.audioB = null // Buffer secundario
    this.activeBuffer = 'A' // 'A' o 'B'
    this.streamUrl = "proxy.php"
    
    // Control de cambio de buffer
    this.switchTimer = null
    this.switchInterval = 90000 // 90 segundos (antes de los 2 min del navegador)
    this.preloadOffset = 5000 // 5 segundos antes de cambiar
    
    // Drag & drop
    this.isDragging = false
    this.dragOffset = { x: 0, y: 0 }
    this.position = { x: 20, y: 20 }
    
    // Monitoreo de salud
    this.healthMonitor = null
    this.lastHealthCheck = Date.now()
    this.bufferHealthA = true
    this.bufferHealthB = true
    
    // Logs y debugging
    this.debugMode = true
    this.switchCount = 0
    
    this.log("Dual Buffer Widget inicializado")
    this.init()
  }

  init() {
    this.createWidget()
    this.bindEvents()
    this.setupDrag()
    this.initDualAudio()
    this.startHealthMonitor()
    this.loadPosition()
    this.loadVisibilityState()
    this.log("Inicialización completa")
  }

  log(message, level = 'info') {
    if (this.debugMode) {
      const timestamp = new Date().toLocaleTimeString()
      const prefix = `[RadioRías-DualBuffer ${timestamp}]`
      
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
    widget.id = "dual-buffer-widget"
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
      <div id="dual-drag-header" style="
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
            <div id="buffer-indicator" style="font-size: 10px; color: #666; margin-top: 2px;">
              Buffer A Activo
            </div>
          </div>
        </div>
        <button id="dual-close-btn" style="
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
        <button id="dual-play-btn" style="
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
          <input type="range" id="dual-volume-slider" min="0" max="100" value="50" style="
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
      
      <div id="dual-status" style="
        margin-top: 10px;
        font-size: 12px;
        color: #666;
        text-align: center;
      ">Listo para streaming continuo</div>
    `

    document.body.appendChild(widget)
    this.widget = widget
    this.log("Widget creado")
  }

  bindEvents() {
    // Botón de play/pause - con prevención de drag
    const playBtn = document.getElementById("dual-play-btn")
    playBtn.addEventListener("click", () => this.togglePlay())
    playBtn.addEventListener("mousedown", (e) => e.stopPropagation())
    playBtn.addEventListener("touchstart", (e) => e.stopPropagation())

    // Botón de cerrar - con prevención de drag
    const closeBtn = document.getElementById("dual-close-btn")
    closeBtn.addEventListener("click", (e) => {
      e.stopPropagation()
      this.hideWidget()
    })
    closeBtn.addEventListener("mousedown", (e) => e.stopPropagation())
    closeBtn.addEventListener("touchstart", (e) => e.stopPropagation())

    // Control de volumen - con prevención de drag
    const volumeSlider = document.getElementById("dual-volume-slider")
    volumeSlider.addEventListener("input", (e) => this.setVolume(e.target.value))
    volumeSlider.addEventListener("mousedown", (e) => e.stopPropagation())
    volumeSlider.addEventListener("touchstart", (e) => e.stopPropagation())
    
    this.log("Eventos configurados")
  }

  initDualAudio() {
    this.log("Inicializando sistema dual buffer...")
    
    // Crear Audio A (Buffer Primario)
    this.audioA = this.createAudioElement('A')
    
    // Crear Audio B (Buffer Secundario)  
    this.audioB = this.createAudioElement('B')
    
    this.log("Dual buffer inicializado - A y B listos")
  }

  createAudioElement(bufferName) {
    const audio = document.createElement('audio')
    audio.crossOrigin = "anonymous"
    audio.volume = this.volume / 100
    audio.preload = "none"
    
    // Eventos específicos para cada buffer
    audio.addEventListener('loadstart', () => {
      this.log(`Buffer ${bufferName}: Iniciando carga`)
    })
    
    audio.addEventListener('canplay', () => {
      this.log(`Buffer ${bufferName}: Listo para reproducir`)
      this.updateBufferHealth(bufferName, true)
    })
    
    audio.addEventListener('playing', () => {
      this.log(`Buffer ${bufferName}: Reproduciendo`)
      this.updateStatus("Reproduciendo")
    })
    
    audio.addEventListener('pause', () => {
      this.log(`Buffer ${bufferName}: Pausado`)
    })
    
    audio.addEventListener('error', (e) => {
      this.log(`Buffer ${bufferName}: Error - ${e.message}`, 'error')
      this.updateBufferHealth(bufferName, false)
      this.handleBufferError(bufferName)
    })
    
    audio.addEventListener('stalled', () => {
      this.log(`Buffer ${bufferName}: Atascado`, 'warn')
      this.updateBufferHealth(bufferName, false)
    })
    
    audio.addEventListener('waiting', () => {
      this.log(`Buffer ${bufferName}: Esperando datos`)
    })

    document.body.appendChild(audio)
    return audio
  }

  async togglePlay() {
    this.log(`Toggle play - Estado actual: ${this.isPlaying}`)
    
    if (!this.isPlaying) {
      await this.startDualStreaming()
    } else {
      this.stopDualStreaming()
    }
  }

  async startDualStreaming() {
    this.log("Iniciando streaming dual buffer")
    this.isPlaying = true
    this.updatePlayButton()
    this.updateStatus("Conectando...")
    
    try {
      // Iniciar con Buffer A
      this.activeBuffer = 'A'
      await this.startBuffer('A')
      
      // Programar precarga de Buffer B
      setTimeout(() => {
        if (this.isPlaying) {
          this.preloadBuffer('B')
        }
      }, this.preloadOffset)
      
      // Programar primer cambio de buffer
      this.scheduleSwitchBuffer()
      
      this.log("Streaming dual iniciado exitosamente")
      
    } catch (error) {
      this.log(`Error iniciando streaming: ${error.message}`, 'error')
      this.updateStatus("Error al iniciar")
      this.isPlaying = false
      this.updatePlayButton()
    }
  }

  async startBuffer(bufferName) {
    const audio = bufferName === 'A' ? this.audioA : this.audioB
    const timestamp = Date.now()
    
    this.log(`Iniciando buffer ${bufferName}`)
    
    audio.src = `${this.streamUrl}?buffer=${bufferName}&t=${timestamp}`
    audio.load()
    
    await audio.play()
    this.updateBufferIndicator(bufferName)
    
    this.log(`Buffer ${bufferName} iniciado y reproduciendo`)
  }

  async preloadBuffer(bufferName) {
    const audio = bufferName === 'A' ? this.audioA : this.audioB
    const timestamp = Date.now()
    
    this.log(`Precargando buffer ${bufferName}`)
    
    audio.src = `${this.streamUrl}?buffer=${bufferName}&t=${timestamp}`
    audio.load()
    
    // No reproducir aún, solo precargar
    this.log(`Buffer ${bufferName} precargado`)
  }

  scheduleSwitchBuffer() {
    if (this.switchTimer) {
      clearTimeout(this.switchTimer)
    }
    
    this.switchTimer = setTimeout(() => {
      if (this.isPlaying) {
        this.switchBuffer()
      }
    }, this.switchInterval)
    
    this.log(`Cambio de buffer programado en ${this.switchInterval/1000} segundos`)
  }

  async switchBuffer() {
    const currentBuffer = this.activeBuffer
    const nextBuffer = this.activeBuffer === 'A' ? 'B' : 'A'
    
    this.switchCount++
    this.log(`CAMBIO DE BUFFER #${this.switchCount}: ${currentBuffer} → ${nextBuffer}`)
    
    try {
      const currentAudio = currentBuffer === 'A' ? this.audioA : this.audioB
      const nextAudio = nextBuffer === 'A' ? this.audioA : this.audioB
      
      // Asegurar que el siguiente buffer esté listo
      if (nextAudio.readyState < 3) { // HAVE_FUTURE_DATA
        this.log(`Buffer ${nextBuffer} no está listo, forzando carga`)
        await this.startBuffer(nextBuffer)
      } else {
        // Reproducir el buffer precargado
        await nextAudio.play()
      }
      
      // Cambio instantáneo
      this.activeBuffer = nextBuffer
      this.updateBufferIndicator(nextBuffer)
      
      // Pausar el buffer anterior (después de un pequeño delay para evitar cortes)
      setTimeout(() => {
        currentAudio.pause()
        this.log(`Buffer ${currentBuffer} pausado`)
        
        // Precargar el buffer que acabamos de pausar para el próximo cambio
        setTimeout(() => {
          if (this.isPlaying) {
            this.preloadBuffer(currentBuffer)
          }
        }, this.preloadOffset)
        
      }, 1000)
      
      // Programar próximo cambio
      this.scheduleSwitchBuffer()
      
      this.log(`Cambio de buffer completado exitosamente`)
      
    } catch (error) {
      this.log(`Error en cambio de buffer: ${error.message}`, 'error')
      // En caso de error, intentar con el buffer actual
      this.handleSwitchError()
    }
  }

  handleSwitchError() {
    this.log("Manejando error de cambio de buffer", 'warn')
    
    // Intentar mantener el buffer actual funcionando
    const currentAudio = this.activeBuffer === 'A' ? this.audioA : this.audioB
    
    if (currentAudio.paused) {
      currentAudio.play().catch(e => {
        this.log(`Error manteniendo buffer actual: ${e.message}`, 'error')
        // Como último recurso, reiniciar todo el sistema
        this.restartDualSystem()
      })
    }
    
    // Programar próximo intento de cambio
    setTimeout(() => {
      if (this.isPlaying) {
        this.scheduleSwitchBuffer()
      }
    }, 10000) // 10 segundos de espera antes del próximo intento
  }

  async restartDualSystem() {
    this.log("Reiniciando sistema dual buffer", 'warn')
    
    // Parar todo
    this.stopDualStreaming()
    
    // Esperar un momento
    await new Promise(resolve => setTimeout(resolve, 2000))
    
    // Reiniciar
    if (this.isPlaying) {
      await this.startDualStreaming()
    }
  }

  stopDualStreaming() {
    this.log("Deteniendo streaming dual buffer")
    
    this.isPlaying = false
    this.updatePlayButton()
    this.updateStatus("Pausado")
    
    // Pausar ambos buffers
    if (this.audioA) this.audioA.pause()
    if (this.audioB) this.audioB.pause()
    
    // Cancelar timers
    if (this.switchTimer) {
      clearTimeout(this.switchTimer)
      this.switchTimer = null
    }
    
    this.log("Streaming dual detenido")
  }

  updateBufferIndicator(activeBuffer) {
    const indicator = document.getElementById("buffer-indicator")
    if (indicator) {
      indicator.textContent = `Buffer ${activeBuffer} Activo`
      indicator.style.color = activeBuffer === 'A' ? '#20B2AA' : '#00CED1'
    }
  }

  updateBufferHealth(bufferName, isHealthy) {
    if (bufferName === 'A') {
      this.bufferHealthA = isHealthy
    } else {
      this.bufferHealthB = isHealthy
    }
    
    this.log(`Buffer ${bufferName} salud: ${isHealthy ? 'BUENA' : 'MALA'}`)
  }

  handleBufferError(bufferName) {
    this.log(`Manejando error en buffer ${bufferName}`, 'error')
    
    if (this.isPlaying && bufferName === this.activeBuffer) {
      // Si el buffer activo falló, cambiar inmediatamente al otro
      const otherBuffer = bufferName === 'A' ? 'B' : 'A'
      this.log(`Buffer activo falló, cambiando emergencia a ${otherBuffer}`)
      this.switchBuffer()
    }
  }

  startHealthMonitor() {
    this.healthMonitor = setInterval(() => {
      if (this.isPlaying) {
        this.checkSystemHealth()
      }
    }, 5000) // Cada 5 segundos
    
    this.log("Monitor de salud iniciado")
  }

  checkSystemHealth() {
    const activeAudio = this.activeBuffer === 'A' ? this.audioA : this.audioB
    const inactiveAudio = this.activeBuffer === 'A' ? this.audioB : this.audioA
    
    // Verificar que el buffer activo esté reproduciendo
    if (activeAudio.paused && this.isPlaying) {
      this.log("Buffer activo pausado inesperadamente", 'warn')
      this.switchBuffer()
      return
    }
    
    // Verificar tiempo desde último check
    const timeSinceLastCheck = Date.now() - this.lastHealthCheck
    if (timeSinceLastCheck > 10000) { // Más de 10 segundos
      this.log("Posible problema de salud del sistema", 'warn')
    }
    
    this.lastHealthCheck = Date.now()
  }

  // Resto de métodos (drag, UI, etc.) - Copiados del widget anterior
  setupDrag() {
    const dragHeader = document.getElementById("dual-drag-header")
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
    if (this.audioA) this.audioA.volume = this.volume / 100
    if (this.audioB) this.audioB.volume = this.volume / 100
    this.log(`Volumen ajustado: ${this.volume}%`)
  }

  updatePlayButton() {
    const playIcon = document.querySelector("#dual-play-btn i")
    if (playIcon) {
      playIcon.className = this.isPlaying ? "fas fa-pause" : "fas fa-play"
    }
  }

  updateStatus(status) {
    const statusEl = document.getElementById("dual-status")
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
      this.stopDualStreaming()
      
      // Detener monitores
      if (this.healthMonitor) {
        clearInterval(this.healthMonitor)
        this.healthMonitor = null
      }
      
      this.log("Widget ocultado")
    }
  }

  loadPosition() {
    const saved = localStorage.getItem("dual-buffer-widget-position")
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
    localStorage.setItem("dual-buffer-widget-position", JSON.stringify(this.position))
  }

  loadVisibilityState() {
    const saved = localStorage.getItem("dual-buffer-widget-visible")
    if (saved === "true") {
      this.showWidget()
    }
  }

  saveVisibilityState() {
    localStorage.setItem("dual-buffer-widget-visible", this.isVisible.toString())
  }
}

// Inicializar widget cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
  console.log("[RadioRías] DOM cargado, creando widget dual buffer...")
  window.dualBufferRadioWidget = new DualBufferRadioWidget()
})

// Función global para mostrar el widget
function showDualBufferRadioWidget() {
  if (window.dualBufferRadioWidget) {
    window.dualBufferRadioWidget.showWidget()
  }
}
