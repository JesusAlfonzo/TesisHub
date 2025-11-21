<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { FileText, Plus, Pencil, Trash2, Eye } from 'lucide-vue-next';
import { route } from 'ziggy-js';

// Props
defineProps<{
    tesis: Array<any>;
}>();

const getStatusColor = (status: string) => {
    switch(status) {
        case 'aprobado': return 'bg-green-100 text-green-800 border-green-200 dark:bg-green-900 dark:text-green-300 dark:border-green-800';
        case 'rechazado': return 'bg-red-100 text-red-800 border-red-200 dark:bg-red-900 dark:text-red-300 dark:border-red-800';
        default: return 'bg-yellow-100 text-yellow-800 border-yellow-200 dark:bg-yellow-900 dark:text-yellow-300 dark:border-yellow-800';
    }
};

const deleteTesis = (id: number) => {
    if (confirm('¿Estás seguro de eliminar este proyecto? Esta acción no se puede deshacer.')) {
        router.delete(route('mis-tesis.destroy', id));
    }
};
</script>

<template>
    <Head title="Mis Proyectos" />

    <AppLayout :breadcrumbs="[{ title: 'Estudiante', href: '#' }, { title: 'Mis Proyectos', href: '/mis-tesis' }]">
        <div class="flex flex-col gap-6 p-4">

            <!-- Header -->
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Mis Proyectos de Grado</h1>
                    <p class="text-muted-foreground">Gestiona tus entregas y revisa el estado de tus evaluaciones.</p>
                </div>
                <Link :href="route('mis-tesis.create')">
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        Nueva Entrega
                    </Button>
                </Link>
            </div>

            <!-- Tabla de Tesis -->
            <div class="rounded-md border bg-card">
                <div class="relative w-full overflow-auto">
                    <table class="w-full caption-bottom text-sm">
                        <thead class="[&_tr]:border-b">
                            <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                                <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Título</th>
                                <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Carrera</th>
                                <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Fecha</th>
                                <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Estado</th>
                                <th class="h-12 px-4 text-right align-middle font-medium text-muted-foreground">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="[&_tr:last-child]:border-0">
                            <tr v-for="item in tesis" :key="item.id" class="border-b transition-colors hover:bg-muted/50">
                                <td class="p-4 align-middle font-medium">{{ item.titulo }}</td>
                                <td class="p-4 align-middle text-muted-foreground">{{ item.carrera?.nombre }}</td>
                                <td class="p-4 align-middle text-muted-foreground">
                                    {{ new Date(item.created_at).toLocaleDateString() }}
                                </td>
                                <td class="p-4 align-middle">
                                    <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 uppercase" :class="getStatusColor(item.estado)">
                                        {{ item.estado }}
                                    </span>
                                </td>
                                <td class="p-4 align-middle text-right">
                                    <div class="flex justify-end gap-2">

                                        <!-- Botón Ver: Usa la ruta segura tesis.ver para evitar error 403 -->
                                        <a v-if="item.ruta_archivo" :href="route('tesis.ver', item.id)" target="_blank">
                                            <Button variant="ghost" size="icon" title="Ver PDF">
                                                <Eye class="h-4 w-4" />
                                            </Button>
                                        </a>

                                        <!-- Botón Editar -->
                                        <Link v-if="item.estado !== 'aprobado'" :href="route('mis-tesis.edit', item.id)">
                                            <Button variant="ghost" size="icon" title="Editar">
                                                <Pencil class="h-4 w-4 text-blue-600" />
                                            </Button>
                                        </Link>

                                        <!-- Botón Eliminar -->
                                        <Button
                                            v-if="item.estado !== 'aprobado'"
                                            variant="ghost"
                                            size="icon"
                                            title="Eliminar"
                                            @click="deleteTesis(item.id)"
                                        >
                                            <Trash2 class="h-4 w-4 text-red-600" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Estado vacio -->
                            <tr v-if="tesis.length === 0">
                                <td colspan="5" class="p-8 text-center text-muted-foreground">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <FileText class="h-8 w-8 opacity-50" />
                                        <p>No has subido ningún proyecto aún.</p>
                                        <Link :href="route('mis-tesis.create')" class="text-primary underline">
                                            Comienza subiendo tu anteproyecto
                                        </Link>
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
