<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import UiToast from '@/components/UiToast.vue';
import AccessibilityMenu from '@/components/AccessibilityMenu.vue';
import { usePage } from '@inertiajs/vue3';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

// Obtenemos la instancia de la página para detectar cambios de URL
// Esto sirve para reiniciar la animación cuando el usuario navega
const page = usePage();
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        
        <div :key="page.url" class="animate-in fade-in slide-in-from-bottom-2 duration-500 p-1">
            <slot />
        </div>

        <UiToast />
        <AccessibilityMenu />
        
    </AppLayout>
</template>