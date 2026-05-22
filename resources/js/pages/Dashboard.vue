<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import ClinicHeader from '@/clinic/components/layout/ClinicHeader.vue';
import LocalizedText from '@/clinic/components/ui/LocalizedText.vue';
import { 
    Zap, 
    Plug, 
    Gauge, 
    AlertTriangle, 
    TrendingDown,
    Activity,
    Info,
    ArrowUpRight,
    Search
} from 'lucide-vue-next';
import { ref } from 'vue';

const kpis = [
    { name: 'Potencia Actual', value: '1,240', unit: 'W', icon: Zap, color: 'text-[#00E5FF]', bg: 'bg-[#00E5FF]/10' },
    { name: 'Energía Hoy', value: '14.2', unit: 'kWh', icon: Gauge, color: 'text-[#32D74B]', bg: 'bg-[#32D74B]/10' },
    { name: 'Costo Estimado', value: '12.80', unit: 'Bs.', icon: Plug, color: 'text-[#00E5FF]', bg: 'bg-[#00E5FF]/10' },
];

const alerts = [
    { title: 'Consumo Vampiro Detectado', type: 'warning', message: 'Dispositivo en Stand-by detectado en Sala.', time: 'Hace 5m' },
    { title: 'Pico de Consumo', type: 'danger', message: 'Uso inusual de energía en Cocina (Horno).', time: 'Hace 12m' },
    { title: 'Voltaje Estable', type: 'success', message: 'Fase A operando en niveles óptimos.', time: 'En vivo' },
];

// Simple SVG Chart Data Mock
const chartPoints = "0,80 40,75 80,95 120,60 160,110 200,85 240,120 280,70 320,90 360,65 400,100 440,80 480,115 520,60 560,95 600,75 640,105 680,85 720,130 760,100 800,110";
</script>

