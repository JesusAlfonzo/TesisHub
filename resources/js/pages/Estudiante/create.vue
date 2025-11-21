<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm, usePage, Link } from '@inertiajs/vue3';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea'; // Si no tienes este componente, usa un <textarea> nativo con clases
import InputError from '@/components/InputError.vue';
import { Upload, ArrowLeft } from 'lucide-vue-next';

// Obtener carreras globales
const page = usePage();
// @ts-ignore
const carreras = (page.props.carreras || []) as Array<{ id: number; nombre: string }>;
// @ts-ignore
const user = page.props.auth.user;

const form = useForm({
    titulo: '',
    resumen: '',
    carrera_id: user.carrera_id || '', // Pre-seleccionar si el usuario ya tiene carrera
    archivo: null as File | null,
});

const submit = () => {
    form.post(route('mis-tesis.store'), {
        forceFormData: true, // Importante para subir archivos
    });
};

// Manejador del input file
const handleFileChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        form.archivo = target.files[0];
    }
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
                            class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 appearance-none dark:bg-gray-950"
                        >
                            <option value="" disabled>Selecciona la carrera</option>
                            <option v-for="carrera in carreras" :key="carrera.id" :value="carrera.id" class="dark:bg-gray-900">
                                {{ carrera.nombre }}
                            </option>
                        </select>
                    </div>
                    <InputError :message="form.errors.carrera_id" />
                </div>

                <!-- Resumen -->
                <div class="space-y-2">
                    <Label for="resumen">Resumen / Abstract</Label>
                    <textarea 
                        id="resumen" 
                        v-model="form.resumen" 
                        class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                        rows="5"
                        placeholder="Describe brevemente el objetivo de tu tesis..."
                        required
                    ></textarea>
                    <InputError :message="form.errors.resumen" />
                </div>

                <!-- Archivo PDF -->
                <div class="space-y-2">
                    <Label for="archivo">Documento PDF</Label>
                    <div class="border-2 border-dashed rounded-lg p-6 flex flex-col items-center justify-center text-center hover:bg-muted/50 transition-colors cursor-pointer relative">
                        <input 
                            type="file" 
                            id="archivo" 
                            @change="handleFileChange" 
                            accept=".pdf"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                            required
                        />
                        <Upload class="h-8 w-8 text-muted-foreground mb-2" />
                        <p class="text-sm font-medium" v-if="!form.archivo">Haz clic o arrastra tu PDF aquí</p>
                        <p class="text-sm font-medium text-primary" v-else>Archivo seleccionado: {{ form.archivo.name }}</p>
                        <p class="text-xs text-muted-foreground mt-1">Solo formato PDF (Max 10MB)</p>
                    </div>
                    
                    <!-- Barra de Progreso -->
                    <div v-if="form.progress" class="w-full bg-secondary rounded-full h-2.5 mt-2">
                        <div class="bg-primary h-2.5 rounded-full" :style="{ width: form.progress.percentage + '%' }"></div>
                    </div>
                    
                    <InputError :message="form.errors.archivo" />
                </div>

                <!-- Acciones -->
                <div class="pt-4 flex justify-end">
                    <Button type="submit" :disabled="form.processing">
                        <span v-if="form.processing">Subiendo...</span>
                        <span v-else>Guardar Entrega</span>
                    </Button>
                </div>

            </form>
        </div>
    </AppLayout>
</template>