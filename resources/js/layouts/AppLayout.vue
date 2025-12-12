<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import UiToast from '@/components/UiToast.vue';
import { usePage } from '@inertiajs/vue3'; // 1. Importamos usePage

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

// 2. Obtenemos la instancia de la página para detectar cambios de URL
const page = usePage();
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        
        <div :key="page.url" class="animate-in fade-in slide-in-from-bottom-2 duration-500 p-1">
            <slot />
        </div>

        <UiToast />
    </AppLayout>
</template>