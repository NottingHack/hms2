@extends('layouts.app')

@section('pageTitle', 'Roles')

@section('content')
<div class="container">
  @foreach ($roles as $category => $categoryRoles)
  <h2 style="text-transform: capitalize">{{ $category }}</h2>
  <hr>
  <table class="table table-bordered table-hover">
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
        <td>
          @can('role.edit.all')
          <a class="btn btn-primary" href="{{ route('roles.edit', $role->getId()) }}">Edit</a>
          @endcan
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @endforeach
</div>
@endsection
