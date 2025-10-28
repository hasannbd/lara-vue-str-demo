<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
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
import admin from "@/routes/admin";

interface Role {
    name: string;
}

interface User {
    id: number;
    name: string;
    email: string;
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
    role?: string;
    sort_by?: string;
    sort_order?: 'asc' | 'desc';
}

interface Props {
    users: {
        data: User[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        links: PaginationLink[];
    };
    roles: Role[];
    filters?: Filters;
}

const props = defineProps<Props>();

const search = ref(props.filters?.search || '');
const selectedRole = ref(props.filters?.role || '');
const sortBy = ref(props.filters?.sort_by || '');
const sortOrder = ref<'' | 'asc' | 'desc'>(props.filters?.sort_order || '');

const deleteUser = (userId: number) => {
    if (confirm('Are you sure you want to delete this user?')) {
        router.delete(admin.user.delete.url({ id: userId }), {
            preserveScroll: true,
        });
    }
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};

const performSearch = useDebounceFn(() => {
    router.get(
        admin.user.index.url(),
        {
            search: search.value || undefined,
            role: selectedRole.value || undefined,
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

watch(selectedRole, () => {
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
    selectedRole.value = '';
    sortBy.value = '';
    sortOrder.value = '';
    performSearch();
};

const getSortIcon = (column: string) => {
    if (sortBy.value !== column) return null;
    return sortOrder.value;
};

const hasActiveFilters = () => {
    return search.value || selectedRole.value;
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users',
        href: admin.user.index.url()
    },
];
</script>

<template>
    <Head title="User List" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container mx-auto px-4 py-6">
            <div class="mb-4 flex items-center justify-between">
                <HeadingSmall title="User List"></HeadingSmall>

                <Link :href="admin.user.create.url()" class="ml-auto">
                    <Button
                        class="flex items-center hover:cursor-pointer"
                        variant="default"
                        size="sm"
                    >
                        <Plus class="h-4 w-4" />
                        Create User
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
                        placeholder="Search by name or email..."
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

                <!-- Role Filter Dropdown -->
                <div class="relative inline-block">
                    <Select v-model="selectedRole">
                        <SelectTrigger class="w-[200px]">
                            <SelectValue placeholder="Filter by role" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="role in roles"
                                :key="role.name"
                                :value="role.name"
                            >
                                {{ role.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <!-- Clear button (ghost style) -->
                    <Button
                        v-if="selectedRole"
                        @click="selectedRole = ''"
                        variant="ghost"
                        size="icon"
                        class="absolute top-1/2 right-8 h-6 w-6 -translate-y-1/2 text-muted-foreground"
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
                                    @click="sortTable('email')"
                                    class="flex items-center gap-1 hover:text-foreground"
                                >
                                    Email
                                    <ArrowUpDown
                                        :class="[
                                            'h-4 w-4',
                                            getSortIcon('email') === 'desc'
                                                ? 'rotate-180'
                                                : '',
                                            sortBy === 'email'
                                                ? 'text-foreground'
                                                : 'text-muted-foreground',
                                        ]"
                                    />
                                </button>
                            </TableHead>
                            <TableHead>Roles</TableHead>
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
                        <TableRow v-for="user in users.data" :key="user.id">
                            <TableCell>{{ user.name }}</TableCell>
                            <TableCell>{{ user.email }}</TableCell>
                            <TableCell>
                                <template v-if="user.roles.length > 0">
                                    <Badge
                                        v-for="role in user.roles"
                                        :key="role.name"
                                        class="mr-2"
                                    >
                                        {{ role.name }}
                                    </Badge>
                                </template>
                            </TableCell>
                            <TableCell>{{
                                formatDate(user.created_at)
                            }}</TableCell>
                            <TableCell class="text-right">
                                <div class="flex justify-end gap-2">
                                    <Link
                                        :href="admin.user.show.url(user.id)"
                                        v-if="can('user.view')"
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
                                        :href="admin.user.edit.url(user.id)"
                                        v-if="can('user.update')"
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
                                        @click="deleteUser(user.id)"
                                        class="flex items-center hover:cursor-pointer"
                                        v-if="can('user.delete')"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="users.data.length === 0">
                            <TableCell
                                colspan="5"
                                class="py-8 text-center text-muted-foreground"
                            >
                                No users found.
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <!-- Pagination -->
            <div
                v-if="users.last_page > 1"
                class="mt-4 flex items-center justify-between"
            >
                <div class="text-sm text-muted-foreground">
                    Showing
                    {{ (users.current_page - 1) * users.per_page + 1 }}
                    to
                    {{
                        Math.min(
                            users.current_page * users.per_page,
                            users.total,
                        )
                    }}
                    of {{ users.total }} results
                </div>
                <div class="flex gap-2">
                    <template v-for="(link, index) in users.links" :key="index">
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
