<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AdminPageLayout from '@/clinic/layouts/AdminPageLayout.vue';
import ClinicHeader from '@/clinic/components/layout/ClinicHeader.vue';
import { 
    Users, 
    FileText, 
    DollarSign, 
    TrendingUp,
    Activity,
    ArrowUpRight,
    ArrowDownRight,
    Clock,
    MapPin,
    ArrowRight
} from 'lucide-vue-next';

const props = defineProps<{
    stats: {
        total_patients: number;
        total_submissions: number;
        total_revenue: string;
    };
    branch_stats: Array<{ name: string, count: number }>;
    monthly_stats: Array<{ month: string, count: number }>;
    recent_submissions: Array<any>;
}>();

// Simple Chart logic
const maxBranchCount = Math.max(...props.branch_stats.map(b => b.count), 1);
const maxMonthlyCount = Math.max(...props.monthly_stats.map(m => m.count), 1);

function getMonthlyPoints() {
    if (props.monthly_stats.length === 0) return "0,150 800,150";
    const width = 800;
    const height = 150;
    const step = width / (props.monthly_stats.length - 1 || 1);
    
    return props.monthly_stats.map((m, i) => {
        const x = i * step;
        const y = height - (m.count / maxMonthlyCount) * height;
        return `${x},${y}`;
    }).join(' ');
}

const chartPoints = getMonthlyPoints();

</script>

<template>
    <Head title="لوحة التحكم الإدارية" />

    <AdminPageLayout
        :form-name="{
            ar: 'لوحة التحكم الإدارية',
            en: 'Admin Dashboard',
        }"
    >
        <div class="dashboard-container">
            <div class="dashboard-header">
                <div>
                    <h1 class="page-title">نظرة عامة على النظام</h1>
                    <p class="page-subtitle">إحصائيات الأداء والعمليات عبر كافة الفروع</p>
                </div>
                <div class="header-actions">
                    <Link href="/dashboard/clinic/patients" class="btn btn-primary">
                        <Users class="size-4 mr-2" />
                        إدارة المرضى
                    </Link>
                    <Link href="/dashboard/form-categories" class="btn btn-outline" style="margin-right: 10px;">
                        تصنيفات النماذج
                    </Link>
                    <Link href="/dashboard/form-templates" class="btn btn-outline" style="margin-right: 10px;">
                        نماذج النظام
                    </Link>
                    <Link href="/dashboard/clinic/home" class="btn btn-outline" style="margin-right: 10px;">
                        العودة للنماذج الطبية
                    </Link>
                </div>

            </div>

        <!-- KPI Grid -->
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-icon-wrap blue">
                    <Users class="size-6" />
                </div>
                <div class="kpi-data">
                    <p class="kpi-label">إجمالي المرضى</p>
                    <div class="kpi-value-row">
                        <span class="kpi-value">{{ stats.total_patients }}</span>
                        <span class="kpi-trend up"><ArrowUpRight class="size-3" /> 12%</span>
                    </div>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon-wrap green">
                    <FileText class="size-6" />
                </div>
                <div class="kpi-data">
                    <p class="kpi-label">إجمالي النماذج</p>
                    <div class="kpi-value-row">
                        <span class="kpi-value">{{ stats.total_submissions }}</span>
                        <span class="kpi-trend up"><ArrowUpRight class="size-3" /> 8%</span>
                    </div>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon-wrap orange">
                    <DollarSign class="size-6" />
                </div>
                <div class="kpi-data">
                    <p class="kpi-label">إجمالي الإيرادات</p>
                    <div class="kpi-value-row">
                        <span class="kpi-value">{{ stats.total_revenue }}</span>
                        <span class="currency">ر.س</span>
                    </div>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon-wrap purple">
                    <Activity class="size-6" />
                </div>
                <div class="kpi-data">
                    <p class="kpi-label">متوسط الزيارات</p>
                    <div class="kpi-value-row">
                        <span class="kpi-value">42</span>
                        <span class="kpi-trend down"><ArrowDownRight class="size-3" /> 3%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="charts-section">
            <div class="main-chart-card">
                <div class="card-header">
                    <h3 class="card-title">نمو النماذج الشهرية</h3>
                    <div class="chart-legend">
                        <div class="legend-item"><span class="dot blue"></span> النماذج المكتملة</div>
                    </div>
                </div>
                <div class="chart-content">
                    <div class="svg-wrap">
                        <svg viewBox="0 0 800 150" preserveAspectRatio="none">
                            <defs>
                                <linearGradient id="chartGrad" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="var(--primary)" stop-opacity="0.2" />
                                    <stop offset="100%" stop-color="var(--primary)" stop-opacity="0" />
                                </linearGradient>
                            </defs>
                            <polyline fill="url(#chartGrad)" :points="`0,150 ${chartPoints} 800,150`" />
                            <polyline fill="none" stroke="var(--primary)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" :points="chartPoints" />
                        </svg>
                    </div>
                    <div class="chart-x-axis">
                        <span v-for="m in monthly_stats" :key="m.month">{{ m.month }}</span>
                    </div>
                </div>
            </div>

            <div class="side-chart-card">
                <h3 class="card-title">توزيع الفروع</h3>
                <div class="branch-bars">
                    <div v-for="branch in branch_stats" :key="branch.name" class="branch-bar-item">
                        <div class="bar-info">
                            <span class="branch-name">{{ branch.name }}</span>
                            <span class="branch-count">{{ branch.count }}</span>
                        </div>
                        <div class="bar-track">
                            <div class="bar-fill" :style="{ width: (branch.count / maxBranchCount * 100) + '%' }"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity Table -->
        <div class="activity-section">
            <div class="card">
                <div class="card-header flex justify-between items-center">
                    <h3 class="card-title">آخر النماذج المضافة</h3>
                    <Link href="/dashboard/clinic/records" class="view-all">عرض الكل <ArrowRight class="size-3 ml-1" /></Link>
                </div>
                <div class="table-wrap">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>المريض</th>
                                <th>الفرع</th>
                                <th>بواسطة</th>
                                <th>المبلغ</th>
                                <th>التاريخ</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="sub in recent_submissions" :key="sub.id">
                                <td class="font-bold">{{ sub.patient_name }}</td>
                                <td>
                                    <span class="branch-tag">
                                        <MapPin class="size-3 mr-1" /> {{ sub.branch?.name }}
                                    </span>
                                </td>
                                <td>{{ sub.user?.name }}</td>
                                <td class="price">{{ sub.grand_total }} ر.س</td>
                                <td class="date">
                                    <Clock class="size-3 mr-1" /> {{ new Date(sub.created_at).toLocaleDateString('ar-SA') }}
                                </td>
                                <td>
                                    <a :href="`/dashboard/clinic/submissions/${sub.uuid}/pdf`" target="_blank" class="icon-btn">
                                        <FileText class="size-4" />
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        </div>
    </AdminPageLayout>
