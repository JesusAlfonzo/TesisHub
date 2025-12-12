<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { edit as editAppearance } from '@/routes/appearance';
import { edit as editProfile } from '@/routes/profile';
import { show } from '@/routes/two-factor';
import { edit as editPassword } from '@/routes/user-password';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { User, Lock, ShieldCheck, Palette, Settings } from 'lucide-vue-next';

const page = usePage();

// Definimos los items con iconos y traducción
const sidebarNavItems = [
    {
        title: 'Perfil',
        href: editProfile(),
        icon: User
    },
    {
        title: 'Contraseña',
        href: editPassword(),
        icon: Lock
    },
    {
        title: 'Autenticación en Dos Pasos',
        href: show(),
        icon: ShieldCheck
    },
    {
        title: 'Apariencia',
        href: editAppearance(),
        icon: Palette
    },
];

// Función reactiva para determinar si la URL está activa
// Compara la URL actual de Inertia con la del item
const isActive = (url: string) => page.url === new URL(url, window.location.origin).pathname;
</script>

<template>
    <div class="px-4 py-6 md:px-8 animate-in fade-in duration-500">
        
        <div class="space-y-0.5 mb-6">
            <h2 class="text-2xl font-bold tracking-tight flex items-center gap-2">
                <Settings class="h-6 w-6" aria-hidden="true" />
                Configuración
            </h2>
            <p class="text-muted-foreground">
                Administra tu perfil, seguridad y preferencias de la cuenta.
            </p>
        </div>

        <Separator class="my-6" />

        <div class="flex flex-col lg:flex-row lg:space-x-12 space-y-8 lg:space-y-0">
            
            <aside class="lg:w-1/5">
                <nav class="flex space-x-2 lg:flex-col lg:space-x-0 lg:space-y-1 overflow-x-auto pb-2 lg:pb-0" aria-label="Menú de configuración">
                    <Link
                        v-for="item in sidebarNavItems"
                        :key="item.href"
                        :href="item.href"
                    >
                        <Button
                            variant="ghost"
                            :class="[
                                'w-full justify-start gap-2',
                                { 'bg-muted hover:bg-muted': isActive(item.href) }
                            ]"
                            :aria-current="isActive(item.href) ? 'page' : undefined"
                        >
                            <component :is="item.icon" class="h-4 w-4 text-muted-foreground" aria-hidden="true" />
                            {{ item.title }}
                        </Button>
                    </Link>
                </nav>
            </aside>

            <div class="flex-1 lg:max-w-2xl">
                <section class="space-y-6 animate-in slide-in-from-bottom-2 duration-700 delay-100">
                    <slot />
                </section>
            </div>
        </div>
    </div>
</template>