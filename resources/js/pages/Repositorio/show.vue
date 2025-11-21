<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { FileText, User, GraduationCap, Calendar, Download } from 'lucide-vue-next';
import { route } from 'ziggy-js';

const props = defineProps<{
    tesis: {
        id: number;
        titulo: string;
        resumen: string;
        ruta_archivo: string;
        created_at: string;
        autor: { name: string };
        carrera: { nombre: string };
    };
}>();

const breadcrumbs = [
    { title: 'Repositorio', href: route('tesis.index') },
    { title: props.tesis.titulo, href: '#' },
];
</script>

<template>
    <Head :title="tesis.titulo" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 grid gap-8 lg:grid-cols-3">

            <!-- Resumen -->
            <div class="lg:col-span-2">
                <div class="bg-card p-6 rounded-xl border shadow-sm">
                    <h1 class="text-3xl font-extrabold mb-2 text-primary tracking-tight">{{ tesis.titulo }}</h1>

                    <!-- Información clave -->
                    <div class="flex flex-wrap gap-4 text-sm text-muted-foreground mt-4 border-b pb-4">
                        <span class="flex items-center gap-1.5 font-medium">
                            <User class="h-4 w-4" /> Autor: {{ tesis.autor.name }}
                        </span>
                        <span class="flex items-center gap-1.5">
                            <GraduationCap class="h-4 w-4" /> Carrera: {{ tesis.carrera.nombre }}
                        </span>
                        <span class="flex items-center gap-1.5">
                            <Calendar class="h-4 w-4" /> Fecha: {{ new Date(tesis.created_at).toLocaleDateString() }}
                        </span>
                    </div>

                    <!-- Resumen -->
                    <div class="mt-6">
                        <h2 class="text-xl font-bold mb-3 border-b pb-1">Resumen del Proyecto</h2>
                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ tesis.resumen }}</p>
                    </div>
                </div>
            </div>

            <!-- Archivo PDF y Acciones -->
            <div class="lg:col-span-1">
                <div class="bg-card p-6 rounded-xl border shadow-sm sticky top-4">
                    <h2 class="text-lg font-bold mb-4 flex items-center gap-2 border-b pb-2">
                        <FileText class="h-5 w-5" /> Documento Final
                    </h2>

                    <!-- Previsualización -->
                    <div v-if="tesis.ruta_archivo" class="aspect-video bg-muted rounded-lg flex items-center justify-center mb-4">
                        <p class="text-sm text-muted-foreground">Previsualización de PDF</p>
                    </div>

                    <p v-else class="text-center text-muted-foreground text-sm mb-4">Archivo no disponible.</p>

                    <!-- Boton de Descarga -->
                    <a v-if="tesis.ruta_archivo" :href="route('tesis.ver', tesis.id)" target="_blank">
                        <Button class="w-full bg-blue-600 hover:bg-blue-700 text-white">
                            <Download class="h-4 w-4 mr-2" />
                            Ver / Descargar PDF
                        </Button>
                    </a>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
