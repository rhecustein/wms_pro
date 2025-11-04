<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\DB;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        // Query builder untuk activity logs
        $query = Activity::with(['subject', 'causer'])
            ->latest();

        // Filter by log name
        if ($request->filled('log_name')) {
            $query->where('log_name', $request->log_name);
        }

        // Filter by description/search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('description', 'like', '%' . $request->search . '%')
                  ->orWhereHasMorph('causer', ['App\Models\User'], function($q) use ($request) {
                      $q->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        // Filter by subject type
        if ($request->filled('subject_type')) {
            $query->where('subject_type', $request->subject_type);
        }

        // Filter by causer (user)
        if ($request->filled('causer_id')) {
            $query->where('causer_id', $request->causer_id)
                  ->where('causer_type', 'App\Models\User');
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Paginate results
        $activityLogs = $query->paginate(15)->withQueryString();

        // Get statistics
        $stats = [
            'total' => Activity::count(),
            'today' => Activity::whereDate('created_at', today())->count(),
            'this_week' => Activity::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => Activity::whereMonth('created_at', now()->month)
                                    ->whereYear('created_at', now()->year)
                                    ->count(),
        ];

        // Get unique log names for filter
        $logNames = Activity::select('log_name')
            ->distinct()
            ->whereNotNull('log_name')
            ->pluck('log_name');

        // Get unique subject types for filter
        $subjectTypes = Activity::select('subject_type')
            ->distinct()
            ->whereNotNull('subject_type')
            ->get()
            ->map(function($item) {
                return [
                    'value' => $item->subject_type,
                    'label' => class_basename($item->subject_type)
                ];
            });

        // Get users who have performed activities
        $users = DB::table('activity_log')
            ->where('causer_type', 'App\Models\User')
            ->distinct()
            ->join('users', 'activity_log.causer_id', '=', 'users.id')
            ->select('users.id', 'users.name')
            ->get();

        return view('system.activity-logs.index', compact(
            'activityLogs',
            'stats',
            'logNames',
            'subjectTypes',
            'users'
        ));
    }

    public function show(Activity $activityLog)
    {
        $activityLog->load(['subject', 'causer']);

        return view('system.activity-logs.show', compact('activityLog'));
    }

    public function destroy(Activity $activityLog)
    {
        try {
            $activityLog->delete();

            return redirect()
                ->route('system.activity-logs.index')
                ->with('success', 'Activity log deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete activity log: ' . $e->getMessage());
        }
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:1'
        ]);

        try {
            $date = now()->subDays($request->days);
            $deleted = Activity::where('created_at', '<', $date)->delete();

            return redirect()
                ->route('system.activity-logs.index')
                ->with('success', "Successfully deleted {$deleted} activity logs older than {$request->days} days.");
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete activity logs: ' . $e->getMessage());
        }
    }
}