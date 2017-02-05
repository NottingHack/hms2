@extends('layouts.app')

@section('content')
<h1>{{ $role->getDisplayName() }}</h1>

    <table>
        <tbody>
            <tr>
                <th>Name:</th>
                <td>{{ $role->getName() }}</td>
            </tr>
            <tr>
                <th>Description:</th>
                <td>{{ $role->getDescription() }}</td>
            </tr>
        </tbody>
    </table>

<h2>Permissions</h2>

<ul>
@foreach ($role->getPermissions() as $permission)
    <li>{{ $permission->getName() }}</li>
@endforeach
</ul>

<h2>Users</h2>

<ul>
@foreach ($role->getUsers() as $user)
    <li>
        <form action="{{ route('roles.removeUser', ['roleId' => $role->getId(), 'userId' => $user->getId()]) }}" method="POST">
            {{ csrf_field() }}
            {{ method_field('DELETE') }}

            <a href="#" class="alert form-submit-link" aria-label="delete"><i class="fa fa-minus-circle" aria-hidden="true"></i></a>
            {{ $user->getFullName() }}
        </form>
    </li>
@endforeach
</ul>

@can('role.edit.all')
<a href="{{ route('roles.edit', $role->getId()) }}" class="button">edit</a>
@endcan

@endsection
