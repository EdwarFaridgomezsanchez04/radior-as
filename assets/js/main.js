// Main JavaScript functionality for Radio Rías

// Mobile Menu Toggle
function toggleMobileMenu() {
  const menu = document.getElementById("mobile-menu")
  const button = document.getElementById("mobile-menu-button")
  const hamburgerIcon = button?.querySelector(".hamburger-icon")
  
  console.log("Toggle menu clicked", {menu, button, hamburgerIcon}); // Debug
  
  if (!menu) {
    console.error("Menu not found!")
    return
  }
  
  const isHidden = menu.classList.contains("hidden")
  
  if (isHidden) {
    // Mostrar menú
    menu.classList.remove("hidden")
    setTimeout(() => {
      menu.classList.add("show")
    }, 50)
    hamburgerIcon?.classList.add("active")
    document.body.style.overflow = "hidden"
    console.log("Menu shown")
  } else {
    // Ocultar menú
    menu.classList.remove("show")
    hamburgerIcon?.classList.remove("active")
    document.body.style.overflow = ""
    setTimeout(() => {
      menu.classList.add("hidden")
    }, 300)
    console.log("Menu hidden")
  }
}

// Smooth scrolling for anchor links
document.addEventListener("DOMContentLoaded", () => {
  // Smooth scrolling for internal anchors
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      e.preventDefault()
      const target = document.querySelector(this.getAttribute("href"))
      if (target) {
        target.scrollIntoView({
          behavior: "smooth",
          block: "start",
        })
      }
    })
  })

  // Close mobile menu when clicking on links
  document.querySelectorAll(".mobile-nav-link").forEach((link) => {
    link.addEventListener("click", () => {
      const menu = document.getElementById("mobile-menu")
      const button = document.getElementById("mobile-menu-button")
      const hamburgerIcon = button.querySelector(".hamburger-icon")
      
      menu.classList.remove("show")
      if (hamburgerIcon) hamburgerIcon.classList.remove("active")
      document.body.style.overflow = ""
      setTimeout(() => {
        menu.classList.add("hidden")
      }, 300)
    })
  })

  // Close mobile menu when clicking outside
  document.addEventListener("click", (e) => {
    const menu = document.getElementById("mobile-menu")
    const button = document.getElementById("mobile-menu-button")
    
    if (!menu.contains(e.target) && !button.contains(e.target) && !menu.classList.contains("hidden")) {
      toggleMobileMenu()
    }
  })

  // Close mobile menu on escape key
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      const menu = document.getElementById("mobile-menu")
      if (!menu.classList.contains("hidden")) {
        toggleMobileMenu()
      }
    }
  })

  // Initialize scroll animations
  initScrollAnimations()

  // Initialize audio players
  initAudioPlayers()

  // Initialize scroll to top button
  initScrollToTop()

  // Initialize contact form
  initContactForm()

  // Initialize hero background slider
  // Important: URLs here are resolved relative to the CSS file that contains the url(),
  // so use paths relative to assets/css/style.css (hence ../images/...)
  initHeroBackgroundSlider(["../images/morrazo-beach.png"], 15000)

  // Initialize language switch handler
  handleLanguageSwitch()
})

// Scroll Animations
function initScrollAnimations() {
  const observerOptions = {
    threshold: 0.1,
    rootMargin: "0px 0px -50px 0px",
  }

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add("fade-in")
      }
    })
  }, observerOptions)

  // Observe sections for animations
  document.querySelectorAll("section, .card-hover").forEach((element) => {
    observer.observe(element)
  })
}

// Audio Player Functionality
function initAudioPlayers() {
  const playButtons = document.querySelectorAll(".play-btn")

  playButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const isPlaying = this.classList.contains("playing")

      // Stop all other players
      playButtons.forEach((btn) => {
        btn.classList.remove("playing")
        btn.innerHTML = '<i class="fas fa-play mr-2"></i>Reproducir'
      })

      if (!isPlaying) {
        this.classList.add("playing")
        this.innerHTML = '<i class="fas fa-pause mr-2"></i>Pausar'
        showNotification("Reproduciendo audio...")
      }
    })
  })
}

