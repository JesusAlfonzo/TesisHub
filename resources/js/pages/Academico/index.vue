<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { 
    FileText, CheckCircle, XCircle, Eye, Search, Filter, 
    AlertTriangle, Loader2, ChevronDown 
} from 'lucide-vue-next';
import { route } from 'ziggy-js';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';

// Props
const props = defineProps<{
    tesis: {
        data: Array<{
            id: number;
            titulo: string;
            ruta_archivo: string;
            created_at: string;
            carrera?: { nombre: string };
            autor?: { name: string; cedula?: string };
        }>;
        links: Array<any>;
    };
    filters: {
        search: string;
        carrera_id: string;
    };
    carreras: Array<{ id: number; nombre: string }>;
}>();

// Estado
const search = ref(props.filters.search || '');
const carreraSeleccionada = ref(props.filters.carrera_id || '');
const processingId = ref<number | null>(null);

// Filtros
const applyFilters = debounce(() => {
    router.get(route('evaluaciones.index'), { 
        search: search.value,
        carrera_id: carreraSeleccionada.value 
    }, { 
        preserveState: true, 
        replace: true, 
        preserveScroll: true 
    });
}, 300);

watch([search, carreraSeleccionada], () => applyFilters());

// Acción Evaluar
const evaluar = (id: number, nuevoEstado: 'aprobado' | 'rechazado', titulo: string) => {
    const accion = nuevoEstado === 'aprobado' ? 'APROBAR' : 'RECHAZAR';
    if (confirm(`¿Confirma que desea ${accion} la tesis: "${titulo}"?`)) {
        processingId.value = id;
        router.patch(route('evaluaciones.update', id), { estado: nuevoEstado }, {
            onFinish: () => processingId.value = null,
            preserveScroll: true
        });
    }
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
};
</script>

