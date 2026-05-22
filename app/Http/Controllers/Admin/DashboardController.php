<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\FormSubmission;
use App\Models\Patient;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        // KPI Data
        $totalPatients = Patient::count();
        $totalSubmissions = FormSubmission::count();
        $totalRevenue = FormSubmission::sum('grand_total');

        // Submissions per branch (for chart)
        $branchStats = Branch::withCount('formSubmissions')
            ->get()
            ->map(fn($b) => [
                'name' => $b->name,
                'count' => $b->form_submissions_count,
            ]);

        // Submissions by month (for chart) - SQLite compatible
        $monthlyStats = FormSubmission::select(
            DB::raw('count(id) as count'),
            DB::raw("strftime('%Y-%m', created_at) as month")
        )
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(6)
            ->get()
            ->reverse()
            ->values();

        // Recent Submissions
        $recentSubmissions = FormSubmission::with(['branch', 'user'])
            ->latest()
            ->limit(5)
            ->get();

        return Inertia::render('admin/Dashboard', [
            'stats' => [
                'total_patients' => $totalPatients,
                'total_submissions' => $totalSubmissions,
                'total_revenue' => number_format($totalRevenue, 2),
            ],
            'branch_stats' => $branchStats,
            'monthly_stats' => $monthlyStats,
            'recent_submissions' => $recentSubmissions,
        ]);
    }
}
