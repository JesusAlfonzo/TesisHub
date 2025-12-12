<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, usePage, Link } from '@inertiajs/vue3';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import { Upload, ArrowLeft, FileText, X, ChevronDown } from 'lucide-vue-next';
import { route } from 'ziggy-js';
import { ref } from 'vue';

const page = usePage();
// @ts-ignore
const carreras = (page.props.carreras || []) as Array<{ id: number; nombre: string }>;
// @ts-ignore
const user = page.props.auth.user;
const fileInputRef = ref<HTMLInputElement | null>(null);

const form = useForm({
    titulo: '',
    resumen: '',
    carrera_id: user?.carrera_id || '',
    archivo: null as File | null,
});

const submit = () => {
    form.post(route('mis-tesis.store'), {
        forceFormData: true,
        onSuccess: () => form.reset(),
    });
};

const handleFileChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    const file = target.files?.[0];
    
    // Limpiamos errores previos del campo archivo
    form.clearErrors('archivo');

    if (file) {
        // Validación Front-end accesible (Principio Comprensible)
        if (file.type !== 'application/pdf') {
            form.setError('archivo', 'El formato debe ser PDF.'); // Usamos el sistema de errores del form en vez de alert
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

const removeFile = () => {
    form.archivo = null;
    if (fileInputRef.value) fileInputRef.value.value = '';
    // Devolver el foco al contenedor de carga para no perder al usuario de teclado
    // (Principio Operable - Gestión de foco)
    setTimeout(() => document.getElementById('archivo')?.focus(), 100); 
};
</script>

<template>
    <Head title="Nueva Entrega" />

    <AppLayout :breadcrumbs="[{ title: 'Mis Proyectos', href: '/mis-tesis' }, { title: 'Nueva Entrega', href: '#' }]">
        <main class="max-w-2xl mx-auto p-4" id="main-content">

            <div class="mb-6">
                <Link 
                    :href="route('mis-tesis.index')" 
                    class="flex items-center text-sm text-muted-foreground hover:text-primary mb-2 focus-visible:ring-2 focus-visible:ring-ring rounded-md p-1"
                    aria-label="Volver al listado de tesis"
                >
                    <ArrowLeft class="mr-1 h-4 w-4" aria-hidden="true" /> 
                    Volver al listado
                </Link>
                <h1 class="text-2xl font-bold text-foreground">Subir Nuevo Proyecto</h1>
                <p class="text-muted-foreground">Completa los datos de tu investigación para registrarla en el repositorio.</p>
            </div>

            <form @submit.prevent="submit" class="space-y-6 bg-card p-6 rounded-xl border shadow-sm" novalidate>

                <div class="space-y-2">
                    <Label for="titulo" class="text-foreground">Título del Proyecto <span class="text-destructive" aria-hidden="true">*</span></Label>
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
                            :aria-invalid="!!form.errors.carrera_id"
                            aria-describedby="carrera-error"
                            required
                        >
                            <option value="" disabled>Selecciona la carrera</option>
                            <option v-for="carrera in carreras" :key="carrera.id" :value="carrera.id">
                                {{ carrera.nombre }}
                            </option>
                        </select>
                        <span class="absolute right-3 top-3 pointer-events-none opacity-50">
                            <ChevronDown class="h-4 w-4" aria-hidden="true"/>
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
                    <Label id="archivo-label" for="archivo">Documento PDF (Máx 10MB) <span class="text-destructive" aria-hidden="true">*</span></Label>

                    <div
                        v-if="!form.archivo"
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
                            required
                            aria-labelledby="archivo-label"
                            aria-describedby="archivo-constraints archivo-error"
                        />
                        <Upload class="h-8 w-8 text-muted-foreground mb-2" aria-hidden="true" />
                        <p class="text-sm font-medium">Haz clic o arrastra tu PDF aquí</p>
                        <p id="archivo-constraints" class="text-xs text-muted-foreground mt-1">Solo formato PDF, máximo 10 MB</p>
                    </div>

                    <div v-else class="flex items-center justify-between p-3 border rounded-lg bg-muted/30">
                        <div class="flex items-center gap-3 overflow-hidden">
                            <div class="h-10 w-10 rounded bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0" aria-hidden="true">
                                <FileText class="h-6 w-6" />
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-medium truncate">{{ form.archivo.name }}</p>
                                <p class="text-xs text-muted-foreground">{{ (form.archivo.size / 1024 / 1024).toFixed(2) }} MB</p>
                            </div>
                        </div>
                        <Button 
                            type="button" 
                            variant="ghost" 
                            size="icon" 
                            @click="removeFile"
                            aria-label="Eliminar archivo seleccionado"
                        >
                            <X class="h-4 w-4 text-muted-foreground hover:text-destructive" />
                        </Button>
                    </div>

                    <div v-if="form.progress" 
                         role="progressbar" 
                         :aria-valuenow="form.progress.percentage" 
                         aria-valuemin="0" 
                         aria-valuemax="100"
                         class="w-full bg-secondary rounded-full h-2.5 mt-2 overflow-hidden">
                        <div class="bg-primary h-2.5 rounded-full transition-all duration-300" :style="{ width: form.progress.percentage + '%' }"></div>
                        <span class="sr-only">Subiendo: {{ form.progress.percentage }}%</span>
                    </div>

                    <InputError id="archivo-error" :message="form.errors.archivo" />
                </div>

                <div class="pt-4 flex justify-end">
                    <Button type="submit" :disabled="form.processing">
                        <span v-if="form.processing" class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" aria-hidden="true" class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"/><path fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12zm2 5.291A7.96 7.96 0 0 1 4 12H0c0 3.042 1.135 5.824 3 7.938z" class="opacity-75"/></svg>
                            Subiendo...
                        </span>
                        <span v-else>Guardar Entrega</span>
                    </Button>
                </div>

            </form>
        </main>
    </AppLayout>
</template>