@extends('layouts.app')

@section('pageTitle')
    @isset($role)
        {{ $users->total() }} Users with Role: {{ $role->getDisplayName() }}
    @else
        {{ $users->total() }} Users
    @endif
@endsection

@section('content')
<table>
    <thead>
        <tr>
            <th>Username</th>
            <th>Name</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    @foreach ($users as $user)
        <tr>
            <td>{{ $user->getUsername() }}</td>
            <td>{{ $user->getFullName() }}</td>
            <td>{{ $user->getEmail() }}</td>
            <td>
              <a href="{{ route('users.show', $user->getId()) }}">View</a> 
              - <a href="{{ route('users.edit', $user->getId()) }}">Edit</a> 
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
<div classs="pagination-links">
    {{ $users->links() }}
</div>
@endsection
