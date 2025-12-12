<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref, watch, nextTick } from 'vue';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Search, Trash2, Pencil, X, BookOpen, GraduationCap } from 'lucide-vue-next';
import InputError from '@/components/InputError.vue';
import { route } from 'ziggy-js';

const props = defineProps<{
    carreras: { data: Array<any>, links: Array<any> };
    filters: { search: string };
}>();

// Formulario (Solo Editar)
const isEditing = ref(false);
const inputNombreRef = ref<HTMLInputElement | null>(null);

const form = useForm({
    id: null as number | null,
    nombre: '',
    codigo: '',
});

const openEdit = async (carrera: any) => {
    form.clearErrors();
    form.id = carrera.id;
    form.nombre = carrera.nombre;
    form.codigo = carrera.codigo;
    isEditing.value = true;

    // Accesibilidad: Llevar el foco al input al abrir edición
    await nextTick();
    if (inputNombreRef.value) inputNombreRef.value.focus();
};

const closeEdit = () => {
    isEditing.value = false;
    form.reset();
};

const submit = () => {
    if (form.id) {
        form.put(route('carreras.update', form.id), {
            onSuccess: () => closeEdit()
        });
    }
};

const deleteCarrera = (id: number, nombre: string) => {
    // Confirmación con contexto (Criterio 3.3.1)
    if (confirm(`⚠️ ATENCIÓN: Eliminar la carrera "${nombre}" es irreversible.\n\nEsto podría afectar a estudiantes asignados. ¿Desea continuar?`)) {
        router.delete(route('carreras.destroy', id));
    }
};

// --- Búsqueda ---
const search = ref(props.filters.search || '');
let timeout: ReturnType<typeof setTimeout>;
watch(search, (val) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get(route('carreras.index'), { search: val }, { preserveState: true, replace: true });
    }, 300);
});
</script>

