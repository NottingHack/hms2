@extends('layouts.app')

@section('pageTitle', 'Edit Role')

@section('content')
<div class="container">
  <h1>{{ $role->getName() }}</h1>
  <hr>
</div>

<div class="container">
  <form role="form" method="POST" action="{{ route('roles.update', $role->getId()) }}">
    @csrf
    @method('PUT')

    <div class="form-group">
      <label for="displayName" class="form-label">Display Name</label>
      <input class="form-control" id="displayName" type="text" name="displayName" value="{{ old('displayName', $role->getDisplayName()) }}" required autofocus>
      @if ($errors->has('displayName'))
      <p class="help-text">
        <strong>{{ $errors->first('displayName') }}</strong>
      </p>
      @endif
    </div>

    <div class="form-group">
      <label for="description" class="form-label">Description</label>
      <textarea class="form-control" id="description" name="description" rows="10">{{ old('description', $role->getDescription()) }}</textarea>
      @if ($errors->has('description'))
      <p class="help-text">
        <strong>{{ $errors->first('description') }}</strong>
      </p>
      @endif
    </div>

    <div class="form-group">
      <label for="email" class="form-label">Email</label>
      <input class="form-control" id="email" type="text" name="email" value="{{ old('email', $role->getEmail()) }}">
      @if ($errors->has('email'))
      <p class="help-text">
        <strong>{{ $errors->first('email') }}</strong>
      </p>
      @endif
    </div>

    <div class="form-group">
      <label for="slackChannel" class="form-label">Slack channel</label>
      <input class="form-control" id="slackChannel" type="text" name="slackChannel" value="{{ old('slackChannel', $role->getSlackChannel()) }}">
      @if ($errors->has('slackChannel'))
      <p class="help-text">
        <strong>{{ $errors->first('slackChannel') }}</strong>
      </p>
      @endif
    </div>

    <div class="form-check">
      <input class="form-check-input" id="retained" type="checkbox" name="retained"
      {{ old('retained', $role->getRetained()) ? 'checked="checked"' : '' }}>
      <label for="retained" class="form-label">Retained</label>
      @if ($errors->has('retained'))
      <p class="help-text">
        <strong>{{ $errors->first('retained') }}</strong>
      </p>
      @endif
    </div>

    <br>

    <h2>Permissions</h2>
    <hr>
    <select class="js-permission-select custom-select" style="width: 100%" name="permissions[]" multiple="multiple">
      @foreach ($allPermissions as $category => $permissions)
      <optgroup label="{{ $category }}">
        @foreach ($permissions as $permission)
        <option value="{{ $permission->getName() }}" {{ $role->getPermissions()->contains($permission) ? 'selected="selected"' : '' }}>{{ $permission->getName() }}</option>
        @endforeach
      </optgroup>
      @endforeach
    </select>

    <br>
    <hr>
    <div class="form-group">
      <button type="submit" class="btn btn-primary btn-block">Save</button>
    </div>
  </form>
</div>
@endsection
