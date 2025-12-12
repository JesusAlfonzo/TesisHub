<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { 
    FileText, CheckCircle, XCircle, Search, Filter, 
    AlertTriangle, ChevronDown, Calendar, User, History
} from 'lucide-vue-next';
import { route } from 'ziggy-js';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';

// Props recibidos del EvaluacionController
const props = defineProps<{
    tesis: {
        data: Array<{
            id: number;
            titulo: string;
            ruta_archivo: string;
            updated_at: string; // Fecha de dictamen
            estado: 'aprobado' | 'rechazado';
            carrera?: { nombre: string };
            autor?: { name: string; cedula?: string };
        }>;
        links: Array<any>;
        current_page: number;
        last_page: number;
    };
    filters: {
        search: string;
        carrera_id: string;
    };
    carreras: Array<{ id: number; nombre: string }>;
}>();

// --- ESTADO ---
const search = ref(props.filters.search || '');
const carreraSeleccionada = ref(props.filters.carrera_id || '');

// --- FILTROS ---
const applyFilters = debounce(() => {
    router.get(route('evaluaciones.historial'), { 
        search: search.value,
        carrera_id: carreraSeleccionada.value 
    }, { 
        preserveState: true, 
        replace: true, 
        preserveScroll: true 
    });
}, 300);

watch([search, carreraSeleccionada], () => applyFilters());

const clearFilters = () => {
    search.value = '';
    carreraSeleccionada.value = '';
};

