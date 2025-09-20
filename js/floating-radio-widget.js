/**
 * Widget Flotante de RadioRías - Versión 3.0 Optimizada
 * Reproductor de radio flotante sin cortes
 * Última actualización: 2024-12-19
 */

class FloatingRadioWidget {
  constructor() {
    this.isExpanded = false
    this.isPlaying = false
    this.isDragging = false
    this.isVisible = false
    this.volume = 50
    this.dragOffset = { x: 0, y: 0 }
    this.position = { x: 20, y: 20 }

    this.audio = null
    this.streamUrl = null
    this.isLoading = false
    this.retryCount = 0
    this.maxRetries = 10 // Más intentos
    this.retryDelay = 1000 // 1 segundo inicial
    this.maxRetryDelay = 5000 // Máximo 5 segundos
    this.playPromise = null
    this.lastToggleTime = 0
    this.toggleDebounceDelay = 300

    this.connectionMonitor = null
    this.lastDataTime = Date.now()
    this.connectionTimeout = 15000 // 15 segundos sin datos = reconectar
    this.isReconnecting = false
    this.reconnectAttempts = 0
    this.maxReconnectAttempts = 20

    this.preferredBufferSize = 10 // 10 segundos de buffer
    this.audioContext = null
    this.sourceNode = null

    this.init()
  }

  init() {
    this.createWidget()
    this.bindEvents()
    this.loadPosition()
    this.loadVisibilityState() // Cargar estado de visibilidad
    this.initAudio()
    this.startConnectionMonitor()
  }

  createWidget() {
    // Crear contenedor principal
    const widget = document.createElement("div")
    widget.id = "floating-radio-widget"
    widget.className = "floating-widget"

    // Widget compacto
    widget.innerHTML = `
            <div class="widget-compact" id="widget-compact">
                <div class="widget-header">
                    <div class="radio-info">
                        <div class="radio-logo">
                            <i class="fas fa-broadcast-tower"></i>
                        </div>
                    </div>
                    <div class="widget-controls">
                        <button class="close-btn" id="close-btn" title="Cerrar">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="widget-controls-compact">
                    <button class="control-btn play-btn" id="play-btn" title="">
                        <i class="fas fa-play"></i>
                    </button>
                    <div class="volume-control-compact">
                        <i class="fas fa-volume-up volume-icon"></i>
                        <input type="range" id="volume-slider-compact" class="volume-slider-compact" min="0" max="100" value="50" title="Volumen">
                    </div>
                </div>
            </div>

        `

    document.body.appendChild(widget)
    this.widget = widget
    this.updatePosition()
  }

  bindEvents() {
    // Botón de cerrar
    document.getElementById("close-btn").addEventListener("click", () => this.hideWidget())

    // Control de reproducción (play/pause)
    document.getElementById("play-btn").addEventListener("click", () => this.togglePlay())

    // Control de volumen compacto
    const volumeSliderCompact = document.getElementById("volume-slider-compact")
    volumeSliderCompact.addEventListener("input", (e) => this.setVolume(e.target.value))

    // Funcionalidad de arrastre
    this.setupDrag()
  }

  setupDrag() {
    const widget = this.widget
    const header = widget.querySelector(".widget-header")

    // Función para iniciar arrastre
    const startDrag = (e) => {
      this.isDragging = true
      const rect = widget.getBoundingClientRect()
      this.dragOffset.x = e.clientX - rect.left
      this.dragOffset.y = e.clientY - rect.top

      widget.style.transition = "none"
      document.addEventListener("mousemove", this.drag)
      document.addEventListener("mouseup", this.endDrag)
      e.preventDefault()
    }

    // Función de arrastre
    this.drag = (e) => {
      if (!this.isDragging) return

      this.position.x = e.clientX - this.dragOffset.x
      this.position.y = e.clientY - this.dragOffset.y

      // Limitar a los bordes de la pantalla
      const maxX = window.innerWidth - widget.offsetWidth
      const maxY = window.innerHeight - widget.offsetHeight

      this.position.x = Math.max(0, Math.min(this.position.x, maxX))
      this.position.y = Math.max(0, Math.min(this.position.y, maxY))

      this.updatePosition()
    }

    // Función para terminar arrastre
    this.endDrag = () => {
      this.isDragging = false
      widget.style.transition = ""
      document.removeEventListener("mousemove", this.drag)
      document.removeEventListener("mouseup", this.endDrag)
      this.savePosition()
    }

    // Event listeners para arrastre
    header.addEventListener("mousedown", startDrag)

    // Soporte para touch (móviles)
    header.addEventListener("touchstart", (e) => {
      e.preventDefault()
      startDrag(e.touches[0])
    })
  }

