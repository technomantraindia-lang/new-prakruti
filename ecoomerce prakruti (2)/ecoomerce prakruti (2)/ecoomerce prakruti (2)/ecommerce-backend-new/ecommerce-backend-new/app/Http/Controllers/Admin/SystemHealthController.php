<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SystemHealthController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        if (! $user || ! $user->isSuperAdmin()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Super Admin access required.'], 403);
            }
            abort(403, 'Super Admin access required.');
        }

        // 1. Database Check
        $dbStatus = 'Healthy';
        try {
            DB::connection()->getPdo();
        } catch (\Throwable $e) {
            $dbStatus = 'Failed';
        }

        // 2. Storage Writable Check
        $storageWritable = is_writable(storage_path()) ? 'Healthy' : 'Failed';

        // 3. Failed Jobs Count
        $failedJobsCount = 0;
        try {
            $failedJobsCount = DB::table('failed_jobs')->count();
        } catch (\Throwable $e) {
            // table might not exist in un-migrated test environment
        }

        $healthMetrics = [
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'environment' => config('app.env'),
            'database_status' => $dbStatus,
            'queue_driver' => config('queue.default', 'sync'),
            'failed_jobs_count' => $failedJobsCount,
            'storage_writable' => $storageWritable,
            'mail_driver' => config('mail.default', 'smtp'),
            'cache_driver' => config('cache.default', 'file'),
        ];

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'health' => $healthMetrics,
            ]);
        }

        return view('admin.system.health', [
            'health' => $healthMetrics,
        ]);
    }
}
