<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm, usePage, Link } from '@inertiajs/vue3';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import { Upload, ArrowLeft, FileText, X, AlertTriangle } from 'lucide-vue-next';
import { route } from 'ziggy-js';

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

// --- FORMULARIO PRECARGADO ---
const form = useForm({
    // Precargamos los datos actuales de la tesis
    titulo: props.tesis.titulo,
    resumen: props.tesis.resumen,
    carrera_id: props.tesis.carrera_id,
    archivo: null as File | null, // Campo para el nuevo archivo
    _method: 'PUT', // Campo especial para simular la petición PUT/PATCH en Laravel
});

const submit = () => {
    // Usamos el ID de la tesis para enviar la petición PUT
    form.post(route('mis-tesis.update', props.tesis.id), {
        forceFormData: true,
        // Agregamos feedback en caso de error
        onError: (errors) => {
            console.error("Error al actualizar:", errors);
            alert("Error de validación. Revisa los campos obligatorios.");
        }
    });
};

// --- VALIDACIÓN DE ARCHIVO EN FRONTEND ---
const handleFileChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    const file = target.files?.[0];

    form.clearErrors('archivo');

    if (file) {
        if (file.type !== 'application/pdf') {
            alert('Solo se permiten archivos PDF.');
            target.value = '';
            form.archivo = null;
            return;
        }
        if (file.size > 10 * 1024 * 1024) {
            alert('El archivo es demasiado pesado (Máx 10MB).');
            target.value = '';
            form.archivo = null;
            return;
        }
        form.archivo = file;
    }
};

const removeFile = () => {
    form.archivo = null;
    const input = document.getElementById('archivo') as HTMLInputElement;
    if (input) input.value = '';
};
</script>

<template>
    <Head :title="`Editar: ${tesis.titulo}`" />

    <AppLayout :breadcrumbs="[{ title: 'Mis Proyectos', href: route('mis-tesis.index') }, { title: 'Editar', href: '#' }]">
        <div class="max-w-2xl mx-auto p-4">

            <div class="mb-6">
                <Link :href="route('mis-tesis.index')" class="flex items-center text-sm text-muted-foreground hover:text-primary mb-2">
                    <ArrowLeft class="mr-1 h-4 w-4" /> Volver al listado
                </Link>
                <h1 class="text-2xl font-bold">Editar Proyecto</h1>
                <p class="text-muted-foreground">Actualiza la información y sube una nueva versión si es necesario.</p>
            </div>

            <!-- ADVERTENCIA DE ESTADO -->
            <div v-if="tesis.estado === 'rechazado'" class="mb-6 p-4 rounded-xl border border-red-400 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300">
                <div class="flex items-center">
                    <AlertTriangle class="h-5 w-5 mr-3 flex-shrink-0" />
                    <p class="text-sm font-medium">
                        Este proyecto fue **RECHAZADO**. Al guardar, se enviará la nueva versión a revisión.
                    </p>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-6 bg-card p-6 rounded-xl border shadow-sm">

                <!-- Título -->
                <div class="space-y-2">
                    <Label for="titulo">Título del Proyecto</Label>
                    <Input id="titulo" v-model="form.titulo" placeholder="Ej: Sistema de Gestión para..." required />
                    <InputError :message="form.errors.titulo" />
                </div>

                <!-- Carrera -->
                <div class="space-y-2">
                    <Label for="carrera">Carrera</Label>
                    <div class="relative">
                         <select
                            id="carrera"
                            v-model="form.carrera_id"
                            class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 appearance-none dark:bg-gray-950 dark:text-gray-100"
                            required
                        >
                            <option value="" disabled>Selecciona la carrera</option>
                            <option v-for="carrera in carreras" :key="carrera.id" :value="carrera.id" class="dark:bg-gray-900">
                                {{ carrera.nombre }}
                            </option>
                        </select>
                        <span class="absolute right-3 top-3 pointer-events-none opacity-50">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down"><path d="m6 9 6 6 6-6"/></svg>
                        </span>
                    </div>
                    <InputError :message="form.errors.carrera_id" />
                </div>

                <!-- Resumen -->
                <div class="space-y-2">
                    <Label for="resumen">Resumen / Abstract</Label>
                    <textarea
                        id="resumen"
                        v-model="form.resumen"
                        class="flex min-h-[120px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-gray-950"
                        rows="5"
                        placeholder="Describe brevemente el objetivo de tu tesis..."
                        required
                    ></textarea>
                    <InputError :message="form.errors.resumen" />
                </div>

                <!-- Archivo PDF -->
                <div class="space-y-2">
                    <Label for="archivo">Nuevo Documento PDF (Máx 10MB)</Label>

                    <!-- Estado: Archivo Actual -->
                    <div class="flex items-center justify-between p-3 border rounded-lg bg-yellow-50 dark:bg-yellow-900/10 border-yellow-300 mb-2">
                        <div class="flex items-center gap-3 overflow-hidden">
                             <div class="h-10 w-10 rounded bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0">
                                <FileText class="h-6 w-6" />
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-medium truncate">Archivo actual: {{ tesis.ruta_archivo.split('/').pop() }}</p>
                                <p class="text-xs text-muted-foreground">Sube un archivo nuevo para reemplazar.</p>
                            </div>
                        </div>
                         <a :href="route('tesis.ver', tesis.id)" target="_blank">
                             <Button type="button" variant="ghost" size="sm" class="text-blue-600">Ver actual</Button>
                         </a>
                    </div>

                    <!-- Input de Subida (Opcional) -->
                    <div
                        class="border-2 border-dashed rounded-lg p-6 flex flex-col items-center justify-center text-center hover:bg-muted/50 transition-colors cursor-pointer relative bg-background/50"
                        :class="{ 'border-red-500 bg-red-50 dark:bg-red-900/10': form.errors.archivo }"
                    >
                        <input
                            type="file"
                            id="archivo"
                            @change="handleFileChange"
                            accept=".pdf"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                        />
                        <Upload class="h-8 w-8 text-muted-foreground mb-2" />
                        <p class="text-sm font-medium" v-if="!form.archivo">Sube aquí para reemplazar (Opcional)</p>
                        <p class="text-sm font-medium text-primary" v-else>Nuevo archivo seleccionado: {{ form.archivo.name }}</p>
                        <p class="text-xs text-muted-foreground mt-1">Solo formato PDF</p>
                    </div>

                    <!-- Barra de Progreso -->
                    <div v-if="form.progress" class="w-full bg-secondary rounded-full h-2.5 mt-2 overflow-hidden">
                        <div class="bg-primary h-2.5 rounded-full transition-all duration-300" :style="{ width: form.progress.percentage + '%' }"></div>
                    </div>

                    <InputError :message="form.errors.archivo" />
                </div>

                <!-- Acciones -->
                <div class="pt-4 flex justify-end">
                    <Button type="submit" :disabled="form.processing">
                        <span v-if="form.processing">Actualizando...</span>
                        <span v-else>Guardar Cambios</span>
                    </Button>
                </div>

            </form>
        </div>
    </AppLayout>
</template>
