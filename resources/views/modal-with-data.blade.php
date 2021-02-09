<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
    </button>
    <h4 class="modal-title" id="myModalLabel">Field Force List</h4>
</div>
<div class="modal-body">
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Code</th>
            <th>SPO</th>
            <th>Distributor</th>
            <th>Mobile</th>
        </tr>
        </thead>
        <tbody>
        @forelse($field_forces as $field_force)
            <tr>
                <td>{{ $field_force->Code }}</td>
                <td>{{ $field_force->Name }}</td>
                <td>{{ $field_force->DistributorName }}</td>
                <td>{{ $field_force->Mobile }}</td>
            </tr>
        @empty
        @endforelse

        </tbody>
    </table>
</div>


