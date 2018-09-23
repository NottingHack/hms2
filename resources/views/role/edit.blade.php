@extends('layouts.app')

@section('pageTitle', 'Edit Role')

@section('content')
<div class="container">
  <h1>{{ $role->getName() }}</h1>
</div>

<div class="container">
  <form role="form" method="POST" action="{{ route('roles.update', $role->getId()) }}">
    {{ csrf_field() }}
    {{ method_field('PUT') }}
    <div class="row">
      <label for="displayName" class="form-label">Display Name</label>
      <div class="form-control">
        <input id="displayName" type="text" name="displayName" value="{{ old('displayName', $role->getDisplayName()) }}" required autofocus>
        @if ($errors->has('displayName'))
        <p class="help-text">
          <strong>{{ $errors->first('displayName') }}</strong>
        </p>
        @endif
      </div>
    </div>

    <div class="row">
      <label for="description" class="form-label">Description</label>
      <div class="form-control">
        <input id="description" type="text" name="description" value="{{ old('description', $role->getDescription()) }}" required autofocus>
        @if ($errors->has('description'))
        <p class="help-text">
          <strong>{{ $errors->first('description') }}</strong>
        </p>
        @endif
      </div>
    </div>

    <div class="row">
      <label for="email" class="form-label">Email</label>
      <div class="form-control">
        <input id="email" type="text" name="email" value="{{ old('email', $role->getEmail()) }}" autofocus>
        @if ($errors->has('email'))
        <p class="help-text">
          <strong>{{ $errors->first('email') }}</strong>
        </p>
        @endif
      </div>
    </div>

    <div class="row">
      <label for="slackChannel" class="form-label">Slack channel</label>
      <div class="form-control">
        <input id="slackChannel" type="text" name="slackChannel" value="{{ old('slackChannel', $role->getSlackChannel()) }}" autofocus>
        @if ($errors->has('slackChannel'))
        <p class="help-text">
          <strong>{{ $errors->first('slackChannel') }}</strong>
        </p>
        @endif
      </div>
    </div>

    <div class="row">
      <label for="retained" class="form-label">Retained</label>
      <div class="form-control">
        <input id="retained" type="checkbox" name="retained"
        {{ old('retained', $role->getRetained()) ? 'checked="checked"' : '' }}
        autofocus>
        @if ($errors->has('retained'))
        <p class="help-text">
          <strong>{{ $errors->first('retained') }}</strong>
        </p>
        @endif
      </div>
    </div>

    <div class="container">
      <h2>Permissions</h2>
      <hr>
      <div class="container">
        <select class="js-permission-select custom-select" style="width: 100%" name="permissions[]" multiple="multiple">
          @foreach ($allPermissions as $category => $permissions)
          <optgroup label="{{ $category }}">
            @foreach ($permissions as $permission)
            <option value="{{ $permission->getName() }}" {{ $role->getPermissions()->contains($permission) ? 'selected="selected"' : '' }}>{{ $permission->getName() }}</option>
            @endforeach
          </optgroup>
          @endforeach
        </select>
      </div>
    </div>

    <div class="row">
      <div class="form-control">
        <input type="submit" class="button" name="save" value="Save">
      </div>
    </div>
  </form>
</div>
@endsection
