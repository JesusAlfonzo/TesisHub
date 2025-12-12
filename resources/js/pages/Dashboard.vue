<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, usePage, Link } from '@inertiajs/vue3';
import {
    Users,
    FileText,
    CheckCircle,
    Clock,
    GraduationCap,
    BookOpen,
    Plus,
    Search,
    ListChecks,
    Activity
} from 'lucide-vue-next';
import { computed } from 'vue';

interface User {
    name: string;
}

interface Stats {
    // Admin
    total_usuarios?: number;
    total_tesis?: number;
    tesis_aprobadas?: number;
    tesis_pendientes?: number;
    estudiantes?: number;
    tutores?: number;
    // Tutor
    asignadas?: number;
    evaluadas?: number;
    // Estudiante
    mis_tesis?: number;
    aprobadas?: number;
}

const props = defineProps<{
    stats: Stats;
}>();

const page = usePage();
const user = computed(() => page.props.auth.user as User);
// @ts-ignore (Inertia a veces complica los tipos de roles globales, lo mantenemos controlado aquí)
const roles = (page.props.auth.roles || []) as string[];

// Computed properties para lógica de roles
const isAdmin = computed(() => roles.includes('super-admin') || roles.includes('coordinador'));
const isTutor = computed(() => roles.includes('tutor'));
const isStudent = computed(() => roles.includes('estudiante'));

// Lógica de estado del estudiante
const studentStatus = computed(() => {
    if ((props.stats.aprobadas || 0) > 0) return { label: 'Aprobado', color: 'text-green-600', bg: 'bg-green-100', icon: CheckCircle };
    if ((props.stats.mis_tesis || 0) > 0) return { label: 'En Revisión', color: 'text-orange-600', bg: 'bg-orange-100', icon: Clock };
    return { label: 'Sin Entregas', color: 'text-muted-foreground', bg: 'bg-muted', icon: FileText };
});
</script>

