<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Search, Trash2, Pencil, X } from 'lucide-vue-next';
import InputError from '@/components/InputError.vue';
import { route } from 'ziggy-js';

const props = defineProps<{
    carreras: { data: Array<any>, links: Array<any> };
    filters: { search: string };
}>();

// Formulario (Solo Editar)
const isEditing = ref(false);
const form = useForm({
    id: null as number | null,
    nombre: '',
    codigo: '',
});

const openEdit = (carrera: any) => {
    form.clearErrors();
    form.id = carrera.id;
    form.nombre = carrera.nombre;
    form.codigo = carrera.codigo;
    isEditing.value = true;
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

const deleteCarrera = (id: number) => {
    if (confirm('⚠️ Atención: Eliminar una carrera es irreversible y dejará a los estudiantes sin referencia. ¿Desea continuar?')) {
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
        <div class="flex flex-col gap-6 p-4">

            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Carreras Universitarias</h1>
                    <p class="text-muted-foreground">Administra la oferta académica del instituto (Solo Edición/Eliminación).</p>
                </div>
                <div class="flex gap-2 w-full sm:w-auto">
                    <div class="relative w-full sm:w-64">
                        <Search class="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
                        <Input v-model="search" placeholder="Buscar carrera..." class="pl-8" />
                    </div>
                </div>
            </div>

            <!-- FORMULARIO INLINE (Aparece solo para editar) -->
            <div v-if="isEditing" class="bg-muted/30 border rounded-xl p-6 animate-in fade-in slide-in-from-top-2">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-lg">Editar Carrera: {{ form.nombre }}</h3>
                    <Button variant="ghost" size="icon" @click="closeEdit"><X class="h-4 w-4" /></Button>
                </div>
                <form @submit.prevent="submit" class="grid gap-4 md:grid-cols-4 items-end">

                    <!-- Nombre -->
                    <div class="space-y-2 md:col-span-2">
                        <Label>Nombre de la Carrera</Label>
                        <Input v-model="form.nombre" placeholder="Ingeniería Informática" required />
                        <InputError :message="form.errors.nombre" />
                    </div>

                    <!-- Codigo -->
                    <div class="space-y-2">
                        <Label>Código (Único)</Label>
                        <Input v-model="form.codigo" placeholder="INF-2024" required />
                        <InputError :message="form.errors.codigo" />
                    </div>

                    <!-- Acciones -->
                    <div class="flex gap-2 w-full">
                        <Button type="submit" class="flex-1" :disabled="form.processing">
                            Actualizar
                        </Button>
                        <Button type="button" variant="outline" @click="closeEdit" class="flex-1">Cancelar</Button>
                    </div>
                </form>
            </div>

            <!-- TABLA -->
            <div class="rounded-md border bg-card">
                <table class="w-full caption-bottom text-sm">
                    <thead class="[&_tr]:border-b">
                        <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Código</th>
                            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Nombre</th>
                            <th class="h-12 px-4 text-right align-middle font-medium text-muted-foreground">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="[&_tr:last-child]:border-0">
                        <tr v-for="item in carreras.data" :key="item.id" class="border-b transition-colors hover:bg-muted/50">
                            <td class="p-4 align-middle font-mono text-xs">{{ item.codigo }}</td>
                            <td class="p-4 align-middle font-medium">{{ item.nombre }}</td>
                            <td class="p-4 align-middle text-right">
                                <Button variant="ghost" size="icon" @click="openEdit(item)">
                                    <Pencil class="h-4 w-4 text-blue-600" />
                                </Button>
                                <Button variant="ghost" size="icon" @click="deleteCarrera(item.id)">
                                    <Trash2 class="h-4 w-4 text-red-600" />
                                </Button>
                            </td>
                        </tr>
                        <tr v-if="carreras.data.length === 0">
                            <td colspan="3" class="p-8 text-center text-muted-foreground">No se encontraron carreras.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
