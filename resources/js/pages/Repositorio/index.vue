<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, router, usePage, Link } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Search, BookOpen, FileText, Calendar } from 'lucide-vue-next';
import debounce from 'lodash/debounce';
import { route } from 'ziggy-js';

const props = defineProps<{
    tesis: {
        data: Array<any>;
        links: Array<any>;
    };
    filters: {
        search: string;
        carrera_id: string;
    };
}>();

const page = usePage();
// @ts-ignore
const carreras = (page.props.carreras || []) as Array<{ id: number; nombre: string }>;

const search = ref(props.filters.search || '');
const carreraSeleccionada = ref(props.filters.carrera_id || '');

const updateSearch = debounce((value) => {
    router.get(route('tesis.index'), {
        search: value,
        carrera_id: carreraSeleccionada.value
    }, {
        preserveState: true,
        replace: true
    });
}, 300);

watch(search, (value) => updateSearch(value));

watch(carreraSeleccionada, (value) => {
    router.get(route('tesis.index'), {
        search: search.value,
        carrera_id: value
    }, {
        preserveState: true
    });
});

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' });
};

const getColorCarrera = (id: number) => {
    const colors = ['bg-blue-500', 'bg-green-500', 'bg-purple-500', 'bg-orange-500', 'bg-pink-500'];
    return colors[id % colors.length] || 'bg-gray-500';
};
</script>

<template>
    <Head title="Repositorio de Tesis" />

    <AppLayout :breadcrumbs="[{ title: 'Repositorio Académico', href: '/tesis' }]">
        <div class="flex flex-col gap-6 p-4">

            <!-- Header y filtros -->
            <div class="flex flex-col md:flex-row gap-4 justify-between items-end md:items-center bg-sidebar-accent/20 p-6 rounded-xl border border-sidebar-border">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Repositorio Digital</h1>
                    <p class="text-muted-foreground">Explora las investigaciones aprobadas de nuestra comunidad.</p>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                    <!-- Buscador -->
                    <div class="relative w-full md:w-64">
                        <Search class="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
                        <Input
                            v-model="search"
                            placeholder="Buscar por título..."
                            class="pl-8 bg-background"
                        />
                    </div>

                    <!-- Filtro por Carrera -->
                    <div class="relative w-full md:w-64">
                        <select
                            v-model="carreraSeleccionada"
                            class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 appearance-none dark:bg-gray-950"
                        >
                            <option value="">Todas las carreras</option>
                            <option v-for="carrera in carreras" :key="carrera.id" :value="carrera.id">
                                {{ carrera.nombre }}
                            </option>
                        </select>
                         <span class="absolute right-3 top-3 pointer-events-none text-muted-foreground">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down"><path d="m6 9 6 6 6-6"/></svg>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Tesis -->
            <div v-if="tesis.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <Card v-for="item in tesis.data" :key="item.id" class="flex flex-col hover:shadow-lg transition-shadow duration-200 dark:hover:border-primary/50">
                    <CardHeader>
                        <div class="flex justify-between items-start gap-2">
                            <Badge :class="getColorCarrera(item.carrera_id)" class="hover:opacity-90">
                                {{ item.carrera?.nombre || 'General' }}
                            </Badge>
                        </div>
                        <CardTitle class="text-lg leading-tight mt-2 line-clamp-2" :title="item.titulo">
                            {{ item.titulo }}
                        </CardTitle>
                        <div class="flex items-center gap-2 text-xs text-muted-foreground mt-1">
                            <Calendar class="h-3 w-3" />
                            <span>{{ formatDate(item.created_at) }}</span>
                        </div>
                    </CardHeader>

                    <CardContent class="flex-grow">
                        <p class="text-sm text-muted-foreground line-clamp-3">
                            {{ item.resumen }}
                        </p>
                        <div class="mt-4 flex items-center gap-2">
                            <div class="h-6 w-6 rounded-full bg-primary/10 flex items-center justify-center text-xs font-bold text-primary">
                                {{ item.autor?.name.charAt(0) }}
                            </div>
                            <span class="text-sm font-medium">{{ item.autor?.name }}</span>
                        </div>
                    </CardContent>

                    <CardFooter class="pt-0">
                        <Link :href="route('tesis.show', item.id)" class="w-full">
                            <Button class="w-full" variant="outline">
                                <BookOpen class="mr-2 h-4 w-4" />
                                Ver Detalles
                            </Button>
                        </Link>
                    </CardFooter>
                </Card>
            </div>

            <!-- Estado vacio (por si no hay resultados) -->
            <div v-else class="flex flex-col items-center justify-center py-12 text-center">
                <div class="bg-muted/50 p-6 rounded-full mb-4">
                    <FileText class="h-12 w-12 text-muted-foreground" />
                </div>
                <h3 class="text-lg font-semibold">No se encontraron tesis</h3>
                <p class="text-muted-foreground max-w-sm mt-2">
                    Intenta ajustar los filtros de búsqueda o selecciona otra carrera.
                </p>
                <Button
                    variant="link"
                    class="mt-4"
                    @click="search = ''; carreraSeleccionada = ''"
                >
                    Limpiar filtros
                </Button>
            </div>

        </div>
    </AppLayout>
</template>
