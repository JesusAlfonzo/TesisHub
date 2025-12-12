<script setup lang="ts">
import { onMounted, ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { CheckCircle, AlertCircle, X } from 'lucide-vue-next';

const page = usePage();
const show = ref(false);
const message = ref('');
const type = ref('success');
const title = ref('');

let timeout: ReturnType<typeof setTimeout>;

const showToast = (msg: string, toastType: 'success' | 'error' | 'message' = 'success') => {
    message.value = msg;
    type.value = toastType;
    
    // Asignar título basado en el tipo
    if (toastType === 'success') {
        title.value = '¡Éxito!';
    } else if (toastType === 'error') {
        title.value = 'Error de Operación';
    } else {
        title.value = 'Información';
    }
    
    show.value = true;
    clearTimeout(timeout);
    // Auto-cierre después de 5 segundos
    timeout = setTimeout(() => { show.value = false; }, 5000);
};

// Observar los mensajes flash inyectados por el middleware
watch(() => page.props.flash, (flash: any) => {
    if (flash?.success) showToast(flash.success, 'success');
    else if (flash?.error) showToast(flash.error, 'error');
    // Usamos 'message' como fallback genérico si existe
    else if (flash?.message) showToast(flash.message, 'message');
}, { deep: true });

onMounted(() => {
    // Para el primer render
    // @ts-ignore
    const flash = page.props.flash;
    if (flash?.success) showToast(flash.success, 'success');
    if (flash?.error) showToast(flash.error, 'error');
    if (flash?.message) showToast(flash.message, 'message');
});

const closeToast = () => {
    show.value = false;
    clearTimeout(timeout);
};

// Helpers de estilo con alto contraste (WCAG AA)
const styleClasses = {
    success: 'bg-green-50 border-green-300 text-green-700 dark:bg-green-900 dark:border-green-800 dark:text-green-300',
    error: 'bg-red-50 border-red-300 text-red-700 dark:bg-red-900 dark:border-red-800 dark:text-red-300',
    message: 'bg-blue-50 border-blue-300 text-blue-700 dark:bg-blue-900 dark:border-blue-800 dark:text-blue-300',
    icon: {
        success: 'text-green-500',
        error: 'text-red-500',
        message: 'text-blue-500',
    }
};

</script>

<template>
    <!-- Contenedor principal para la transición y la posición fija -->
    <transition 
        enter-active-class="transform ease-out duration-300 transition" 
        enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2" 
        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0" 
        leave-active-class="transition ease-in duration-100" 
        leave-from-class="opacity-100" 
        leave-to-class="opacity-0"
    >
        <div 
            v-if="show" 
            class="fixed top-4 right-4 z-50 w-full max-w-sm overflow-hidden rounded-xl shadow-lg ring-1 ring-black ring-opacity-5 transition-all"
            :class="[styleClasses[type]]"
            role="alert" 
            aria-live="assertive" 
            tabindex="-1"
        >
            <div class="p-4">
                <div class="flex items-start">
                    
                    <!-- Icono -->
                    <div class="flex-shrink-0">
                        <CheckCircle v-if="type === 'success' || type === 'message'" class="h-6 w-6" :class="styleClasses.icon[type]" aria-hidden="true" />
                        <AlertCircle v-else class="h-6 w-6" :class="styleClasses.icon[type]" aria-hidden="true" />
                    </div>
                    
                    <!-- Contenido del Mensaje -->
                    <div class="ml-3 w-0 flex-1 pt-0.5">
                        <p class="text-sm font-medium">{{ title }}</p>
                        <p class="mt-1 text-sm">{{ message }}</p>
                    </div>
                    
                    <!-- Botón de Cerrar Accesible -->
                    <div class="ml-4 flex flex-shrink-0">
                        <button 
                            type="button"
                            @click="closeToast" 
                            class="inline-flex rounded-md p-1 focus:outline-none focus:ring-2 focus:ring-offset-2"
                            :class="[
                                type === 'success' ? 'text-green-700 focus:ring-green-600' : 
                                type === 'error' ? 'text-red-700 focus:ring-red-600' : 'text-blue-700 focus:ring-blue-600'
                            ]"
                            aria-label="Cerrar notificación"
                        >
                            <X class="h-5 w-5" aria-hidden="true" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>