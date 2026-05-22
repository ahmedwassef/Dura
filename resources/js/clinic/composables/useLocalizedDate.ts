import { computed } from 'vue';

export function useLocalizedDate() {
    const today = computed(() =>
        new Date().toLocaleDateString('en-GB', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
        }),
    );

    function formatDateTime(value: string): string {
        const date = new Date(value);

        if (Number.isNaN(date.getTime())) {
            return value;
        }

        return `${date.toLocaleDateString('en-GB')} · ${date.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' })}`;
    }

    return { today, formatDateTime };
}