</template>

<style scoped>
.dashboard-container {
    padding: 2rem;
    max-width: 1400px;
    margin: 0 auto;
}

.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-bottom: 2.5rem;
}

.page-title {
    font-size: 2rem;
    font-weight: 800;
    color: var(--primary);
    margin: 0;
}

.page-subtitle {
    color: var(--ink-mute);
    margin-top: 0.5rem;
}

/* KPI Cards */
.kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2.5rem;
}

.kpi-card {
    background: white;
    padding: 1.5rem;
    border-radius: 1.25rem;
    border: 1.5px solid var(--line);
    display: flex;
    align-items: center;
    gap: 1.25rem;
    transition: all 0.3s ease;
}

.kpi-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.05);
    border-color: var(--primary-soft);
}

.kpi-icon-wrap {
    width: 3.5rem;
    height: 3.5rem;
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.kpi-icon-wrap.blue { background: rgba(13, 148, 136, 0.1); color: var(--primary); }
.kpi-icon-wrap.green { background: rgba(34, 197, 94, 0.1); color: #22c55e; }
.kpi-icon-wrap.orange { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
.kpi-icon-wrap.purple { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; }

.kpi-label {
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--ink-mute);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.25rem;
}

.kpi-value-row {
    display: flex;
    align-items: baseline;
    gap: 0.5rem;
}

.kpi-value {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--ink);
}

.currency {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--ink-mute);
}

.kpi-trend {
    font-size: 0.75rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.kpi-trend.up { color: #22c55e; }
.kpi-trend.down { color: #ef4444; }

/* Charts */
.charts-section {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 1.5rem;
    margin-bottom: 2.5rem;
}

@media (max-width: 1024px) {
    .charts-section { grid-template-columns: 1fr; }
}

.main-chart-card, .side-chart-card, .card {
    background: white;
    border-radius: 1.25rem;
    border: 1.5px solid var(--line);
    padding: 1.5rem;
}

.card-header {
    margin-bottom: 1.5rem;
}

.card-title {
    font-size: 1.125rem;
    font-weight: 800;
    color: var(--ink);
    margin: 0;
}

.chart-legend {
    margin-top: 0.5rem;
}

.legend-item {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--ink-mute);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

.dot.blue { background: var(--primary); }

.chart-content {
    margin-top: 1rem;
}

.svg-wrap {
    height: 200px;
    width: 100%;
}

.svg-wrap svg {
    width: 100%;
    height: 100%;
    overflow: visible;
}

.chart-x-axis {
    display: flex;
    justify-content: space-between;
    margin-top: 1rem;
    font-size: 0.7rem;
    font-weight: 700;
    color: var(--ink-mute);
}

/* Branch Bars */
.branch-bars {
    margin-top: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.branch-bar-item {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.bar-info {
    display: flex;
    justify-content: space-between;
    font-size: 0.875rem;
    font-weight: 700;
}

.branch-count {
    color: var(--primary);
}

.bar-track {
    height: 8px;
    background: var(--bg-soft);
    border-radius: 4px;
    overflow: hidden;
}

.bar-fill {
    height: 100%;
    background: var(--primary);
    border-radius: 4px;
}

/* Table */
.dashboard-table {
    width: 100%;
    border-collapse: collapse;
}

.dashboard-table th {
    text-align: right;
    padding: 1rem;
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--ink-mute);
    text-transform: uppercase;
    border-bottom: 1px solid var(--line);
}

.dashboard-table td {
    padding: 1.25rem 1rem;
    font-size: 0.875rem;
    border-bottom: 1px solid var(--line);
}

.dashboard-table tr:last-child td {
    border-bottom: none;
}

.branch-tag {
    background: var(--bg-soft);
    padding: 0.25rem 0.625rem;
    border-radius: 0.5rem;
    font-size: 0.75rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
}

.price {
    font-weight: 800;
    color: var(--primary);
}

.date {
    color: var(--ink-mute);
    display: flex;
    align-items: center;
}

.icon-btn {
    color: var(--ink-mute);
    transition: color 0.2s;
}

.icon-btn:hover {
    color: var(--primary);
}

.view-all {
    font-size: 0.875rem;
    font-weight: 700;
    color: var(--primary);
    display: flex;
    align-items: center;
}
</style>
