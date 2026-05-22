<script setup lang="ts">
import { ref, onMounted, onUnmounted, watch } from 'vue';
import { useClinicLocale } from '@/clinic/composables/useClinicLocale';
import LocalizedText from '@/clinic/components/ui/LocalizedText.vue';
import { Eraser } from 'lucide-vue-next';

const props = defineProps<{
    modelValue?: string;
    label: { ar: string; en: string };
    placeholder?: string;
    disabled?: boolean;
}>();

const emit = defineEmits(['update:modelValue', 'clear']);

const { isArabic } = useClinicLocale();
const canvas = ref<HTMLCanvasElement | null>(null);
const isDrawing = ref(false);
const hasDrawing = ref(false);

let ctx: CanvasRenderingContext2D | null = null;

function loadSignature(dataUrl: string) {
    if (!ctx || !dataUrl) return;
    const img = new Image();
    img.crossOrigin = 'anonymous';
    img.onload = () => {
        if (!canvas.value) return;
        ctx?.clearRect(0, 0, canvas.value.width, canvas.value.height);
        ctx?.drawImage(img, 0, 0);
        hasDrawing.value = true;
    };
    img.src = dataUrl;
}

onMounted(() => {
    if (canvas.value) {
        ctx = canvas.value.getContext('2d');
        if (ctx) {
            ctx.strokeStyle = '#000';
            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';
        }
        
        if (props.modelValue) {
            loadSignature(props.modelValue);
        }
    }
});

watch(() => props.modelValue, (newVal) => {
    if (newVal) {
        loadSignature(newVal);
    } else {
        clear();
    }
});

function getPos(e: MouseEvent | TouchEvent) {
    if (!canvas.value) return { x: 0, y: 0 };
    const rect = canvas.value.getBoundingClientRect();
    const clientX = 'touches' in e ? e.touches[0].clientX : e.clientX;
    const clientY = 'touches' in e ? e.touches[0].clientY : e.clientY;
    return {
        x: clientX - rect.left,
        y: clientY - rect.top
    };
}

function startDrawing(e: MouseEvent | TouchEvent) {
    if (props.disabled) return;
    isDrawing.value = true;
    const pos = getPos(e);
    ctx?.beginPath();
    ctx?.moveTo(pos.x, pos.y);
    e.preventDefault();
}

function draw(e: MouseEvent | TouchEvent) {
    if (!isDrawing.value || !ctx) return;
    const pos = getPos(e);
    ctx.lineTo(pos.x, pos.y);
    ctx.stroke();
    hasDrawing.value = true;
    e.preventDefault();
}

function stopDrawing() {
    if (!isDrawing.value) return;
    isDrawing.value = false;
    saveSignature();
}

function clear() {
    if (!canvas.value || !ctx) return;
    ctx.clearRect(0, 0, canvas.value.width, canvas.value.height);
    hasDrawing.value = false;
    emit('update:modelValue', '');
    emit('clear');
}

function saveSignature() {
    if (!canvas.value || !hasDrawing.value) return;
    const dataUrl = canvas.value.toDataURL('image/png');
    emit('update:modelValue', dataUrl);
}
</script>

<template>
    <div class="signature-container" :class="{ disabled }">
        <label class="sig-label"><LocalizedText :value="label" /></label>
        <div class="canvas-wrap" :class="{ disabled }">
            <canvas 
                ref="canvas"
                width="400"
                height="150"
                @mousedown="startDrawing"
                @mousemove="draw"
                @mouseup="stopDrawing"
                @mouseleave="stopDrawing"
                @touchstart="startDrawing"
                @touchmove="draw"
                @touchend="stopDrawing"
            ></canvas>
            
            <div class="canvas-overlay" v-if="!hasDrawing">
                <span>{{ placeholder || (isArabic ? 'وقع هنا' : 'Sign here') }}</span>
            </div>

            <button type="button" class="clear-btn" @click="clear" v-if="hasDrawing && !disabled">
                <Eraser class="size-4" />
            </button>
        </div>
    </div>
</template>

<style scoped>
.signature-container {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.sig-label {
    font-size: 13px;
    font-weight: 700;
    color: var(--ink-mute);
}

.canvas-wrap {
    position: relative;
    background: #fdfdfd;
    border: 2px dashed var(--line);
    border-radius: 12px;
    overflow: hidden;
    cursor: crosshair;
    height: 150px;
}

.canvas-wrap.disabled {
    cursor: default;
    background: #f5f5f5;
    border-style: solid;
    opacity: 0.8;
}

canvas {
    width: 100%;
    height: 100%;
    touch-action: none;
}

.canvas-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    pointer-events: none;
    color: var(--ink-mute);
    opacity: 0.5;
    font-size: 14px;
}

.clear-btn {
    position: absolute;
    top: 8px;
    right: 8px;
    background: white;
    border: 1px solid var(--line);
    padding: 6px;
    border-radius: 6px;
    color: var(--red);
    cursor: pointer;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}

[dir="rtl"] .clear-btn {
    right: auto;
    left: 8px;
}
</style>
