@extends('layouts.admin.page.index')

@section('title', 'Companies')
@section('subtitle', 'Manage Companies')

@section('actions')
    <a href="/company/create" class="btn btn-primary"><i class="fa fa-plus"></i> Add New Company</a>
@stop

@section('thead')
    <th>ID</th>
    <th>Name</th>
    <th>Corp Code</th>
    <th>Action</th>
@stop

@section('tbody')
    @foreach ($companies as $company)
       <tr>
            <td>{{ $company->id }}</td>
            <td>{{ $company->name }}</td>
            <td>{{ $company->shortname }}</td>
            <td>
                <div class="btn-group">
                    <a href="/company/{{ $company->id }}/edit" class="btn-white btn btn-sm"><i class="fa fa-pencil"></i> Edit</a>
                </div>
            </td>
       </tr>
    @endforeach
@stop