// --- UTILIDADES ---
const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('es-ES', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const getStatusConfig = (status: string) => {
    return status === 'aprobado'
        ? { color: 'text-green-700 bg-green-50 border-green-200', icon: CheckCircle, label: 'Aprobado' }
        : { color: 'text-red-700 bg-red-50 border-red-200', icon: XCircle, label: 'Rechazado' };
};
</script>

<template>
    <Head title="Historial de Evaluaciones" />

    <AppLayout :breadcrumbs="[{ title: 'Académico', href: '#' }, { title: 'Historial', href: '/evaluaciones/historial' }]">
        
        <main class="flex flex-col gap-6 p-4 md:p-8 max-w-7xl mx-auto w-full animate-in fade-in duration-500">

            <header class="flex flex-col gap-4 border-b pb-6">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-foreground flex items-center gap-2">
                        <History class="h-6 w-6 text-muted-foreground" />
                        Historial de Evaluaciones
                    </h1>
                    <p class="text-muted-foreground">Registro auditor de todas las tesis aprobadas y rechazadas.</p>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 mt-2">
                    <div class="relative flex-1">
                        <Search class="absolute left-3 top-2.5 h-4 w-4 text-muted-foreground pointer-events-none" />
                        <Input 
                            v-model="search" 
                            placeholder="Buscar por estudiante, título o dictamen..." 
                            class="pl-9 bg-background"
                        />
                    </div>
                    
                    <div class="relative w-full sm:w-[250px]">
                        <Filter class="absolute left-3 top-2.5 h-4 w-4 text-muted-foreground pointer-events-none z-10" />
                        <select 
                            v-model="carreraSeleccionada"
                            class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background pl-9 pr-8 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 appearance-none text-foreground truncate cursor-pointer"
                        >
                            <option value="">Todas las carreras</option>
                            <option v-for="carrera in carreras" :key="carrera.id" :value="carrera.id">
                                {{ carrera.nombre }}
                            </option>
                        </select>
                        <ChevronDown class="absolute right-3 top-3 h-4 w-4 text-muted-foreground pointer-events-none" />
                    </div>
                </div>
            </header>

            <div class="space-y-4">

                <ul class="grid grid-cols-1 gap-4 md:hidden" role="list">
                    <li v-for="item in tesis.data" :key="'mob-' + item.id">
                        <article class="bg-card rounded-xl border shadow-sm p-5 space-y-3 relative overflow-hidden">
                            
                            <div 
                                class="absolute left-0 top-0 bottom-0 w-1" 
                                :class="item.estado === 'aprobado' ? 'bg-green-500' : 'bg-red-500'"
                            ></div>

                            <div class="flex justify-between items-start pl-2">
                                <div class="space-y-1">
                                    <p class="text-xs text-muted-foreground flex items-center gap-1">
                                        <Calendar class="h-3 w-3" />
                                        {{ formatDate(item.updated_at) }}
                                    </p>
                                    <Badge variant="outline" class="font-normal text-[10px]">
                                        {{ item.carrera?.nombre }}
                                    </Badge>
                                </div>
                                <Badge 
                                    variant="outline" 
                                    class="capitalize"
                                    :class="getStatusConfig(item.estado).color"
                                >
                                    <component :is="getStatusConfig(item.estado).icon" class="h-3 w-3 mr-1" />
                                    {{ getStatusConfig(item.estado).label }}
                                </Badge>
                            </div>

                            <div class="pl-2 border-t pt-2 mt-2">
                                <h3 class="font-medium text-sm leading-snug">{{ item.titulo }}</h3>
                            </div>

                            <div class="flex flex-col gap-2 pt-2 pl-2">
                                <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                    <User class="h-4 w-4" />
                                    <span>{{ item.autor?.name }}</span>
                                </div>
                                
                                <a 
                                    v-if="item.ruta_archivo" 
                                    :href="route('tesis.ver', item.id)"  
                                    target="_blank" 
                                    class="flex items-center justify-center w-full h-10 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-100 rounded-md hover:bg-blue-100 mt-2"
                                >
                                    <FileText class="h-4 w-4 mr-2" /> Ver Documento Evaluado
                                </a>
                            </div>
                        </article>
                    </li>
                </ul>

                <div class="hidden md:block rounded-md border bg-card shadow-sm overflow-hidden">
                    <div class="relative w-full overflow-auto">
                        <table class="w-full caption-bottom text-sm">
                            <caption class="sr-only">Historial de evaluaciones</caption>
                            <thead class="bg-muted/50 [&_tr]:border-b">
                                <tr class="border-b transition-colors">
                                    <th scope="col" class="h-12 px-4 text-left align-middle font-medium text-muted-foreground w-[180px]">Fecha Dictamen</th>
                                    <th scope="col" class="h-12 px-4 text-left align-middle font-medium text-muted-foreground w-[250px]">Estudiante</th>
                                    <th scope="col" class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Proyecto</th>
                                    <th scope="col" class="h-12 px-4 text-left align-middle font-medium text-muted-foreground w-[120px]">Archivo</th>
                                    <th scope="col" class="h-12 px-4 text-right align-middle font-medium text-muted-foreground w-[150px]">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="[&_tr:last-child]:border-0">
                                <tr v-for="item in tesis.data" :key="item.id" class="border-b transition-colors hover:bg-muted/50">
                                    
                                    <td class="p-4 align-middle text-muted-foreground">
                                        <div class="flex flex-col">
                                            <span class="font-medium text-foreground">{{ new Date(item.updated_at).toLocaleDateString() }}</span>
                                            <span class="text-xs">{{ new Date(item.updated_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) }}</span>
                                        </div>
                                    </td>

                                    <th scope="row" class="p-4 align-middle font-normal">
                                        <div class="flex items-center gap-3">
                                            <div class="h-8 w-8 rounded-full bg-secondary flex items-center justify-center text-xs font-bold shrink-0">
                                                {{ item.autor?.name.charAt(0) || '?' }}
                                            </div>
                                            <div class="text-left">
                                                <div class="font-medium text-foreground">{{ item.autor?.name }}</div>
                                                <div class="text-xs text-muted-foreground">{{ item.autor?.cedula }}</div>
                                            </div>
                                        </div>
                                    </th>

                                    <td class="p-4 align-middle">
                                        <div class="space-y-1">
                                            <p class="font-medium leading-snug line-clamp-2" :title="item.titulo">{{ item.titulo }}</p>
                                            <Badge variant="outline" class="font-normal text-[10px] text-muted-foreground">
                                                {{ item.carrera?.nombre }}
                                            </Badge>
                                        </div>
                                    </td>

                                    <td class="p-4 align-middle">
                                        <a 
                                            v-if="item.ruta_archivo" 
                                            :href="route('tesis.ver', item.id)" 
                                            target="_blank" 
                                            class="inline-flex items-center gap-1.5 text-sm font-medium text-blue-600 hover:text-blue-800"
                                        >
                                            <FileText class="h-4 w-4" /> PDF
                                        </a>
                                        <span v-else class="text-xs text-muted-foreground">-</span>
                                    </td>

                                    <td class="p-4 align-middle text-right">
                                        <Badge 
                                            variant="outline" 
                                            class="capitalize shadow-none"
                                            :class="getStatusConfig(item.estado).color"
                                        >
                                            <component :is="getStatusConfig(item.estado).icon" class="h-3 w-3 mr-1.5" />
                                            {{ getStatusConfig(item.estado).label }}
                                        </Badge>
                                    </td>
                                </tr>

                                <tr v-if="tesis.data.length === 0">
                                    <td colspan="5" class="h-48 text-center text-muted-foreground">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <Search class="h-8 w-8 opacity-20" />
                                            <p>No se encontraron registros en el historial.</p>
                                            <Button variant="link" @click="clearFilters">Limpiar filtros</Button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div v-if="tesis.links.length > 3" class="flex justify-center mt-4">
                    <nav aria-label="Navegación de historial" class="flex flex-wrap gap-1">
                        <template v-for="(link, key) in tesis.links" :key="key">
                            <div v-if="link.url === null" class="px-3 py-1 text-sm text-muted-foreground" v-html="link.label" />
                            <Link 
                                v-else 
                                :href="link.url" 
                                class="px-3 py-1 rounded-md text-sm font-medium transition-colors"
                                :class="link.active ? 'bg-primary text-primary-foreground' : 'hover:bg-muted'"
                                v-html="link.label"
                            />
                        </template>
                    </nav>
                </div>

            </div>
        </main>
    </AppLayout>
</template>