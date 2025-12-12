<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent } from '@/components/ui/card';
import { FileText, User, GraduationCap, Calendar, Download, ExternalLink, ArrowLeft, Eye } from 'lucide-vue-next';
import { route } from 'ziggy-js';
import { computed } from 'vue';

// Definimos props
const props = defineProps<{
    tesis: {
        id: number;
        titulo: string;
        resumen: string;
        ruta_archivo: string | null;
        created_at: string;
        autor?: { name: string; email?: string };
        carrera?: { nombre: string; id: number };
    };
}>();

const page = usePage();

// 1. LAYOUT DINÁMICO
// Si hay usuario -> AppLayout (con Sidebar)
// Si es visitante -> PublicLayout (solo Header)
const layout = computed(() => {
    return page.props.auth.user ? AppLayout : PublicLayout;
});

// 2. Computed para fecha segura
const formattedDate = computed(() => {
    if (!props.tesis.created_at) return 'Fecha desconocida';
    return new Date(props.tesis.created_at).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
});

// 3. Helpers
const getInitials = (name?: string) => {
    if (!name) return '??';
    return name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
};

const nombreCarrera = computed(() => props.tesis.carrera?.nombre || 'General');
const nombreAutor = computed(() => props.tesis.autor?.name || 'Autor Desconocido');
</script>

<template>
    <Head :title="tesis.titulo" />

    <component 
        :is="layout" 
        :breadcrumbs="[
            { title: 'Repositorio', href: route('tesis.index') },
            { title: 'Detalle del Proyecto', href: '#' },
        ]"
    >
        
        <main class="max-w-7xl mx-auto p-4 md:p-8 animate-in fade-in duration-500" id="main-content">
            
            <div class="mb-6">
                <Link 
                    :href="route('tesis.index')" 
                    class="inline-flex items-center text-sm text-muted-foreground hover:text-primary transition-colors focus-visible:ring-2 focus-visible:ring-ring rounded-md px-2 py-1 -ml-2"
                >
                    <ArrowLeft class="mr-1 h-4 w-4" aria-hidden="true" />
                    Volver al repositorio
                </Link>
            </div>

            <div class="grid gap-8 lg:grid-cols-12">

                <article class="lg:col-span-8 space-y-8">
                    
                    <header class="space-y-4">
                        <div class="flex flex-wrap gap-2">
                            <Badge variant="secondary" class="text-xs font-semibold px-2.5 py-0.5" aria-label="Carrera">
                                <GraduationCap class="w-3 h-3 mr-1" aria-hidden="true" />
                                {{ nombreCarrera }}
                            </Badge>
                            <Badge variant="outline" class="text-xs font-normal" aria-label="Fecha de publicación">
                                <Calendar class="w-3 h-3 mr-1" aria-hidden="true" />
                                <time :datetime="tesis.created_at">{{ formattedDate }}</time>
                            </Badge>
                        </div>

                        <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-foreground leading-tight text-balance">
                            {{ tesis.titulo }}
                        </h1>

                        <div class="flex items-center gap-4 py-4 border-y border-border">
                            <div class="h-12 w-12 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-lg" aria-hidden="true">
                                {{ getInitials(nombreAutor) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Autor(a)</p>
                                <p class="text-lg font-semibold text-foreground leading-none">{{ nombreAutor }}</p>
                            </div>
                        </div>
                    </header>

                    <section aria-labelledby="abstract-heading">
                        <h2 id="abstract-heading" class="text-xl font-bold mb-4 flex items-center gap-2">
                            <FileText class="h-5 w-5 text-muted-foreground" aria-hidden="true" />
                            Resumen / Abstract
                        </h2>
                        <div class="prose dark:prose-invert max-w-none text-muted-foreground leading-relaxed text-justify whitespace-pre-line">
                            <p>{{ tesis.resumen }}</p>
                        </div>
                    </section>

                </article>

                <aside class="lg:col-span-4 space-y-6">
                    
                    <Card class="sticky top-6 border-muted bg-sidebar-accent/10 overflow-hidden shadow-sm">
                        <div class="bg-muted/50 p-4 border-b flex items-center justify-between">
                            <h3 class="font-semibold flex items-center gap-2">
                                <Eye class="h-4 w-4 text-muted-foreground" aria-hidden="true" />
                                Documento PDF
                            </h3>
                            <span class="text-xs text-muted-foreground font-mono">PDF</span>
                        </div>

                        <CardContent class="p-6 space-y-6">
                            
                            <div class="aspect-[3/4] w-full bg-white rounded-md border shadow-inner overflow-hidden relative group">
                                <object 
                                    v-if="tesis.ruta_archivo" 
                                    :data="route('tesis.ver', tesis.id)" 
                                    type="application/pdf" 
                                    class="w-full h-full object-cover"
                                    aria-label="Previsualización del documento PDF"
                                >
                                    <div class="flex flex-col items-center justify-center h-full text-center p-4 text-muted-foreground bg-gray-50 dark:bg-gray-900">
                                        <FileText class="h-12 w-12 mb-2 opacity-50" aria-hidden="true"/>
                                        <p class="text-sm">Vista previa no disponible.</p>
                                    </div>
                                </object>
                                <div v-else class="flex items-center justify-center h-full bg-muted">
                                    <p class="text-sm text-muted-foreground">Archivo no disponible</p>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <a 
                                    v-if="tesis.ruta_archivo" 
                                    :href="route('tesis.ver', tesis.id)" 
                                    target="_blank"
                                    class="block w-full"
                                    rel="noopener noreferrer"
                                >
                                    <Button 
                                        class="w-full font-semibold shadow-md" 
                                        size="lg"
                                    >
                                        <ExternalLink class="h-4 w-4 mr-2" />
                                        Abrir en nueva pestaña
                                    </Button>
                                </a>

                                <a 
                                    v-if="tesis.ruta_archivo" 
                                    :href="route('tesis.ver', tesis.id)" 
                                    download
                                    class="block w-full"
                                >
                                    <Button 
                                        variant="outline" 
                                        class="w-full border-primary/20 hover:bg-primary/5"
                                    >
                                        <Download class="h-4 w-4 mr-2" />
                                        Descargar Archivo
                                    </Button>
                                </a>
                            </div>

                            <p class="text-xs text-center text-muted-foreground pt-2">
                                Formato PDF • Accesible
                            </p>

                        </CardContent>
                    </Card>

                </aside>

            </div>
        </main>
    </component>
</template>