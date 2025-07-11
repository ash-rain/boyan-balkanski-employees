import { HomeIcon, SettingsIcon, UserIcon, UsersIcon } from 'lucide-vue-next';

export interface NavItem {
    title: string;
    href: string;
    icon: any;
    isAdmin?: boolean;
}

export const navItems: NavItem[] = [
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

export interface User {
    is_admin?: boolean;
}
