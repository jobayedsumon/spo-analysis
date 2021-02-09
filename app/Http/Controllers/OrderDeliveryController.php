<?php

namespace App\Http\Controllers;

use App\Models\FieldForce;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use TCG\Voyager\Facades\Voyager;
use function MongoDB\BSON\toJSON;

class OrderDeliveryController extends Controller
{
    //

    public function region_wise_field_forces()
    {
        $regions = DB::table('field_forces')
            ->where('Name', '!=', 'VACANT')
            ->select('RSMArea')
            ->selectRaw('count(*) as total')
            ->selectRaw("count(case when isCapable = 1 then 1 end) as capable")
            ->selectRaw("count(case when isCapable = 0 then 1 end) as incapable")
            ->selectRaw("count(case when canGeotag = 1 then 1 end) as can_geotag")
            ->selectRaw("count(case when canOrderVisit = 1 then 1 end) as can_order_visit")
            ->selectRaw("count(case when canConfirmDelivery = 1 then 1 end) as can_confirm_delivery")
            ->groupBy('RSMArea')
            ->get();

        return view('region-wise-field-forces', compact('regions'));
    }

    public function region_field_force($region)
    {
        $field_forces = DB::table('field_forces')
            ->where('Name', '!=', 'VACANT')
            ->where('RSMArea', $region)
            ->get();

        return view('region-field-force', compact('field_forces', 'region'));
    }

    public function area_wise_field_forces()
    {
        $areas = DB::table('field_forces')
            ->where('Name', '!=', 'VACANT')
            ->select('ASMArea')
            ->selectRaw('count(*) as total')
            ->selectRaw("count(case when isCapable = 1 then 1 end) as capable")
            ->selectRaw("count(case when isCapable = 0 then 1 end) as incapable")
            ->selectRaw("count(case when canGeotag = 1 then 1 end) as can_geotag")
            ->selectRaw("count(case when canOrderVisit = 1 then 1 end) as can_order_visit")
            ->selectRaw("count(case when canConfirmDelivery = 1 then 1 end) as can_confirm_delivery")
            ->groupBy('ASMArea')
            ->get();

        return view('area-wise-field-forces', compact('areas'));
    }

    public function area_field_force($area)
    {
        $field_forces = DB::table('field_forces')
            ->where('Name', '!=', 'VACANT')
            ->where('ASMArea', $area)
            ->get();

        return view('area-field-force', compact('field_forces', 'area'));
    }

