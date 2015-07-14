<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Cargo</th>
            <th>Type</th>
            <th>Status</th>
            <th>L x W x H</th>
            <th>Weight</th>
            <th>Tracking #</th>
            <th>Invoice #</th>
            <th>Invoice $</th>
            <th>Description</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($packages as $package)
            <tr class="{{ $package->present()->colorStatus() }}">
                <td>{{ $package->id }}</td>
                <td>{!! $package->present()->cargoLink() !!}</td>
                <td>{{ $package->present()->type() }}</td>
                <td>{{ $package->present()->status() }}</td>
                <td>{{ $package->present()->dimensions() }}</td>
                <td>{{ $package->present()->weight() }}</td>
                <td>{{ $package->tracking_number }}</td>
                <td>{{ $package->invoice_number }}</td>
                <td>{{ $package->invoice_amount }}</td>
                <td>{{ $package->description }}</td>
            </tr>
        @endforeach
    </tbody>
</table>