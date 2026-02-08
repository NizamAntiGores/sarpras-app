<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = ActivityLog::with('user');

        // Filter berdasarkan pencarian (deskripsi atau nama user)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter berdasarkan aksi
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter berdasarkan user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter berdasarkan tanggal
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        // Get unique actions for filter dropdown
        $actions = ActivityLog::select('action')->distinct()->pluck('action');

        // Get users who have activity logs
        $users = \App\Models\User::whereHas('activityLogs')->orderBy('name')->get(['id', 'name', 'role']);

        return view('activity_logs.index', compact('logs', 'actions', 'users'));
    }
}
