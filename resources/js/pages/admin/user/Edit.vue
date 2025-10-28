<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { LoaderCircle, Plus } from 'lucide-vue-next';
import { can } from '@/lib/can';
import admin from "@/routes/admin";

interface Role {
    id: number;
    name: string;
}
interface User {
    id: number;
    name: string;
    email: string;
    roles: Role[];
    created_at: string;
}
interface Props {
    user: User;
    roles: Role[];
}

const props = defineProps<Props>();

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    password: '',
    password_confirmation: '',
    roles: props.user.roles.map((role) => role.name),
});

const handleSubmit = () => {
    form.put(admin.user.update.url({ id: props.user.id }), {
        onSuccess: () => {
            form.reset();
        },
    });
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Update User',
        href: admin.user.edit.url({ id: props.user.id })
    },
];
</script>

<template>
    <Head title="Update User" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container mx-auto px-4 py-6">
            <div class="mb-4 flex items-center justify-between">
                <HeadingSmall title="Update User" />

                <Link
                    :href="admin.user.index.url()"
                    class="ml-auto"
                    v-if="can('user.view')"
                >
                    <Button
                        class="flex items-center hover:cursor-pointer"
                        variant="default"
                        size="sm"
                    >
                        <Plus class="h-4 w-4" />
                        User List
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
                        <Label for="email">Email</Label>
                        <Input
                            id="email"
                            v-model="form.email"
                            type="email"
                            :error="form.errors.email"
                            required
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div class="space-y-2">
                        <Label for="password">Password</Label>
                        <Input
                            id="password"
                            v-model="form.password"
                            type="password"
                            :error="form.errors.password"
                            required
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.password"
                        />
                    </div>

                    <div class="space-y-2">
                        <Label for="password_confirmation"
                            >Confirm Password</Label
                        >
                        <Input
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            type="password"
                            required
                        />
                    </div>

                    <div class="space-y-2">
                        <Label for="role">Role</Label>
                        <Select v-model="form.roles" multiple class="w-full">
                            <SelectTrigger>
                                <SelectValue placeholder="Select a role" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="role in roles"
                                    :key="role.id"
                                    :value="role.name"
                                >
                                    {{ role.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError class="mt-2" :message="form.errors.roles" />
                    </div>

                    <div class="flex justify-end space-x-2">
                        <Button
                            type="button"
                            variant="outline"
                            :disabled="form.processing"
                            @click="router.get(admin.user.index.url())"
                        >
                            Cancel
                        </Button>
                        <Button type="submit" :disabled="form.processing">
                            <LoaderCircle
                                v-if="form.processing"
                                class="h-4 w-4 animate-spin"
                            />
                            Update User
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