  updatePosition() {
    this.widget.style.left = this.position.x + "px"
    this.widget.style.top = this.position.y + "px"
  }


  showWidget() {
    this.isVisible = true
    this.widget.style.display = "block"
    this.widget.style.opacity = "0"
    this.widget.style.transform = "scale(0.8)"

    // Animación de entrada
    setTimeout(() => {
      this.widget.style.transition = "all 0.3s cubic-bezier(0.4, 0, 0.2, 1)"
      this.widget.style.opacity = "1"
      this.widget.style.transform = "scale(1)"
    }, 10)

    this.saveVisibilityState()
    console.log("[RadioRías] Widget mostrado")
  }

  hideWidget() {
    this.isVisible = false
    this.widget.style.transition = "all 0.3s cubic-bezier(0.4, 0, 0.2, 1)"
    this.widget.style.opacity = "0"
    this.widget.style.transform = "scale(0.8)"

    setTimeout(() => {
      this.widget.style.display = "none"
    }, 300)

    this.saveVisibilityState()
    console.log("[RadioRías] Widget ocultado")
  }

  initAudio() {
    // Usar video element como en la imagen para mejor compatibilidad
    this.audio = document.createElement('video')
    this.audio.preload = "auto"
    this.audio.crossOrigin = "anonymous"
    this.audio.volume = this.volume / 100
    this.audio.autoplay = false // Controlado manualmente
    this.audio.controls = false // Sin controles visibles
    this.audio.style.display = 'none' // Oculto

    if ("mediaSession" in navigator) {
      navigator.mediaSession.metadata = new MediaMetadata({
        title: "RadioRías",
        artist: "Península de Morrazo",
        album: "En Vivo",
        artwork: [
          { src: "/images/radio-logo-96.png", sizes: "96x96", type: "image/png" },
          { src: "/images/radio-logo-256.png", sizes: "256x256", type: "image/png" },
        ],
      })
    }

    this.audio.addEventListener("loadstart", () => {
      this.setLoadingState(true)
      this.lastDataTime = Date.now()
      console.log("[RadioRías] Iniciando carga del stream...")
    })

    this.audio.addEventListener("loadeddata", () => {
      this.lastDataTime = Date.now()
      console.log("[RadioRías] Datos iniciales cargados")
    })

    this.audio.addEventListener("canplay", () => {
      this.setLoadingState(false)
      this.lastDataTime = Date.now()
      console.log("[RadioRías] Stream listo para reproducir")
    })

    this.audio.addEventListener("playing", () => {
      this.setLoadingState(false)
      this.updateStatus("Reproduciendo RadioRías")
      this.lastDataTime = Date.now()
      this.retryCount = 0 // Reset retry count on successful play
      this.reconnectAttempts = 0
      console.log("[RadioRías] Reproduciendo...")
    })

    this.audio.addEventListener("pause", () => {
      if (!this.isReconnecting) {
        this.updateStatus("Pausado")
        console.log("[RadioRías] Pausado por usuario")
      }
    })

    this.audio.addEventListener("stalled", () => {
      console.log("[RadioRías] Stream estancado, iniciando reconexión...")
      this.handleStreamInterruption("stalled")
    })

    this.audio.addEventListener("waiting", () => {
      console.log("[RadioRías] Buffering...")
      this.updateStatus("Buffering...")
      this.lastDataTime = Date.now()
    })

    this.audio.addEventListener("canplaythrough", () => {
      console.log("[RadioRías] Buffer completo, reproducción fluida")
      this.lastDataTime = Date.now()
    })

    this.audio.addEventListener("ended", () => {
      console.log("[RadioRías] Stream terminado, reconectando automáticamente...")
      this.handleStreamInterruption("ended")
    })

    this.audio.addEventListener("error", (e) => {
      console.error("[RadioRías] Error en el stream:", e)
      this.handleStreamInterruption("error")
    })

    this.audio.addEventListener("suspend", () => {
      console.log("[RadioRías] Descarga suspendida")
      this.handleStreamInterruption("suspend")
    })

    this.audio.addEventListener("abort", () => {
      console.log("[RadioRías] Carga abortada")
      this.handleStreamInterruption("abort")
    })

    this.audio.addEventListener("progress", () => {
      this.lastDataTime = Date.now()
    })

    this.audio.addEventListener("timeupdate", () => {
      this.lastDataTime = Date.now()
    })
  }

