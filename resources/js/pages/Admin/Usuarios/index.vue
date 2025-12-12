<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import {
    Search, Ban, CheckCircle, User, Mail, CreditCard, Shield,
    Filter, ChevronDown
} from 'lucide-vue-next';
import { route } from 'ziggy-js';
import debounce from 'lodash/debounce';

// Definición de tipos
interface Role {
    name: string;
}

interface UserData {
    id: number;
    name: string;
    email: string;
    cedula: string | null;
    is_active: boolean;
    roles: Role[];
    carrera?: { nombre: string };
}

const props = defineProps<{
    users: { data: Array<UserData>, links: Array<any> };
    filters: { search: string; carrera_id: string };
    carreras: Array<{ id: number; nombre: string }>;
}>();

// Estado de filtros
const search = ref(props.filters.search || '');
const carreraSeleccionada = ref(props.filters.carrera_id || '');

// Lógica de búsqueda unificada (Debounce)
const applyFilters = debounce(() => {
    router.get(route('users.index'), {
        search: search.value,
        carrera_id: carreraSeleccionada.value
    }, {
        preserveState: true,
        replace: true,
        preserveScroll: true
    });
}, 300);

// Observamos ambos campos
watch([search, carreraSeleccionada], () => applyFilters());

const toggleUser = (user: UserData) => {
    const action = user.is_active ? 'DESACTIVAR' : 'ACTIVAR';
    if (confirm(`¿Estás seguro de ${action} el acceso al usuario "${user.name}"?`)) {
        router.patch(route('users.toggle', user.id));
    }
};

// Helper de estilos para roles
const getRoleStyle = (roles: Role[]) => {
    if (!roles.length) return 'bg-gray-100 text-gray-800 border-gray-200';
    const role = roles[0].name;
    switch (role) {
        case 'super-admin': return 'bg-purple-100 text-purple-800 border-purple-200 dark:bg-purple-900/30 dark:text-purple-300';
        case 'coordinador': return 'bg-indigo-100 text-indigo-800 border-indigo-200 dark:bg-indigo-900/30 dark:text-indigo-300';
        case 'tutor': return 'bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-900/30 dark:text-blue-300';
        default: return 'bg-green-100 text-green-800 border-green-200 dark:bg-green-900/30 dark:text-green-300';
    }
};
</script>

