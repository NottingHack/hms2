@extends('layouts.app')

@section('content')
@section('pageTitle', 'Permissions for ' . $form->getName())

  <div class="container">

    <form method="POST" action="{{ route('forms.updatePermissions', $form->getId()) }}">
      @csrf

      <div class="form-group">
        <label for="respond">Roles able to respond:</label>
        <select class="js-permission-select custom-select" style="width: 100%" name="respond[]" multiple="multiple">
        @foreach ($roles as $role)
          <option value="{{ $role->getName() }}" {{ $role->getPermissions()->contains($permissionRespond) ? 'selected="selected"' : '' }}>
            {{ $role->getDisplayName() }}
          </option>
        @endforeach
        </select>
      </div>

      <div class="form-group">
        <label for="edit">Roles able to edit:</label>
        <select class="js-permission-select custom-select" style="width: 100%" name="edit[]" multiple="multiple">
        @foreach ($roles as $role)
          <option value="{{ $role->getName() }}" {{ $role->getPermissions()->contains($permissionEdit) ? 'selected="selected"' : '' }}>
            {{ $role->getDisplayName() }}
          </option>
        @endforeach
        </select>
      </div>

      <div class="form-group">
        <label for="viewResponses">Roles able to view responses:</label>
        <select class="js-permission-select custom-select" style="width: 100%" name="viewResponses[]" multiple="multiple">
        @foreach ($roles as $role)
          <option value="{{ $role->getName() }}" {{ $role->getPermissions()->contains($permissionViewResponses) ? 'selected="selected"' : '' }}>
            {{ $role->getDisplayName() }}
          </option>
        @endforeach
        </select>
      </div>

      <button type="submit" class="btn btn-primary mt-3">
        Update Permissions
      </button>
    </form>

</div>

@endsection
