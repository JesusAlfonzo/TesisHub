<script setup lang="ts">
import { reactive, watch, onMounted, onUnmounted, ref } from 'vue';
import { Button } from '@/components/ui/button';
import { 
    Accessibility, 
    Type, 
    Sun, 
    Ear, 
    RefreshCcw, 
    X,
    MousePointerClick
} from 'lucide-vue-next';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';

// --- 1. ESTADO DE CONFIGURACIÓN (Con Persistencia) ---
const config = reactive({
    fontSize: 100,
    highContrast: false,
    dyslexicFont: false,
    textToSpeech: false
});

const isOpen = ref(false);
const synth = window.speechSynthesis;

// Cargar configuración guardada al iniciar
onMounted(() => {
    const savedConfig = localStorage.getItem('access-config');
    if (savedConfig) {
        const parsed = JSON.parse(savedConfig);
        Object.assign(config, parsed);
    }
    // Aplicar configuraciones iniciales
    applyVisualChanges();
    setupSpeechListeners();
});

// Guardar cambios automáticamente
watch(config, (newVal) => {
    localStorage.setItem('access-config', JSON.stringify(newVal));
    applyVisualChanges();
    setupSpeechListeners();
}, { deep: true });

// --- 2. LÓGICA VISUAL ---
const applyVisualChanges = () => {
    // Fuente
    document.documentElement.style.fontSize = `${config.fontSize}%`;
    
    // Contraste
    if (config.highContrast) document.documentElement.classList.add('high-contrast');
    else document.documentElement.classList.remove('high-contrast');
    
    // Dislexia
    if (config.dyslexicFont) document.documentElement.classList.add('font-dyslexic');
    else document.documentElement.classList.remove('font-dyslexic');
};

// --- 3. LÓGICA DE VOZ (MEJORADA: TAB + CLICK + TOUCH) ---

// Función para hablar
const speakText = (text: string | null | undefined) => {
    if (!config.textToSpeech || !text || text.trim() === '') return;
    
    // Cancelar audio anterior para que no se solapen
    synth.cancel();

    const utterance = new SpeechSynthesisUtterance(text);
    utterance.lang = 'es-ES'; // Forzar español
    utterance.rate = 1.1; // Un poco más dinámico
    synth.speak(utterance);
};

// Manejador de eventos (Foco y Click)
const handleInteraction = (event: Event) => {
    if (!config.textToSpeech) return;

    const target = event.target as HTMLElement;
    
    // Buscar texto significativo: aria-label -> alt -> innerText
    // Esto es clave para botones que solo tienen iconos
    let textToRead = target.getAttribute('aria-label') || 
                     target.getAttribute('alt') || 
                     target.innerText || 
                     (target as HTMLInputElement).placeholder ||
                     (target as HTMLInputElement).value;

    // Evitar leer todo el body si se hace click en el fondo
    if (target.tagName === 'BODY' || target.tagName === 'HTML' || target.tagName === 'MAIN') return;

    speakText(textToRead);
};

// Configurar Listeners
const setupSpeechListeners = () => {
    // Limpiamos primero para no duplicar
    document.removeEventListener('focusin', handleInteraction); // Para el TAB
    document.removeEventListener('click', handleInteraction);   // Para Click/Touch

    if (config.textToSpeech) {
        // 'focusin' detecta cuando un elemento recibe el foco (Tab)
        document.addEventListener('focusin', handleInteraction);
        // 'click' detecta el toque en móvil o click de ratón
        document.addEventListener('click', handleInteraction);
    }
};

// Limpiar al salir
onUnmounted(() => {
    document.removeEventListener('focusin', handleInteraction);
    document.removeEventListener('click', handleInteraction);
    synth.cancel();
});

const resetSettings = () => {
    config.fontSize = 100;
    config.highContrast = false;
    config.dyslexicFont = false;
    config.textToSpeech = false;
};
</script>