  startConnectionMonitor() {
    if (this.connectionMonitor) {
      clearInterval(this.connectionMonitor)
    }

    this.connectionMonitor = setInterval(() => {
      if (this.isPlaying && !this.isReconnecting) {
        const timeSinceLastData = Date.now() - this.lastDataTime

        if (timeSinceLastData > this.connectionTimeout) {
          console.log(`[RadioRías] Sin datos por ${timeSinceLastData}ms, reconectando...`)
          this.handleStreamInterruption("timeout")
        }
      }
    }, 2000) // Verificar cada 2 segundos
  }

  async handleStreamInterruption(reason) {
    if (this.isReconnecting || !this.isPlaying) {
      return
    }

    this.isReconnecting = true
    this.reconnectAttempts++

    console.log(`[RadioRías] Interrupción detectada: ${reason} (intento ${this.reconnectAttempts})`)
    this.updateStatus(`Reconectando... (${this.reconnectAttempts})`)

    if (this.reconnectAttempts > this.maxReconnectAttempts) {
      console.log("[RadioRías] Máximo de intentos de reconexión alcanzado")
      this.isReconnecting = false
      this.isPlaying = false
      this.updatePlayButton()
      this.updateStatus("Error de conexión")
      return
    }

    const delay = Math.min(1000 * this.reconnectAttempts, 10000) // Máximo 10 segundos

    setTimeout(async () => {
      try {
        // Reinicializar el audio
        this.audio.pause()
        this.audio.currentTime = 0

        // Reconectar al stream
        const connected = await this.connectToStream()

        if (connected && this.audio) {
          this.playPromise = this.audio.play()
          await this.playPromise

          console.log("[RadioRías] Reconexión exitosa")
          this.isReconnecting = false
          this.updateStatus("Reproduciendo RadioRías")
        } else {
          // Reintentar
          this.isReconnecting = false
          this.handleStreamInterruption("reconnect_failed")
        }
      } catch (error) {
        console.error("[RadioRías] Error en reconexión:", error)
        this.isReconnecting = false
        this.handleStreamInterruption("reconnect_error")
      }
    }, delay)
  }

