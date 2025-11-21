<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { CheckCircle, XCircle, FileText } from 'lucide-vue-next';

defineProps<{
    tesis: { data: Array<any>, links: Array<any> };
}>();
</script>

<template>
    <Head title="Historial de Evaluaciones" />

    <AppLayout :breadcrumbs="[{ title: 'Académico', href: '#' }, { title: 'Historial', href: '/evaluaciones/historial' }]">
        <div class="flex flex-col gap-6 p-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Historial de Evaluaciones</h1>
                <p class="text-muted-foreground">Registro de tesis aprobadas y rechazadas.</p>
            </div>

            <div class="rounded-md border bg-card">
                <table class="w-full caption-bottom text-sm">
                    <thead class="[&_tr]:border-b">
                        <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Fecha Evaluación</th>
                            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Estudiante</th>
                            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Título</th>
                            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Dictamen</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in tesis.data" :key="item.id" class="border-b transition-colors hover:bg-muted/50">
                            <td class="p-4 align-middle text-muted-foreground">
                                {{ new Date(item.updated_at).toLocaleDateString() }}
                            </td>
                            <td class="p-4 align-middle font-medium">{{ item.autor?.name }}</td>
                            <td class="p-4 align-middle max-w-xs truncate">{{ item.titulo }}</td>
                            <td class="p-4 align-middle">
                                <span v-if="item.estado === 'aprobado'" class="inline-flex items-center text-green-700 font-medium text-xs">
                                    <CheckCircle class="h-3 w-3 mr-1" /> Aprobado
                                </span>
                                <span v-else class="inline-flex items-center text-red-700 font-medium text-xs">
                                    <XCircle class="h-3 w-3 mr-1" /> Rechazado
                                </span>
                            </td>
                        </tr>
                        <tr v-if="tesis.data.length === 0">
                            <td colspan="4" class="p-8 text-center text-muted-foreground">No hay historial disponible.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
