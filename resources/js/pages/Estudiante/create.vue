<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, usePage, Link } from '@inertiajs/vue3';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import { Upload, ArrowLeft, FileText, X } from 'lucide-vue-next';
import { route } from 'ziggy-js';

const page = usePage();
// @ts-ignore
const carreras = (page.props.carreras || []) as Array<{ id: number; nombre: string }>;
// @ts-ignore
const user = page.props.auth.user;

const form = useForm({
    titulo: '',
    resumen: '',
    carrera_id: user?.carrera_id || '',
    archivo: null as File | null,
});

const submit = () => {
    console.log("🚀 Iniciando envío de formulario...");
    console.log("Datos a enviar:", {
        titulo: form.titulo,
        resumen: form.resumen,
        carrera: form.carrera_id,
        archivo: form.archivo
    });

    form.post(route('mis-tesis.store'), {
        forceFormData: true, // Vital para archivos
        onBefore: () => {
            // Confirmamos que la petición va a salir
            console.log("📡 Conectando con el servidor...");
        },
        onSuccess: () => {
            console.log("✅ ¡Éxito! El servidor aceptó la tesis.");
            form.reset();
        },
        onError: (errors) => {
            console.error("❌ Error de Validación Backend:", errors);
            alert("El servidor rechazó la solicitud. Revisa la consola para ver los errores.");
        },
        onFinish: () => {
            console.log("🏁 Proceso finalizado.");
        }
    });
};

// Validacion de archivo en frontend
const handleFileChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    const file = target.files?.[0];

    form.clearErrors('archivo');

    if (file) {
        // Validar PDF
        if (file.type !== 'application/pdf') {
            alert('Solo se permiten archivos PDF.');
            target.value = '';
            form.archivo = null;
            return;
        }
        // Validar Tamaño (10MB)
        if (file.size > 10 * 1024 * 1024) {
            alert('El archivo es demasiado pesado (Máx 10MB).');
            target.value = '';
            form.archivo = null;
            return;
        }
        form.archivo = file;
        console.log("📂 Archivo cargado en memoria:", file.name);
    }
};

const removeFile = () => {
    form.archivo = null;
    const input = document.getElementById('archivo') as HTMLInputElement;
    if (input) input.value = '';
};
</script>

<template>
    <Head title="Nueva Entrega" />

    <AppLayout :breadcrumbs="[{ title: 'Mis Proyectos', href: '/mis-tesis' }, { title: 'Nueva Entrega', href: '#' }]">
        <div class="max-w-2xl mx-auto p-4">

            <div class="mb-6">
                <Link :href="route('mis-tesis.index')" class="flex items-center text-sm text-muted-foreground hover:text-primary mb-2">
                    <ArrowLeft class="mr-1 h-4 w-4" /> Volver al listado
                </Link>
                <h1 class="text-2xl font-bold">Subir Nuevo Proyecto</h1>
                <p class="text-muted-foreground">Completa los datos de tu investigación.</p>
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
                    <Label for="archivo">Documento PDF (Máx 10MB)</Label>

                    <div
                        v-if="!form.archivo"
                        class="border-2 border-dashed rounded-lg p-6 flex flex-col items-center justify-center text-center hover:bg-muted/50 transition-colors cursor-pointer relative bg-background/50"
                        :class="{ 'border-red-500 bg-red-50 dark:bg-red-900/10': form.errors.archivo }"
                    >
                        <input
                            type="file"
                            id="archivo"
                            @change="handleFileChange"
                            accept=".pdf"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                            required
                        />
                        <Upload class="h-8 w-8 text-muted-foreground mb-2" />
                        <p class="text-sm font-medium">Haz clic o arrastra tu PDF aquí</p>
                        <p class="text-xs text-muted-foreground mt-1">Solo formato PDF</p>
                    </div>

                    <div v-else class="flex items-center justify-between p-3 border rounded-lg bg-muted/30">
                        <div class="flex items-center gap-3 overflow-hidden">
                            <div class="h-10 w-10 rounded bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0">
                                <FileText class="h-6 w-6" />
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-medium truncate">{{ form.archivo.name }}</p>
                                <p class="text-xs text-muted-foreground">{{ (form.archivo.size / 1024 / 1024).toFixed(2) }} MB</p>
                            </div>
                        </div>
                        <Button type="button" variant="ghost" size="icon" @click="removeFile">
                            <X class="h-4 w-4 text-muted-foreground hover:text-destructive" />
                        </Button>
                    </div>

                    <div v-if="form.progress" class="w-full bg-secondary rounded-full h-2.5 mt-2 overflow-hidden">
                        <div class="bg-primary h-2.5 rounded-full transition-all duration-300" :style="{ width: form.progress.percentage + '%' }"></div>
                    </div>

                    <InputError :message="form.errors.archivo" />
                </div>

                <!-- Acciones -->
                <div class="pt-4 flex justify-end">
                    <Button type="submit" :disabled="form.processing">
                        <span v-if="form.processing" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Subiendo...
                        </span>
                        <span v-else>Guardar Entrega</span>
                    </Button>
                </div>

            </form>
        </div>
    </AppLayout>
</template>
