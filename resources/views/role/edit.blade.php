@extends('layouts.app')

@section('content')
<h1>{{ $role['name'] }}</h1>

<form role="form" method="POST" action="{{ route('roles.update', $role['id']) }}">
    {{ csrf_field() }}
    {{ method_field('PUT') }}

    <div class="row">
        <label for="displayName" class="form-label">Display Name</label>
        <div class="form-control">
            <input id="displayName" type="text" name="displayName" value="{{ old('displayName', $role['displayName']) }}" required autofocus>

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
            <input id="description" type="text" name="description" value="{{ old('description', $role['description']) }}" required autofocus>

            @if ($errors->has('description'))
            <p class="help-text">
                <strong>{{ $errors->first('description') }}</strong>
            </p>
            @endif
        </div>
    </div>



<h2>Permissions</h2>

@foreach ($allPermissions as $category => $permissions)

<h3>{{ $category }}</h3>

@foreach ($permissions as $permission)

    <div class="row">
        <label for="permissions[{{ $permission['name'] }}]" class="form-label">{{ $permission['name'] }}</label>
        <div class="form-control">
            <input id="permissions" type="checkbox" name="permissions[{{ $permission['name'] }}]" {{ in_array($permission['name'], array_keys(old('permissions', $role['permissions']))) ? 'checked="checked"' : '' }} autofocus>
        </div>
    </div>


@endforeach

@endforeach
    <div class="row">
        <div class="form-control">
            <input type="submit" class="button" name="save" value="Save">
        </div>
    </div>

</form>

@endsection
