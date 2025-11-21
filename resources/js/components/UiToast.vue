<script setup lang="ts">
import { onMounted, ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { CheckCircle, AlertCircle, X } from 'lucide-vue-next';

const page = usePage();
const show = ref(false);
const message = ref('');
const type = ref('success'); // 'success' | 'error'

let timeout: ReturnType<typeof setTimeout>;

const showToast = (msg: string, toastType: 'success' | 'error' = 'success') => {
    message.value = msg;
    type.value = toastType;
    show.value = true;

    clearTimeout(timeout);
    timeout = setTimeout(() => {
        show.value = false;
    }, 4000); // Se oculta a los 4 segundos
};

// Escuchar cambios constantes en los mensajes flash de Inertia
watch(() => page.props.flash, (flash: any) => {
    if (flash?.message) {
        showToast(flash.message, 'success');
    } else if (flash?.error) {
        showToast(flash.error, 'error');
    } else if (flash?.success) { // Por si usas 'success' en el backend
        showToast(flash.success, 'success');
    }
}, { deep: true });

// Chequear si ya hay mensaje al cargar la página (ej: redirect después de login)
onMounted(() => {
    // @ts-ignore
    const flash = page.props.flash;
    if (flash?.message) showToast(flash.message, 'success');
    if (flash?.error) showToast(flash.error, 'error');
});
</script>

<template>
    <transition
        enter-active-class="transform ease-out duration-300 transition"
        enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
        leave-active-class="transition ease-in duration-100"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div v-if="show" class="fixed top-4 right-4 z-50 w-full max-w-sm overflow-hidden rounded-xl bg-white shadow-lg ring-1 ring-black ring-opacity-5 dark:bg-gray-800 border dark:border-gray-700">
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <CheckCircle v-if="type === 'success'" class="h-6 w-6 text-green-500" />
                        <AlertCircle v-else class="h-6 w-6 text-red-500" />
                    </div>
                    <div class="ml-3 w-0 flex-1 pt-0.5">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ type === 'success' ? 'Operación Exitosa' : 'Error' }}
                        </p>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            {{ message }}
                        </p>
                    </div>
                    <div class="ml-4 flex flex-shrink-0">
                        <button @click="show = false" type="button" class="inline-flex rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:bg-gray-800 dark:text-gray-500 dark:hover:text-gray-400">
                            <span class="sr-only">Cerrar</span>
                            <X class="h-5 w-5" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>