<template>
    <Head title="Panel de Control" />

    <AppLayout :breadcrumbs="[{ title: 'Inicio', href: '/dashboard' }]">
        
        <main class="flex flex-col gap-8 p-6 max-w-7xl mx-auto w-full animate-in fade-in duration-500">

            <header class="bg-gradient-to-r from-sidebar-accent/50 to-background p-6 rounded-xl border border-sidebar-border shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                        <Activity class="h-6 w-6" aria-hidden="true" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-foreground">
                            ¡Hola, {{ user.name }}! <span class="inline-block animate-wave origin-[70%_70%]">👋</span>
                        </h1>
                        <p class="text-muted-foreground mt-1">
                            Bienvenido al Sistema de Gestión de Tesis del IUJO.
                        </p>
                    </div>
                </div>
            </header>

            <section aria-label="Resumen de actividad">
                <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                    <Activity class="h-5 w-5 text-muted-foreground" aria-hidden="true" />
                    Resumen General
                </h2>

                <div v-if="isAdmin" class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <article class="rounded-xl border bg-card text-card-foreground shadow-sm p-6 hover:shadow-md transition-shadow">
                        <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <h3 class="tracking-tight text-sm font-medium text-muted-foreground">Total Investigaciones</h3>
                            <FileText class="h-4 w-4 text-muted-foreground" aria-hidden="true" />
                        </div>
                        <div class="text-3xl font-bold text-foreground">{{ stats.total_tesis || 0 }}</div>
                        <p class="text-xs text-muted-foreground mt-1">Documentos registrados</p>
                    </article>

                    <article class="rounded-xl border bg-card text-card-foreground shadow-sm p-6 hover:shadow-md transition-shadow">
                        <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <h3 class="tracking-tight text-sm font-medium text-muted-foreground">Usuarios Totales</h3>
                            <Users class="h-4 w-4 text-muted-foreground" aria-hidden="true" />
                        </div>
                        <div class="text-3xl font-bold text-foreground">{{ stats.total_usuarios || 0 }}</div>
                        <div class="flex gap-2 mt-2">
                            <span class="text-[10px] font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 px-2 py-0.5 rounded-full">
                                {{ stats.estudiantes || 0 }} Est.
                            </span>
                            <span class="text-[10px] font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300 px-2 py-0.5 rounded-full">
                                {{ stats.tutores || 0 }} Tut.
                            </span>
                        </div>
                    </article>

                    <article class="rounded-xl border bg-card text-card-foreground shadow-sm p-6 hover:shadow-md transition-shadow border-l-4 border-l-green-500">
                        <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <h3 class="tracking-tight text-sm font-medium text-muted-foreground">Publicadas</h3>
                            <CheckCircle class="h-4 w-4 text-green-600" aria-hidden="true" />
                        </div>
                        <div class="text-3xl font-bold text-green-600">{{ stats.tesis_aprobadas || 0 }}</div>
                        <p class="text-xs text-muted-foreground mt-1">Disponibles en repositorio</p>
                    </article>

                    <article class="rounded-xl border bg-card text-card-foreground shadow-sm p-6 hover:shadow-md transition-shadow border-l-4 border-l-orange-500">
                        <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <h3 class="tracking-tight text-sm font-medium text-muted-foreground">Por Revisar</h3>
                            <Clock class="h-4 w-4 text-orange-500" aria-hidden="true" />
                        </div>
                        <div class="text-3xl font-bold text-orange-500">{{ stats.tesis_pendientes || 0 }}</div>
                        <p class="text-xs text-muted-foreground mt-1">Requieren atención</p>
                    </article>
                </div>

                <div v-else-if="isTutor" class="grid gap-4 md:grid-cols-2">
                    <article class="rounded-xl border text-card-foreground shadow-sm p-6 border-l-4 border-l-orange-500 bg-orange-50/50 dark:bg-orange-900/10">
                        <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <h3 class="tracking-tight text-sm font-medium text-foreground">Pendientes de Revisión</h3>
                            <Clock class="h-4 w-4 text-orange-600" aria-hidden="true" />
                        </div>
                        <div class="text-3xl font-bold text-orange-700 dark:text-orange-400">{{ stats.asignadas || 0 }}</div>
                        <p class="text-xs text-muted-foreground mt-1">Tesis esperando tu dictamen</p>
                    </article>

                    <article class="rounded-xl border bg-card text-card-foreground shadow-sm p-6 border-l-4 border-l-green-500">
                        <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <h3 class="tracking-tight text-sm font-medium text-muted-foreground">Tesis Evaluadas</h3>
                            <CheckCircle class="h-4 w-4 text-green-600" aria-hidden="true" />
                        </div>
                        <div class="text-3xl font-bold text-foreground">{{ stats.evaluadas || 0 }}</div>
                        <p class="text-xs text-muted-foreground mt-1">Historial de correcciones</p>
                    </article>
                </div>

                <div v-else-if="isStudent" class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <article class="rounded-xl border bg-card text-card-foreground shadow-sm p-6">
                        <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <h3 class="tracking-tight text-sm font-medium text-muted-foreground">Mis Entregas</h3>
                            <GraduationCap class="h-4 w-4 text-primary" aria-hidden="true" />
                        </div>
                        <div class="text-3xl font-bold text-foreground">{{ stats.mis_tesis || 0 }}</div>
                        <p class="text-xs text-muted-foreground mt-1">Proyectos cargados</p>
                    </article>

                    <article class="rounded-xl border bg-card text-card-foreground shadow-sm p-6">
                        <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <h3 class="tracking-tight text-sm font-medium text-muted-foreground">Estado Actual</h3>
                            <component :is="studentStatus.icon" class="h-4 w-4" :class="studentStatus.color" aria-hidden="true" />
                        </div>
                        <div class="text-2xl font-bold" :class="studentStatus.color">
                            {{ studentStatus.label }}
                        </div>
                        <p class="text-xs text-muted-foreground mt-1">Estatus del último proyecto</p>
                    </article>
                </div>
            </section>

            <section aria-label="Accesos rápidos">
                <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                    <ListChecks class="h-5 w-5 text-muted-foreground" aria-hidden="true" />
                    Acciones Rápidas
                </h2>

                <div class="grid gap-4 md:grid-cols-3">
                    
                    <template v-if="isStudent">
                        <Link href="/mis-tesis/create" class="group relative flex items-center gap-4 p-4 rounded-xl border hover:border-primary/50 bg-card hover:bg-accent transition-all duration-200">
                            <div class="h-10 w-10 rounded-lg bg-primary/10 flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-colors">
                                <Plus class="h-5 w-5" />
                            </div>
                            <div>
                                <h3 class="font-medium text-sm">Nueva Entrega</h3>
                                <p class="text-xs text-muted-foreground">Sube un nuevo proyecto</p>
                            </div>
                        </Link>
                        <Link href="/mis-tesis" class="group relative flex items-center gap-4 p-4 rounded-xl border hover:border-primary/50 bg-card hover:bg-accent transition-all duration-200">
                            <div class="h-10 w-10 rounded-lg bg-primary/10 flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-colors">
                                <ListChecks class="h-5 w-5" />
                            </div>
                            <div>
                                <h3 class="font-medium text-sm">Mis Proyectos</h3>
                                <p class="text-xs text-muted-foreground">Ver historial y estatus</p>
                            </div>
                        </Link>
                    </template>

                    <template v-if="isAdmin || isTutor">
                        <Link :href="route('tesis.index')" class="group relative flex items-center gap-4 p-4 rounded-xl border hover:border-primary/50 bg-card hover:bg-accent transition-all duration-200">
                            <div class="h-10 w-10 rounded-lg bg-primary/10 flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-colors">
                                <Search class="h-5 w-5" />
                            </div>
                            <div>
                                <h3 class="font-medium text-sm">Explorar Repositorio</h3>
                                <p class="text-xs text-muted-foreground">Ver todas las tesis</p>
                            </div>
                        </Link>
                    </template>
                    
                    <template v-if="isAdmin">
                         <Link href="#" class="group relative flex items-center gap-4 p-4 rounded-xl border hover:border-primary/50 bg-card hover:bg-accent transition-all duration-200 opacity-60 cursor-not-allowed" aria-disabled="true">
                            <div class="h-10 w-10 rounded-lg bg-primary/10 flex items-center justify-center">
                                <Users class="h-5 w-5" />
                            </div>
                            <div>
                                <h3 class="font-medium text-sm">Gestionar Usuarios</h3>
                                <p class="text-xs text-muted-foreground">Administrar roles (Pronto)</p>
                            </div>
                        </Link>
                    </template>

                </div>
            </section>

            <footer class="mt-auto border-t pt-6 text-center text-sm text-muted-foreground">
                <p>Sistema de Gestión de Trabajos de Grado • IUJO</p>
            </footer>

        </main>
    </AppLayout>
</template>

<style scoped>
@keyframes wave {
  0% { transform: rotate(0deg); }
  10% { transform: rotate(14deg); }
  20% { transform: rotate(-8deg); }
  30% { transform: rotate(14deg); }
  40% { transform: rotate(-4deg); }
  50% { transform: rotate(10deg); }
  60% { transform: rotate(0deg); }
  100% { transform: rotate(0deg); }
}

.animate-wave {
  animation: wave 2.5s infinite;
  display: inline-block;
}
</style>