/**
 * Sistema de Analytics Simplificado para RadioRías
 * Solo rastrea lo que hPanel NO puede rastrear
 * hPanel se encarga automáticamente de: visitantes, páginas, descargas, países, dispositivos
 */

class SimpleRadioAnalytics {
    constructor() {
        this.apiUrl = 'api/radio-counter.php';
        this.heartbeatInterval = null;
        this.sessionStart = null;
        
        console.log('📻 Simple Radio Analytics initialized');
    }
    
    /**
     * Registrar inicio de radio
     */
    async trackRadioPlay() {
        try {
            this.sessionStart = Date.now();
            
            await fetch(this.apiUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'radio_play' })
            });
            
            // Iniciar heartbeat cada 30 segundos
            this.startHeartbeat();
            
            console.log('📻 Radio play tracked');
        } catch (error) {
            console.error('Error tracking radio play:', error);
        }
    }
    
    /**
     * Registrar fin de radio
     */
    async trackRadioStop() {
        try {
            const duration = this.sessionStart ? 
                Math.floor((Date.now() - this.sessionStart) / 1000) : 0;
            
            await fetch(this.apiUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    action: 'radio_stop',
                    duration: duration 
                })
            });
            
            this.stopHeartbeat();
            this.sessionStart = null;
            
            console.log(`📻 Radio stop tracked - Duration: ${duration}s`);
        } catch (error) {
            console.error('Error tracking radio stop:', error);
        }
    }
    
    /**
     * Heartbeat para mantener oyente activo
     */
    startHeartbeat() {
        this.stopHeartbeat(); // Limpiar anterior
        
        this.heartbeatInterval = setInterval(async () => {
            try {
                await fetch(this.apiUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'heartbeat' })
                });
                
                console.log('💓 Radio heartbeat sent');
            } catch (error) {
                console.error('Error sending heartbeat:', error);
            }
        }, 30000); // 30 segundos
    }
    
    /**
     * Detener heartbeat
     */
    stopHeartbeat() {
        if (this.heartbeatInterval) {
            clearInterval(this.heartbeatInterval);
            this.heartbeatInterval = null;
        }
    }
    
    /**
     * Obtener estadísticas de radio
     */
    async getRadioStats() {
        try {
            const response = await fetch(`${this.apiUrl}?action=get_stats`);
            const result = await response.json();
            
            if (result.success) {
                return result.data;
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('Error getting radio stats:', error);
            return {
                today_plays: 0,
                active_listeners: 0,
                avg_duration: 0,
                total_duration: 0,
                unique_listeners_today: 0
            };
        }
    }
    
    /**
     * Formatear duración en segundos
     */
    formatDuration(seconds) {
        if (seconds < 60) {
            return seconds + 's';
        } else if (seconds < 3600) {
            const minutes = Math.floor(seconds / 60);
            const secs = seconds % 60;
            return minutes + 'm ' + secs + 's';
        } else {
            const hours = Math.floor(seconds / 3600);
            const minutes = Math.floor((seconds % 3600) / 60);
            return hours + 'h ' + minutes + 'm';
        }
    }
}

// Inicializar analytics globalmente
window.simpleRadioAnalytics = new SimpleRadioAnalytics();

// Funciones globales simples
window.trackRadioPlay = () => window.simpleRadioAnalytics.trackRadioPlay();
window.trackRadioStop = () => window.simpleRadioAnalytics.trackRadioStop();

// Limpiar al salir de la página
window.addEventListener('beforeunload', () => {
    if (window.simpleRadioAnalytics.sessionStart) {
        // Usar sendBeacon para envío confiable al salir
        navigator.sendBeacon(
            window.simpleRadioAnalytics.apiUrl,
            JSON.stringify({ 
                action: 'radio_stop',
                duration: Math.floor((Date.now() - window.simpleRadioAnalytics.sessionStart) / 1000)
            })
        );
    }
});

console.log('📊 Simple Analytics loaded - hPanel handles the rest!');