<template>

    <Head title="Gestión de Carreras" />

    <AppLayout :breadcrumbs="[{ title: 'Administración', href: '#' }, { title: 'Carreras', href: '/admin/carreras' }]">

        <main class="flex flex-col gap-6 p-4 md:p-8 max-w-7xl mx-auto w-full animate-in fade-in duration-500">

            <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b pb-6">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-foreground">Carreras Universitarias</h1>
                    <p class="text-muted-foreground text-sm mt-1">Administra la oferta académica del instituto.</p>
                </div>

                <div class="relative w-full sm:w-72">
                    <Search class="absolute left-3 top-2.5 h-4 w-4 text-muted-foreground pointer-events-none"
                        aria-hidden="true" />
                    <Input v-model="search" placeholder="Buscar por nombre o código..." class="pl-9"
                        aria-label="Buscar carrera" />
                </div>
            </header>

            <section aria-live="polite">
                <div v-if="isEditing"
                    class="bg-muted/30 border rounded-xl p-6 shadow-sm mb-6 animate-in slide-in-from-top-2">
                    <div class="flex justify-between items-center mb-4 border-b pb-2">
                        <h3 class="font-semibold text-lg flex items-center gap-2">
                            <Pencil class="h-4 w-4 text-primary" aria-hidden="true" />
                            Editar Carrera
                        </h3>
                        <Button variant="ghost" size="sm" @click="closeEdit" aria-label="Cerrar edición">
                            <X class="h-4 w-4 mr-1" /> Cerrar
                        </Button>
                    </div>

                    <form @submit.prevent="submit" class="grid gap-6 md:grid-cols-12 items-end">

                        <div class="space-y-2 md:col-span-6">
                            <Label for="nombre-carrera">Nombre de la Carrera <span
                                    class="text-destructive">*</span></Label>
                            <Input id="nombre-carrera" ref="inputNombreRef" v-model="form.nombre"
                                placeholder="Ej: Ingeniería Informática" required />
                            <InputError :message="form.errors.nombre" />
                        </div>

                        <div class="space-y-2 md:col-span-3">
                            <Label for="codigo-carrera">Código <span
                                    class="text-xs text-muted-foreground">(Único)</span> <span
                                    class="text-destructive">*</span></Label>
                            <Input id="codigo-carrera" v-model="form.codigo" placeholder="Ej: INF-2024" required
                                class="font-mono" />
                            <InputError :message="form.errors.codigo" />
                        </div>

                        <div class="flex gap-2 md:col-span-3 w-full">
                            <Button type="submit" class="flex-1" :disabled="form.processing">
                                <span v-if="form.processing">Guardando...</span>
                                <span v-else>Actualizar</span>
                            </Button>
                            <Button type="button" variant="outline" @click="closeEdit" class="flex-1">
                                Cancelar
                            </Button>
                        </div>
                    </form>
                </div>
            </section>

            <section aria-label="Listado de carreras">

                <div class="hidden md:block rounded-md border bg-card shadow-sm w-full overflow-hidden">
                    <table class="w-full caption-bottom text-sm">
                        <caption class="sr-only">Listado de carreras registradas</caption>
                        <thead class="bg-muted/50 [&_tr]:border-b">
                            <tr class="border-b transition-colors">
                                <th scope="col"
                                    class="h-12 px-4 text-left align-middle font-medium text-muted-foreground w-[150px]">
                                    Código</th>
                                <th scope="col"
                                    class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Nombre de
                                    la Carrera</th>
                                <th scope="col"
                                    class="h-12 px-4 text-right align-middle font-medium text-muted-foreground w-[120px]">
                                    Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="[&_tr:last-child]:border-0">
                            <tr v-for="item in carreras.data" :key="item.id"
                                class="border-b transition-colors hover:bg-muted/50 group">

                                <td class="p-4 align-middle font-mono text-xs text-muted-foreground">
                                    <span class="bg-muted px-2 py-1 rounded">{{ item.codigo }}</span>
                                </td>

                                <th scope="row" class="p-4 align-middle font-medium text-foreground">
                                    <div class="flex items-center gap-2">
                                        <GraduationCap class="h-4 w-4 text-muted-foreground" aria-hidden="true" />
                                        {{ item.nombre }}
                                    </div>
                                </th>

                                <td class="p-4 align-middle text-right">
                                    <div class="flex justify-end gap-1 opacity-100 transition-opacity">
                                        <Button variant="ghost" size="icon" @click="openEdit(item)"
                                            :aria-label="`Editar carrera ${item.nombre}`" title="Editar">
                                            <Pencil class="h-4 w-4 text-blue-600" aria-hidden="true" />
                                        </Button>

                                        <Button variant="ghost" size="icon" @click="deleteCarrera(item.id, item.nombre)"
                                            :aria-label="`Eliminar carrera ${item.nombre}`" title="Eliminar"
                                            class="hover:bg-red-50">
                                            <Trash2 class="h-4 w-4 text-red-600" aria-hidden="true" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <ul class="md:hidden grid grid-cols-1 gap-4" role="list">
                    <li v-for="item in carreras.data" :key="'mob-' + item.id">
                        <article class="bg-card rounded-xl border shadow-sm p-4 flex flex-col gap-3">
                            <div class="flex justify-between items-start">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="h-10 w-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
                                        <BookOpen class="h-5 w-5" aria-hidden="true" />
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-sm">{{ item.nombre }}</h3>
                                        <p
                                            class="text-xs font-mono text-muted-foreground bg-muted inline-block px-1.5 rounded mt-1">
                                            {{ item.codigo }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col gap-3 pt-2 border-t mt-1">
                                <Button variant="outline"
                                    class="w-full h-10 border-blue-200 text-blue-700 hover:bg-blue-50 justify-center"
                                    @click="openEdit(item)">
                                    <Pencil class="mr-2 h-4 w-4" /> Editar
                                </Button>
                                <Button variant="outline"
                                    class="w-full h-10 border-red-200 text-red-700 hover:bg-red-50 justify-center"
                                    @click="deleteCarrera(item.id, item.nombre)">
                                    <Trash2 class="mr-2 h-4 w-4" /> Eliminar
                                </Button>
                            </div>
                        </article>
                    </li>
                </ul>

                <div v-if="carreras.data.length === 0"
                    class="flex flex-col items-center justify-center py-12 text-center border-2 border-dashed rounded-xl bg-muted/20">
                    <div class="bg-muted p-4 rounded-full mb-4">
                        <Search class="h-8 w-8 text-muted-foreground opacity-50" aria-hidden="true" />
                    </div>
                    <p class="text-muted-foreground">No se encontraron carreras con ese criterio.</p>
                </div>

            </section>

        </main>
    </AppLayout>
</template>