<template>
    <Head title="WattVision - Monitoreo Eléctrico" />

    <div class="min-h-screen bg-[#121212] text-white selection:bg-[#00E5FF]/30">
        <ClinicHeader
            :form-name="{
                ar: 'لوحة التحكم',
                en: 'Control Panel',
            }"
            show-admin
        />

        <main class="mx-auto max-w-[1400px] p-6 md:p-8 space-y-8">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">
                        <LocalizedText :value="{ ar: 'لوحة التحكم', en: 'Control Panel' }" />
                    </h1>
                    <p class="text-[#98989D] font-medium mt-1">Monitoreo de red eléctrica residencial • Santa Cruz, BO</p>
                </div>
                <div class="flex gap-2">
                    <button class="bg-[#1E1E1E] border border-[#2C2C2E] px-4 py-2 rounded-xl text-sm font-bold hover:bg-[#252525] transition-colors">Histórico</button>
                    <button class="bg-[#00E5FF] text-[#121212] px-4 py-2 rounded-xl text-sm font-bold hover:shadow-[0_0_20px_rgba(0,229,255,0.4)] transition-all">Exportar Datos</button>
                </div>
            </div>

            <!-- KPI Row -->
            <div class="grid gap-6 md:grid-cols-3">
                <div v-for="kpi in kpis" :key="kpi.name" class="group overflow-hidden bg-[#1E1E1E] border border-[#2C2C2E] rounded-2xl shadow-xl hover:border-[#00E5FF]/30 transition-all duration-500">
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div :class="[kpi.bg, kpi.color, 'p-3 rounded-2xl group-hover:scale-110 transition-transform duration-500']">
                                <component :is="kpi.icon" class="size-6" />
                            </div>
                            <div class="text-[10px] font-bold text-[#98989D] uppercase tracking-widest flex items-center gap-1">
                                Promedio <ArrowUpRight class="size-3" />
                            </div>
                        </div>
                        <div class="mt-6 space-y-1">
                            <p class="text-[#98989D] font-bold text-xs uppercase tracking-widest">{{ kpi.name }}</p>
                            <div class="flex items-baseline gap-2">
                                <span class="text-4xl font-mono font-bold tracking-tighter">{{ kpi.value }}</span>
                                <span class="text-xl font-bold text-[#98989D]">{{ kpi.unit }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Grid: Charts & Alerts -->
            <div class="grid gap-6 lg:grid-cols-12">
                <!-- Main Consumption Chart -->
                <div class="lg:col-span-8 bg-[#1E1E1E] border border-[#2C2C2E] rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-6 flex flex-row items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-white">Consumo en Tiempo Real</h3>
                            <p class="text-xs text-[#98989D] font-medium mt-1">Carga total combinada en Watts (W)</p>
                        </div>
                        <div class="flex items-center gap-4 text-[10px] font-bold uppercase tracking-widest">
                            <div class="flex items-center gap-1.5"><div class="size-2 rounded-full bg-[#00E5FF]"></div> Actual</div>
                            <div class="flex items-center gap-1.5"><div class="size-2 rounded-full bg-white/10"></div> Referencia</div>
                        </div>
                    </div>
                    <div class="p-6 pb-10 pt-4">
                        <div class="relative h-[300px] w-full">
                            <!-- SVG Grid Lines -->
                            <div class="absolute inset-0 flex flex-col justify-between opacity-20 pointer-events-none">
                                <div v-for="i in 5" :key="i" class="w-full h-px bg-[#2C2C2E]"></div>
                            </div>
                            <!-- Mock SVG Chart -->
                            <svg class="h-full w-full overflow-visible" viewBox="0 0 800 150" preserveAspectRatio="none">
                                <!-- Area Gradient -->
                                <defs>
                                    <linearGradient id="areaGradient" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#00E5FF" stop-opacity="0.3" />
                                        <stop offset="100%" stop-color="#00E5FF" stop-opacity="0" />
                                    </linearGradient>
                                </defs>
                                <polyline
                                    fill="url(#areaGradient)"
                                    stroke="none"
                                    :points="`0,150 ${chartPoints} 800,150`"
                                />
                                <polyline
                                    fill="none"
                                    stroke="#00E5FF"
                                    stroke-width="3"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    :points="chartPoints"
                                    class="drop-shadow-[0_0_8px_rgba(0,229,255,0.5)]"
                                />
                            </svg>
                            <!-- X-Axis Labels -->
                            <div class="flex justify-between text-[10px] font-bold text-[#98989D] mt-6 px-2 uppercase tracking-widest">
                                <span>12:00</span><span>13:00</span><span>14:00</span><span>15:00</span><span>16:00</span><span>16:33</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alerts & Side Panel -->
                <div class="lg:col-span-4 space-y-6">
                    <div class="bg-[#1E1E1E] border border-[#2C2C2E] rounded-2xl shadow-xl overflow-hidden bg-transparent">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-white">Alertas de Sistema</h3>
                        </div>
                        <div class="space-y-4 p-0">
                            <div v-for="alert in alerts" :key="alert.title" 
                                 class="p-4 flex gap-4 transition-colors hover:bg-white/5 border-b border-[#2C2C2E] last:border-0"
                                 :class="{ 'border-l-4 border-l-[#FF453A] bg-[#FF453A]/5': alert.type === 'danger' || alert.type === 'warning' }">
                                <div class="shrink-0 mt-1">
                                    <AlertTriangle v-if="alert.type === 'danger' || alert.type === 'warning'" class="size-4 text-[#FF453A]" />
                                    <Info v-else class="size-4 text-[#32D74B]" />
                                </div>
                                <div class="space-y-1">
                                    <div class="flex items-center justify-between gap-2">
                                        <h4 class="text-sm font-bold tracking-tight" :class="{ 'text-[#FF453A]': alert.type === 'danger' || alert.type === 'warning' }">
                                            {{ alert.title }}
                                        </h4>
                                        <span class="text-[9px] font-bold text-[#98989D] uppercase tracking-widest whitespace-nowrap">{{ alert.time }}</span>
                                    </div>
                                    <p class="text-xs text-[#98989D] font-medium leading-relaxed">{{ alert.message }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 rounded-2xl bg-gradient-to-br from-[#00E5FF]/10 to-transparent border border-[#00E5FF]/20 space-y-3">
                        <h4 class="text-xs font-bold text-[#00E5FF] uppercase tracking-widest">Tip de Eficiencia</h4>
                        <p class="text-xs text-white font-medium leading-relaxed">Apagar el monitor secundario reduciría su consumo base en un 15% (Aprox. <span class="font-mono text-[#00E5FF]">2.50 Bs./mes</span>).</p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<style>
/* Font import for WattVision */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=JetBrains+Mono:wght@700&display=swap');
</style>
