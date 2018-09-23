@extends('layouts.app')

@section('pageTitle', 'Role')

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
    <tr>
      <th>Email:</th>
      <td>{{ $role->getEmail() }}</td>
    </tr>
    <tr>
      <th>Slack Channel:</th>
      <td>{{ $role->getSlackChannel() }}</td>
    </tr>
    <tr>
      <th>Retained:</th>
      <td>
        @if ($role->getRetained())
        Yes
        @else
        No
        @endif
      </td>
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
  @foreach ($users as $user)
  <li>
    <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="alert form-submit-link" aria-label="delete">
      <form action="{{ route('roles.removeUser', ['roleId' => $role->getId(), 'userId' => $user->getId()]) }}" method="POST" style="display: inline">
       {{ method_field('DELETE') }}
       {{ csrf_field() }}
       <i class="fa fa-minus-circle" aria-hidden="true"></i>
     </form>
   </a>{{ $user->getFullName() }}
 </li>
 @endforeach
</ul>

<div classs="pagination-links">
    {{ $users->links() }}
</div>

@can('role.edit.all')
<a href="{{ route('roles.edit', $role->getId()) }}" class="button">edit</a>
@endcan
@endsection
