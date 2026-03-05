<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityLog;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $filter = $request->input('filter'); // action filter

        $logs = ActivityLog::latest()
            ->when($search, function ($q) use ($search) {
                return $q->where('description', 'like', "%{$search}%")
                         ->orWhere('user_name', 'like', "%{$search}%");
            })
            ->when($filter, function ($q) use ($filter) {
                return $q->where('action', $filter);
            })
            ->paginate(20)
            ->withQueryString();

        $actionCounts = [
            'total' => ActivityLog::count(),
            'login' => ActivityLog::where('action', 'login')->count(),
            'created' => ActivityLog::where('action', 'created')->count(),
            'updated' => ActivityLog::where('action', 'updated')->count(),
            'deleted' => ActivityLog::where('action', 'deleted')->count(),
        ];

        return view('dashboard.activity_log', compact('logs', 'search', 'filter', 'actionCounts'));
    }
}
