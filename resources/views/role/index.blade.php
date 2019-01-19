@extends('layouts.app')

@section('pageTitle', 'Roles')

@section('content')
<div class="container">
  @foreach ($roles as $category => $categoryRoles)
  <h2 style="text-transform: capitalize">{{ $category }}</h2>
  <hr>
  <div class="table-responsive no-more-tables">
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
          <td data-title="Role"><a href="{{ route('roles.show', $role->getId()) }}">{{ $role->getDisplayName() }}</a></td>
          <td data-title="Code">{{ $role->getName() }}</td>
          <td data-title="Description">{{ $role->getDescription() }}</td>
          <td data-title="Actions" class="actions">
            @can('role.edit.all')
            <a class="btn btn-primary" href="{{ route('roles.edit', $role->getId()) }}"><i class="fas fa-pencil" aria-hidden="true"></i> Edit</a>
            @endcan
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endforeach
</div>
@endsection
