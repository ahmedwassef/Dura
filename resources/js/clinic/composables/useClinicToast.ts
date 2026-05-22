import { ref } from 'vue';

type ToastType = '' | 'success' | 'error';

const visible = ref(false);
const message = ref('');
const type = ref<ToastType>('');

let hideTimer: ReturnType<typeof setTimeout> | null = null;

export function useClinicToast() {
    function toast(text: string, toastType: ToastType = ''): void {
        message.value = text;
        type.value = toastType;
        visible.value = true;

        if (hideTimer) {
            clearTimeout(hideTimer);
        }

        hideTimer = setTimeout(() => {
            visible.value = false;
        }, 3200);
    }

    return {
        visible,
        message,
        type,
        toast,
    };
}
