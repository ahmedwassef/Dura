<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Zap } from 'lucide-vue-next';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="flex min-h-svh flex-col items-center justify-center gap-6 bg-[#121212] text-white p-6 md:p-10"
    >
        <div class="w-full max-w-sm">
            <div class="flex flex-col gap-8">
                <div class="flex flex-col items-center gap-4">
                    <Link
                        :href="home()"
                        class="flex flex-col items-center gap-2 font-medium"
                    >
                        <div
                            class="mb-1 flex size-12 items-center justify-center rounded-xl bg-[#00E5FF] shadow-[0_0_15px_rgba(0,229,255,0.3)]"
                        >
                            <Zap
                                class="size-7 text-[#121212] fill-current"
                            />
                        </div>
                        <span class="sr-only">{{ title }}</span>
                    </Link>
                    <div class="space-y-2 text-center">
                        <h1 class="text-2xl font-bold tracking-tight">{{ title }}</h1>
                        <p class="text-center text-sm text-[#98989D] font-medium">
                            {{ description }}
                        </p>
                    </div>
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>
