@extends('layouts.app')

@section('content')
<h1>{{ $role['displayName'] }}</h1>

    <table>
        <tbody>
            <tr>
                <th>Name:</th>
                <td>{{ $role['name'] }}</td>
            </tr>
            <tr>
                <th>Description:</th>
                <td>{{ $role['description'] }}</td>
            </tr>
        </tbody>
    </table>

<h2>Permissions</h2>

<ul>
@foreach ($role['permissions'] as $permission)
    <li>{{ $permission['name'] }}</li>
@endforeach
</ul>

@can('role.edit.all')
<a href="{{ route('roles.edit', $role['id']) }}" class="button">edit</a>
@endcan

@endsection
