<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { Head, router, usePage, Link } from '@inertiajs/vue3'; 
import { ref, watch, computed } from 'vue';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Search, BookOpen, FileText, Calendar, Filter, ChevronDown, X } from 'lucide-vue-next';
import debounce from 'lodash/debounce';
import { route } from 'ziggy-js';

// --- PROPS ---
const props = defineProps<{
    tesis: {
        data: Array<any>;
        links: Array<any>;
    };
    filters: {
        search: string;
        carrera_id: string;
        year: string;
    };
    carreras: Array<{ id: number; nombre: string }>;
}>();

const page = usePage();

// --- LAYOUT DINÁMICO ---
// Si hay usuario logueado -> AppLayout (Sidebar)
// Si es visitante -> PublicLayout (Header público)
const layout = computed(() => {
    return page.props.auth.user ? AppLayout : PublicLayout;
});

// --- ESTADO DE FILTROS ---
const search = ref(props.filters.search || '');
const carreraSeleccionada = ref(props.filters.carrera_id || '');
const anioSeleccionado = ref(props.filters.year || '');

// Generar últimos 10 años
const years = computed(() => {
    const currentYear = new Date().getFullYear();
    const yearsList = [];
    for (let i = 0; i <= 10; i++) {
        yearsList.push(currentYear - i);
    }
    return yearsList;
});

// --- LÓGICA DE BÚSQUEDA ---
const applyFilters = debounce(() => {
    router.get(route('tesis.index'), { 
        search: search.value, 
        carrera_id: carreraSeleccionada.value,
        year: anioSeleccionado.value
    }, { 
        preserveState: true, 
        replace: true, 
        preserveScroll: true 
    });
}, 300);

watch([search, carreraSeleccionada, anioSeleccionado], () => {
    applyFilters();
});

const clearFilters = () => {
    search.value = '';
    carreraSeleccionada.value = '';
    anioSeleccionado.value = '';
};

// --- UTILIDADES ---
const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' });
};

const getColorCarrera = (id: number) => {
    const colors = [
        'bg-blue-700 text-white border-blue-800 hover:bg-blue-800', 
        'bg-emerald-700 text-white border-emerald-800 hover:bg-emerald-800', 
        'bg-purple-700 text-white border-purple-800 hover:bg-purple-800', 
        'bg-orange-700 text-white border-orange-800 hover:bg-orange-800', 
        'bg-pink-700 text-white border-pink-800 hover:bg-pink-800'
    ];
    return colors[id % colors.length] || 'bg-slate-700 text-white border-slate-800';
};
</script>

