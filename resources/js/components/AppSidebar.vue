<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, Folder, File, LayoutGrid, Users, PackageSearch, ShoppingCart } from 'lucide-vue-next';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import AppLogo from './AppLogo.vue';
import { computed } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth.user);

const dashboardHref = computed(() =>
    user.value?.role === 'admin' ? '/admin/dashboard' : '/user/dashboard'
);

const mainNavItems = computed<NavItem[]>(() => {
    const role = user.value?.role;

    if (role === 'admin') {
        return [
            { title: 'Dashboard', href: '/admin/dashboard', icon: LayoutGrid },
            { title: 'Users', href: '/admin/users', icon: Users },
            { title: 'Folders', href: '/admin/folders', icon: Folder },
            { title: 'File', href: '/admin/folders/file', icon: File },
        ];
    }
    return [
        { title: 'Dashboard', href: '/user/dashboard', icon: LayoutGrid },
        { title: 'Folders', href: '/user/folders', icon: Folder },
        { title: 'Files', href: '/user/files', icon: File },
    ];
});


</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboardHref">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>
        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>
        <SidebarFooter>
        
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>