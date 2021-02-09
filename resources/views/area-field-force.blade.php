@extends('voyager::master')



@section('content')
    <div class="page-content browse container-fluid">
        @include('voyager::alerts')

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">

                        <h1 id="pageTitle" class="text-primary">Field Force List of {{ $area }} Area</h1>

                        <div class="table-responsive">
                            <table id="dataTable" class="table table-hover table-striped">
                                <thead>
                                <tr>
                                    <th>S/L</th>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Can Geo Tag ({{ \TCG\Voyager\Facades\Voyager::setting('admin.geo-tag-percent') }}%)</th>
                                    <th>Can Order Visit ({{ \TCG\Voyager\Facades\Voyager::setting('admin.order-visit-percent') }}%)</th>
                                    <th>Can Confirm Delivery ({{ \TCG\Voyager\Facades\Voyager::setting('admin.confirm-delivery-percent') }}%)</th>
                                    <th class="text-red">CAPABILITY</th>

                                </tr>
                                </thead>
                                <tbody>
                                @forelse($field_forces as $field_force)
                                    <tr>
                                        <td>{{ $loop->index+1 }}</td>
                                        <td>{{ $field_force->Code }}</td>
                                        <td>{{ $field_force->Name }}</td>
                                        <td>{{ $field_force->canGeotag ? 'YES' : 'NO' }}</td>
                                        <td>{{ $field_force->canOrderVisit ? 'YES' : 'NO' }}</td>
                                        <td>{{ $field_force->canConfirmDelivery ? 'YES' : 'NO' }}</td>
                                        <td>{{ $field_force->isCapable ? 'CAPABLE' : 'INCAPABLE' }}</td>

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
                        title: $('#pageTitle').text()
                    },
                    {
                        extend: 'excelHtml5',
                        title: $('#pageTitle').text()
                    },
                    'copy'
                ]
            })
        });
    </script>
@stop
