@extends('admin.layout')

@section('content')
<h4>Organizer Detail</h4>

<table class="table table-bordered">
    <tr>
        <th>Club Name</th>
        <td>{{ $organizer->club_name }}</td>
    </tr>
    <tr>
        <th>PIC Name</th>
        <td>{{ $organizer->pic_name }}</td>
    </tr>
    <tr>
        <th>Email</th>
        <td>{{ $organizer->email }}</td>
    </tr>
    <tr>
        <th>Phone</th>
        <td>{{ $organizer->phone }}</td>
    </tr>
    <tr>
        <th>Status</th>
        <td>{{ ucfirst($organizer->status) }}</td>
    </tr>
</table>

<a href="/admin/organizers" class="btn btn-secondary">Back</a>
@endsection
