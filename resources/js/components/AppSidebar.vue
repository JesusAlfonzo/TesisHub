<script setup lang="ts">
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
    GraduationCap,   // Estudiante
    FileText,        // Sub-item Tesis
    Upload,          // Sub-item Subir
    ClipboardCheck,  // Tutor
    History,         // Sub-item Historial
    Shield,          // Admin
    Users,           // Sub-item Usuarios
    School,          // Sub-item Carreras
    BarChart3        // Sub-item Reportes
} from 'lucide-vue-next';
import { computed } from 'vue';

const page = usePage();

const hasPermission = (permissionName: string) => {
    // @ts-expect-error: Tipos dinámicos de Inertia
    const permissions = page.props.auth.permissions || [];
    // @ts-expect-error: Tipos dinámicos de Inertia
    const roles = page.props.auth.roles || [];

    if (roles.includes('super-admin')) return true;
    return permissions.includes(permissionName);
};

const mainNavItems = computed<NavItem[]>(() => {
    const items: NavItem[] = [];

    // 1. DASHBOARD
    items.push({
        title: 'Dashboard',
        href: dashboard() as unknown as string,
        icon: LayoutGrid,
    });

    // 2. REPOSITORIO
    items.push({
        title: 'Repositorio Tesis',
        href: '/tesis',
        icon: Library,
    });

    // 3. ESTUDIANTE
    if (hasPermission('crear tesis')) {
        items.push({
            title: 'Mi Gestión',
            icon: GraduationCap,
            isActive: page.url.startsWith('/mis-tesis'),
            items: [
                {
                    title: 'Nueva Entrega',
                    href: '/mis-tesis/create',
                    icon: Upload
                },
                {
                    title: 'Mis Proyectos',
                    href: '/mis-tesis',
                    icon: FileText
                },
            ]
        });
    }

    // 4. TUTOR / ACADÉMICO
    if (hasPermission('evaluar tesis') || hasPermission('aprobar defensa')) {
        items.push({
            title: 'Zona Académica',
            icon: ClipboardCheck,
            isActive: page.url.startsWith('/evaluaciones'),
            items: [
                {
                    title: 'Por Revisar',
                    href: '/evaluaciones/pendientes',
                },
                {
                    title: 'Historial Evaluaciones',
                    href: '/evaluaciones/historial',
                    icon: History
                }
            ]
        });
    }

    // 5. ADMINISTRACIÓN
    if (hasPermission('gestionar usuarios')) {
        items.push({
            title: 'Administración',
            icon: Shield,
            isActive: page.url.startsWith('/admin'),
            items: [
                {
                    title: 'Usuarios',
                    href: '/admin/usuarios',
                    icon: Users
                },
                {
                    title: 'Carreras',
                    href: '/admin/carreras',
                    icon: School
                },
                {
                    title: 'Reportes y Métricas',
                    href: '/admin/reportes',
                    icon: BarChart3
                }
            ]
        });
    }

    return items;
});
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard() as unknown as string">
                            <div
                                class="flex aspect-square size-8 items-center justify-center rounded-lg bg-sidebar-primary text-sidebar-primary-foreground">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="size-6">
                                    <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20" />
                                    <path d="M10 8h4" />
                                    <path d="M12 8v6" />
                                    <path d="M6.5 17H20" opacity="0.5" />
                                </svg>
                            </div>
                            <div class="grid flex-1 text-left text-sm leading-tight">
                                <span class="truncate font-semibold">TesisHub IUJO</span>
                                <span class="truncate text-xs">Repositorio Digital</span>
                            </div>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>

    <slot />
</template>