<script setup lang="ts">
import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { HomeIcon, SettingsIcon, UserIcon, UsersIcon } from 'lucide-vue-next';

const page = usePage();
const user = computed(() => page.props.auth.user);

const navItems = [
  {
    title: 'Dashboard',
    href: '/dashboard',
    icon: HomeIcon,
  },
  {
    title: 'Collaborations',
    href: '/employee-collaboration',
    icon: UsersIcon,
  },
  {
    title: 'Settings',
    href: '/settings/profile',
    icon: SettingsIcon,
  },
  {
    title: 'Users',
    href: '/users',
    icon: UserIcon,
    isAdmin: true,
  },
];

const filteredNavItems = computed(() => {
  return navItems.filter(item => !item.isAdmin || (user.value?.is_admin));
});
</script>

<template>
  <SidebarGroup class="px-2 py-0">
    <SidebarGroupLabel>Platform</SidebarGroupLabel>
    <SidebarMenu>
      <SidebarMenuItem v-for="item in filteredNavItems" :key="item.title">
        <SidebarMenuButton as-child :is-active="item.href === page.url" :tooltip="item.title">
          <Link :href="item.href">
            <component :is="item.icon" />
            <span>{{ item.title }}</span>
          </Link>
        </SidebarMenuButton>
      </SidebarMenuItem>
    </SidebarMenu>
  </SidebarGroup>
</template>