<template>
    <div class="fixed bottom-6 right-6 z-50 print:hidden flex flex-col items-end gap-2">
        
        <div v-if="isOpen" class="w-72 bg-background border rounded-lg shadow-xl p-4 animate-in slide-in-from-bottom-5 fade-in duration-300 mb-2">
            <div class="flex items-center justify-between mb-4">
                <h4 class="font-semibold text-sm">Herramientas de Accesibilidad</h4>
                <div class="flex gap-2">
                    <Button variant="ghost" size="sm" class="h-6 px-2 text-xs" @click="resetSettings">
                        <RefreshCcw class="mr-1 h-3 w-3" /> Reset
                    </Button>
                    <Button variant="ghost" size="sm" class="h-6 w-6 p-0" @click="isOpen = false">
                        <X class="h-4 w-4" />
                    </Button>
                </div>
            </div>
            
            <Separator class="my-4" />

            <div class="space-y-3 mb-4">
                <div class="flex items-center gap-2">
                    <Type class="h-4 w-4 text-muted-foreground" />
                    <Label class="text-sm font-medium">Tamaño de Texto</Label>
                </div>
                <div class="flex items-center justify-between gap-2 bg-muted p-1 rounded-lg">
                    <Button variant="ghost" size="sm" @click="config.fontSize = Math.max(80, config.fontSize - 10)">A-</Button>
                    <span class="text-xs font-mono w-12 text-center">{{ config.fontSize }}%</span>
                    <Button variant="ghost" size="sm" @click="config.fontSize = Math.min(150, config.fontSize + 10)">A+</Button>
                </div>
            </div>

            <Separator class="my-4" />

            <div class="space-y-4">
                
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <Sun class="h-4 w-4 text-muted-foreground" />
                        <Label class="text-sm cursor-pointer" @click="config.highContrast = !config.highContrast">Alto Contraste</Label>
                    </div>
                    <button 
                        class="w-9 h-5 rounded-full transition-colors relative focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                        :class="config.highContrast ? 'bg-primary' : 'bg-input'"
                        @click="config.highContrast = !config.highContrast"
                        aria-label="Alternar alto contraste"
                    >
                        <span 
                            class="absolute top-0.5 left-0.5 w-4 h-4 bg-background rounded-full transition-transform shadow-sm"
                            :class="config.highContrast ? 'translate-x-4' : 'translate-x-0'"
                        />
                    </button>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <Type class="h-4 w-4 text-muted-foreground" />
                        <Label class="text-sm cursor-pointer" @click="config.dyslexicFont = !config.dyslexicFont">Fuente Legible</Label>
                    </div>
                    <button 
                        class="w-9 h-5 rounded-full transition-colors relative focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                        :class="config.dyslexicFont ? 'bg-primary' : 'bg-input'"
                        @click="config.dyslexicFont = !config.dyslexicFont"
                        aria-label="Alternar fuente legible"
                    >
                        <span 
                            class="absolute top-0.5 left-0.5 w-4 h-4 bg-background rounded-full transition-transform shadow-sm"
                            :class="config.dyslexicFont ? 'translate-x-4' : 'translate-x-0'"
                        />
                    </button>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <Ear class="h-4 w-4 text-muted-foreground" />
                        <div class="flex flex-col cursor-pointer" @click="config.textToSpeech = !config.textToSpeech">
                            <Label class="text-sm cursor-pointer">Modo Lectura (Voz)</Label>
                            <span class="text-[10px] text-muted-foreground">Lee al hacer click o usar Tab</span>
                        </div>
                    </div>
                    <button 
                        class="w-9 h-5 rounded-full transition-colors relative focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                        :class="config.textToSpeech ? 'bg-primary' : 'bg-input'"
                        @click="config.textToSpeech = !config.textToSpeech"
                        aria-label="Alternar lectura de texto"
                    >
                        <span 
                            class="absolute top-0.5 left-0.5 w-4 h-4 bg-background rounded-full transition-transform shadow-sm"
                            :class="config.textToSpeech ? 'translate-x-4' : 'translate-x-0'"
                        />
                    </button>
                </div>
            </div>
        </div>

        <Button 
            size="icon" 
            class="h-14 w-14 rounded-full shadow-2xl bg-blue-600 hover:bg-blue-700 text-white transition-all hover:scale-110 focus:ring-offset-2 border-4 border-white dark:border-gray-800"
            @click="isOpen = !isOpen"
            aria-label="Abrir menú de accesibilidad"
        >
            <Accessibility class="h-8 w-8" v-if="!isOpen" />
            <X class="h-8 w-8" v-else />
        </Button>
    </div>
</template>

<style>
/* ESTILOS GLOBALES DE ACCESIBILIDAD 
  Se aplican a toda la app cuando se activan las clases en <html>
*/

/* Alto Contraste */
html.high-contrast {
    filter: contrast(130%) brightness(110%);
}
html.high-contrast img, 
html.high-contrast video {
    filter: none; /* Protege imágenes para que no se vean extrañas */
}
html.high-contrast .bg-card {
    border: 2px solid white !important; /* Bordes más visibles */
}

/* Fuente Disléxica (Simulada para mayor legibilidad) */
html.font-dyslexic body {
    font-family: 'Comic Sans MS', 'Chalkboard SE', 'Comic Neue', sans-serif !important;
    line-height: 1.8 !important;
    letter-spacing: 0.05em !important;
    word-spacing: 0.1em !important;
}

/* Indicador de Foco más visible cuando el modo lectura está activo */
html:has(button[aria-label="Alternar lectura de texto"][class*="bg-primary"]) *:focus-visible {
    outline: 4px solid #F59E0B !important; /* Naranja fuerte */
    outline-offset: 2px !important;
}
</style>