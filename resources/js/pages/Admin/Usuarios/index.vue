<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, Link } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Search, UserCog, Ban, CheckCircle } from 'lucide-vue-next';
// @ts-ignore
const route = window.route;

const props = defineProps<{
    users: { data: Array<any>, links: Array<any> };
    filters: { search: string };
}>();

const search = ref(props.filters.search || '');

// Búsqueda manual con timeout
let timeout: ReturnType<typeof setTimeout>;
const updateSearch = (value: string) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get(route('users.index'), { search: value }, { preserveState: true, replace: true });
    }, 300);
};

watch(search, (val) => updateSearch(val));

// Función para cambiar estado
const toggleUser = (user: any) => {
    const action = user.is_active ? 'desactivar' : 'activar';
    if (confirm(`¿Estás seguro de ${action} al usuario ${user.name}?`)) {
        router.patch(route('users.toggle', user.id));
    }
};

// Helper para roles
const getRoleBadge = (roles: any[]) => {
    if (!roles.length) return 'bg-gray-100 text-gray-800';
    const role = roles[0].name;
    switch (role) {
        case 'super-admin': return 'bg-purple-100 text-purple-800 border-purple-200';
        case 'coordinador': return 'bg-indigo-100 text-indigo-800 border-indigo-200';
        case 'tutor': return 'bg-blue-100 text-blue-800 border-blue-200';
        default: return 'bg-green-100 text-green-800 border-green-200'; // Estudiante
    }
};
</script>

<template>
    <Head title="Gestión de Usuarios" />

    <AppLayout :breadcrumbs="[{ title: 'Administración', href: '#' }, { title: 'Usuarios', href: '/admin/usuarios' }]">
        <div class="flex flex-col gap-6 p-4">

            <!-- Header y Buscador -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Gestión de Usuarios</h1>
                    <p class="text-muted-foreground">Administra el acceso y roles de la plataforma.</p>
                </div>
                <div class="relative w-full sm:w-72">
                    <Search class="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
                    <Input
                        v-model="search"
                        placeholder="Buscar por nombre, cédula o email..."
                        class="pl-8"
                    />
                </div>
            </div>

            <!-- Tabla de Usuarios -->
            <div class="rounded-md border bg-card">
                <div class="relative w-full overflow-auto">
                    <table class="w-full caption-bottom text-sm">
                        <thead class="[&_tr]:border-b">
                            <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                                <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Usuario</th>
                                <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Rol</th>
                                <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Carrera</th>
                                <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Estado</th>
                                <th class="h-12 px-4 text-right align-middle font-medium text-muted-foreground">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="[&_tr:last-child]:border-0">
                            <tr v-for="user in users.data" :key="user.id" class="border-b transition-colors hover:bg-muted/50">
                                <!-- Datos Usuario -->
                                <td class="p-4 align-middle">
                                    <div class="font-medium">{{ user.name }}</div>
                                    <div class="text-xs text-muted-foreground">{{ user.email }}</div>
                                    <div class="text-xs text-muted-foreground font-mono mt-0.5">V-{{ user.cedula || 'N/A' }}</div>
                                </td>

                                <!-- Rol -->
                                <td class="p-4 align-middle">
                                    <span
                                        class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none uppercase"
                                        :class="getRoleBadge(user.roles)"
                                    >
                                        {{ user.roles[0]?.name || 'Sin Rol' }}
                                    </span>
                                </td>

                                <!-- Carrera -->
                                <td class="p-4 align-middle text-muted-foreground">
                                    {{ user.carrera?.nombre || '—' }}
                                </td>

                                <!-- Estado -->
                                <td class="p-4 align-middle">
                                    <div class="flex items-center gap-2">
                                        <div class="h-2.5 w-2.5 rounded-full" :class="user.is_active ? 'bg-green-500' : 'bg-red-500'"></div>
                                        <span class="text-sm">{{ user.is_active ? 'Activo' : 'Inactivo' }}</span>
                                    </div>
                                </td>

                                <!-- Acciones -->
                                <td class="p-4 align-middle text-right">
                                    <Button
                                        v-if="user.id !== $page.props.auth.user.id"
                                        :variant="user.is_active ? 'outline' : 'default'"
                                        size="sm"
                                        @click="toggleUser(user)"
                                        :title="user.is_active ? 'Desactivar acceso' : 'Reactivar acceso'"
                                    >
                                        <component :is="user.is_active ? Ban : CheckCircle" class="h-4 w-4 mr-1" />
                                        {{ user.is_active ? 'Desactivar' : 'Activar' }}
                                    </Button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </AppLayout>
</template>
