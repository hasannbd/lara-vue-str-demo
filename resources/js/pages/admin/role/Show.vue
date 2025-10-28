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
import { can } from '@/lib/can';
import { formatDateTime } from '@/lib/utils';
import type { BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Plus } from 'lucide-vue-next';
import admin from "@/routes/admin";

interface Permission {
    id: number;
    name: string;
}
interface Role {
    id: number;
    name: string;
    permissions: Permission[];
    created_at: string;
    updated_at: string;
}
interface Props {
    role: Role;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Role Detail',
        href: admin.role.show.url({ id: props.role.id })
    }
];
</script>

<template>
    <Head title="Role Detail" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container mx-auto px-4 py-6">
            <div class="mb-4 flex items-center justify-between">
                <HeadingSmall title="User Detail" />

                <Link :href="admin.role.index.url()" class="ml-auto" v-if="can('role.view')">
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
            <div class="w-full flex-1 justify-start">
                <Table>
                    <TableBody>
                        <TableRow>
                            <TableHead
                                class="w-36 max-w-40 text-right text-black"
                                >Name:</TableHead
                            >
                            <TableCell class="text-left">
                                {{ role.name }}
                            </TableCell>
                        </TableRow>

                        <TableRow v-if="role.permissions.length > 0">
                            <TableHead class="text-right text-black"
                                >Permissions:</TableHead
                            >
                            <TableCell class="text-left">
                                <Badge
                                    v-for="permission in role.permissions"
                                    :key="permission.id"
                                    class="my-1 mr-2"
                                >
                                    {{ permission.name }}
                                </Badge>
                            </TableCell>
                        </TableRow>
                        <TableRow>
                            <TableHead class="text-right text-black"
                                >Created At:</TableHead
                            >
                            <TableCell class="text-left">
                                {{ formatDateTime(role.created_at) }}
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="role.updated_at">
                            <TableHead class="text-right text-black"
                                >Updated At:</TableHead
                            >
                            <TableCell class="text-left">
                                {{ formatDateTime(role.updated_at) }}
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </AppLayout>
</template>