// Scroll to Top Button
function initScrollToTop() {
  const scrollTopBtn = document.createElement("button")
  scrollTopBtn.className = "scroll-top"
  scrollTopBtn.innerHTML = '<i class="fas fa-arrow-up"></i>'
  scrollTopBtn.setAttribute("aria-label", "Volver arriba")
  document.body.appendChild(scrollTopBtn)

  window.addEventListener("scroll", () => {
    if (window.pageYOffset > 300) {
      scrollTopBtn.classList.add("visible")
    } else {
      scrollTopBtn.classList.remove("visible")
    }
  })

  scrollTopBtn.addEventListener("click", () => {
    window.scrollTo({
      top: 0,
      behavior: "smooth",
    })
  })
}

// Contact Form Handling
function initContactForm() {
  const contactForm = document.getElementById("contact-form")
  if (contactForm) {
    contactForm.addEventListener("submit", function (e) {
      e.preventDefault()

      const formData = new FormData(this)
      const submitBtn = this.querySelector('button[type="submit"]')
      const originalText = submitBtn.innerHTML

      // Show loading state
      submitBtn.innerHTML = '<span class="loading"></span> Enviando...'
      submitBtn.disabled = true

      // Simulate form submission (replace with actual AJAX call)
      setTimeout(() => {
        showNotification("¡Mensaje enviado correctamente!", "success")
        this.reset()
        submitBtn.innerHTML = originalText
        submitBtn.disabled = false
      }, 2000)
    })
  }
}

// Category Filter for Podcasts/Audiobooks
function filterByCategory(category) {
  const items = document.querySelectorAll(".media-item")
  const buttons = document.querySelectorAll(".category-btn")

  // Update active button
  buttons.forEach((btn) => btn.classList.remove("active"))
  event.target.classList.add("active")

  // Filter items
  items.forEach((item) => {
    if (category === "all" || item.dataset.category === category) {
      item.style.display = "block"
      item.classList.add("fade-in")
    } else {
      item.style.display = "none"
    }
  })
}

