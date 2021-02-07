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
                  $Chart = new FusionCharts("angulargauge", "angulargauge-1" , "400", "200", "angulargauge-container", "json", $gaugeData);

                  // Render the chart
                  $Chart->render();

                    $Chart2 = new FusionCharts("angulargauge", "angulargauge-2" , "400", "200", "angulargauge-container2", "json", $gaugeData2);

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


                <div class="col-md-6">
                    <div id="piechart" style="width: 600px; height: 200px;"></div>

                </div>
                <div class="col-md-6">

                    <div id="barchart" style="width: 600px; height: 300px;"></div>
                </div>







            </div>





        </div>






















    </div>
@stop

@section('javascript')

    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

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
            regionChart.draw(regionData, regionOptions);

            var areaChart = new google.visualization.BarChart(document.getElementById('barchart'));
            areaChart.draw(areaData, areaOptions);


        }
    </script>


@stop
