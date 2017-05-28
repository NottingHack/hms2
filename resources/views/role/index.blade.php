@extends('layouts.app')

@section('pageTitle', 'Roles')

@section('content')

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
                <td><a href="{{ route('roles.show', $role->getId()) }}">{{ $role->getDisplayName() }}</a></td>
                <td>{{ $role->getName() }}</td>
                <td>{{ $role->getDescription() }}</td>
                <td>@can('role.edit.all')
                <a href="{{ route('roles.edit', $role->getId()) }}">edit</a>
                @endcan</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endforeach

@endsection