  async togglePlay() {
    if (this.isLoading || this.isReconnecting) return

    if (this.playPromise) {
      console.log("[RadioRías] Operación en curso, ignorando...")
      return
    }

    const now = Date.now()
    if (now - this.lastToggleTime < this.toggleDebounceDelay) {
      console.log("[RadioRías] Click muy rápido, ignorando...")
      return
    }
    this.lastToggleTime = now

    if (!this.isPlaying) {
      this.updateStatus("Conectando...")
      this.updatePlayButton()

      const connected = await this.connectToStream()

      if (connected && this.audio) {
        try {
          this.playPromise = this.audio.play()
          this.isPlaying = true
          this.updatePlayButton()

          await this.playPromise

          if (this.isPlaying) {
            this.updateStatus("Reproduciendo RadioRías")
            this.lastDataTime = Date.now()
            console.log("[RadioRías] Reproducción iniciada exitosamente")
          }
        } catch (error) {
          console.error("[RadioRías] Error reproduciendo:", error)
          this.isPlaying = false
          this.updatePlayButton()
          this.updateStatus("Error de reproducción")
          this.handleAudioError(error)
        } finally {
          this.playPromise = null
        }
      } else {
        this.isPlaying = false
        this.updatePlayButton()
        this.updateStatus("Error de conexión")
      }
    } else {
      this.isPlaying = false
      this.isReconnecting = false
      this.reconnectAttempts = 0
      this.updatePlayButton()

      if (this.audio) {
        this.audio.pause()
      }

      if (this.playPromise) {
        this.playPromise = null
      }

      this.updateStatus("Pausado")
      console.log("[RadioRías] Pausado por usuario")
    }
  }

  async connectToStream() {
    if (this.isLoading) return false

    this.isLoading = true
    this.setLoadingState(true)

    const streamObtained = await this.getStreamInfo()

    if (!streamObtained) {
      this.setLoadingState(false)
      this.isLoading = false
      return false
    }

    try {
      this.audio.preload = "auto"
      this.audio.crossOrigin = "anonymous"
      this.audio.volume = this.volume / 100

      // Usar directamente la URL del servidor como en la imagen
      this.audio.src = this.streamUrl

      const loadPromise = new Promise((resolve, reject) => {
        const timeout = setTimeout(() => {
          reject(new Error("Timeout cargando stream"))
        }, 15000) // 15 segundos timeout

        const onCanPlay = () => {
          clearTimeout(timeout)
          this.audio.removeEventListener("canplay", onCanPlay)
          this.audio.removeEventListener("error", onError)
          resolve()
        }

        const onError = (e) => {
          clearTimeout(timeout)
          this.audio.removeEventListener("canplay", onCanPlay)
          this.audio.removeEventListener("error", onError)
          reject(e)
        }

        this.audio.addEventListener("canplay", onCanPlay)
        this.audio.addEventListener("error", onError)
        this.audio.load()
      })

      await loadPromise

      this.setLoadingState(false)
      this.isLoading = false
      this.lastDataTime = Date.now()
      console.log("[RadioRías] Stream cargado exitosamente")
      return true
    } catch (error) {
      console.error("[RadioRías] Error conectando al stream:", error)
      this.setLoadingState(false)
      this.isLoading = false
      this.handleAudioError(error)
      return false
    }
  }

  stop() {
    if (this.audio) {
      this.audio.pause()
      this.audio.currentTime = 0
    }

    this.isPlaying = false
    this.isReconnecting = false
    this.reconnectAttempts = 0
    this.updatePlayButton()
    this.updateStatus("Detenido")
    this.setLoadingState(false)

    // Cancelar cualquier promesa de play pendiente
    if (this.playPromise) {
      this.playPromise = null
    }

    console.log("[RadioRías] Deteniendo...")
  }

  setVolume(value) {
    this.volume = Number.parseInt(value)

    // Actualizar icono de volumen según el nivel
    const volumeIcon = document.querySelector(".volume-icon")
    if (this.volume === 0) {
      volumeIcon.className = "fas fa-volume-mute volume-icon"
    } else if (this.volume < 30) {
      volumeIcon.className = "fas fa-volume-down volume-icon"
    } else if (this.volume < 70) {
      volumeIcon.className = "fas fa-volume-up volume-icon"
    } else {
      volumeIcon.className = "fas fa-volume-up volume-icon"
    }

    // Aplicar volumen al audio
    if (this.audio) {
      this.audio.volume = this.volume / 100
    }

    console.log("[RadioRías] Volumen ajustado a:", this.volume + "%")
  }