    public function dashboard()
    {
        $total_field_forces_count = FieldForce::all()->count();
        $occupied_field_forces_count = FieldForce::where('Name', '!=', 'VACANT')->count();
        $vacant_field_forces_count = $total_field_forces_count - $occupied_field_forces_count;

        $occupied_percent = ceil(($occupied_field_forces_count * 100) / $total_field_forces_count);
        $vacant_percent = ceil(($vacant_field_forces_count * 100) / $total_field_forces_count);

        $capable = FieldForce::where('isCapable', true)->count();
        $incapable = FieldForce::where('isCapable', false)->count();

        $capable_percent = ceil(($capable * 100) / $occupied_field_forces_count);
        $incapable_percent = ceil(($incapable * 100) / $occupied_field_forces_count);

        $date = '7th February, 2021';

        $gaugeData = "{
                                \"chart\": {
                                    \"caption\": \"As Of: $date | Occupied SPO: $occupied_field_forces_count | Capable SPO: $capable | Incapable SPO: $incapable\",
                                    \"captionFontSize\": \"12\",
                                    \"captionFontBold\": \"1\",
                                    \"captionFontColor\": \"#000\",
                                    \"lowerLimit\": \"0\",
                                    \"upperLimit\": \"100\",
                                    \"showValue\": \"1\",
                                    \"numberSuffix\": \"%\",
                                    \"theme\": \"fusion\",
                                    \"showToolTip\": \"0\"
                                },
                                \"colorRange\": {
                                    \"color\": [
                                    {
                                        \"minValue\": \"0\",
                                        \"maxValue\": \"$capable_percent\",
                                        \"code\": \"#62B58F\"
                                    },
                                    {
                                        \"minValue\": \"$incapable_percent\",
                                        \"maxValue\": \"100\",
                                        \"code\": \"#F2726F\"
                                    }]
                                },
                                \"dials\": {
                                    \"dial\": [{
                                        \"value\": \"$capable_percent\"
                                    }]
                                }
                            }";

        $gaugeData2 = "{
                                \"chart\": {
                                    \"caption\": \"As Of: $date | Total SPO: $total_field_forces_count | Occupied SPO: $occupied_field_forces_count | Vacant SPO: $vacant_field_forces_count\",
                                    \"lowerLimit\": \"0\",
                                    \"captionFontSize\": \"12\",
                                    \"captionFontBold\": \"1\",
                                    \"captionFontColor\": \"#000\",
                                    \"upperLimit\": \"100\",
                                    \"showValue\": \"1\",
                                    \"numberSuffix\": \"%\",
                                    \"theme\": \"fusion\",
                                    \"showToolTip\": \"0\"
                                },
                                \"colorRange\": {
                                    \"color\": [
                                    {
                                        \"minValue\": \"0\",
                                        \"maxValue\": \"$occupied_percent\",
                                        \"code\": \"#62B58F\"
                                    },
                                    {
                                        \"minValue\": \"$vacant_percent\",
                                        \"maxValue\": \"100\",
                                        \"code\": \"#F2726F\"
                                    }]
                                },
                                \"dials\": {
                                    \"dial\": [{
                                        \"value\": \"$occupied_percent\"
                                    }]
                                }
                            }";


        $data['regionData'] = DB::table('field_forces')
            ->where('Name', '!=', 'VACANT')
            ->select('RSMArea')
            ->selectRaw('count(*) as total')
            ->selectRaw("count(case when isCapable = 1 then 1 end) as capable")
            ->selectRaw("count(case when isCapable != 1 then 1 end) as incapable")
            ->groupBy('RSMArea')
            ->orderBy('capable')
            ->get();


        $data['areaData'] = DB::table('field_forces')
            ->where('Name', '!=', 'VACANT')
            ->select('ASMArea')
            ->selectRaw('count(*) as total')
            ->selectRaw("count(case when isCapable = 1 then 1 end) as capable")
            ->selectRaw("count(case when isCapable != 1 then 1 end) as incapable")
            ->groupBy('ASMArea')
            ->orderBy('capable')
            ->get();

        $data['spoEvaluation'] = DB::table('field_forces')
            ->where('Name', '!=', 'VACANT')
            ->selectRaw('count(*) as total')
            ->selectRaw("count(case when canGeotag = 1 then 1 end) as can_geotag")
            ->selectRaw("count(case when canOrderVisit = 1 then 1 end) as can_order_visit")
            ->selectRaw("count(case when canConfirmDelivery = 1 then 1 end) as can_confirm_delivery")
            ->first();

        $data['orderDelivery'] = DB::table('order_deliveries')
            ->whereNotNull('OrderNo')
            ->whereNotNull('OrderDate')
            ->select('OrderDate')
