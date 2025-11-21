<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthBase from '@/layouts/AuthLayout.vue';
import { login } from '@/routes';

import { Head, useForm } from '@inertiajs/vue3';

defineProps<{
    carreras: Array<{ id: number; nombre: string }>;
}>();

const form = useForm({
    name: '',
    email: '',
    cedula: '',
    carrera_id: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post('/register', {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
};
</script>

<template>
    <AuthBase
        title="Creación de cuenta"
        description="Ingresa tu información debajo para crear una cuenta."
    >
        <Head title="Registro" />

        <form @submit.prevent="submit" class="flex flex-col gap-6">

            <div class="grid gap-6">
                <!-- Nombre -->
                <div class="grid gap-2">
                    <Label for="name">Nombre</Label>
                    <Input
                        id="name"
                        v-model="form.name"
                        type="text"
                        required
                        autofocus
                        autocomplete="name"
                        placeholder="Nombre Completo"
                    />
                    <InputError :message="form.errors.name" />
                </div>

                <!-- Cédula -->
                <div class="grid gap-2">
                    <Label for="cedula">Cédula de Identidad</Label>
                    <Input
                        id="cedula"
                        v-model="form.cedula"
                        type="text"
                        required
                        placeholder="V-12345678"
                    />
                    <InputError :message="form.errors.cedula" />
                </div>

                <!-- Carrera -->
                <div class="grid gap-2">
                    <Label for="carrera_id">Carrera a Cursar</Label>
                    <div class="relative">
                        <select
                            id="carrera_id"
                            v-model="form.carrera_id"
                            required
                            class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 appearance-none dark:bg-gray-950 dark:text-gray-100"
                        >
                            <option value="" disabled class="dark:bg-gray-900 dark:text-gray-100">
                                Selecciona tu carrera...
                            </option>
                            <option
                                v-for="carrera in carreras"
                                :key="carrera.id"
                                :value="carrera.id"
                                class="dark:bg-gray-900 dark:text-gray-100"
                            >
                                {{ carrera.nombre }}
                            </option>
                        </select>
                        <span class="absolute right-3 top-3 pointer-events-none text-muted-foreground">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down"><path d="m6 9 6 6 6-6"/></svg>
                        </span>
                    </div>
                    <InputError :message="form.errors.carrera_id" />
                </div>

                <!-- Email -->
                <div class="grid gap-2">
                    <Label for="email">Correo Electrónico</Label>
                    <Input
                        id="email"
                        v-model="form.email"
                        type="email"
                        required
                        autocomplete="username"
                        placeholder="email@ejemplo.com"
                    />
                    <InputError :message="form.errors.email" />
                </div>

                <!-- Password -->
                <div class="grid gap-2">
                    <Label for="password">Contraseña</Label>
                    <Input
                        id="password"
                        v-model="form.password"
                        type="password"
                        required
                        autocomplete="new-password"
                        placeholder="Contraseña"
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <!-- Confirm Password -->
                <div class="grid gap-2">
                    <Label for="password_confirmation">Confirmar Contraseña</Label>
                    <Input
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        required
                        autocomplete="new-password"
                        placeholder="Confirme su Contraseña"
                    />
                    <InputError :message="form.errors.password_confirmation" />
                </div>

                <!-- Botón -->
                <Button
                    type="submit"
                    class="mt-2 w-full"
                    :disabled="form.processing"
                >
                    <Spinner v-if="form.processing" class="mr-2" />
                    Crear Cuenta
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                ¿Ya tienes una cuenta?
                <TextLink
                    :href="login()"
                    class="underline underline-offset-4"
                >
                    Ingresa
                </TextLink>
            </div>
        </form>
    </AuthBase>
</template>
