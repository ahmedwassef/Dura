<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ChevronRight, ChevronLeft, Home } from 'lucide-vue-next';
import LocalizedText from '@/clinic/components/ui/LocalizedText.vue';
import { useClinicLocale } from '@/clinic/composables/useClinicLocale';

interface BreadcrumbItem {
    label: { ar: string; en: string } | string;
    href?: string;
}

const props = defineProps<{
    items: BreadcrumbItem[];
}>();

const { isArabic } = useClinicLocale();
</script>

<template>
    <nav class="breadcrumbs" aria-label="Breadcrumb">
        <div class="breadcrumb-container">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item">
                    <Link href="/dashboard/clinic/home" class="breadcrumb-link home-link">
                        <Home class="size-4" />
                    </Link>
                </li>

                <li v-for="(item, index) in items" :key="index" class="breadcrumb-item">
                    <span class="separator">
                        <ChevronLeft v-if="isArabic" class="size-4" />
                        <ChevronRight v-else class="size-4" />
                    </span>
                    
                    <Link v-if="item.href && index < items.length - 1" :href="item.href" class="breadcrumb-link">
                        <LocalizedText v-if="typeof item.label === 'object'" :value="item.label" />
                        <span v-else>{{ item.label }}</span>
                    </Link>
                    <span v-else class="breadcrumb-current">
                        <LocalizedText v-if="typeof item.label === 'object'" :value="item.label" />
                        <span v-else>{{ item.label }}</span>
                    </span>
                </li>
            </ol>
        </div>
    </nav>
</template>

<style scoped>
.breadcrumbs {
    background: #f8fafc;
    border-bottom: 1px solid var(--line);
    padding: 10px 0;
}

.breadcrumb-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 24px;
}

.breadcrumb-list {
    display: flex;
    align-items: center;
    list-style: none;
    margin: 0;
    padding: 0;
    flex-wrap: wrap;
    gap: 4px;
}

.breadcrumb-item {
    display: flex;
    align-items: center;
    font-size: 12px;
}

.breadcrumb-link {
    color: var(--ink-mute);
    text-decoration: none;
    transition: all 0.2s;
    font-weight: 600;
}

.breadcrumb-link:hover {
    color: var(--primary);
}

.home-link {
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary);
}

.separator {
    color: var(--line);
    margin: 0 4px;
    display: flex;
    align-items: center;
}

.breadcrumb-current {
    color: var(--ink);
    font-weight: 700;
}
</style>
