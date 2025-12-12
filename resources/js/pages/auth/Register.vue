<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthBase from '@/layouts/AuthLayout.vue';
import { login } from '@/routes';
import { Head, useForm, Link } from '@inertiajs/vue3';
import AccessibilityMenu from '@/components/AccessibilityMenu.vue';
import { 
    User, 
    CreditCard, 
    GraduationCap, 
    Mail, 
    Lock, 
    ArrowRight,
    ChevronDown,
    UserPlus
} from 'lucide-vue-next'; // Iconos nuevos

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
        title="Únete a la Comunidad"
        description="Completa el formulario para registrar tu perfil de estudiante"
    >
        <Head title="Registro de Estudiante" />

        <form @submit.prevent="submit" class="flex flex-col gap-6">

            <div class="grid gap-5">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label for="name">Nombre Completo</Label>
                        <div class="relative">
                            <User class="absolute left-3 top-2.5 h-4 w-4 text-muted-foreground pointer-events-none" />
                            <Input
                                id="name"
                                v-model="form.name"
                                type="text"
                                required
                                autofocus
                                autocomplete="name"
                                placeholder="Nombre Completo"
                                class="pl-9 bg-background/50 focus:bg-background"
                            />
                        </div>
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="space-y-2">
                        <Label for="cedula">Cédula</Label>
                        <div class="relative">
                            <CreditCard class="absolute left-3 top-2.5 h-4 w-4 text-muted-foreground pointer-events-none" />
                            <Input
                                id="cedula"
                                v-model="form.cedula"
                                type="text"
                                required
                                placeholder="Cedula"
                                class="pl-9 bg-background/50 focus:bg-background"
                            />
                        </div>
                        <InputError :message="form.errors.cedula" />
                    </div>
                </div>

                <div class="space-y-2">
                    <Label for="carrera_id">Carrera a Cursar</Label>
                    <div class="relative">
                        <GraduationCap class="absolute left-3 top-2.5 h-4 w-4 text-muted-foreground pointer-events-none z-10" />
                        <select
                            id="carrera_id"
                            v-model="form.carrera_id"
                            required
                            class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background/50 px-3 py-2 pl-9 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 appearance-none focus:bg-background transition-colors cursor-pointer"
                        >
                            <option value="" disabled selected>Selecciona tu carrera...</option>
                            <option
                                v-for="carrera in carreras"
                                :key="carrera.id"
                                :value="carrera.id"
                            >
                                {{ carrera.nombre }}
                            </option>
                        </select>
                        <ChevronDown class="absolute right-3 top-3 h-4 w-4 text-muted-foreground pointer-events-none" />
                    </div>
                    <InputError :message="form.errors.carrera_id" />
                </div>

                <div class="space-y-2">
                    <Label for="email">Correo Institucional</Label>
                    <div class="relative">
                        <Mail class="absolute left-3 top-2.5 h-4 w-4 text-muted-foreground pointer-events-none" />
                        <Input
                            id="email"
                            v-model="form.email"
                            type="email"
                            required
                            autocomplete="username"
                            placeholder="Correo Institucional"
                            class="pl-9 bg-background/50 focus:bg-background"
                        />
                    </div>
                    <InputError :message="form.errors.email" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label for="password">Contraseña</Label>
                        <div class="relative">
                            <Lock class="absolute left-3 top-2.5 h-4 w-4 text-muted-foreground pointer-events-none" />
                            <Input
                                id="password"
                                v-model="form.password"
                                type="password"
                                required
                                autocomplete="new-password"
                                placeholder="Contraseña"
                                class="pl-9 bg-background/50 focus:bg-background"
                            />
                        </div>
                        <InputError :message="form.errors.password" />
                    </div>

                    <div class="space-y-2">
                        <Label for="password_confirmation">Confirmar</Label>
                        <div class="relative">
                            <Lock class="absolute left-3 top-2.5 h-4 w-4 text-muted-foreground pointer-events-none" />
                            <Input
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                type="password"
                                required
                                autocomplete="new-password"
                                placeholder="Confirme su Contraseña"
                                class="pl-9 bg-background/50 focus:bg-background"
                            />
                        </div>
                        <InputError :message="form.errors.password_confirmation" />
                    </div>
                </div>

                <Button
                    type="submit"
                    class="mt-4 w-full font-semibold shadow-md transition-all hover:scale-[1.01]"
                    size="lg"
                    :disabled="form.processing"
                >
                    <Spinner v-if="form.processing" class="mr-2 h-4 w-4" />
                    <UserPlus v-else class="mr-2 h-4 w-4" />
                    Crear Cuenta de Estudiante
                </Button>
            </div>

            <div class="relative my-2">
                <div class="absolute inset-0 flex items-center">
                    <span class="w-full border-t" />
                </div>
                <div class="relative flex justify-center text-xs uppercase">
                    <span class="bg-background px-2 text-muted-foreground">
                        o
                    </span>
                </div>
            </div>

            <div class="text-center">
                <Link
                    :href="login()"
                    class="inline-flex items-center justify-center text-sm font-medium text-primary hover:text-primary/80 transition-colors group"
                >
                    <ArrowRight class="mr-1 h-3 w-3 rotate-180 transition-transform group-hover:-translate-x-1" />
                    Ya tengo una cuenta, ingresar
                </Link>
            </div>
        </form>
    </AuthBase>
        <AccessibilityMenu />
</template>