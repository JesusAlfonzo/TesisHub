|<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3'; 
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { 
    FileText, 
    Plus, 
    Pencil, 
    Trash2, 
    Eye, 
    Calendar, 
    CheckCircle, 
    XCircle, 
    Clock
} from 'lucide-vue-next';
import { route } from 'ziggy-js';

// Props
defineProps<{
    tesis: Array<{
        id: number;
        titulo: string;
        estado: string;
        created_at: string;
        ruta_archivo: string | null;
        carrera?: { nombre: string };
    }>;
}>();

// Helper de colores y configuración de estado
const getStatusConfig = (status: string) => {
    switch(status) {
        case 'aprobado': 
            return { 
                class: 'bg-green-100 text-green-800 hover:bg-green-200 border-green-200 dark:bg-green-900/30 dark:text-green-300', 
                icon: CheckCircle,
                label: 'Aprobado'
            };
        case 'rechazado': 
            return { 
                class: 'bg-red-100 text-red-800 hover:bg-red-200 border-red-200 dark:bg-red-900/30 dark:text-red-300', 
                icon: XCircle,
                label: 'Rechazado'
            };
        default: 
            return { 
                class: 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200 border-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-300', 
                icon: Clock,
                label: 'Pendiente'
            };
    }
};

const deleteTesis = (id: number, titulo: string) => {
    if (confirm(`¿Estás seguro de eliminar el proyecto "${titulo}"?\n\nEsta acción no se puede deshacer.`)) {
        router.delete(route('mis-tesis.destroy', id));
    }
};
</script>

