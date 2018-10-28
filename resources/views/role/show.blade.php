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
@can('role.edit.all')
<div class="card">
<a href="{{ route('roles.edit', $role->getId()) }}" class="btn btn-info"> <i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>
</div>
<br>
@endcan

<h2>Users</h2>
<hr>

<table class="table table-bordered table-sm table-hover">
  @foreach ($users as $user)
  <tr>
    <td>
     {{ $user->getFullName() }}
   </td>
   <td>
    <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn btn-danger btn-sm" aria-label="delete">
      <form action="{{ route('roles.removeUser', ['roleId' => $role->getId(), 'userId' => $user->getId()]) }}" method="POST" style="display: inline">
       {{ method_field('DELETE') }}
       {{ csrf_field() }}
     </form>
     <i class="fa fa-trash" aria-hidden="true"></i> Remove
   </a> 
 </td>
</tr>
 @endforeach
</table>
</ul>

<div class="pagination-links center">
    {{ $users->links() }}
</div>



</div>
@endsection
