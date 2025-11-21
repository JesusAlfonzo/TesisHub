<script setup lang="ts">
import { dashboard, login, register } from '@/routes';
import { Head, Link } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);
</script>

<template>
    <Head title="Bienvenido - Repositorio de Tesis IUJO">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>

    <div
        class="relative min-h-screen flex flex-col items-center justify-center p-6 text-[#1b1b18] lg:p-8 dark:text-[#EDEDEC]"
    >
        <div
            class="absolute inset-0 z-0 bg-cover bg-center"
            style="background-image: url('/images/auth/login.webp');"
        >
            <div class="absolute inset-0 bg-white/60 backdrop-blur-sm dark:bg-black/60"></div>
        </div>

        <header
            class="z-10 mb-6 w-full max-w-[335px] text-sm not-has-[nav]:hidden lg:max-w-4xl"
        >
            <nav class="flex items-center justify-end gap-4">
                <Link
                    v-if="$page.props.auth.user"
                    :href="dashboard()"
                    class="inline-block rounded-sm border px-5 py-1.5 text-sm leading-normal text-gray-900 border-gray-900/50 hover:border-gray-900/75 dark:text-white dark:border-white/50 dark:hover:border-white/75"
                >
                    Dashboard
                </Link>
                <template v-else>
                    <Link
                        :href="login()"
                        class="inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-gray-900 hover:border-gray-900/50 dark:text-white dark:hover:border-white/50"
                    >
                        Iniciar Sesión
                    </Link>
                    <Link
                        v-if="canRegister"
                        :href="register()"
                        class="inline-block rounded-sm border px-5 py-1.5 text-sm leading-normal text-gray-900 border-gray-900/50 hover:border-gray-900/75 dark:text-white dark:border-white/50 dark:hover:border-white/75"
                    >
                        Registrarse
                    </Link>
                </template>
            </nav>
        </header>

        <div
            class="z-10 flex w-full items-center justify-center opacity-100 transition-opacity duration-750 lg:grow starting:opacity-0"
        >
            <main
                class="flex w-full max-w-[335px] flex-col-reverse overflow-hidden rounded-lg lg:max-w-4xl lg:flex-row"
            >
                <div
                    class="flex-1 rounded-br-lg rounded-bl-lg bg-white bg-opacity-90 p-6 pb-12 text-[13px] leading-[20px] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] lg:rounded-tl-lg lg:rounded-br-none lg:p-20 dark:bg-[#161615] dark:bg-opacity-90 dark:text-[#EDEDEC] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]"
                >
                    <h1 class="mb-2 text-2xl font-bold dark:text-white">
                        Repositorio de Tesis IUJO
                    </h1>

                    <p class="mb-4 text-base text-[#706f6c] dark:text-[#A1A09A]">
                        Un espacio centralizado para descubrir, alojar y
                        compartir el conocimiento generado en el Instituto
                        Universitario Jesús Obrero.
                    </p>

                    <ul class="mb-4 flex flex-col lg:mb-6">
                        <li
                            class="relative flex items-center gap-4 py-2 before:absolute before:top-1/2 before:bottom-0 before:left-[0.4rem] before:border-l before:border-[#e3e3e0] dark:before:border-[#3E3E3A]"
                        >
                            <span
                                class="relative bg-white bg-opacity-90 py-1 dark:bg-[#161615] dark:bg-opacity-90"
                            >
                                <span
                                    class="flex h-3.5 w-3.5 items-center justify-center rounded-full border border-[#e3e3e0] bg-[#FDFDFC] bg-opacity-90 shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)] dark:border-[#3E3E3A] dark:bg-[#161615] dark:bg-opacity-90"
                                >
                                    <span
                                        class="h-1.5 w-1.5 rounded-full bg-[#dbdbd7] dark:bg-[#3E3E3A]"
                                    />
                                </span>
                            </span>
                            <span class="text-base">
                                Explora trabajos de grado por carrera.
                            </span>
                        </li>
                        <li
                            class="relative flex items-center gap-4 py-2 before:absolute before:top-0 before:bottom-1/2 before:left-[0.4rem] before:border-l before:border-[#e3e3e0] dark:before:border-[#3E3E3A]"
                        >
                            <span
                                class="relative bg-white bg-opacity-90 py-1 dark:bg-[#161615] dark:bg-opacity-90"
                            >
                                <span
                                    class="flex h-3.5 w-3.5 items-center justify-center rounded-full border border-[#e3e3e0] bg-[#FDFDFC] bg-opacity-90 shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)] dark:border-[#3E3E3A] dark:bg-[#161615] dark:bg-opacity-90"
                                >
                                    <span
                                        class="h-1.5 w-1.5 rounded-full bg-[#dbdbd7] dark:bg-[#3E3E3A]"
                                    />
                                </span>
                            </span>
                            <span class="text-base">
                                Publica y gestiona tu investigación.
                            </span>
                        </li>
                    </ul>

                    <ul class="flex gap-3 text-sm leading-normal">
                        <li>
                            <Link
                                :href="dashboard()"
                                class="inline-block rounded-sm border border-black bg-[#1b1b18] px-5 py-1.5 text-sm leading-normal text-white hover:border-black hover:bg-black dark:border-[#eeeeec] dark:bg-[#eeeeec] dark:text-[#1C1C1A] dark:hover:border-white dark:hover:bg-white"
                            >
                                Explorar Tesis
                            </Link>
                        </li>
                    </ul>
                </div>

                <div
                    class="relative flex items-center justify-center -mb-px aspect-335/376 w-full shrink-0 overflow-hidden rounded-t-lg bg-slate-100 bg-opacity-90 lg:mb-0 lg:-ml-px lg:aspect-auto lg:w-[438px] lg:rounded-t-none lg:rounded-r-lg dark:bg-zinc-900 dark:bg-opacity-90"
                >
                    <AppLogoIcon
                        class="w-3/5 h-auto text-gray-800 dark:text-white opacity-40"
                    />
                </div>
            </main>
        </div>
    </div>
</template>