<template>
    <Head title="Mis Proyectos" />

    <AppLayout :breadcrumbs="[{ title: 'Estudiante', href: '#' }, { title: 'Mis Proyectos', href: '/mis-tesis' }]">
        
        <main class="flex flex-col gap-6 p-4 md:p-8 max-w-7xl mx-auto w-full animate-in fade-in duration-500">
            
            <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b pb-6">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-foreground">Mis Proyectos de Grado</h1>
                    <p class="text-muted-foreground text-sm mt-1">Gestiona tus entregas y revisa el estado de tus evaluaciones.</p>
                </div>
                <Link :href="route('mis-tesis.create')">
                    <Button aria-label="Crear una nueva entrega de tesis" class="shadow-sm">
                        <Plus class="mr-2 h-4 w-4" aria-hidden="true" />
                        Nueva Entrega
                    </Button>
                </Link>
            </header>

            <section aria-label="Listado de proyectos">
                
                <div class="hidden md:block rounded-md border bg-card shadow-sm w-full overflow-hidden">
                    <div class="relative w-full overflow-auto">
                        <table class="w-full caption-bottom text-sm">
                            <caption class="sr-only">Tabla de mis proyectos de tesis</caption>
                            <thead class="bg-muted/50 [&_tr]:border-b">
                                <tr class="border-b transition-colors">
                                    <th scope="col" class="h-12 px-4 text-left align-middle font-medium text-muted-foreground w-[40%]">Título del Proyecto</th>
                                    <th scope="col" class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Carrera</th>
                                    <th scope="col" class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Fecha de Entrega</th>
                                    <th scope="col" class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Estado</th>
                                    <th scope="col" class="h-12 px-4 text-right align-middle font-medium text-muted-foreground">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="[&_tr:last-child]:border-0">
                                <tr v-for="item in tesis" :key="item.id" class="border-b transition-colors hover:bg-muted/50 group">
                                    
                                    <th scope="row" class="p-4 align-middle font-medium text-foreground">
                                        <div class="truncate max-w-[300px]" :title="item.titulo">
                                            {{ item.titulo }}
                                        </div>
                                    </th>

                                    <td class="p-4 align-middle text-muted-foreground">
                                        {{ item.carrera?.nombre || 'General' }}
                                    </td>

                                    <td class="p-4 align-middle text-muted-foreground">
                                        <div class="flex items-center gap-2">
                                            <Calendar class="h-3 w-3" aria-hidden="true" />
                                            {{ new Date(item.created_at).toLocaleDateString() }}
                                        </div>
                                    </td>

                                    <td class="p-4 align-middle">
                                        <Badge variant="outline" :class="getStatusConfig(item.estado).class" class="font-medium shadow-none">
                                            <component :is="getStatusConfig(item.estado).icon" class="h-3 w-3 mr-1.5" aria-hidden="true" />
                                            {{ getStatusConfig(item.estado).label }}
                                        </Badge>
                                    </td>

                                    <td class="p-4 align-middle text-right">
                                        <div class="flex justify-end gap-2 opacity-100 transition-opacity">
                                            <a v-if="item.ruta_archivo" :href="route('tesis.ver', item.id)" target="_blank">
                                                <Button variant="ghost" size="icon" :aria-label="`Ver PDF de ${item.titulo}`" title="Ver PDF">
                                                    <Eye class="h-4 w-4 text-muted-foreground" aria-hidden="true" />
                                                </Button>
                                            </a>

                                            <Link v-if="item.estado !== 'aprobado'" :href="route('mis-tesis.edit', item.id)">
                                                <Button variant="ghost" size="icon" :aria-label="`Editar tesis ${item.titulo}`" title="Editar">
                                                    <Pencil class="h-4 w-4 text-blue-600 dark:text-blue-400" aria-hidden="true" />
                                                </Button>
                                            </Link>

                                            <Button 
                                                v-if="item.estado !== 'aprobado'"
                                                variant="ghost" 
                                                size="icon" 
                                                class="text-red-600 hover:text-red-700 hover:bg-red-50"
                                                :aria-label="`Eliminar tesis ${item.titulo}`"
                                                title="Eliminar"
                                                @click="deleteTesis(item.id, item.titulo)"
                                            >
                                                <Trash2 class="h-4 w-4" aria-hidden="true" />
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <ul class="md:hidden grid grid-cols-1 gap-4" role="list">
                    <li v-for="item in tesis" :key="'mob-' + item.id">
                        <article class="rounded-xl border bg-card text-card-foreground shadow-sm p-5 space-y-4 relative overflow-hidden">
                            
                            <div class="absolute left-0 top-0 bottom-0 w-1" :class="item.estado === 'aprobado' ? 'bg-green-500' : (item.estado === 'rechazado' ? 'bg-red-500' : 'bg-yellow-500')"></div>

                            <div class="flex justify-between items-start pl-2 gap-3">
                                <div class="space-y-1">
                                    <h3 class="font-semibold leading-snug line-clamp-2 text-foreground">{{ item.titulo }}</h3>
                                    <p class="text-xs text-muted-foreground">{{ item.carrera?.nombre }}</p>
                                </div>
                                <Badge variant="outline" :class="getStatusConfig(item.estado).class" class="shrink-0 text-[10px] px-2 py-0.5">
                                    {{ getStatusConfig(item.estado).label }}
                                </Badge>
                            </div>
                            
                            <div class="flex items-center text-xs text-muted-foreground pl-2 border-t pt-3 mt-1">
                                <Calendar class="mr-1.5 h-3.5 w-3.5" aria-hidden="true" />
                                Subido el: {{ new Date(item.created_at).toLocaleDateString() }}
                            </div>

                            <div class="flex flex-col gap-3 pt-2 pl-2 mt-2 border-t">
                                <a v-if="item.ruta_archivo" :href="route('tesis.ver', item.id)" target="_blank" class="w-full">
                                    <Button variant="outline" size="sm" class="w-full h-10 justify-center">
                                        <Eye class="mr-2 h-4 w-4" /> Ver Archivo
                                    </Button>
                                </a>
                                
                                <template v-if="item.estado !== 'aprobado'">
                                    <Link :href="route('mis-tesis.edit', item.id)" class="w-full">
                                        <Button variant="outline" size="sm" class="w-full h-10 justify-center border-blue-200 text-blue-700 hover:bg-blue-50">
                                            <Pencil class="mr-2 h-4 w-4" /> Editar Proyecto
                                        </Button>
                                    </Link>

                                    <Button 
                                        variant="outline" 
                                        size="sm" 
                                        class="w-full h-10 justify-center border-red-200 text-red-700 hover:bg-red-50"
                                        @click="deleteTesis(item.id, item.titulo)"
                                    >
                                        <Trash2 class="mr-2 h-4 w-4" /> Eliminar Proyecto
                                    </Button>
                                </template>
                            </div>
                        </article>
                    </li>
                </ul>

                <div v-if="tesis.length === 0" class="flex flex-col items-center justify-center py-16 text-center border-2 border-dashed rounded-xl bg-muted/20">
                    <div class="bg-muted p-4 rounded-full mb-4">
                        <FileText class="h-8 w-8 text-muted-foreground" aria-hidden="true" />
                    </div>
                    <h3 class="text-lg font-semibold text-foreground">No tienes proyectos registrados</h3>
                    <p class="text-muted-foreground max-w-sm mx-auto mt-2 text-sm">
                        Comienza tu proceso de grado subiendo tu anteproyecto hoy mismo.
                    </p>
                    <Link :href="route('mis-tesis.create')" class="mt-6">
                        <Button class="shadow-md">
                            <Plus class="mr-2 h-4 w-4" /> Comenzar ahora
                        </Button>
                    </Link>
                </div>

            </section>

        </main>
    </AppLayout>
</template>