<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { can } from '@/lib/can';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { useDebounceFn } from '@vueuse/core';
import { ArrowUpDown, Eye, Search, Trash2, X } from 'lucide-vue-next';
import { ref, watch } from 'vue';
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
}
interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface Filters {
    search?: string;
    sort_by?: string;
    sort_order?: 'asc' | 'desc';
}

interface Props {
    permissions: {
        data: Permission[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        links: PaginationLink[];
    };
    filters?: Filters;
}

const props = defineProps<Props>();
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Permissions',
        href: admin.permission.index.url()
    },
];

const search = ref(props.filters?.search || '');
const sortBy = ref(props.filters?.sort_by || '');
const sortOrder = ref<'' | 'asc' | 'desc'>(props.filters?.sort_order || '');

const deletePermission = (permissionId: number) => {
    if (confirm('Are you sure you want to delete this permission?')) {
        router.delete(admin.permission.delete.url({ id: permissionId }), {
            preserveScroll: true,
        });
    }
};

const performSearch = useDebounceFn(() => {
    router.get(
        admin.permission.index.url(),
        {
            search: search.value || undefined,
            sort_by: sortBy.value || undefined,
            sort_order: sortOrder.value || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
}, 300);

watch(search, () => {
    performSearch();
});

const sortTable = (column: string) => {
    if (sortBy.value === column) {
        sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortBy.value = column;
        sortOrder.value = 'asc';
    }
    performSearch();
};

const clearSearch = () => {
    search.value = '';
    sortBy.value = '';
    sortOrder.value = '';
    performSearch();
};

const getSortIcon = (column: string) => {
    if (sortBy.value !== column) return null;
    return sortOrder.value;
};

const hasActiveFilters = () => {
    return search.value;
};
</script>

<template>
    <Head title="Permission List" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container mx-auto px-4 py-6">
            <div class="mb-4 flex items-center justify-between">
                <HeadingSmall title="Permission List"></HeadingSmall>
            </div>

            <!-- Search Form -->
            <div class="mb-4 flex items-center gap-2">
                <div class="relative flex-1">
                    <Search
                        class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground"
                    />
                    <Input
                        v-model="search"
                        placeholder="Search by name..."
                        class="h-10 pr-9 pl-9"
                    />
                    <Button
                        v-if="search"
                        variant="ghost"
                        size="sm"
                        class="absolute top-1/2 right-1 h-7 w-7 -translate-y-1/2 p-0"
                        @click="search = ''"
                    >
                        <X class="h-4 w-4" />
                    </Button>
                </div>

                <!-- Clear Filters Button -->
                <Button
                    v-if="hasActiveFilters()"
                    variant="outline"
                    size="sm"
                    @click="clearSearch"
                    class="whitespace-nowrap"
                >
                    <X class="h-4 w-4" />
                </Button>
            </div>

            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>
                                <button
                                    @click="sortTable('name')"
                                    class="flex items-center gap-1 hover:text-foreground"
                                >
                                    Name
                                    <ArrowUpDown
                                        :class="[
                                            'h-4 w-4',
                                            getSortIcon('name') === 'desc'
                                                ? 'rotate-180'
                                                : '',
                                            sortBy === 'name'
                                                ? 'text-foreground'
                                                : 'text-muted-foreground',
                                        ]"
                                    />
                                </button>
                            </TableHead>
                            <TableHead> Gard Name </TableHead>
                            <TableHead> Roles </TableHead>
                            <TableHead class="w-[200px] text-right">
                                Actions
                            </TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-for="permission in permissions.data"
                            :key="permission.id"
                        >
                            <TableCell>{{ permission.name }}</TableCell>
                            <TableCell>{{ permission.guard_name }}</TableCell>
                            <TableCell>
                                <span
                                    v-for="(
                                        role, index
                                    ) in permission.roles.slice(0, 3)"
                                    :key="index"
                                >
                                    <Badge class="my-1 mr-2">
                                        {{ role.name }}
                                    </Badge>
                                </span>
                                <span v-if="permission.roles.length > 3"
                                    >...</span
                                >
                            </TableCell>
                            <TableCell class="text-right">
                                <div class="flex justify-end gap-2">
                                    <Link
                                        :href="admin.permission.show.url({id:permission.id})"
                                        v-if="can('permission.view')"
                                    >
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            class="flex items-center hover:cursor-pointer"
                                        >
                                            <Eye class="h-4 w-4" />
                                        </Button>
                                    </Link>

                                    <Button
                                        variant="destructive"
                                        size="sm"
                                        @click="deletePermission(permission.id)"
                                        class="flex items-center hover:cursor-pointer"
                                        v-if="can('permission.delete')"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="permissions.data.length === 0">
                            <TableCell
                                colspan="3"
                                class="py-8 text-center text-muted-foreground"
                            >
                                No roles found.
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <!-- Pagination -->
            <div
                v-if="permissions.last_page > 1"
                class="mt-4 flex items-center justify-between"
            >
                <div class="text-sm text-muted-foreground">
                    Showing
                    {{
                        (permissions.current_page - 1) * permissions.per_page +
                        1
                    }}
                    to
                    {{
                        Math.min(
                            permissions.current_page * permissions.per_page,
                            permissions.total,
                        )
                    }}
                    of {{ permissions.total }} results
                </div>
                <div class="flex gap-2">
                    <template
                        v-for="(link, index) in permissions.links"
                        :key="index"
                    >
                        <Button
                            v-if="link.url"
                            :variant="link.active ? 'default' : 'outline'"
                            size="sm"
                            :disabled="!link.url"
                            @click="router.visit(link.url)"
                        >
                            <span v-html="link.label"></span>
                        </Button>
                    </template>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
