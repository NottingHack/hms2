@extends('layouts.app')

@section('content')
<h1>Roles</h1>

@foreach ($roles as $category => $categoryRoles)
    <h2>{{ $category }}</h2>
    <table>
        <thead>
            <tr>
                <th>Role</th>
                <th>Code</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($categoryRoles as $role)
            <tr>
                <td><a href="{{ route('roles.show', $role['id']) }}">{{ $role['displayName'] }}</a></td>
                <td>{{ $role['name'] }}</td>
                <td>{{ $role['description'] }}</td>
                <td>@can('role.edit.all')
                <a href="{{ route('roles.edit', $role['id']) }}">edit</a>
                @endcan</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endforeach

@endsection
