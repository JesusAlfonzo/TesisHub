<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm, usePage, Link } from '@inertiajs/vue3';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import { Upload, ArrowLeft, FileText, AlertTriangle, ChevronDown, ExternalLink } from 'lucide-vue-next';
import { route } from 'ziggy-js';
import { ref } from 'vue';

// --- PROPS ---
const props = defineProps<{
    tesis: {
        id: number;
        titulo: string;
        resumen: string;
        carrera_id: number;
        ruta_archivo: string;
        estado: 'pendiente' | 'aprobado' | 'rechazado';
    };
}>();

// Obtener carreras globales
const page = usePage();
// @ts-ignore
const carreras = (page.props.carreras || []) as Array<{ id: number; nombre: string }>;
const fileInputRef = ref<HTMLInputElement | null>(null);

// --- FORMULARIO PRECARGADO ---
const form = useForm({
    titulo: props.tesis.titulo,
    resumen: props.tesis.resumen,
    carrera_id: props.tesis.carrera_id,
    archivo: null as File | null,
    _method: 'PUT',
});

const submit = () => {
    form.post(route('mis-tesis.update', props.tesis.id), {
        forceFormData: true,
        onError: () => {
             // Foco al primer error encontrado para accesibilidad (Mejora UX)
             const firstError = document.querySelector('[aria-invalid="true"]') as HTMLElement;
             if (firstError) firstError.focus();
        }
    });
};

// --- Validacion de archivo en frontend (Principio Comprensible) ---
const handleFileChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    const file = target.files?.[0];

    form.clearErrors('archivo');

    if (file) {
        if (file.type !== 'application/pdf') {
            form.setError('archivo', 'El formato debe ser PDF.');
            target.value = '';
            form.archivo = null;
            return;
        }
        if (file.size > 10 * 1024 * 1024) {
            form.setError('archivo', 'El archivo excede los 10MB permitidos.');
            target.value = '';
            form.archivo = null;
            return;
        }
        form.archivo = file;
    }
};
</script>

