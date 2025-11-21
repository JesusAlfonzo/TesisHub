<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import {
    Users,
    FileText,
    CheckCircle,
    Clock,
    GraduationCap,
    BookOpen
} from 'lucide-vue-next';

// Recibimos las estadísticas del controlador
defineProps<{
    stats: Record<string, number>;
}>();

const page = usePage();
// @ts-ignore
const user = page.props.auth.user;
// @ts-ignore
const roles = page.props.auth.roles || [];

const isAdmin = roles.includes('super-admin') || roles.includes('coordinador');
const isTutor = roles.includes('tutor');
const isStudent = roles.includes('estudiante');
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="[{ title: 'Inicio', href: '/dashboard' }]">
        <div class="flex flex-col gap-6 p-4">

            <!-- Bienvenida -->
            <div class="bg-sidebar-accent/20 p-6 rounded-xl border border-sidebar-border">
                <h1 class="text-2xl font-bold tracking-tight">¡Hola, {{ user.name }}! 👋</h1>
                <p class="text-muted-foreground mt-1">
                    Bienvenido al Sistema de Gestión de Tesis del IUJO. Aquí tienes un resumen de la actividad.
                </p>
            </div>

            <!-- VISTA ADMIN / COORDINADOR -->
            <div v-if="isAdmin" class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">

                <!-- Total Tesis -->
                <div class="rounded-xl border bg-card text-card-foreground shadow p-6">
                    <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <h3 class="tracking-tight text-sm font-medium text-muted-foreground">Total Investigaciones</h3>
                        <FileText class="h-4 w-4 text-muted-foreground" />
                    </div>
                    <div class="text-2xl font-bold">{{ stats.total_tesis }}</div>
                    <p class="text-xs text-muted-foreground">Documentos registrados</p>
                </div>

                <!-- Usuarios -->
                <div class="rounded-xl border bg-card text-card-foreground shadow p-6">
                    <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <h3 class="tracking-tight text-sm font-medium text-muted-foreground">Usuarios Totales</h3>
                        <Users class="h-4 w-4 text-muted-foreground" />
                    </div>
                    <div class="text-2xl font-bold">{{ stats.total_usuarios }}</div>
                    <div class="flex gap-2 mt-1">
                        <span class="text-xs bg-blue-100 text-blue-800 px-1.5 py-0.5 rounded">{{ stats.estudiantes }} Est.</span>
                        <span class="text-xs bg-purple-100 text-purple-800 px-1.5 py-0.5 rounded">{{ stats.tutores }} Tut.</span>
                    </div>
                </div>

                <!-- Aprobadas -->
                <div class="rounded-xl border bg-card text-card-foreground shadow p-6">
                    <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <h3 class="tracking-tight text-sm font-medium text-muted-foreground">Publicadas</h3>
                        <CheckCircle class="h-4 w-4 text-green-600" />
                    </div>
                    <div class="text-2xl font-bold text-green-600">{{ stats.tesis_aprobadas }}</div>
                    <p class="text-xs text-muted-foreground">Disponibles en repositorio</p>
                </div>

                <!-- Pendientes -->
                <div class="rounded-xl border bg-card text-card-foreground shadow p-6">
                    <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <h3 class="tracking-tight text-sm font-medium text-muted-foreground">Por Revisar</h3>
                        <Clock class="h-4 w-4 text-orange-500" />
                    </div>
                    <div class="text-2xl font-bold text-orange-500">{{ stats.tesis_pendientes }}</div>
                    <p class="text-xs text-muted-foreground">Requieren atención</p>
                </div>
            </div>

            <!-- VISTA TUTOR -->
            <div v-else-if="isTutor" class="grid gap-4 md:grid-cols-3">
                <div class="rounded-xl border bg-card text-card-foreground shadow p-6 bg-orange-50 dark:bg-orange-900/20 border-orange-200">
                    <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <h3 class="tracking-tight text-sm font-medium text-orange-700 dark:text-orange-400">Pendientes de Revisión</h3>
                        <Clock class="h-4 w-4 text-orange-600" />
                    </div>
                    <div class="text-2xl font-bold text-orange-700 dark:text-orange-400">{{ stats.asignadas }}</div>
                    <p class="text-xs text-orange-600/80">Tesis esperando tu dictamen</p>
                </div>
            </div>

            <!-- VISTA ESTUDIANTE -->
            <div v-else-if="isStudent" class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <div class="rounded-xl border bg-card text-card-foreground shadow p-6">
                    <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <h3 class="tracking-tight text-sm font-medium text-muted-foreground">Mis Entregas</h3>
                        <GraduationCap class="h-4 w-4 text-blue-500" />
                    </div>
                    <div class="text-2xl font-bold">{{ stats.mis_tesis }}</div>
                    <p class="text-xs text-muted-foreground">Proyectos cargados</p>
                </div>

                <div class="rounded-xl border bg-card text-card-foreground shadow p-6">
                    <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <h3 class="tracking-tight text-sm font-medium text-muted-foreground">Estado Actual</h3>
                        <BookOpen class="h-4 w-4 text-muted-foreground" />
                    </div>
                    <div class="text-2xl font-bold">
                        {{ stats.aprobadas > 0 ? 'Aprobado 🎉' : (stats.mis_tesis > 0 ? 'En Revisión' : 'Sin Entregas') }}
                    </div>
                </div>
            </div>

            <!-- Banner informativo -->
            <div class="min-h-[200px] rounded-xl border border-sidebar-border/50 bg-sidebar-accent/10 p-6 flex items-center justify-center text-muted-foreground">
                <div class="text-center">
                    <p class="text-sm">Sistema de Gestión de Trabajos de Grado</p>
                    <p class="text-xs mt-1">Instituto Universitario Jesús Obrero (IUJO)</p>
                </div>
            </div>

        </div>
    </AppLayout>
</template>
