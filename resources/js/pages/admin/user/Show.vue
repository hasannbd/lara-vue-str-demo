<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { formatDateTime } from '@/lib/utils';
import type { BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Plus } from 'lucide-vue-next';
import admin from '@/routes/admin';



interface Role {
    name: string;
}
interface User {
    id: number;
    name: string;
    email: string;
    roles: Role[];
    created_at: string;
    updated_at: string;
}
interface Props {
    user: User;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'User Detail',
        href: admin.user.show.url({ id: props.user.id })
    },
];
</script>

<template>
    <Head title="User Detail" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container mx-auto px-4 py-6">
            <div class="mb-4 flex items-center justify-between">
                <HeadingSmall title="User Detail" />

                <Link :href="admin.user.index.url()" class="ml-auto">
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
            <div class="w-full flex-1 justify-start">
                <Table>
                    <TableBody>
                        <TableRow>
                            <TableHead
                                class="w-36 max-w-40 text-right text-black"
                                >Name:</TableHead
                            >
                            <TableCell class="text-left">
                                {{ user.name }}
                            </TableCell>
                        </TableRow>
                        <TableRow>
                            <TableHead class="text-right text-black"
                                >Email:</TableHead
                            >
                            <TableCell class="text-left">
                                {{ user.email }}
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="user.roles.length > 0">
                            <TableHead class="text-right text-black"
                                >Roles:</TableHead
                            >
                            <TableCell class="text-left">
                                <Badge
                                    v-for="role in user.roles"
                                    :key="role.name"
                                    class="mr-2 my-1"
                                >
                                    {{ role.name }}
                                </Badge>
                            </TableCell>
                        </TableRow>
                        <TableRow>
                            <TableHead class="text-right text-black"
                                >Created At:</TableHead
                            >
                            <TableCell class="text-left">
                                {{ formatDateTime(user.created_at) }}
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="user.updated_at">
                            <TableHead class="text-right text-black"
                                >Updated At:</TableHead
                            >
                            <TableCell class="text-left">
                                {{ formatDateTime(user.updated_at) }}
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </AppLayout>
</template>
