<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Printer, FileBarChart } from 'lucide-vue-next';

defineProps<{
    stats: Array<any>;
    listado: Array<any>;
    fecha: string;
}>();

const printReport = () => {
    window.print();
};
</script>

<template>
    <Head title="Reportes del Sistema" />

    <AppLayout :breadcrumbs="[{ title: 'Administración', href: '#' }, { title: 'Reportes', href: '/admin/reportes' }]">
        <div class="flex flex-col gap-6 p-4 print:p-0">

            <!-- Header -->
            <div class="flex justify-between items-center print:hidden">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Reportes y Métricas</h1>
                    <p class="text-muted-foreground">Genera informes impresos o PDF de la actividad del repositorio.</p>
                </div>
                <Button @click="printReport">
                    <Printer class="mr-2 h-4 w-4" />
                    Imprimir / Guardar PDF
                </Button>
            </div>

            <!-- CONTENIDO DEL REPORTE -->
            <div class="bg-card border rounded-xl p-8 shadow-sm print:border-0 print:shadow-none print:p-0">

                <!-- Encabezado del Documento -->
                <div class="text-center mb-8 border-b pb-6">
                    <h2 class="text-xl font-bold uppercase tracking-widest">Instituto Universitario Jesús Obrero</h2>
                    <h3 class="text-lg font-medium text-muted-foreground">Informe de Gestión de Tesis</h3>
                    <p class="text-sm text-muted-foreground mt-2">Generado el: {{ fecha }}</p>
                </div>

                <!-- Resumen Estadistico -->
                <div class="mb-8">
                    <h4 class="text-lg font-bold mb-4 flex items-center gap-2">
                        <FileBarChart class="h-5 w-5" /> Resumen por Carrera
                    </h4>
                    <div class="rounded-md border overflow-hidden">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-muted/50 font-medium border-b">
                                <tr>
                                    <th class="p-3">Carrera</th>
                                    <th class="p-3 text-center">Total Registros</th>
                                    <th class="p-3 text-center text-green-700">Aprobadas</th>
                                    <th class="p-3 text-center text-orange-600">Pendientes</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                <tr v-for="stat in stats" :key="stat.id">
                                    <td class="p-3 font-medium">{{ stat.nombre }}</td>
                                    <td class="p-3 text-center font-bold">{{ stat.total }}</td>
                                    <td class="p-3 text-center">{{ stat.aprobadas }}</td>
                                    <td class="p-3 text-center">{{ stat.pendientes }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Ultimas Publicaciones -->
                <div>
                    <h4 class="text-lg font-bold mb-4">Últimas Tesis Aprobadas</h4>
                    <div class="rounded-md border overflow-hidden">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-muted/50 font-medium border-b">
                                <tr>
                                    <th class="p-3 w-24">Fecha</th>
                                    <th class="p-3">Título</th>
                                    <th class="p-3 w-48">Autor</th>
                                    <th class="p-3 w-48">Carrera</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                <tr v-for="item in listado" :key="item.id">
                                    <td class="p-3 text-xs text-muted-foreground">
                                        {{ new Date(item.updated_at).toLocaleDateString() }}
                                    </td>
                                    <td class="p-3 font-medium line-clamp-1">{{ item.titulo }}</td>
                                    <td class="p-3 text-muted-foreground">{{ item.autor?.name }}</td>
                                    <td class="p-3 text-xs">{{ item.carrera?.nombre }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p class="text-xs text-center mt-4 text-muted-foreground print:mt-8">
                        Fin del reporte. Documento generado automáticamente por el sistema TesisHub.
                    </p>
                </div>

            </div>
        </div>
    </AppLayout>
</template>

<style>
@media print {
    /* Ocultar elementos de la interfaz al imprimir */
    nav, aside, header, footer, button {
        display: none !important;
    }

    /* Expandir el contenido */
    main {
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
    }

    /* Asegurar colores de fondo en impresión */
    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
}
</style>
