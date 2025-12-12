<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Printer, FileBarChart, Calendar, FileText } from 'lucide-vue-next';

// Definición de tipos
interface Stat {
    id: number;
    nombre: string;
    total: number;
    aprobadas: number;
    pendientes: number;
}

interface TesisItem {
    id: number;
    titulo: string;
    updated_at: string;
    autor?: { name: string };
    carrera?: { nombre: string };
}

defineProps<{
    stats: Array<Stat>;
    listado: Array<TesisItem>;
    fecha: string;
}>();

const printReport = () => {
    window.print();
};
</script>

<template>

    <Head title="Reportes del Sistema" />

    <AppLayout :breadcrumbs="[{ title: 'Administración', href: '#' }, { title: 'Reportes', href: '/admin/reportes' }]">

        <main id="print-area"
            class="flex flex-col gap-6 p-4 md:p-8 max-w-5xl mx-auto w-full print:p-0 print:max-w-none">

            <header class="flex justify-between items-center print:hidden pb-6 border-b">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-foreground">Reportes y Métricas</h1>
                    <p class="text-muted-foreground text-sm mt-1">Genera informes impresos o PDF de la actividad del
                        repositorio.</p>
                </div>
                <Button @click="printReport" class="shadow-sm">
                    <Printer class="mr-2 h-4 w-4" aria-hidden="true" />
                    Imprimir / Guardar PDF
                </Button>
            </header>

            <article
                class="bg-card border rounded-xl p-8 shadow-sm print:border-0 print:shadow-none print:p-0 print:bg-white">

                <div class="text-center mb-10 border-b pb-6 print:mb-6">
                    <div class="flex items-center justify-center gap-3 mb-2 opacity-50">
                        <FileBarChart class="h-8 w-8" aria-hidden="true" />
                    </div>
                    <h2 class="text-xl font-bold uppercase tracking-widest text-foreground print:text-black">Instituto
                        Universitario Jesús Obrero</h2>
                    <h3 class="text-lg font-medium text-muted-foreground print:text-black">Informe de Gestión de Tesis
                    </h3>
                    <p
                        class="text-sm text-muted-foreground mt-2 flex items-center justify-center gap-1 print:text-black">
                        <Calendar class="h-3 w-3" aria-hidden="true" />
                        Generado el: <time :datetime="new Date().toISOString()">{{ fecha }}</time>
                    </p>
                </div>

                <section aria-labelledby="stats-heading" class="mb-10 print:mb-6">
                    <h4 id="stats-heading" class="text-lg font-bold mb-4 flex items-center gap-2 print:text-black">
                        <FileBarChart class="h-5 w-5 text-muted-foreground print:text-black" aria-hidden="true" />
                        Resumen por Carrera
                    </h4>

                    <div class="rounded-md border overflow-hidden print:border-black">
                        <table class="w-full text-sm text-left">
                            <caption class="sr-only">Estadísticas de tesis aprobadas y pendientes por carrera</caption>
                            <thead class="bg-muted/50 font-medium border-b print:bg-gray-100 print:border-black">
                                <tr>
                                    <th scope="col" class="p-3 print:text-black">Carrera</th>
                                    <th scope="col" class="p-3 text-center print:text-black">Total Registros</th>
                                    <th scope="col" class="p-3 text-center text-green-700 print:text-black">Aprobadas
                                    </th>
                                    <th scope="col" class="p-3 text-center text-orange-600 print:text-black">Pendientes
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y print:divide-black">
                                <tr v-for="stat in stats" :key="stat.id" class="print:break-inside-avoid">
                                    <th scope="row" class="p-3 font-medium print:text-black">{{ stat.nombre }}</th>
                                    <td class="p-3 text-center font-bold print:text-black">{{ stat.total }}</td>
                                    <td class="p-3 text-center print:text-black">{{ stat.aprobadas }}</td>
                                    <td class="p-3 text-center print:text-black">{{ stat.pendientes }}</td>
                                </tr>
                            </tbody>
                            <tfoot class="bg-muted/20 font-semibold border-t print:border-black">
                                <tr>
                                    <td class="p-3 print:text-black">TOTALES</td>
                                    <td class="p-3 text-center print:text-black">{{stats.reduce((acc, s) => acc +
                                        s.total, 0)}}</td>
                                    <td class="p-3 text-center print:text-black">{{stats.reduce((acc, s) => acc +
                                        s.aprobadas, 0)}}</td>
                                    <td class="p-3 text-center print:text-black">{{stats.reduce((acc, s) => acc +
                                        s.pendientes, 0)}}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </section>

                <section aria-labelledby="latest-heading" class="print:break-before-auto">
                    <h4 id="latest-heading" class="text-lg font-bold mb-4 flex items-center gap-2 print:text-black">
                        <FileText class="h-5 w-5 text-muted-foreground print:text-black" aria-hidden="true" />
                        Últimas Tesis Aprobadas
                    </h4>

                    <div class="rounded-md border overflow-hidden print:border-black">
                        <table class="w-full text-sm text-left">
                            <caption class="sr-only">Listado de las últimas tesis aprobadas</caption>
                            <thead class="bg-muted/50 font-medium border-b print:bg-gray-100 print:border-black">
                                <tr>
                                    <th scope="col" class="p-3 w-32 print:text-black">Fecha</th>
                                    <th scope="col" class="p-3 print:text-black">Título del Proyecto</th>
                                    <th scope="col" class="p-3 w-48 print:text-black">Autor</th>
                                    <th scope="col" class="p-3 w-48 print:text-black">Carrera</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y print:divide-black">
                                <tr v-for="item in listado" :key="item.id" class="print:break-inside-avoid">
                                    <td class="p-3 text-xs text-muted-foreground print:text-black">
                                        {{ new Date(item.updated_at).toLocaleDateString() }}
                                    </td>
                                    <th scope="row" class="p-3 font-medium line-clamp-2 print:text-black">
                                        {{ item.titulo }}
                                    </th>
                                    <td class="p-3 text-muted-foreground print:text-black">{{ item.autor?.name }}</td>
                                    <td class="p-3 text-xs print:text-black">{{ item.carrera?.nombre }}</td>
                                </tr>
                                <tr v-if="listado.length === 0">
                                    <td colspan="4" class="p-6 text-center text-muted-foreground">No hay registros
                                        recientes.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <footer
                    class="mt-12 pt-4 border-t text-center text-xs text-muted-foreground print:text-black print:mt-auto print:pt-8 print:border-black">
                    <p>Documento generado automáticamente por el sistema de Repositorio Digital IUJO.</p>
                </footer>

            </article>
        </main>
    </AppLayout>
</template>

<style>
@media print {

    /* 1. Configuración base de la hoja */
    @page {
        margin: 0;
        size: auto;
    }

    /* 2. Ocultar todo lo demás */
    body,
    body * {
        visibility: hidden !important;
        height: auto !important;
        overflow: visible !important;
    }

    /* 3. Hacer visible el reporte */
    #print-area,
    #print-area * {
        visibility: visible !important;
    }

    /* 4. Posicionamiento y Márgenes */
    #print-area {
        position: fixed !important;
        left: 0 !important;
        top: 0 !important;
        width: 100vw !important;
        min-height: 100vh !important;
        margin: 0 !important;
        padding: 1cm 2.5cm !important;
        box-sizing: border-box !important;

        background-color: white !important;
        z-index: 99999 !important;
        display: block !important;
    }

    /* 5. Tablas y saltos de página */
    table {
        width: 100% !important;
        border-collapse: collapse !important;
    }

    tr {
        break-inside: avoid;
        page-break-inside: avoid;
    }

    thead {
        display: table-header-group;
    }

    /* 6. Colores precisos */
    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
}
</style>