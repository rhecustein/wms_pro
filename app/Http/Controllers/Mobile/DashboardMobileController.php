<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\PutawayTask;
use App\Models\PickingOrder;
use Illuminate\Http\Request;

class DashboardMobileController extends Controller
{
    /**
     * Show the Mobile Operator Dashboard (Task Summary).
     */
    public function index()
    {
        $user = auth()->user();
        
        // Mengambil jumlah tugas yang tertunda untuk operator yang login
        $pendingPutaway = PutawayTask::where('assigned_to_user_id', $user->id)
                                    ->where('status', 'pending')
                                    ->count();

        $pendingPicking = PickingOrder::where('assigned_to_user_id', $user->id)
                                    ->where('status', 'pending')
                                    ->count();
        
        // Catatan: Tampilan ini biasanya sederhana dan berfokus pada navigasi cepat.
        return view('mobile.dashboard.index', compact('pendingPutaway', 'pendingPicking'));
    }
}