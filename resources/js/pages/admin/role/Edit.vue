<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import {
    FormControl,
    FormField,
    FormItem,
    FormLabel,
} from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { can } from '@/lib/can';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { LoaderCircle, Plus } from 'lucide-vue-next';
import admin from "@/routes/admin";

interface Permission {
    id: number;
    name: string;
}
interface Role {
    id: number;
    name: string;
    permissions: Permission[];
}
interface Props {
    role: Role;
    permissions: Permission[];
}

const props = defineProps<Props>();

const form = useForm({
    name: props.role.name,
    permissions: props.role.permissions.map((permission) => permission.name),
});

// Update handler
const onPermissionUpdate = (
    permission: any,
    checked: boolean | 'indeterminate',
) => {
    if (checked) {
        // Add permission if not already in array
        if (!form.permissions.includes(permission)) {
            form.permissions.push(permission);
        }
    } else {
        // Remove permission
        form.permissions = form.permissions.filter((p) => p !== permission);
    }
};
const handleSubmit = () => {
    form.put(admin.role.update.url(props.role.id), {
        onSuccess: () => {
            form.reset();
        },
    });
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Update Role',
        href: admin.role.edit.url({ id: props.role.id })
    },
];
</script>

<template>
    <Head title="Edit Role" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container mx-auto px-4 py-6">
            <div class="mb-4 flex items-center justify-between">
                <HeadingSmall title="Update Role" />

                <Link
                    :href="admin.role.index.url()"
                    class="ml-auto"
                    v-if="can('role.view')"
                >
                    <Button
                        class="flex items-center hover:cursor-pointer"
                        variant="default"
                        size="sm"
                    >
                        <Plus class="h-4 w-4" />
                        Role List
                    </Button>
                </Link>
            </div>
            <div class="flex-1 justify-start lg:max-w-[50%]">
                <form @submit.prevent="handleSubmit" class="space-y-6">
                    <div class="space-y-2">
                        <Label for="name">Name</Label>
                        <Input
                            id="name"
                            v-model="form.name"
                            type="text"
                            :error="form.errors.name"
                            required
                        />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div class="space-y-2">
                        <Label>Permissions</Label>

                        <div class="grid gap-4">
                            <FormField
                                v-for="permission in permissions"
                                :key="permission.name"
                                type="checkbox"
                                :value="permission.name"
                                :unchecked-value="false"
                                name="permissions"
                            >
                                <FormItem
                                    class="flex flex-row items-start space-y-0 space-x-3"
                                >
                                    <FormControl>
                                        <Checkbox
                                            :model-value="
                                                form.permissions.includes(
                                                    permission.name,
                                                )
                                            "
                                            @update:model-value="
                                                (checked) =>
                                                    onPermissionUpdate(
                                                        permission.name,
                                                        checked,
                                                    )
                                            "
                                        />
                                    </FormControl>
                                    <FormLabel class="font-normal">
                                        {{ permission.name }}
                                    </FormLabel>
                                </FormItem>
                            </FormField>
                        </div>

                        <InputError
                            class="mt-2"
                            :message="form.errors.permissions"
                        />
                    </div>

                    <div class="flex justify-end space-x-2">
                        <Button
                            type="button"
                            variant="outline"
                            :disabled="form.processing"
                            @click="router.get(admin.role.index.url())"
                        >
                            Cancel
                        </Button>
                        <Button type="submit" :disabled="form.processing">
                            <LoaderCircle
                                v-if="form.processing"
                                class="h-4 w-4 animate-spin"
                            />
                            Update Role
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
