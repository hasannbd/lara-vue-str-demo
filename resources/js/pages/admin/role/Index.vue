<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
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
import { formatDate } from '@/lib/utils';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { useDebounceFn } from '@vueuse/core';
import {
    ArrowUpDown,
    Eye,
    Pencil,
    Plus,
    Search,
    Trash2,
    X,
} from 'lucide-vue-next';
import { ref, watch } from 'vue';
import admin from '@/routes/admin';

interface Role {
    id: number;
    name: string;
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
    roles: {
        data: Role[];
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
        title: 'Roles',
        href: admin.role.index.url(),
    },
];

const search = ref(props.filters?.search || '');
const sortBy = ref(props.filters?.sort_by || '');
const sortOrder = ref<'' | 'asc' | 'desc'>(props.filters?.sort_order || '');

const deleteRole = (roleId: number) => {
    if (confirm('Are you sure you want to delete this role?')) {
        router.delete(admin.role.delete.url({ id: roleId }), {
            preserveScroll: true,
        });
    }
};

const performSearch = useDebounceFn(() => {
    router.get(
        admin.role.index.url(),
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
    <Head title="Role List" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container mx-auto px-4 py-6">
            <div class="mb-4 flex items-center justify-between">
                <HeadingSmall title="Role List"></HeadingSmall>

                <Link
                    :href="admin.role.create.url()"
                    class="ml-auto"
                    v-if="can('role.create')"
                >
                    <Button
                        class="flex items-center hover:cursor-pointer"
                        variant="default"
                        size="sm"
                    >
                        <Plus class="h-4 w-4" />
                        Create Role
                    </Button>
                </Link>
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

                            <TableHead>
                                <button
                                    @click="sortTable('created_at')"
                                    class="flex items-center gap-1 hover:text-foreground"
                                >
                                    Created At
                                    <ArrowUpDown
                                        :class="[
                                            'h-4 w-4',
                                            getSortIcon('created_at') === 'desc'
                                                ? 'rotate-180'
                                                : '',
                                            sortBy === 'created_at'
                                                ? 'text-foreground'
                                                : 'text-muted-foreground',
                                        ]"
                                    />
                                </button>
                            </TableHead>
                            <TableHead class="w-[200px] text-right">
                                Actions
                            </TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="role in roles.data" :key="role.id">
                            <TableCell>{{ role.name }}</TableCell>
                            <TableCell>{{
                                formatDate(role.created_at)
                            }}</TableCell>
                            <TableCell class="text-right">
                                <div class="flex justify-end gap-2">
                                    <Link
                                        :href="admin.role.show.url({ id: role.id })"
                                        v-if="can('role.view')"
                                    >
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            class="flex items-center hover:cursor-pointer"
                                        >
                                            <Eye class="h-4 w-4" />
                                        </Button>
                                    </Link>
                                    <Link
                                        :href="admin.role.edit.url({ id: role.id })"
                                        v-if="can('role.update')"
                                    >
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            class="flex items-center hover:cursor-pointer"
                                        >
                                            <Pencil class="h-4 w-4" />
                                        </Button>
                                    </Link>
                                    <Button
                                        variant="destructive"
                                        size="sm"
                                        @click="deleteRole(role.id)"
                                        class="flex items-center hover:cursor-pointer"
                                        v-if="can('role.delete')"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="roles.data.length === 0">
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
                v-if="roles.last_page > 1"
                class="mt-4 flex items-center justify-between"
            >
                <div class="text-sm text-muted-foreground">
                    Showing
                    {{ (roles.current_page - 1) * roles.per_page + 1 }}
                    to
                    {{
                        Math.min(
                            roles.current_page * roles.per_page,
                            roles.total,
                        )
                    }}
                    of {{ roles.total }} results
                </div>
                <div class="flex gap-2">
                    <template v-for="(link, index) in roles.links" :key="index">
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