  // Obtener información del stream de RadioRías
  async getStreamInfo() {
    try {
      console.log("[RadioRías] Conectando al servidor...")

      // Usar directamente la URL del servidor como en la imagen
      this.streamUrl = "http://88.150.230.110:8950/stream"
        console.log("[RadioRías] Stream conectado:", this.streamUrl)
        this.updateStatus("Conectado")
        return true
    } catch (error) {
      console.error("[RadioRías] Error conectando al stream:", error)
      // Usar URL de respaldo en caso de error
      this.streamUrl = "http://88.150.230.110:8950/stream"
      this.updateStatus("Conectando a respaldo...")
      return true
    }
  }

  handleAudioError(error) {
    console.error("[RadioRías] Error de audio:", error)

    if (this.retryCount < this.maxRetries && this.isPlaying) {
      this.retryCount++
      const delay = Math.min(this.retryDelay * Math.pow(2, this.retryCount - 1), this.maxRetryDelay)

      console.log(`[RadioRías] Reintentando en ${delay}ms (${this.retryCount}/${this.maxRetries})...`)
      this.updateStatus(`Reintentando... (${this.retryCount}/${this.maxRetries})`)

      setTimeout(() => {
        if (this.isPlaying) {
          // Solo reintentar si aún queremos reproducir
          this.connectToStream().then((connected) => {
            if (connected) {
              this.audio.play().catch((e) => {
                console.error("[RadioRías] Error en retry play:", e)
                this.handleAudioError(e)
              })
            }
          })
        }
      }, delay)
    } else {
      this.updateStatus("Error de conexión")
      this.retryCount = 0
      this.isPlaying = false
      this.updatePlayButton()
      console.log("[RadioRías] Máximo de reintentos alcanzado o reproducción detenida")
    }
  }

  destroy() {
    if (this.connectionMonitor) {
      clearInterval(this.connectionMonitor)
    }

    if (this.audio) {
      this.audio.pause()
      this.audio.src = ""
      this.audio = null
    }

    if (this.widget) {
      this.widget.remove()
    }
  }

  updateStatus(status) {
    const statusElements = document.querySelectorAll("#radio-status, #radio-status-expanded")
    statusElements.forEach((element) => {
      element.textContent = status
    })
  }

  updatePlayButton() {
    const playIcon = document.querySelector(".play-btn i")
    if (playIcon) {
      playIcon.className = this.isPlaying ? "fas fa-pause" : "fas fa-play"
    }
  }

  savePosition() {
    localStorage.setItem("radio-widget-position", JSON.stringify(this.position))
  }

  loadPosition() {
    const saved = localStorage.getItem("radio-widget-position")
    if (saved) {
      this.position = JSON.parse(saved)
      this.updatePosition()
    }
  }

  loadVisibilityState() {
    const isVisible = localStorage.getItem("radio-widget-visible")
    if (isVisible === "true") {
      this.showWidget()
    } else {
      this.hideWidget()
    }
  }

  saveVisibilityState() {
    localStorage.setItem("radio-widget-visible", this.isVisible.toString())
  }

  // Método público para reiniciar manualmente
  restart() {
    console.log("[RadioRías] Reinicio manual del stream")
    this.currentReconnectAttempt = 0
    this.reconnectAttempts = 0
    this.isReconnecting = false
    
    // Usar directamente la URL del servidor como en la imagen
    this.audio.src = this.streamUrl
    this.audio.load()
    
    // Si estaba reproduciendo, intentar reproducir automáticamente
    if (this.isPlaying) {
      setTimeout(() => {
        this.audio.play().catch(e => {
          console.warn("[RadioRías] No se pudo reproducir automáticamente:", e)
        })
      }, 1000)
    }
  }
}

// Inicializar el widget cuando el DOM esté listo
let radioWidget

document.addEventListener("DOMContentLoaded", () => {
  radioWidget = new FloatingRadioWidget()

  // Hacer el widget disponible globalmente
  window.radioWidget = radioWidget

  // Función global para mostrar el widget desde botones "Escuchar En Vivo"
  window.showRadioWidget = () => {
    radioWidget.showWidget()
  }
})
