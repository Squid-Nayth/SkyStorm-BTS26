<?php

namespace App\Http\Controllers;

use App\Models\PostReport;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    protected function ensureAdmin(): void
    {
        abort_unless(auth()->check() && auth()->user()->is_admin, 403);
    }

    public function index()
    {
        $this->ensureAdmin();

        $reports = PostReport::with(['post.user', 'user'])
            ->latest()
            ->get();

        return view('admin.reports.index', compact('reports'));
    }

    public function update(Request $request, PostReport $report)
    {
        $this->ensureAdmin();

        $request->validate([
            'status' => 'required|in:accepted,rejected',
            'admin_note' => 'nullable|string|max:255',
        ]);

        $report->update([
            'status' => $request->input('status'),
            'admin_note' => $request->input('admin_note'),
            'reviewed_at' => now(),
        ]);

        return back()->with('status', 'Signalement traité.');
    }
}
