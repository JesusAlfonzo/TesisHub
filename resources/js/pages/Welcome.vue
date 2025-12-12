<script setup lang="ts">
import { dashboard, login, register } from '@/routes';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import AccessibilityMenu from '@/components/AccessibilityMenu.vue';
import { route } from 'ziggy-js';

import { ArrowRight, Search, UploadCloud, BookOpen } from 'lucide-vue-next';

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);
</script>

<template>
    <AccessibilityMenu />

    <Head title="Bienvenido - Repositorio de Tesis IUJO" />

    <div class="relative min-h-screen flex flex-col font-sans selection:bg-primary selection:text-primary-foreground">

        <div class="fixed inset-0 -z-10" aria-hidden="true">
            <div class="absolute inset-0 bg-cover bg-center opacity-20 dark:opacity-10"
                style="background-image: url('/images/auth/login.webp');"></div>

            <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-background via-background/80 to-background/20"></div>

            <svg class="absolute inset-0 h-full w-full stroke-gray-200 dark:stroke-white/5 [mask-image:radial-gradient(100%_100%_at_top_right,white,transparent)]">
                <defs>
                    <pattern id="grid-pattern" width="40" height="40" patternUnits="userSpaceOnUse">
                        <path d="M0 40L40 0H20L0 20M40 40V20L20 40" stroke-width="1" fill="none" />
                    </pattern>
                </defs>
                <rect width="100%" height="100%" stroke-width="0" fill="url(#grid-pattern)" />
            </svg>
        </div>

        <header class="container mx-auto px-6 py-6 flex justify-between items-center z-10">
            <div class="flex items-center gap-3">
                <div class="bg-primary/10 p-2 rounded-lg backdrop-blur-sm border border-primary/20 text-primary" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="h-7 w-7" viewBox="0 0 24 24">
                        <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20M10 8h4m-2 0v6" />
                        <path d="M6.5 17H20" opacity=".5" />
                    </svg>
                </div>
                <span class="font-bold text-xl tracking-tight hidden sm:block">TesisHub IUJO</span>
            </div>

            <nav class="flex items-center gap-3" aria-label="Menú principal">
                <template v-if="$page.props.auth.user">
                    <Button as-child variant="outline" class="border-primary/20 hover:bg-primary/10">
                        <Link :href="dashboard()">
                            Ir al Dashboard
                            <ArrowRight class="ml-2 h-4 w-4" aria-hidden="true" />
                        </Link>
                    </Button>
                </template>
                <template v-else>
                    <Button as-child variant="ghost" class="hover:bg-primary/5">
                        <Link :href="login()">
                            Iniciar Sesión
                        </Link>
                    </Button>
                    
                    <Button v-if="canRegister" as-child class="shadow-lg shadow-primary/20">
                        <Link :href="register()">
                            Registrarse
                        </Link>
                    </Button>
                </template>
            </nav>
        </header>

        <main class="flex-grow flex items-center justify-center px-6 relative z-10">
            <div class="max-w-4xl w-full text-center space-y-8 animate-in fade-in slide-in-from-bottom-8 duration-700">

                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 border border-primary/20 text-primary text-sm font-medium mb-4" role="status">
                    <span class="relative flex h-2 w-2" aria-hidden="true">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                    </span>
                    Repositorio Digital Institucional
                </div>

                <h1 class="text-4xl sm:text-6xl md:text-7xl font-extrabold tracking-tight text-foreground text-balance">
                    Descubre el conocimiento <br class="hidden sm:block" />
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-blue-600 dark:to-blue-400">
                        que transforma el futuro
                    </span>
                </h1>

                <p class="text-lg sm:text-xl text-muted-foreground max-w-2xl mx-auto leading-relaxed text-balance">
                    Accede a la colección completa de trabajos de grado, investigaciones y proyectos académicos del
                    Instituto Universitario Jesús Obrero.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
                    <Button as-child size="lg" class="h-12 px-8 text-base shadow-xl shadow-primary/20 w-full sm:w-auto">
                        <Link :href="route('tesis.index')">
                            <Search class="mr-2 h-5 w-5" aria-hidden="true" /> 
                            Explorar Tesis
                        </Link>
                    </Button>

                    <Button v-if="!$page.props.auth.user" as-child size="lg" variant="outline"
                        class="h-12 px-8 text-base border-primary/20 hover:bg-primary/5 w-full sm:w-auto">
                        <Link :href="login()">
                            <UploadCloud class="mr-2 h-5 w-5" aria-hidden="true" /> 
                            Subir mi Proyecto
                        </Link>
                    </Button>
                </div>

                <div class="pt-12 grid grid-cols-1 sm:grid-cols-3 gap-6 max-w-3xl mx-auto border-t border-border/50">
                    <div class="flex flex-col items-center p-4 rounded-xl hover:bg-muted/50 transition-colors">
                        <BookOpen class="h-6 w-6 text-primary mb-2 opacity-80" aria-hidden="true" />
                        <span class="font-bold text-2xl">Acceso Abierto</span>
                        <span class="text-sm text-muted-foreground">Consulta pública</span>
                    </div>
                    <div class="flex flex-col items-center p-4 rounded-xl hover:bg-muted/50 transition-colors">
                        <Search class="h-6 w-6 text-primary mb-2 opacity-80" aria-hidden="true" />
                        <span class="font-bold text-2xl">Búsqueda</span>
                        <span class="text-sm text-muted-foreground">Filtros avanzados</span>
                    </div>
                    <div class="flex flex-col items-center p-4 rounded-xl hover:bg-muted/50 transition-colors">
                        <UploadCloud class="h-6 w-6 text-primary mb-2 opacity-80" aria-hidden="true" />
                        <span class="font-bold text-2xl">Digitalización</span>
                        <span class="text-sm text-muted-foreground">Gestión eficiente</span>
                    </div>
                </div>

            </div>
        </main>

        <footer class="py-6 text-center text-sm text-muted-foreground relative z-10">
            <p>&copy; {{ new Date().getFullYear() }} Instituto Universitario Jesús Obrero. Todos los derechos reservados.</p>
        </footer>
    </div>
</template>

<style scoped>
.text-balance {
    text-wrap: balance;
}
</style>