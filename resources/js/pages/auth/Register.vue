<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
// Usamos solo componentes básicos que sabemos que tienes (Input, Button)
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
// Iconos
import { BookOpen, Calendar, FileText, Search } from 'lucide-vue-next';

// Props que vienen del Controller
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

// Accedemos a las carreras globales de forma segura
const page = usePage();
// @ts-ignore
const carreras = (page.props.carreras || []) as Array<{
    id: number;
    nombre: string;
}>;

// Estado reactivo para los filtros
const search = ref(props.filters.search || '');
const carreraSeleccionada = ref(props.filters.carrera_id || '');

// Función Debounce manual (para no depender de lodash)
// Esto evita que se haga una petición por cada letra que escribes
let timeout: ReturnType<typeof setTimeout>;

const updateSearch = (value: string) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get(
            route('tesis.index'),
            {
                search: value,
                carrera_id: carreraSeleccionada.value,
            },
            {
                preserveState: true,
                replace: true,
            },
        );
    }, 400); // 400ms de espera
};

// Watchers
watch(search, (value) => updateSearch(value));

watch(carreraSeleccionada, (value) => {
    router.get(
        route('tesis.index'),
        {
            search: search.value,
            carrera_id: value,
        },
        {
            preserveState: true,
        },
    );
});

// Helper para formato de fecha
const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

// Helper para colores de badges (Etiquetas)
const getColorCarrera = (id: number) => {
    const colors = [
        'bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-900 dark:text-blue-300 dark:border-blue-800',
        'bg-green-100 text-green-800 border-green-200 dark:bg-green-900 dark:text-green-300 dark:border-green-800',
        'bg-purple-100 text-purple-800 border-purple-200 dark:bg-purple-900 dark:text-purple-300 dark:border-purple-800',
        'bg-orange-100 text-orange-800 border-orange-200 dark:bg-orange-900 dark:text-orange-300 dark:border-orange-800',
        'bg-pink-100 text-pink-800 border-pink-200 dark:bg-pink-900 dark:text-pink-300 dark:border-pink-800',
    ];
    return (
        colors[id % colors.length] ||
        'bg-gray-100 text-gray-800 border-gray-200'
    );
};
</script>

<template>
    <Head title="Repositorio de Tesis" />

    <AppLayout
        :breadcrumbs="[{ title: 'Repositorio Académico', href: '/tesis' }]"
    >
        <div class="flex flex-col gap-6 p-4">
            <!-- HEADER Y FILTROS -->
            <div
                class="flex flex-col items-end justify-between gap-4 rounded-xl border border-sidebar-border bg-sidebar-accent/20 p-6 md:flex-row md:items-center"
            >
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">
                        Repositorio Digital
                    </h1>
                    <p class="text-muted-foreground">
                        Explora las investigaciones aprobadas de nuestra
                        comunidad.
                    </p>
                </div>

                <div class="flex w-full flex-col gap-3 sm:flex-row md:w-auto">
                    <!-- Buscador -->
                    <div class="relative w-full md:w-64">
                        <Search
                            class="absolute top-2.5 left-2 h-4 w-4 text-muted-foreground"
                        />
                        <Input
                            v-model="search"
                            placeholder="Buscar por título..."
                            class="bg-background pl-8"
                        />
                    </div>

                    <!-- Filtro Carrera -->
                    <div class="relative w-full md:w-64">
                        <select
                            v-model="carreraSeleccionada"
                            class="flex h-10 w-full appearance-none items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:ring-2 focus:ring-ring focus:ring-offset-2 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50 dark:bg-gray-950"
                        >
                            <option value="">Todas las carreras</option>
                            <option
                                v-for="carrera in carreras"
                                :key="carrera.id"
                                :value="carrera.id"
                                class="dark:bg-gray-900"
                            >
                                {{ carrera.nombre }}
                            </option>
                        </select>
                        <span
                            class="pointer-events-none absolute top-3 right-3 text-muted-foreground"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="16"
                                height="16"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                class="lucide lucide-chevron-down"
                            >
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </span>
                    </div>
                </div>
            </div>

            <!-- GRID DE TESIS (Usando Cards Tailwind Nativas) -->
            <div
                v-if="tesis.data.length > 0"
                class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3"
            >
                <div
                    v-for="item in tesis.data"
                    :key="item.id"
                    class="flex flex-col rounded-xl border bg-card text-card-foreground shadow transition-shadow duration-200 hover:shadow-lg dark:border-sidebar-border dark:bg-sidebar-accent/10"
                >
                    <!-- Card Header -->
                    <div class="flex flex-col space-y-1.5 p-6">
                        <div
                            class="mb-2 flex items-start justify-between gap-2"
                        >
                            <!-- Badge Manual -->
                            <span
                                class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:ring-2 focus:ring-ring focus:ring-offset-2 focus:outline-none"
                                :class="getColorCarrera(item.carrera_id)"
                            >
                                {{ item.carrera?.nombre || 'General' }}
                            </span>
                        </div>
                        <h3
                            class="line-clamp-2 leading-none font-semibold tracking-tight"
                            :title="item.titulo"
                        >
                            {{ item.titulo }}
                        </h3>
                        <div
                            class="mt-2 flex items-center gap-2 text-xs text-muted-foreground"
                        >
                            <Calendar class="h-3 w-3" />
                            <span>{{ formatDate(item.created_at) }}</span>
                        </div>
                    </div>

                    <!-- Card Content -->
                    <div class="flex-grow p-6 pt-0">
                        <p class="line-clamp-3 text-sm text-muted-foreground">
                            {{ item.resumen }}
                        </p>
                        <div class="mt-4 flex items-center gap-2">
                            <div
                                class="flex h-6 w-6 items-center justify-center rounded-full bg-primary/10 text-xs font-bold text-primary"
                            >
                                {{ item.autor?.name.charAt(0) }}
                            </div>
                            <span class="text-sm font-medium">{{
                                item.autor?.name
                            }}</span>
                        </div>
                    </div>

                    <!-- Card Footer -->
                    <div class="flex items-center p-6 pt-0">
                        <Button class="w-full" variant="outline">
                            <BookOpen class="mr-2 h-4 w-4" />
                            Ver Detalles
                        </Button>
                    </div>
                </div>
            </div>

            <!-- EMPTY STATE -->
            <div
                v-else
                class="flex flex-col items-center justify-center py-12 text-center"
            >
                <div class="mb-4 rounded-full bg-muted/50 p-6">
                    <FileText class="h-12 w-12 text-muted-foreground" />
                </div>
                <h3 class="text-lg font-semibold">No se encontraron tesis</h3>
                <p class="mt-2 max-w-sm text-muted-foreground">
                    Intenta ajustar los filtros de búsqueda o selecciona otra
                    carrera.
                </p>
                <Button
                    variant="link"
                    class="mt-4"
                    @click="
                        search = '';
                        carreraSeleccionada = '';
                    "
                >
                    Limpiar filtros
                </Button>
            </div>
        </div>
    </AppLayout>
</template>
