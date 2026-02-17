<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Services\OrphanedFileScanner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InfrastructureController extends Controller
{
    public function index()
    {
        // Get storage leaderboard
        $businesses = Business::all()->map(function($business) {
            return [
                'id' => $business->id,
                'name' => $business->name,
                'storage_used' => $business->storageUsed(),
                'is_under_maintenance' => $business->is_under_maintenance
            ];
        })->sortByDesc('storage_used');

        // Total system storage
        $totalStorage = $businesses->sum('storage_used');

        return view('superadmin.infrastructure.index', compact('businesses', 'totalStorage'));
    }

    public function toggleMaintenance(Business $business)
    {
        $business->update([
            'is_under_maintenance' => !$business->is_under_maintenance
        ]);

        return back()->with('success', 'Maintenance mode updated for ' . $business->name);
    }

    public function scanOrphanedFiles(OrphanedFileScanner $scanner)
    {
        $results = $scanner->scan();
        
        return back()->with('info', "Scan complete. Found {$results['count']} orphaned files (" . format_size($results['total_size']) . "). Check system logs for details.");
    }
}