<template>
    <Head title="Evaluación Académica" />

    <AppLayout :breadcrumbs="[{ title: 'Académico', href: '#' }, { title: 'Revisiones Pendientes', href: '/evaluaciones/pendientes' }]">
        
        <main class="flex flex-col gap-6 p-4 md:p-8 max-w-7xl mx-auto w-full animate-in fade-in duration-500">

            <header class="flex flex-col gap-4 border-b pb-6">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-foreground">Revisiones Pendientes</h1>
                    <p class="text-muted-foreground">Evalúa y dictamina las tesis enviadas por los estudiantes.</p>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 mt-2">
                    <div class="relative flex-1">
                        <Search class="absolute left-3 top-2.5 h-4 w-4 text-muted-foreground pointer-events-none" />
                        <Input 
                            v-model="search" 
                            placeholder="Buscar por estudiante, cédula o título..." 
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
                    <li v-for="item in tesis.data" :key="'mobile-' + item.id">
                        <article class="bg-card rounded-xl border shadow-sm p-5 space-y-4 relative overflow-hidden">
                            
                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-blue-500"></div>

                            <div class="flex justify-between items-start gap-3 pl-2">
                                <div class="flex gap-3">
                                    <div class="h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold shrink-0">
                                        {{ item.autor?.name.charAt(0) || 'E' }}
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-sm">{{ item.autor?.name }}</h3>
                                        <p class="text-xs text-muted-foreground">{{ item.autor?.cedula || 'S/C' }}</p>
                                    </div>
                                </div>
                                <Badge variant="outline" class="text-[10px] bg-blue-50 text-blue-700 border-blue-200 truncate max-w-[100px]">
                                    {{ item.carrera?.nombre }}
                                </Badge>
                            </div>

                            <div class="pl-2">
                                <h4 class="font-medium text-sm leading-snug">{{ item.titulo }}</h4>
                                <p class="text-xs text-muted-foreground mt-1">Enviado: {{ formatDate(item.created_at) }}</p>
                            </div>

                            <div class="flex flex-col gap-3 pt-2 border-t pl-2">
                                <a 
                                    v-if="item.ruta_archivo" 
                                    :href="route('tesis.ver', item.id)"  
                                    target="_blank" 
                                    class="flex items-center justify-center w-full h-10 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-100 rounded-md hover:bg-blue-100"
                                >
                                    <Eye class="h-4 w-4 mr-2" /> Revisar Documento
                                </a>
                                
                                <Button 
                                    class="w-full h-10 bg-green-600 hover:bg-green-700 text-white justify-center" 
                                    @click="evaluar(item.id, 'aprobado', item.titulo)" 
                                    :disabled="processingId === item.id"
                                >
                                    <Loader2 v-if="processingId === item.id" class="h-4 w-4 animate-spin mr-2" />
                                    <CheckCircle v-else class="h-4 w-4 mr-2" /> 
                                    Aprobar Tesis
                                </Button>

                                <Button 
                                    variant="outline" 
                                    class="w-full h-10 border-red-200 text-red-600 hover:bg-red-50 hover:text-red-700 justify-center" 
                                    @click="evaluar(item.id, 'rechazado', item.titulo)" 
                                    :disabled="processingId === item.id"
                                >
                                    <XCircle class="h-4 w-4 mr-2" /> 
                                    Rechazar Tesis
                                </Button>
                            </div>
                        </article>
                    </li>
                </ul>

                <div class="hidden md:block rounded-md border bg-card shadow-sm overflow-hidden">
                    <div class="relative w-full overflow-auto">
                        <table class="w-full caption-bottom text-sm">
                            <thead class="bg-muted/50 [&_tr]:border-b">
                                <tr class="border-b transition-colors">
                                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground w-[250px]">Estudiante</th>
                                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Proyecto</th>
                                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground w-[120px]">Archivo</th>
                                    <th class="h-12 px-4 text-right align-middle font-medium text-muted-foreground w-[220px]">Dictamen</th>
                                </tr>
                            </thead>
                            <tbody class="[&_tr:last-child]:border-0">
                                <tr v-for="item in tesis.data" :key="item.id" class="border-b transition-colors hover:bg-muted/50">
                                    <td class="p-4 align-middle">
                                        <div class="flex items-center gap-3">
                                            <div class="h-9 w-9 rounded-full bg-primary/10 flex items-center justify-center text-sm font-bold text-primary">
                                                {{ item.autor?.name.charAt(0) || 'U' }}
                                            </div>
                                            <div>
                                                <div class="font-medium">{{ item.autor?.name }}</div>
                                                <div class="text-xs text-muted-foreground">{{ item.autor?.cedula || 'Sin cédula' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 align-middle">
                                        <div class="space-y-1">
                                            <p class="font-medium leading-snug max-w-[400px] line-clamp-2" :title="item.titulo">{{ item.titulo }}</p>
                                            <Badge variant="outline" class="font-normal text-[10px] text-muted-foreground">
                                                {{ item.carrera?.nombre }}
                                            </Badge>
                                        </div>
                                    </td>
                                    <td class="p-4 align-middle">
                                        <a v-if="item.ruta_archivo" :href="route('tesis.ver', item.id)" target="_blank" class="inline-flex items-center gap-1 text-sm font-medium text-blue-600 hover:text-blue-800">
                                            <FileText class="h-4 w-4" /> Ver PDF
                                        </a>
                                        <span v-else class="text-xs text-muted-foreground flex items-center gap-1">
                                            <AlertTriangle class="h-3 w-3" /> Sin archivo
                                        </span>
                                    </td>
                                    <td class="p-4 align-middle text-right">
                                        <div class="flex justify-end gap-2">
                                            <Button variant="ghost" size="sm" class="text-destructive hover:bg-destructive/10" @click="evaluar(item.id, 'rechazado', item.titulo)" :disabled="processingId === item.id">
                                                Rechazar
                                            </Button>
                                            <Button size="sm" class="bg-green-600 hover:bg-green-700 text-white" @click="evaluar(item.id, 'aprobado', item.titulo)" :disabled="processingId === item.id">
                                                <Loader2 v-if="processingId === item.id" class="h-4 w-4 animate-spin" />
                                                <span v-else>Aprobar</span>
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="tesis.data.length === 0">
                                    <td colspan="4" class="h-48 text-center text-muted-foreground">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <Search class="h-8 w-8 opacity-20" />
                                            <p>No se encontraron resultados para los filtros actuales.</p>
                                            <Button variant="link" @click="search = ''; carreraSeleccionada = ''">Limpiar búsqueda</Button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>
    </AppLayout>
</template>