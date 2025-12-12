
<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { login, dashboard } from '@/routes';
import AccessibilityMenu from '@/components/AccessibilityMenu.vue';

const page = usePage();
</script>

<template>
    <div class="min-h-screen bg-background font-sans text-foreground">
        
        <header class="sticky top-0 z-40 w-full border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
            <div class="container flex h-16 items-center justify-between px-4 md:px-8 mx-auto max-w-7xl">
                
                <Link href="/" class="flex items-center gap-2">
                    <div class="flex aspect-square size-8 items-center justify-center rounded-lg bg-primary text-primary-foreground">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5">
                            <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20" />
                            <path d="M10 8h4" />
                            <path d="M12 8v6" />
                            <path d="M6.5 17H20" opacity="0.5" />
                        </svg>
                    </div>
                    <span class="font-bold hidden sm:inline-block">Repositorio IUJO</span>
                </Link>

                <nav class="flex items-center gap-4">
                    <template v-if="page.props.auth.user">
                        <Link :href="dashboard()">
                            <Button variant="outline">Ir al Dashboard</Button>
                        </Link>
                    </template>
                    
                    <template v-else>
                        <Link :href="login()">
                            <Button>Iniciar Sesión</Button>
                        </Link>
                    </template>
                </nav>
            </div>
        </header>

        <div class="animate-in fade-in slide-in-from-bottom-2 duration-500">
            <slot />
        </div>

        <AccessibilityMenu />
    </div>
</template>