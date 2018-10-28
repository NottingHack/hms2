@extends('layouts.app')

@section('pageTitle', 'Role')

@section('content')

<div class="container">
<h1>{{ $role->getDisplayName() }}</h1>
<hr>
<table class="table table-bordered table-hover ">
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
<hr>

  @foreach ($role->getPermissions() as $permission)
  <span class="badge badge-pill badge-primary">{{ $permission->getName() }}</span>
  @endforeach
<br>
<br>

<h2>Users</h2>
<hr>
@can('role.edit.all')
<nav class="navbar navbar-light bg-light">
<a href="{{ route('roles.edit', $role->getId()) }}" class="btn btn-info"> <i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>
</nav>
<br>
@endcan
<table class="table table-bordered table-hover">
  @foreach ($users as $user)
  <td>
     {{ $user->getFullName() }}
   </td>
   <td>
    <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn btn-danger btn-sm" aria-label="delete">
      <form action="{{ route('roles.removeUser', ['roleId' => $role->getId(), 'userId' => $user->getId()]) }}" method="POST" style="display: inline">
       {{ method_field('DELETE') }}
       {{ csrf_field() }}
       <p>pRemove</p>
     </form>
   </a> 
 </td>
 @endforeach
</table>
</ul>

<div classs="pagination-links">
    {{ $users->links() }}
</div>



</div>
@endsection
