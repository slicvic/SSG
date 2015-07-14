<?php $packages = $warehouse->packages; ?>
<table id="packages" class="table table-bordered table-striped table-condensed">
    <thead>
        <tr>
            <th>ID</th>
            <th>Status</th>
            <th>Type</th>
            <th>Length</th>
            <th>Width</th>
            <th>Height</th>
            <th>Weight</th>
            <th>Action</th>
        </tr>
    </thead>

    {!! view('warehouses.edit.package', ['package' => new \App\Models\Package()]) !!}

    @if (count($packages))
        @foreach ($packages as $package)
            {!! view('warehouses.edit.package', ['package' => $package]) !!}
        @endforeach
    @endif
</table>