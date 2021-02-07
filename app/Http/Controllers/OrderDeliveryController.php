<?php

namespace App\Http\Controllers;

use App\Models\FieldForce;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            ->groupBy('RSMArea')
            ->get();

        return view('region-wise-field-forces', compact('regions'));
    }

    public function area_wise_field_forces()
    {
        $areas = DB::table('field_forces')
            ->where('Name', '!=', 'VACANT')
            ->select('ASMArea')
            ->selectRaw('count(*) as total')
            ->selectRaw("count(case when isCapable = 1 then 1 end) as capable")
            ->selectRaw("count(case when isCapable = 0 then 1 end) as incapable")
            ->groupBy('ASMArea')
            ->get();

        return view('area-wise-field-forces', compact('areas'));
    }

    public function dashboard()
    {


        $total_field_forces_count = FieldForce::all()->count();
        $occupied_field_forces_count = FieldForce::where('Name', '!=', 'VACANT')->count();
        $vacant_field_forces_count = $total_field_forces_count - $occupied_field_forces_count;

        $occupied_percent = ($occupied_field_forces_count * 100) / $total_field_forces_count;
        $vacant_percent = ($vacant_field_forces_count * 100) / $total_field_forces_count;


        $capable = FieldForce::where('isCapable', true)->count();
        $incapable = FieldForce::where('isCapable', false)->count();


//        $obj = [];
//        foreach ($region_wise_capable as $data) {
//            $obj[] = [
//              'label' => $data ->RSMArea,
//              'value' => $data ->total_capable,
//            ];
//        }
//
//        $obj = json_encode($obj);
//        $doughnutData = json_encode('{
//  "chart": {
//    "caption": "Android Distribution for our app",
//    "subcaption": "For all users in 2017",
//    "showpercentvalues": "1",
//    "defaultcenterlabel": "Android Distribution",
//    "aligncaptionwithcanvas": "0",
//    "captionpadding": "0",
//    "decimals": "1",
//    "plottooltext": "<b>$percentValue</b> of our Android users are on <b>$label</b>",
//    "centerlabel": "# Users: $value",
//    "theme": "fusion"
//  },
//  "data": "$obj"
//}');



        $capable_percent = ($capable * 100) / $occupied_field_forces_count;
        $incapable_percent = ($incapable * 100) / $occupied_field_forces_count;

        $date = '6th February, 2021';


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







        return view('voyager::index', compact('gaugeData', 'gaugeData2', 'data'));
    }


    public function synchronize_data()
    {
        $geotagPercent = 1;
        $orderPercent = 20;
        $deliveryConfirmPercent = 20;

        $occupied_field_forces = FieldForce::where('Name', '!=', 'VACANT')->get();

        foreach ($occupied_field_forces as $field_force) {
            $total_order_by_spo = 0;
            $total_delivery_by_spo = 0;

            if ($field_force->geotags) {

                $totalRetailer = $field_force->geotags()->sum('TotalRetailer');
                $totalGeoTag = $field_force->geotags()->sum('TotalGeoTag');

                if ($totalGeoTag >= floor(($totalRetailer * $geotagPercent) / 100)) {
                    $field_force->update([
                        'canGeotag' => true
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

                        if ($total_order_by_spo >= floor(($totalGeoTag * $orderPercent) / 100)) {
                            $field_force->update([
                                'canOrder' => true
                            ]);
                            if ($total_delivery_by_spo >= floor(($total_order_by_spo * $deliveryConfirmPercent) / 100)) {
                                $field_force->update([
                                    'isCapable' => true,
                                    'canConfirmDelivery' => true,
                                ]);
                            }
                            else {
                                $field_force->update([
                                    'isCapable' => false,
                                    'canConfirmDelivery' => false
                                ]);
                            }
                        }
                        else {
                            $field_force->update([
                                'isCapable' => false,
                                'canOrder' => false
                            ]);
                        }
                    }
                    else {
                        $field_force->update([
                            'isCapable' => false,
                            'canOrder' => false
                        ]);
                    }
                }
                else {
                    $field_force->update([
                        'isCapable' => false,
                        'canGeotag' => false
                    ]);
                }
            }
            else {
                $field_force->update([
                    'isCapable' => false,
                    'canGeotag' => false
                ]);
            }
        }

        return redirect(route('dashboard'));
    }

}
