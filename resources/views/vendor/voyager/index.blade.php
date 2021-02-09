@extends('voyager::master')

@section('css')

@stop
@section('content')
    <div class="page-content">
        @include('voyager::alerts')
{{--        @include('voyager::dimmers')--}}


        @php include('../app/Charts/fusioncharts.php') @endphp



{{--        <div class="row">--}}
{{--            <div class="col-md-3">--}}
{{--                <div id="chart" style="height: 350px;"></div>--}}
{{--            </div>--}}
{{--            <div class="col-md-9">--}}
{{--                <div id="chartAreaWise" style="height: 350px;"></div>--}}
{{--            </div>--}}
{{--        </div>--}}


        @php


                  // chart object
                  $Chart = new FusionCharts("angulargauge", "angulargauge-1" , "100%", "200", "angulargauge-container", "json", $gaugeData);

                  // Render the chart
                  $Chart->render();

                    $Chart2 = new FusionCharts("angulargauge", "angulargauge-2" , "100%", "200", "angulargauge-container2", "json", $gaugeData2);

                  // Render the chart
                  $Chart2->render();




        @endphp


{{--        $columnChart = new FusionCharts("doughnut2d", "ex1", "100%", 400, "doughnut-1", "json", $doughnutData);--}}

{{--        $columnChart->render();--}}







        <div class="container-fluid">
            <div class="row">

                <div class="col-sm-4">
                    <div id="angulargauge-container2">Chart will render here!</div>
                </div>
                <div class="col-sm-4">
                    <div id="angulargauge-container">Chart will render here!</div>
                </div>

                <div class="col-sm-2">
                    <form action="{{ route('synchronize-data') }}" method="POST">
                        @csrf
                        <button class="btn btn-primary">Synchronize Data (Will take time)</button>
                    </form>
                </div>




            </div>

            <div class="row">


                <div class="col-md-3">
                    <div id="piechart" style="width: 100%; height: 200px;"></div>

                </div>
                <div class="col-md-2">
                    <h5 style="font-size: 10px">Individual Breakdown of Evaluation <br>(Out of {{ $data['spoEvaluation']->total }} SPOs)</h5>

                    <table class="table table-striped table-hovered" style="font-size: 10px">
                        <thead>
                        <tr>
                            <th>
                                Can Geotag at least {{ \TCG\Voyager\Facades\Voyager::setting('admin.geo-tag-percent') }}%
                            </th>
                            <th>
                                {{ $data['spoEvaluation']->can_geotag }}
                            </th>
                            <th>
                                {{ ceil($data['spoEvaluation']->can_geotag * 100 / $data['spoEvaluation']->total) }}%
                            </th>
                        </tr>
                        <tr>
                            <th>
                                Can Visit / Order at least {{ \TCG\Voyager\Facades\Voyager::setting('admin.order-visit-percent') }}%
                            </th>
                            <th>
                                {{ $data['spoEvaluation']->can_order_visit }}
                            </th>
                            <th>
                                {{ ceil($data['spoEvaluation']->can_order_visit * 100 / $data['spoEvaluation']->total) }}%
                            </th>
                        </tr>
                        <tr>
                            <th>
                                Can Confirm Delivery at least {{ \TCG\Voyager\Facades\Voyager::setting('admin.delivery-confirm-percent') }}%
                            </th>
                            <th>
                                {{ $data['spoEvaluation']->can_confirm_delivery }}
                            </th>
                            <th>
                                {{ ceil($data['spoEvaluation']->can_confirm_delivery * 100 / $data['spoEvaluation']->total) }}%
                            </th>
                        </tr>

                        </thead>
                    </table>
                </div>
                <div class="col-md-6">

                    <div id="barchart" style="width: 100%; height: 300px;"></div>
                </div>







            </div>





        </div>


        <div id="container" style="width:100%; height:400px;"></div>



        <!-- Modal -->
        <div class="modal fade" id="spoModal" tabindex="-1" role="dialog" aria-labelledby="spoModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content" id="dynamic-content">

                </div>
            </div>
        </div>










    </div>
@stop

@section('javascript')

    <script type="text/javascript">

        var orderDelivery =  <?php echo json_encode($data['orderDelivery']) ?>;

        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function drawChart() {

            var regionData = google.visualization.arrayToDataTable([
                ['Region Name', 'Capable', 'Incapable'],

                @php
                    foreach($data['regionData'] as $d) {
                        echo "['".$d->RSMArea."', ".$d->capable.", ".$d->incapable."],";
                    }

                @endphp
            ]);

            var areaData = google.visualization.arrayToDataTable([
                ['Area Name', 'Capable', 'Incapable'],

                @php
                    foreach($data['areaData'] as $d) {
                        echo "['".$d->ASMArea."', ".$d->capable.", ".$d->incapable."],";
                    }
                @endphp
            ]);

            var regionOptions = {
                title: 'Region Wise Capable / Incapable Field Force ',
                is3D: true,
                legend: 'none',
                pieSliceText: 'value',
                tooltip: {
                    text: 'value'
                },
                fontSize: 6,
                titleFontSize: 8,
            };

            var areaOptions = {
                title: 'Area Wise Capable / Incapable Field Force',
                is3D: true,
                fontSize: 8,
                titleFontSize: 8,
            };

            var regionChart = new google.visualization.BarChart(document.getElementById('piechart'));

            function selectHandlerRegion() {
                var selectedItem = regionChart.getSelection()[0];

                if (selectedItem) {
                    var region = regionData.getValue(selectedItem.row, 0);
                    capability = selectedItem.column == 1 ? 'capable' : 'incapable';

                    $.ajax({
                        type:'GET',
                        dataType: 'html',
                        url:'/field-forces/region/'+region+'/capability/'+capability,
                        success:function(data){
                            $('#dynamic-content').html('');
                            $('#dynamic-content').html(data);
                            $('#spoModal').modal('show');

                        }
                    });
                }
            }
            google.visualization.events.addListener(regionChart, 'select', selectHandlerRegion);
            regionChart.draw(regionData, regionOptions);

            var areaChart = new google.visualization.BarChart(document.getElementById('barchart'));
            function selectHandlerArea() {
                var selectedItem = areaChart.getSelection()[0];

                if (selectedItem) {
                    var area = areaData.getValue(selectedItem.row, 0);
                    capability = selectedItem.column == 1 ? 'capable' : 'incapable';

                    $.ajax({
                        type:'GET',
                        dataType: 'html',
                        url:'/field-forces/area/'+area+'/capability/'+capability,
                        success:function(data){
                            $('#dynamic-content').html('');
                            $('#dynamic-content').html(data);
                            $('#spoModal').modal('show');

                        }
                    });
                }
            }
            google.visualization.events.addListener(areaChart, 'select', selectHandlerArea);
            areaChart.draw(areaData, areaOptions);


        }
    </script>


@stop
