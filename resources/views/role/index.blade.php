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
          <th class="w-10">Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($categoryRoles as $role)
        <tr>
          <td data-title="Role"><a href="{{ route('roles.show', $role->getId()) }}">{{ $role->getDisplayName() }}</a></td>
          <td data-title="Code">{{ $role->getName() }}</td>
          <td data-title="Description" class="w-35">{{ $role->getDescriptionTrimed() }}</td>
          <td data-title="Actions" class="actions">
            @can('role.edit.all')
            <a class="btn btn-primary btn-sm mb-1" href="{{ route('roles.edit', $role->getId()) }}"><i class="fas fa-pencil" aria-hidden="true"></i> Edit</a>
            @endcan
            @if ($category == 'team')
            @can('role.grant.team')
            <add-user-to-team-modal :role-id="{{ $role->getId() }}" role-name="{{ $role->getDisplayName() }}" :small="true"></add-user-to-team-modal>
            @endcan
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endforeach
</div>
@endsection
