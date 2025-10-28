<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import { toast, Toaster } from 'vue-sonner';
import 'vue-sonner/style.css';

const page = usePage();

// Compute flash messages reactively
const flashMessage = computed(() => page.props.flash);

// Watch for new flash messages
watch(
    () => flashMessage.value,
    (flash) => {
        if (flash?.success) toast.success(flash.success);
        if (flash?.error) toast.error(flash.error);
        if (flash?.warning) toast.warning(flash.warning);
        if (flash?.info) toast.info(flash.info);
    },
    { deep: true, immediate: true },
);

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Toaster position="top-right" richColors />
        <slot />
    </AppLayout>
</template>
