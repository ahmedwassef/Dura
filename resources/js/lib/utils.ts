import type { InertiaLinkProps } from '@inertiajs/vue3';
import { clsx } from 'clsx';
import type { ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function toUrl(href: NonNullable<InertiaLinkProps['href']>) {
    return typeof href === 'string' ? href : href?.url;
}

/** Map Wayfinder `.form()` output to `useForm().submit(method, url)` for Inertia v3. */
export function wayfinderFormRoute(route: { action: string; method: string }): {
    method: string;
    url: string;
} {
    return { method: route.method, url: route.action };
}
