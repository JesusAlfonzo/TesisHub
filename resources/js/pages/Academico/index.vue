<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { FileText, CheckCircle, XCircle, Eye, User } from 'lucide-vue-next';
import { route } from 'ziggy-js';

defineProps<{
    tesis: Array<any>;
}>();

// Función para evaluar (Aprobar/Rechazar)
const evaluar = (id: number, nuevoEstado: 'aprobado' | 'rechazado') => {
    const confirmacion = nuevoEstado === 'aprobado'
        ? '¿Confirma que desea APROBAR esta tesis para su publicación?'
        : '¿Confirma que desea RECHAZAR esta tesis?';

    if (confirm(confirmacion)) {
        router.patch(route('evaluaciones.update', id), {
            estado: nuevoEstado
        });
    }
};
</script>

<template>
    <Head title="Evaluación Académica" />

    <AppLayout :breadcrumbs="[{ title: 'Académico', href: '#' }, { title: 'Revisiones Pendientes', href: '/evaluaciones/pendientes' }]">
        <div class="flex flex-col gap-6 p-4">

            <!-- Header -->
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Revisiones Pendientes</h1>
                <p class="text-muted-foreground">Evalúa las tesis enviadas por los estudiantes.</p>
            </div>

            <!-- Tabla de Pendientes -->
            <div class="rounded-md border bg-card">
                <div class="relative w-full overflow-auto">
                    <table class="w-full caption-bottom text-sm">
                        <thead class="[&_tr]:border-b">
                            <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                                <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Estudiante</th>
                                <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Título de Tesis</th>
                                <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Carrera</th>
                                <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Archivo</th>
                                <th class="h-12 px-4 text-right align-middle font-medium text-muted-foreground">Dictamen</th>
                            </tr>
                        </thead>
                        <tbody class="[&_tr:last-child]:border-0">
                            <tr v-for="item in tesis" :key="item.id" class="border-b transition-colors hover:bg-muted/50">
                                <!-- Estudiante -->
                                <td class="p-4 align-middle">
                                    <div class="flex items-center gap-2">
                                        <div class="h-8 w-8 rounded-full bg-primary/10 flex items-center justify-center text-xs font-bold text-primary">
                                            <User class="h-4 w-4" />
                                        </div>
                                        <div>
                                            <div class="font-medium">{{ item.autor?.name }}</div>
                                            <div class="text-xs text-muted-foreground">{{ item.autor?.cedula || 'S/C' }}</div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Título -->
                                <td class="p-4 align-middle font-medium max-w-xs truncate" :title="item.titulo">
                                    {{ item.titulo }}
                                </td>

                                <!-- Carrera -->
                                <td class="p-4 align-middle text-muted-foreground">
                                    <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10 dark:bg-blue-900 dark:text-blue-300 dark:ring-blue-900">
                                        {{ item.carrera?.nombre }}
                                    </span>
                                </td>

                                <!-- PDF -->
                                <td class="p-4 align-middle">
                                    <a v-if="item.ruta_archivo" :href="'/storage/' + item.ruta_archivo" target="_blank" class="inline-flex items-center gap-1 text-sm text-blue-600 hover:underline">
                                        <Eye class="h-4 w-4" />
                                        Ver PDF
                                    </a>
                                    <span v-else class="text-muted-foreground text-xs">Sin archivo</span>
                                </td>

                                <!-- Botones de Acción -->
                                <td class="p-4 align-middle text-right">
                                    <div class="flex justify-end gap-2">
                                        <Button
                                            variant="destructive"
                                            size="sm"
                                            @click="evaluar(item.id, 'rechazado')"
                                            title="Rechazar Tesis"
                                        >
                                            <XCircle class="h-4 w-4 mr-1" /> Rechazar
                                        </Button>

                                        <Button
                                            class="bg-green-600 hover:bg-green-700 text-white dark:bg-green-700 dark:hover:bg-green-800"
                                            size="sm"
                                            @click="evaluar(item.id, 'aprobado')"
                                            title="Aprobar Tesis"
                                        >
                                            <CheckCircle class="h-4 w-4 mr-1" /> Aprobar
                                        </Button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Empty State -->
                            <tr v-if="tesis.length === 0">
                                <td colspan="5" class="p-12 text-center text-muted-foreground">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <FileText class="h-10 w-10 opacity-50" />
                                        <p class="text-lg font-medium">¡Estás al día!</p>
                                        <p>No hay tesis pendientes de revisión en este momento.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </AppLayout>
</template>
