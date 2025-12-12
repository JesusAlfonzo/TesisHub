<script setup lang="ts">
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import { Form } from '@inertiajs/vue3';
import { ref, nextTick } from 'vue';

// Components
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { AlertTriangle, Trash2 } from 'lucide-vue-next';

// Referencia al input para foco automático
const passwordInput = ref<HTMLInputElement | null>(null);

// Función para manejar el foco al abrir el modal
const onOpenChange = async (isOpen: boolean) => {
    if (isOpen) {
        await nextTick();
        passwordInput.value?.focus();
    }
};
</script>

<template>
    <div class="space-y-6">
        <HeadingSmall
            title="Eliminar cuenta"
            description="Elimina permanentemente tu cuenta y todos sus recursos asociados."
        />
        
        <div
            class="space-y-4 rounded-lg border border-red-100 bg-red-50 p-4 dark:border-red-900/30 dark:bg-red-900/10"
            role="alert"
        >
            <div class="relative space-y-1 text-red-700 dark:text-red-300">
                <div class="flex items-center gap-2 font-semibold">
                    <AlertTriangle class="h-5 w-5" aria-hidden="true" />
                    Advertencia
                </div>
                <p class="text-sm">
                    Esta acción es irreversible. Una vez eliminada, no podrás recuperar tu información.
                </p>
            </div>

            <Dialog @update:open="onOpenChange">
                <DialogTrigger as-child>
                    <Button variant="destructive" data-test="delete-user-button">
                        <Trash2 class="mr-2 h-4 w-4" aria-hidden="true" />
                        Eliminar mi cuenta
                    </Button>
                </DialogTrigger>
                
                <DialogContent>
                    <Form
                        v-bind="ProfileController.destroy.form()"
                        reset-on-success
                        @error="() => passwordInput?.focus()"
                        :options="{
                            preserveScroll: true,
                        }"
                        class="space-y-6"
                        v-slot="{ errors, processing, reset, clearErrors }"
                    >
                        <DialogHeader class="space-y-3">
                            <DialogTitle>¿Estás seguro de que quieres eliminar tu cuenta?</DialogTitle>
                            <DialogDescription>
                                Una vez que se elimine tu cuenta, todos sus recursos y datos se eliminarán permanentemente.
                                Por favor, ingresa tu contraseña para confirmar que deseas eliminar tu cuenta de forma permanente.
                            </DialogDescription>
                        </DialogHeader>

                        <div class="grid gap-2">
                            <Label for="password" class="sr-only">Contraseña</Label>
                            <Input
                                id="password"
                                type="password"
                                name="password"
                                ref="passwordInput"
                                placeholder="Ingresa tu contraseña"
                                class="w-full"
                            />
                            <InputError :message="errors.password" />
                        </div>

                        <DialogFooter class="gap-2 sm:space-x-0">
                            <DialogClose as-child>
                                <Button
                                    variant="outline"
                                    type="button"
                                    @click="
                                        () => {
                                            clearErrors();
                                            reset();
                                        }
                                    "
                                >
                                    Cancelar
                                </Button>
                            </DialogClose>

                            <Button
                                type="submit"
                                variant="destructive"
                                :disabled="processing"
                                data-test="confirm-delete-user-button"
                            >
                                <span v-if="processing">Eliminando...</span>
                                <span v-else>Sí, eliminar cuenta</span>
                            </Button>
                        </DialogFooter>
                    </Form>
                </DialogContent>
            </Dialog>
        </div>
    </div>
</template>