// Notification System
function showNotification(message, type = "info") {
  const notification = document.createElement("div")
  notification.className = `notification ${type}`
  notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-${type === "success" ? "check" : "info"}-circle mr-2"></i>
            <span>${message}</span>
        </div>
    `

  document.body.appendChild(notification)

  // Show notification
  setTimeout(() => {
    notification.classList.add("show")
  }, 100)

  // Hide notification after 3 seconds
  setTimeout(() => {
    notification.classList.remove("show")
    setTimeout(() => {
      document.body.removeChild(notification)
    }, 300)
  }, 3000)
}

// Live Radio Player
class RadioPlayer {
  constructor() {
    this.isPlaying = false
    this.currentTrack = "Radio Rías - En Vivo"
    this.init()
  }

  init() {
    this.createPlayer()
    this.bindEvents()
  }

  createPlayer() {
    const playerHTML = `
            <div class="audio-player fixed bottom-4 right-4 max-w-sm z-50" id="radio-player" style="display: none;">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-red-500 rounded-full animate-pulse mr-2"></div>
                        <span class="text-sm font-semibold">EN VIVO</span>
                    </div>
                    <button onclick="radioPlayer.close()" class="text-white hover:text-red-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="text-center mb-4">
                    <p class="font-semibold">${this.currentTrack}</p>
                </div>
                <div class="audio-controls flex items-center justify-center space-x-4">
                    <button onclick="radioPlayer.previous()" class="w-10 h-10 rounded-full flex items-center justify-center">
                        <i class="fas fa-backward"></i>
                    </button>
                    <button onclick="radioPlayer.toggle()" class="w-12 h-12 rounded-full flex items-center justify-center" id="play-toggle">
                        <i class="fas fa-play ml-1"></i>
                    </button>
                    <button onclick="radioPlayer.next()" class="w-10 h-10 rounded-full flex items-center justify-center">
                        <i class="fas fa-forward"></i>
                    </button>
                </div>
                <div class="progress-bar mt-4">
                    <div class="progress-fill" style="width: 0%"></div>
                </div>
            </div>
        `

    document.body.insertAdjacentHTML("beforeend", playerHTML)
  }

  bindEvents() {
    // Listen for play button clicks
    document.querySelectorAll('[data-action="play-live"]').forEach((btn) => {
      btn.addEventListener("click", () => this.show())
    })
  }

  show() {
    document.getElementById("radio-player").style.display = "block"
    this.play()
  }

  close() {
    document.getElementById("radio-player").style.display = "none"
    this.pause()
  }

  toggle() {
    if (this.isPlaying) {
      this.pause()
    } else {
      this.play()
    }
  }

  play() {
    this.isPlaying = true
    document.getElementById("play-toggle").innerHTML = '<i class="fas fa-pause"></i>'
    showNotification("Reproduciendo Radio Rías en vivo")
    this.startProgress()
  }

  pause() {
    this.isPlaying = false
    document.getElementById("play-toggle").innerHTML = '<i class="fas fa-play ml-1"></i>'
  }

  previous() {
    showNotification("Función no disponible en transmisión en vivo")
  }

  next() {
    showNotification("Función no disponible en transmisión en vivo")
  }

  startProgress() {
    if (this.isPlaying) {
      const progressFill = document.querySelector(".progress-fill")
      let width = 0
      const interval = setInterval(() => {
        if (!this.isPlaying) {
          clearInterval(interval)
          return
        }
        width += 0.1
        if (width > 100) width = 0
        progressFill.style.width = width + "%"
      }, 100)
    }
  }
}

// Initialize Radio Player
let radioPlayer
document.addEventListener("DOMContentLoaded", () => {
  radioPlayer = new RadioPlayer()
})

// Search Functionality
function initSearch() {
  const searchInput = document.getElementById("search-input")
  if (searchInput) {
    searchInput.addEventListener("input", function () {
      const query = this.value.toLowerCase()
      const items = document.querySelectorAll(".searchable-item")

      items.forEach((item) => {
        const text = item.textContent.toLowerCase()
        if (text.includes(query)) {
          item.style.display = "block"
        } else {
          item.style.display = "none"
        }
      })
    })
  }
}

// Lazy Loading for Images
function initLazyLoading() {
  const images = document.querySelectorAll("img[data-src]")

  const imageObserver = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        const img = entry.target
        img.src = img.dataset.src
        img.classList.remove("lazy")
        imageObserver.unobserve(img)
      }
    })
  })

  images.forEach((img) => imageObserver.observe(img))
}

// Theme Toggle (for future dark mode)
function toggleTheme() {
  document.body.classList.toggle("dark-theme")
  const isDark = document.body.classList.contains("dark-theme")
  localStorage.setItem("theme", isDark ? "dark" : "light")
}

// Load saved theme
document.addEventListener("DOMContentLoaded", () => {
  const savedTheme = localStorage.getItem("theme")
  if (savedTheme === "dark") {
    document.body.classList.add("dark-theme")
  }
})

// Utility Functions
function formatTime(seconds) {
  const minutes = Math.floor(seconds / 60)
  const remainingSeconds = Math.floor(seconds % 60)
  return `${minutes}:${remainingSeconds.toString().padStart(2, "0")}`
}

function debounce(func, wait) {
  let timeout
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout)
      func(...args)
    }
    clearTimeout(timeout)
    timeout = setTimeout(later, wait)
  }
}

// Language Switch Handler
function handleLanguageSwitch() {
  const languageLinks = document.querySelectorAll('.language-toggle a');
  
  languageLinks.forEach(link => {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      
      // Show loading indicator
      const currentText = this.textContent;
      this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
      
      // Get the URL from href
      const url = this.getAttribute('href');
      
      // Navigate after short delay for visual feedback
      setTimeout(() => {
        window.location.href = url;
      }, 300);
    });
  });
}

// Export functions for global use
window.toggleMobileMenu = toggleMobileMenu
window.filterByCategory = filterByCategory
window.showNotification = showNotification
window.toggleTheme = toggleTheme
window.handleLanguageSwitch = handleLanguageSwitch

// Hero background slider
function initHeroBackgroundSlider(imageUrls, intervalMs = 7000) {
  const hero = document.querySelector(".hero-bg")
  if (!hero || !Array.isArray(imageUrls) || imageUrls.length === 0) return

  // Preload images
  imageUrls.forEach((src) => {
    const img = new Image()
    img.src = src
  })

  let index = 0
  const applySlide = () => {
    hero.style.setProperty("--hero-image", `url('${imageUrls[index]}')`)
    hero.classList.add("slider-active")
    setTimeout(() => hero.classList.remove("slider-active"), 900)
  }

  applySlide()
  setInterval(() => {
    index = (index + 1) % imageUrls.length
    applySlide()
  }, intervalMs)
}
