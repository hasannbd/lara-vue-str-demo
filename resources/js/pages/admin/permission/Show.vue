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
import { can } from '@/lib/can';
import admin from '@/routes/admin';


interface Role {
    id: number;
    name: string;
}
interface Permission {
    id: number;
    name: string;
    guard_name: string;
    roles: Role[];
    created_at: string;
    updated_at: string;
}
interface Props {
    permission: Permission;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Permission Detail',
        href: admin.permission.show.url({ id: props.permission.id })
    },
];
</script>

<template>
    <Head title="Permission Detail" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container mx-auto px-4 py-6">
            <div class="mb-4 flex items-center justify-between">
                <HeadingSmall title="Permission Detail" />

                <Link :href="admin.permission.index.url()" class="ml-auto" v-if="can('permission.view')">
                    <Button
                        class="flex items-center hover:cursor-pointer"
                        variant="default"
                        size="sm"
                    >
                        <Plus class="h-4 w-4" />
                        Permission List
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
                                {{ permission.name }}
                            </TableCell>
                        </TableRow>

                        <TableRow>
                            <TableHead
                                class="w-36 max-w-40 text-right text-black"
                            >Gard Name:</TableHead
                            >
                            <TableCell class="text-left">
                                {{ permission.guard_name }}
                            </TableCell>
                        </TableRow>

                        <TableRow v-if="permission.roles.length > 0">
                            <TableHead class="text-right text-black"
                                >Roles:</TableHead
                            >
                            <TableCell class="text-left">
                                <Badge
                                    v-for="role in permission.roles"
                                    :key="role.id"
                                    class="my-1 mr-2"
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
                                {{ formatDateTime(permission.created_at) }}
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="permission.updated_at">
                            <TableHead class="text-right text-black"
                                >Updated At:</TableHead
                            >
                            <TableCell class="text-left">
                                {{ formatDateTime(permission.updated_at) }}
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </AppLayout>
</template>