<template>
    <Head title="Repositorio de Tesis" />

    <component :is="layout" :breadcrumbs="[{ title: 'Repositorio Académico', href: '/tesis' }]">
        
        <main class="flex flex-col gap-6 p-4 md:p-8 w-full max-w-full overflow-hidden animate-in fade-in duration-500 max-w-7xl mx-auto">
            
            <section 
                aria-label="Filtros de búsqueda" 
                role="search"
                class="flex flex-col gap-6 bg-sidebar-accent/20 p-6 rounded-xl border border-sidebar-border w-full shadow-sm"
            >
                <div class="space-y-1">
                    <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-foreground">Repositorio Digital</h1>
                    <p class="text-sm text-muted-foreground">Explora, filtra y descarga las investigaciones aprobadas por la institución.</p>
                </div>
                
                <div class="flex flex-col md:flex-row gap-4 w-full md:items-end">
                    
                    <div class="w-full md:flex-1 space-y-2">
                        <label for="search-input" class="text-xs font-medium text-muted-foreground ml-1">
                            Buscar por título
                        </label>
                        <div class="relative">
                            <Search class="absolute left-3 top-3 h-4 w-4 text-muted-foreground pointer-events-none" aria-hidden="true" />
                            <Input 
                                id="search-input"
                                v-model="search" 
                                placeholder="Escribe para buscar..." 
                                class="pl-9 h-11 bg-background focus-visible:ring-2"
                                type="search"
                            />
                            <button v-if="search" @click="search = ''" class="absolute right-3 top-3 text-muted-foreground hover:text-foreground">
                                <X class="h-4 w-4" />
                            </button>
                        </div>
                    </div>

                    <div class="w-full md:w-[280px] space-y-2">
                        <label for="carrera-select" class="text-xs font-medium text-muted-foreground ml-1">
                            Carrera
                        </label>
                        <div class="relative">
                            <Filter class="absolute left-3 top-3 h-4 w-4 text-muted-foreground z-10 pointer-events-none" aria-hidden="true" />
                            <select 
                                id="carrera-select"
                                v-model="carreraSeleccionada"
                                class="flex h-11 w-full items-center justify-between rounded-md border border-input bg-background pl-9 pr-8 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 appearance-none text-foreground truncate cursor-pointer"
                            >
                                <option value="">Todas las carreras</option>
                                <option v-for="carrera in props.carreras" :key="carrera.id" :value="carrera.id">
                                    {{ carrera.nombre }}
                                </option>
                            </select>
                            <ChevronDown class="absolute right-3 top-3 h-4 w-4 text-muted-foreground pointer-events-none" />
                        </div>
                    </div>

                    <div class="w-full md:w-[150px] space-y-2">
                        <label for="year-select" class="text-xs font-medium text-muted-foreground ml-1">
                            Año Publicación
                        </label>
                        <div class="relative">
                            <Calendar class="absolute left-3 top-3 h-4 w-4 text-muted-foreground z-10 pointer-events-none" aria-hidden="true" />
                            <select 
                                id="year-select"
                                v-model="anioSeleccionado"
                                class="flex h-11 w-full items-center justify-between rounded-md border border-input bg-background pl-9 pr-8 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 appearance-none text-foreground truncate cursor-pointer"
                            >
                                <option value="">Todos</option>
                                <option v-for="year in years" :key="year" :value="year">
                                    {{ year }}
                                </option>
                            </select>
                            <ChevronDown class="absolute right-3 top-3 h-4 w-4 text-muted-foreground pointer-events-none" />
                        </div>
                    </div>

                </div>
            </section>

            <section aria-label="Resultados de la búsqueda">
                <ul v-if="tesis.data.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 w-full list-none p-0 m-0">
                    <li v-for="item in tesis.data" :key="item.id" class="h-full">
                        <article class="h-full">
                            <Card class="flex flex-col h-full hover:shadow-lg transition-all duration-200 hover:-translate-y-1 dark:hover:border-primary/50 overflow-hidden group border-muted">
                                
                                <CardHeader class="p-5 pb-3 space-y-3">
                                    <div class="flex justify-between items-start gap-2">
                                        <Badge :class="getColorCarrera(item.carrera_id)" class="px-2.5 py-1 text-[11px] font-semibold shadow-sm truncate max-w-[90%] block border-0">
                                            {{ item.carrera?.nombre || 'General' }}
                                        </Badge>
                                    </div>
                                    
                                    <CardTitle class="text-lg font-bold leading-tight group-hover:text-primary transition-colors line-clamp-3 min-h-[3.5rem]">
                                        <Link :href="route('tesis.show', item.id)" class="focus-visible:underline outline-none">
                                            {{ item.titulo }}
                                        </Link>
                                    </CardTitle>
                                    
                                    <div class="flex items-center gap-2 text-xs text-muted-foreground">
                                        <Calendar class="h-3.5 w-3.5" aria-hidden="true" />
                                        <time :datetime="item.created_at">{{ formatDate(item.created_at) }}</time>
                                    </div>
                                </CardHeader>
                                
                                <CardContent class="flex-grow p-5 pt-0">
                                    <p class="text-sm text-muted-foreground line-clamp-3 mb-4 h-[4.5em]">
                                        {{ item.resumen }}
                                    </p>
                                    
                                    <div class="flex items-center gap-3 pt-4 border-t mt-auto">
                                        <div class="h-8 w-8 rounded-full bg-primary/10 flex items-center justify-center text-xs font-bold text-primary flex-shrink-0" aria-hidden="true">
                                            {{ item.autor?.name.charAt(0) }}
                                        </div>
                                        <div class="flex flex-col min-w-0">
                                            <span class="text-sm font-medium truncate w-full text-foreground">{{ item.autor?.name }}</span>
                                            <span class="text-xs text-muted-foreground">Autor(a)</span>
                                        </div>
                                    </div>
                                </CardContent>

                                <CardFooter class="p-5 pt-0 mt-auto">
                                    <Link :href="route('tesis.show', item.id)" class="w-full" tabindex="-1"> 
                                        <Button class="w-full" variant="outline" :aria-hidden="true">
                                            <BookOpen class="mr-2 h-4 w-4" />
                                            Ver Detalles
                                        </Button>
                                    </Link>
                                </CardFooter>
                            </Card>
                        </article>
                    </li>
                </ul>

                <div v-else class="flex flex-col items-center justify-center py-16 text-center w-full border-2 border-dashed rounded-xl bg-muted/10 animate-in zoom-in-95">
                    <div class="bg-muted/50 p-6 rounded-full mb-4">
                        <FileText class="h-12 w-12 text-muted-foreground/50" aria-hidden="true" />
                    </div>
                    <h3 class="text-lg font-semibold text-foreground">No se encontraron tesis</h3>
                    <p class="text-muted-foreground max-w-sm mt-2 px-4 text-sm">
                        No hay resultados para "{{ search }}" con los filtros seleccionados.
                    </p>
                    <Button variant="link" class="mt-4 text-primary" @click="clearFilters">
                        Limpiar todos los filtros
                    </Button>
                </div>
            </section>

        </main>
    </component>
</template>