<template>

    <Head title="Gestión de Usuarios" />

    <AppLayout :breadcrumbs="[{ title: 'Administración', href: '#' }, { title: 'Usuarios', href: '/admin/usuarios' }]">

        <main class="flex flex-col gap-6 p-4 md:p-8 max-w-7xl mx-auto w-full animate-in fade-in duration-500">

            <header class="flex flex-col gap-4 border-b pb-6">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-foreground">Gestión de Usuarios</h1>
                    <p class="text-muted-foreground text-sm mt-1">Administra el acceso, roles y carreras de los
                        usuarios.</p>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 mt-2">
                    <div class="relative flex-1">
                        <Search class="absolute left-3 top-2.5 h-4 w-4 text-muted-foreground pointer-events-none"
                            aria-hidden="true" />
                        <Input v-model="search" placeholder="Buscar por nombre, cédula o correo..."
                            class="pl-9 bg-background" aria-label="Buscar usuario" />
                    </div>

                    <div class="relative w-full sm:w-[250px]">
                        <Filter
                            class="absolute left-3 top-2.5 h-4 w-4 text-muted-foreground pointer-events-none z-10" />
                        <select v-model="carreraSeleccionada"
                            class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background pl-9 pr-8 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 appearance-none text-foreground truncate cursor-pointer">
                            <option value="">Todas las carreras</option>
                            <option v-for="carrera in carreras" :key="carrera.id" :value="carrera.id">
                                {{ carrera.nombre }}
                            </option>
                        </select>
                        <ChevronDown class="absolute right-3 top-3 h-4 w-4 text-muted-foreground pointer-events-none" />
                    </div>
                </div>
            </header>

            <section aria-label="Lista de usuarios registrados">

                <div class="hidden md:block rounded-md border bg-card shadow-sm w-full overflow-hidden">
                    <table class="w-full caption-bottom text-sm">
                        <caption class="sr-only">Listado de usuarios del sistema</caption>
                        <thead class="bg-muted/50 [&_tr]:border-b">
                            <tr class="border-b transition-colors">
                                <th scope="col"
                                    class="h-12 px-4 text-left align-middle font-medium text-muted-foreground w-[30%]">
                                    Usuario</th>
                                <th scope="col"
                                    class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Rol</th>
                                <th scope="col"
                                    class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Carrera
                                    Asignada</th>
                                <th scope="col"
                                    class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Estado
                                </th>
                                <th scope="col"
                                    class="h-12 px-4 text-right align-middle font-medium text-muted-foreground">Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="[&_tr:last-child]:border-0">
                            <tr v-for="user in users.data" :key="user.id"
                                class="border-b transition-colors hover:bg-muted/50 group">

                                <th scope="row" class="p-4 align-middle font-normal">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="h-9 w-9 rounded-full bg-secondary flex items-center justify-center text-xs font-bold shrink-0">
                                            {{ user.name.charAt(0).toUpperCase() }}
                                        </div>
                                        <div class="text-left">
                                            <div class="font-medium text-foreground">{{ user.name }}</div>
                                            <div class="text-xs text-muted-foreground">{{ user.email }}</div>
                                            <div class="text-[10px] text-muted-foreground font-mono">V-{{ user.cedula ||
                                                'N/A' }}</div>
                                        </div>
                                    </div>
                                </th>

                                <td class="p-4 align-middle">
                                    <Badge variant="outline" :class="getRoleStyle(user.roles)"
                                        class="uppercase text-[10px] shadow-none">
                                        {{ user.roles[0]?.name || 'Sin Rol' }}
                                    </Badge>
                                </td>

                                <td class="p-4 align-middle text-muted-foreground">
                                    {{ user.carrera?.nombre || '—' }}
                                </td>

                                <td class="p-4 align-middle">
                                    <div class="flex items-center gap-2">
                                        <div class="h-2 w-2 rounded-full"
                                            :class="user.is_active ? 'bg-green-500' : 'bg-red-500'"></div>
                                        <span class="text-sm font-medium">{{ user.is_active ? 'Activo' : 'Inactivo'
                                            }}</span>
                                    </div>
                                </td>

                                <td class="p-4 align-middle text-right">
                                    <Button v-if="user.id !== $page.props.auth.user.id"
                                        :variant="user.is_active ? 'ghost' : 'default'" size="sm" class="h-8"
                                        :class="user.is_active ? 'text-red-600 hover:text-red-700 hover:bg-red-50' : 'bg-green-600 hover:bg-green-700 text-white'"
                                        @click="toggleUser(user)"
                                        :aria-label="user.is_active ? `Desactivar usuario ${user.name}` : `Activar usuario ${user.name}`">
                                        <component :is="user.is_active ? Ban : CheckCircle" class="h-4 w-4 mr-1.5" />
                                        {{ user.is_active ? 'Desactivar' : 'Activar' }}
                                    </Button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <ul class="md:hidden grid grid-cols-1 gap-4" role="list">
                    <li v-for="user in users.data" :key="'mob-' + user.id">
                        <article class="bg-card rounded-xl border shadow-sm p-5 space-y-4 relative overflow-hidden">

                            <div class="absolute left-0 top-0 bottom-0 w-1"
                                :class="user.is_active ? 'bg-green-500' : 'bg-red-500'"></div>

                            <div class="flex justify-between items-start pl-2">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="h-10 w-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary font-bold">
                                        {{ user.name.charAt(0).toUpperCase() }}
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-sm">{{ user.name }}</h3>
                                        <div class="flex items-center gap-1 text-xs text-muted-foreground mt-0.5">
                                            <Shield class="h-3 w-3" />
                                            <span class="uppercase font-medium">{{ user.roles[0]?.name || 'Sin Rol'
                                                }}</span>
                                        </div>
                                    </div>
                                </div>
                                <Badge variant="outline"
                                    :class="user.is_active ? 'text-green-600 border-green-200 bg-green-50' : 'text-red-600 border-red-200 bg-red-50'">
                                    {{ user.is_active ? 'Activo' : 'Inactivo' }}
                                </Badge>
                            </div>

                            <div class="space-y-2 text-sm pl-2 border-t pt-3">
                                <div class="flex items-center gap-2 text-muted-foreground">
                                    <Mail class="h-4 w-4" /> {{ user.email }}
                                </div>
                                <div class="flex items-center gap-2 text-muted-foreground">
                                    <CreditCard class="h-4 w-4" /> V-{{ user.cedula || 'No registrada' }}
                                </div>
                                <div v-if="user.carrera" class="flex items-center gap-2 text-muted-foreground">
                                    <span class="font-medium">Carrera:</span> {{ user.carrera.nombre }}
                                </div>
                            </div>

                            <div class="flex flex-col gap-2 pt-2 pl-2 mt-2 border-t"
                                v-if="user.id !== $page.props.auth.user.id">
                                <Button :variant="user.is_active ? 'outline' : 'default'"
                                    class="w-full h-10 justify-center"
                                    :class="user.is_active ? 'border-red-200 text-red-700 hover:bg-red-50' : 'bg-green-600 hover:bg-green-700 text-white'"
                                    @click="toggleUser(user)">
                                    <component :is="user.is_active ? Ban : CheckCircle" class="h-4 w-4 mr-2" />
                                    {{ user.is_active ? 'Desactivar Acceso' : 'Reactivar Acceso' }}
                                </Button>
                            </div>
                        </article>
                    </li>
                </ul>

                <div v-if="users.data.length === 0"
                    class="flex flex-col items-center justify-center py-12 text-center border-2 border-dashed rounded-xl bg-muted/20">
                    <div class="bg-muted p-4 rounded-full mb-4">
                        <User class="h-8 w-8 text-muted-foreground opacity-50" aria-hidden="true" />
                    </div>
                    <p class="text-muted-foreground">No se encontraron usuarios.</p>
                    <Button variant="link" @click="search = ''; carreraSeleccionada = ''" class="mt-2 text-primary">
                        Limpiar filtros
                    </Button>
                </div>

            </section>

        </main>
    </AppLayout>
</template>