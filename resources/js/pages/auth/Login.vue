<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthBase from '@/layouts/AuthLayout.vue';
import { store } from '@/routes/login';
import { request } from '@/routes/password';
import { register } from '@/routes';
import { Form, Head, Link } from '@inertiajs/vue3';
import { Mail, Lock, ArrowRight, LogIn } from 'lucide-vue-next';
import AccessibilityMenu from '@/components/AccessibilityMenu.vue';

defineProps<{
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
}>();
</script>

<template>
    <AuthBase
        title="Bienvenido de nuevo"
        description="Ingresa tus credenciales para acceder al repositorio"
    >
        <Head title="Inicio de Sesión" />

        <div
            v-if="status"
            class="mb-6 rounded-md bg-green-50 p-3 text-sm font-medium text-green-600 border border-green-200 flex items-center gap-2"
        >
            <div class="h-2 w-2 rounded-full bg-green-600"></div>
            {{ status }}
        </div>

        <Form
            v-bind="store.form()"
            :reset-on-success="['password']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
            <div class="space-y-4">
                
                <div class="space-y-2">
                    <Label for="email">Correo Electrónico</Label>
                    <div class="relative">
                        <Mail class="absolute left-3 top-2.5 h-4 w-4 text-muted-foreground pointer-events-none" />
                        <Input
                            id="email"
                            type="email"
                            name="email"
                            required
                            autofocus
                            :tabindex="1"
                            autocomplete="email"
                            placeholder="nombre@iujo.edu.ve"
                            class="pl-9 bg-background/50 focus:bg-background transition-colors"
                        />
                    </div>
                    <InputError :message="errors.email" />
                </div>

                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <Label for="password">Contraseña</Label>
                        <Link
                            v-if="canResetPassword"
                            :href="request()"
                            class="text-xs font-medium text-primary hover:underline hover:text-primary/80 transition-colors"
                            :tabindex="5"
                        >
                            ¿Olvidaste tu contraseña?
                        </Link>
                    </div>
                    <div class="relative">
                        <Lock class="absolute left-3 top-2.5 h-4 w-4 text-muted-foreground pointer-events-none" />
                        <Input
                            id="password"
                            type="password"
                            name="password"
                            required
                            :tabindex="2"
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="pl-9 bg-background/50 focus:bg-background transition-colors"
                        />
                    </div>
                    <InputError :message="errors.password" />
                </div>

                <div class="flex items-center space-x-2 pt-2">
                    <Checkbox id="remember" name="remember" :tabindex="3" />
                    <Label
                        for="remember"
                        class="text-sm font-normal text-muted-foreground cursor-pointer select-none"
                    >
                        Mantener sesión iniciada
                    </Label>
                </div>

                <Button
                    type="submit"
                    class="w-full mt-2 font-semibold shadow-md transition-all hover:scale-[1.01]"
                    size="lg"
                    :tabindex="4"
                    :disabled="processing"
                >
                    <Spinner v-if="processing" class="mr-2 h-4 w-4" />
                    <LogIn v-else class="mr-2 h-4 w-4" />
                    Ingresar al Sistema
                </Button>
            </div>

            <div class="relative my-2" v-if="canRegister">
                <div class="absolute inset-0 flex items-center">
                    <span class="w-full border-t" />
                </div>
                <div class="relative flex justify-center text-xs uppercase">
                    <span class="bg-background px-2 text-muted-foreground">
                        ¿Eres nuevo aquí?
                    </span>
                </div>
            </div>

            <div class="text-center" v-if="canRegister">
                <Link
                    :href="register()"
                    class="inline-flex items-center justify-center text-sm font-medium text-primary hover:text-primary/80 transition-colors group"
                    :tabindex="5"
                >
                    Crear una cuenta de estudiante
                    <ArrowRight class="ml-1 h-3 w-3 transition-transform group-hover:translate-x-1" />
                </Link>
            </div>
        </Form>
    </AuthBase>
        <AccessibilityMenu />

</template>