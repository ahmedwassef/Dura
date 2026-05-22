import '../css/clinic/index.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { initializeTheme } from '@/composables/useAppearance';
import AppShell from '@/layouts/AppShell.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { initializeFlashToast } from '@/lib/flashToast';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    layout: (name) => {
        switch (true) {
            case name === 'Welcome':
                return null;
            case name.startsWith('clinic/') || name.startsWith('admin/') || name === 'Dashboard':
                return AppShell;
            case name.startsWith('auth/'):
                return [AppShell, AuthLayout];
            case name.startsWith('settings/'):
                return [AppShell, SettingsLayout];
            default:
                return AppShell;
        }
    },
    progress: {
        color: '#0d9488',
    },
});

// This will set light / dark mode on page load...
initializeTheme();

// This will listen for flash toast data from the server...
initializeFlashToast();
