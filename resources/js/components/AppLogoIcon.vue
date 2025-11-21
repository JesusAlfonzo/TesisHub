<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();

// Definimos la ruta de la imagen estática
const logoUrl = 'images/logo/IUJO.webp';
</script>

<template>
    <img
        :src="logoUrl"
        alt="Logo IUJO"
        :class="className"
        v-bind="$attrs"
    />
</template>