//            ->selectRaw('count(*) as total_order')
//            ->selectRaw("count(case when InvoiceNo = '' and InvoiceDate = '' then 1 end) as total_undelivered")
            ->selectRaw("count(case when OrderDate = InvoiceDate then 1 end) as first_day_delivery")
            ->selectRaw("count(case when OrderDate+1 = InvoiceDate then 1 end) as second_day_delivery")
            ->selectRaw("count(case when OrderDate+2 = InvoiceDate then 1 end) as third_day_delivery")
            ->selectRaw("count(case when OrderDate+3 = InvoiceDate then 1 end) as fourth_day_delivery")
            ->selectRaw("count(case when OrderDate+4 = InvoiceDate then 1 end) as fifth_day_delivery")
            ->selectRaw("count(case when OrderDate+5 = InvoiceDate then 1 end) as sixth_day_delivery")
            ->selectRaw("count(case when OrderDate+6 = InvoiceDate then 1 end) as seventh_day_delivery")
            ->groupBy('OrderDate')
            ->get();



        return view('voyager::index', compact('gaugeData', 'gaugeData2', 'data'));
    }


    public function synchronize_data()
    {
        $geotagPercent = Voyager::setting('admin.geo-tag-percent');
        $orderVisitPercent = Voyager::setting('admin.order-visit-percent');
        $deliveryConfirmPercent = Voyager::setting('admin.delivery-confirm-percent');

        $occupied_field_forces = FieldForce::where('Name', '!=', 'VACANT')->get();

        foreach ($occupied_field_forces as $field_force) {
            $total_order_by_spo = 0;
            $total_delivery_by_spo = 0;

            if ($field_force->geotags) {

                $totalRetailer = $field_force->geotags()->sum('TotalRetailer');
                $totalGeoTag = $field_force->geotags()->sum('TotalGeoTag');

                if ($totalGeoTag >= floor(($totalRetailer * $geotagPercent) / 100)) {
                    $field_force->update([
                        'canGeotag' => true,
                        'isCapable' => false,
                        'canOrderVisit' => false,
                        'canConfirmDelivery' => false
                    ]);

                    if ($field_force->retail_visits) {

                        $totalOrderVisit = $field_force->retail_visits()->sum('TotalVisit');

                        if ($totalOrderVisit >= floor(($totalGeoTag * $orderVisitPercent) / 100)) {
                            $field_force->update([
                                'canOrderVisit' => true,
                                'isCapable' => false,
                                'canConfirmDelivery' => false
                            ]);

                            if ($field_force->order_deliveries) {
                                $order_deliveries = $field_force->order_deliveries;

                                foreach ($order_deliveries as $order_delivery) {
                                    if (!empty($order_delivery->OrderNo) && !empty($order_delivery->OrderDate)) {
                                        $total_order_by_spo++;
                                    }
                                    if (!empty($order_delivery->InvoiceNo) && !empty($order_delivery->InvoiceDate)) {
                                        $total_delivery_by_spo++;
                                    }
                                }

                                if ($total_delivery_by_spo >= floor(($total_order_by_spo * $deliveryConfirmPercent) / 100)) {
                                    $field_force->update([
                                        'isCapable' => true,
                                        'canGeotag' => true,
                                        'canOrderVisit' => true,
                                        'canConfirmDelivery' => true
                                    ]);
                                } else {
                                    $field_force->update([
                                        'isCapable' => false,
                                        'canConfirmDelivery' => false,
                                    ]);
                                }
                            }
                            else {
                                $field_force->update([
                                    'isCapable' => false,
                                    'canConfirmDelivery' => false,
                                ]);
                            }
                        } else {
                            $field_force->update([
                                'isCapable' => false,
                                'canOrderVisit' => false,
                                'canConfirmDelivery' => false
                            ]);
                        }

                    }
                    else {
                        $field_force->update([
                            'isCapable' => false,
                            'canOrderVisit' => false,
                            'canConfirmDelivery' => false
                        ]);
                    }
                }
                else {
                    $field_force->update([
                        'isCapable' => false,
                        'canGeotag' => false,
                        'canOrderVisit' => false,
                        'canConfirmDelivery' => false
                    ]);
                }
            }
            else {
                $field_force->update([
                    'isCapable' => false,
                    'canGeotag' => false,
                    'canOrderVisit' => false,
                    'canConfirmDelivery' => false
                ]);
            }
        }

        return redirect(route('dashboard'));
    }

    public function region_capability($region, $capability)
    {
        if ($capability == 'capable') {
            $capabilityValue = 1;
        } else if ($capability == 'incapable'){
            $capabilityValue = 0;
        }
        $field_forces = DB::table('field_forces')
            ->where('Name', '!=', 'VACANT')
            ->where('RSMArea', $region)
            ->where('isCapable', $capabilityValue)
            ->get();

        return view('modal-with-data', compact('field_forces'))->render();

    }

    public function area_capability($area, $capability)
    {
        if ($capability == 'capable') {
            $capabilityValue = 1;
        } else if ($capability == 'incapable'){
            $capabilityValue = 0;
        }
        $field_forces = DB::table('field_forces')
            ->where('Name', '!=', 'VACANT')
            ->where('ASMArea', $area)
            ->where('isCapable', $capabilityValue)
            ->get();

        return view('modal-with-data', compact('field_forces'))->render();

    }

    public function order_delivery()
    {

    }

}
