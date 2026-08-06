<?php

namespace App\Helpers;

nano app/Helpers/DbHelper.php
use Illuminate\Support\Facades\DB;

class DbHelper
{
    public static function getMonthFunction($column)
    {
        $driver = DB::connection()->getDriverName();
        
        if ($driver === 'sqlite') {
            return "strftime('%m', {$column})";
        } elseif ($driver === 'pgsql') {
            return "EXTRACT(MONTH FROM {$column})";
        } else {
            return "MONTH({$column})";
        }
    }
    
    public static function getYearFunction($column)
    {
        $driver = DB::connection()->getDriverName();
        
        if ($driver === 'sqlite') {
            return "strftime('%Y', {$column})";
        } elseif ($driver === 'pgsql') {
            return "EXTRACT(YEAR FROM {$column})";
        } else {
            return "YEAR({$column})";
        }
    }

use App\Helpers\DbHelper;

public function earnings()
{
    $completedJobs = DispatchOrder::where('driver_id', auth()->id())
        ->where('status', 'delivered')
        ->orderBy('updated_at', 'desc')
        ->paginate(20);
    
    $totalEarnings = DispatchOrder::where('driver_id', auth()->id())
        ->where('status', 'delivered')
        ->->sum('base_price') ?? 0;
    
    // Cross-database compatible monthly earnings
    $monthFunction = DbHelper::getMonthFunction('updated_at');
    $yearFunction = DbHelper::getYearFunction('updated_at');
    
    $monthlyEarnings = DispatchOrder::where('driver_id', auth()->id())
        ->where('status', 'delivered')
        ->whereRaw("{$yearFunction} = ?", [date('Y')])
        ->selectRaw("{$monthFunction} as month, SUM(amount) as total")
        ->groupBy('month')
        ->get();
    
    return view('driver.earnings', compact('completedJobs', 'totalEarnings', 'monthlyEarnings'));
}

}