<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, User } from '@/types';

const props = defineProps<{
    user: User;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/user/dashboard',
    },
    {
        title: 'Profile',
        href: '/user/profile',
    },
];

const formatDate = (value: string) =>
    new Date(value).toLocaleDateString(undefined, {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
</script>

<template>
    <Head title="Profile" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto flex w-full max-w-4xl flex-col gap-6 p-4 md:p-6">
            <section class="rounded-xl border border-sidebar-border/70 bg-card p-6">
                <h1 class="text-2xl font-bold">Profile</h1>
                <p class="mt-1 text-sm text-muted-foreground">Read-only account overview.</p>
            </section>

            <section class="rounded-xl border border-sidebar-border/70 bg-card p-6">
                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-muted-foreground">Name</p>
                        <p class="mt-1 text-base font-semibold">{{ props.user.name }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-muted-foreground">Email</p>
                        <p class="mt-1 text-base font-semibold">{{ props.user.email }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-muted-foreground">Role</p>
                        <p class="mt-1 text-base font-semibold capitalize">{{ String(props.user.role ?? 'user') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-muted-foreground">Joined</p>
                        <p class="mt-1 text-base font-semibold">{{ formatDate(props.user.created_at) }}</p>
                    </div>
                </div>
            </section>
        </div>
    </AppLayout>
</template>