<template>
    <Head :title="`Editar: ${tesis.titulo}`" />

    <AppLayout :breadcrumbs="[{ title: 'Mis Proyectos', href: route('mis-tesis.index') }, { title: 'Editar', href: '#' }]">
        
        <main class="max-w-2xl mx-auto p-4" id="main-content">

            <div class="mb-6">
                <Link 
                    :href="route('mis-tesis.index')" 
                    class="flex items-center text-sm text-muted-foreground hover:text-primary mb-2 focus-visible:ring-2 focus-visible:ring-ring rounded-md p-1"
                    aria-label="Volver al listado de proyectos"
                >
                    <ArrowLeft class="mr-1 h-4 w-4" aria-hidden="true" /> 
                    Volver al listado
                </Link>
                <h1 class="text-2xl font-bold text-foreground">Editar Proyecto</h1>
                <p class="text-muted-foreground">Actualiza la información y sube una nueva versión si es necesario.</p>
            </div>

            <div 
                v-if="tesis.estado === 'rechazado'" 
                role="alert"
                class="mb-6 p-4 rounded-xl border border-red-400 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300"
            >
                <div class="flex items-center">
                    <AlertTriangle class="h-5 w-5 mr-3 flex-shrink-0" aria-hidden="true" />
                    <p class="text-sm font-medium">
                        Este proyecto fue <strong>RECHAZADO</strong>. Al guardar, se enviará la nueva versión a revisión.
                    </p>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-6 bg-card p-6 rounded-xl border shadow-sm" novalidate>

                <div class="space-y-2">
                    <Label for="titulo">Título del Proyecto <span class="text-destructive" aria-hidden="true">*</span></Label>
                    <Input 
                        id="titulo" 
                        v-model="form.titulo" 
                        placeholder="Ej: Sistema de Gestión para..." 
                        required 
                        :aria-invalid="!!form.errors.titulo"
                        aria-describedby="titulo-error"
                    />
                    <InputError id="titulo-error" :message="form.errors.titulo" />
                </div>

                <div class="space-y-2">
                    <Label for="carrera">Carrera <span class="text-destructive" aria-hidden="true">*</span></Label>
                    <div class="relative">
                         <select
                            id="carrera"
                            v-model="form.carrera_id"
                            class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 appearance-none text-foreground"
                            required
                            :aria-invalid="!!form.errors.carrera_id"
                            aria-describedby="carrera-error"
                        >
                            <option value="" disabled>Selecciona la carrera</option>
                            <option v-for="carrera in carreras" :key="carrera.id" :value="carrera.id">
                                {{ carrera.nombre }}
                            </option>
                        </select>
                        <span class="absolute right-3 top-3 pointer-events-none opacity-50">
                            <ChevronDown class="h-4 w-4" aria-hidden="true" />
                        </span>
                    </div>
                    <InputError id="carrera-error" :message="form.errors.carrera_id" />
                </div>

                <div class="space-y-2">
                    <Label for="resumen">Resumen / Abstract <span class="text-destructive" aria-hidden="true">*</span></Label>
                    <textarea
                        id="resumen"
                        v-model="form.resumen"
                        class="flex min-h-[120px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 text-foreground"
                        rows="5"
                        placeholder="Describe brevemente el objetivo de tu tesis..."
                        required
                        :aria-invalid="!!form.errors.resumen"
                        aria-describedby="resumen-error"
                    ></textarea>
                    <InputError id="resumen-error" :message="form.errors.resumen" />
                </div>

                <div class="space-y-2">
                    <Label id="archivo-label" for="archivo">Documento PDF</Label>

                    <div class="flex items-center justify-between p-3 border rounded-lg bg-yellow-50 dark:bg-yellow-900/10 border-yellow-300 mb-4">
                        <div class="flex items-center gap-3 overflow-hidden">
                             <div class="h-10 w-10 rounded bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0" aria-hidden="true">
                                <FileText class="h-6 w-6" />
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-medium truncate text-yellow-900 dark:text-yellow-100">
                                    Archivo actual: {{ tesis.ruta_archivo.split('/').pop() }}
                                </p>
                                <p class="text-xs text-yellow-700 dark:text-yellow-300">
                                    Si no subes uno nuevo, se mantendrá este.
                                </p>
                            </div>
                        </div>
                         <a 
                            :href="route('tesis.ver', tesis.id)" 
                            target="_blank"
                            class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-9 px-3"
                            aria-label="Ver archivo actual en nueva pestaña"
                        >
                             <span class="mr-2 text-blue-600 dark:text-blue-400">Ver</span>
                             <ExternalLink class="h-4 w-4 text-blue-600 dark:text-blue-400" aria-hidden="true"/>
                         </a>
                    </div>

                    <div
                        class="relative border-2 border-dashed rounded-lg p-6 flex flex-col items-center justify-center text-center hover:bg-muted/50 transition-colors bg-background/50 focus-within:ring-2 focus-within:ring-ring focus-within:ring-offset-2"
                        :class="{ 'border-destructive bg-destructive/10': form.errors.archivo }"
                    >
                        <input
                            ref="fileInputRef"
                            type="file"
                            id="archivo"
                            @change="handleFileChange"
                            accept=".pdf"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                            aria-labelledby="archivo-label"
                            aria-describedby="archivo-constraints archivo-error"
                        />
                        <Upload class="h-8 w-8 text-muted-foreground mb-2" aria-hidden="true" />
                        
                        <div aria-live="polite">
                            <p class="text-sm font-medium" v-if="!form.archivo">
                                Haz clic o arrastra para <span class="font-bold text-primary">reemplazar</span> el archivo
                            </p>
                            <p class="text-sm font-medium text-green-600 dark:text-green-400" v-else>
                                Nuevo archivo seleccionado: {{ form.archivo.name }}
                            </p>
                        </div>
                        
                        <p id="archivo-constraints" class="text-xs text-muted-foreground mt-1">
                            Solo formato PDF, máximo 10 MB (Opcional)
                        </p>
                    </div>

                    <div v-if="form.progress" 
                         role="progressbar" 
                         :aria-valuenow="form.progress.percentage" 
                         aria-valuemin="0" 
                         aria-valuemax="100"
                         class="w-full bg-secondary rounded-full h-2.5 mt-2 overflow-hidden">
                        <div class="bg-primary h-2.5 rounded-full transition-all duration-300" :style="{ width: form.progress.percentage + '%' }"></div>
                        <span class="sr-only">Subiendo actualización: {{ form.progress.percentage }}%</span>
                    </div>

                    <InputError id="archivo-error" :message="form.errors.archivo" />
                </div>

                <div class="pt-4 flex justify-end">
                    <Button type="submit" :disabled="form.processing">
                        <span v-if="form.processing" class="flex items-center">
                             <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Actualizando...
                        </span>
                        <span v-else>Guardar Cambios</span>
                    </Button>
                </div>

            </form>
        </main>
    </AppLayout>
</template>