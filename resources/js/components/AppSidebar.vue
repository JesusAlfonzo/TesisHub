<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import {
    LayoutGrid,      // Dashboard
    Library,         // Repositorio
    FilePlus2,       // Estudiante
    ClipboardCheck,  // Tutor
    Users,           // Admin Usuarios
    BarChart3,       // Admin Reportes
    School,          // Admin Carreras
    Folder,
    BookOpen
} from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import { computed } from 'vue';

const page = usePage();

/**
 * Verifica si el usuario tiene acceso al módulo.
 * Devuelve true si tiene el permiso O si es 'super-admin'.
 */
const hasPermission = (permissionName: string) => {
    // @ts-expect-error: Las props de auth vienen dinámicas desde el Middleware
    const permissions = page.props.auth.permissions || [];
    // @ts-expect-error: Las props de roles vienen dinámicas desde el Middleware
    const roles = page.props.auth.roles || [];

    // 1. El Super Admin tiene acceso total
    if (roles.includes('super-admin')) {
        return true;
    }

    // 2. Verificación específica del permiso
    return permissions.includes(permissionName);
};

/**
 * Construcción reactiva del menú principal
 */
const mainNavItems = computed<NavItem[]>(() => {
    const items: NavItem[] = [];

    // --- 1. DASHBOARD (Visible para todos) ---
    items.push({
        title: 'Dashboard',
        // Casteamos a string para evitar conflictos de tipos con Ziggy/route()
        href: dashboard() as unknown as string,
        icon: LayoutGrid,
    });

    // --- 2. REPOSITORIO PÚBLICO ---
    items.push({
        title: 'Repositorio Tesis',
        href: '/tesis',
        icon: Library,
    });

    // --- 3. MÓDULO ESTUDIANTE ---
    if (hasPermission('crear tesis')) {
        items.push({
            title: 'Mi Tesis',
            icon: FilePlus2,
            // Se abre solo si la URL actual comienza con /mis-tesis
            isActive: page.url.startsWith('/mis-tesis'),
            items: [
                {
                    title: 'Subir Anteproyecto',
                    href: '/mis-tesis/create',
                },
                {
                    title: 'Mis Entregas',
                    href: '/mis-tesis',
                },
            ]
        });
    }

    // --- 4. MÓDULO ACADÉMICO (Tutores) ---
    if (hasPermission('evaluar tesis') || hasPermission('aprobar defensa')) {
        items.push({
            title: 'Evaluación Académica',
            icon: ClipboardCheck,
            isActive: page.url.startsWith('/evaluaciones'),
            items: [
                {
                    title: 'Pendientes de Revisión',
                    href: '/evaluaciones/pendientes',
                },
                {
                    title: 'Historial Evaluado',
                    href: '/evaluaciones/historial',
                }
            ]
        });
    }

    // --- 5. MÓDULO ADMINISTRACIÓN ---
    if (hasPermission('gestionar usuarios')) {
        items.push({
            title: 'Administración',
            icon: Users,
            isActive: page.url.startsWith('/admin'),
            items: [
                {
                    title: 'Gestión de Usuarios',
                    href: '/admin/usuarios',
                    icon: Users
                },
                {
                    title: 'Gestión de Carreras',
                    href: '/admin/carreras',
                    icon: School
                },
                {
                    title: 'Reportes del Sistema',
                    href: '/admin/reportes',
                    icon: BarChart3
                }
            ]
        });
    }

    return items;
});

const footerNavItems: NavItem[] = [
    {
        title: 'Documentación',
        href: '#',
        icon: BookOpen,
    },
    {
        title: 'Soporte Técnico',
        href: '#',
        icon: Folder,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard() as unknown as string">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
