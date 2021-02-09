@extends('voyager::master')



@section('content')
    <div class="page-content browse container-fluid">
        @include('voyager::alerts')

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">

                        <h1 class="text-primary">Region Wise Capable and Incapable Field Forces</h1>

                        <div class="table-responsive">
                            <table id="dataTable" class="table table-hover table-striped">
                                <thead>
                                <tr>
                                    <th>S/L</th>
                                    <th>Region</th>
                                    <th>Occupied Field Force</th>
                                    <th>Can Geo Tag</th>
                                    <th>Can Order Visit</th>
                                    <th>Can Confirm Delivery</th>
                                    <th>Capable Field Force</th>
                                    <th>Incapable Field Force</th>
                                    <th>View Details</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($regions as $region)
                                    <tr>
                                        <td>{{ $loop->index+1 }}</td>
                                        <td>{{ $region->RSMArea }}</td>
                                        <td>{{ $region->total }}</td>
                                        <td>{{ $region->can_geotag }}</td>
                                        <td>{{ $region->can_order_visit }}</td>
                                        <td>{{ $region->can_confirm_delivery }}</td>
                                        <td>{{ $region->capable }}</td>
                                        <td>{{ $region->incapable }}</td>
                                        <td><a href="{{ route('region-field-force', $region->RSMArea) }}">View Details</a></td>
                                    </tr>
                                @empty
                                @endforelse
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>


@stop

@section('css')

@stop

@section('javascript')

    <script>
        $(document).ready(function() {
            $('table').DataTable( {
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'csvHtml5',
                        title: 'Region Wise Capable and Incapable Field Forces'
                    },
                    {
                        extend: 'excelHtml5',
                        title: 'Region Wise Capable and Incapable Field Forces',
                    },
                    'copy'
                ]
            })
        });
    </script>
@stop
