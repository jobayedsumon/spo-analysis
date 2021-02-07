<?php

declare(strict_types = 1);

namespace App\Charts;

use App\Models\FieldForce;
use App\Models\OrderDelivery;
use Chartisan\PHP\Chartisan;
use ConsoleTVs\Charts\BaseChart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FieldForceChartAreaWise extends BaseChart
{
    /**
     * Handles the HTTP request for the given chart.
     * It must always return an instance of Chartisan
     * and never a string or an array.
     * @param Request $request
     * @return Chartisan
     */
    public function handler(Request $request): Chartisan
    {
        $labels = [];
        $data = [];
        $data2 = [];

        $order_deliveries = DB::table('order_deliveries')
            ->select('ASMArea', DB::raw('count(DISTINCT FieldForceCode) as total_field_force'))
            ->groupByRaw('ASMArea')
            ->get();

        foreach ($order_deliveries as $order_delivery) {
            $total_spo = FieldForce::where('ASMArea', $order_delivery->ASMArea)->count();
            $outside_sfa = $total_spo - $order_delivery->total_field_force;
            if ($outside_sfa < 0) {
                $outside_sfa = 0;
            }
            array_push($labels, $order_delivery->ASMArea);
            array_push($data, $order_delivery->total_field_force);
            array_push($data2, $outside_sfa);
        }

        return Chartisan::build()
            ->labels($labels)
            ->dataset('Inside SFA', $data)
            ->dataset('Outside SFA', $data2);
    }
}
