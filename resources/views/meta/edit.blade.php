@extends('layouts.app')

@section('pageTitle', 'Edit Meta Value')

@section('content')
<div class="container">
  <p>Editing value for <strong>{{ $key }}</strong></p>
  <div class="alert alert-warning">
    You should only be editing this value if you really know what they are doing!
  </div>
  <hr>
  <form role="form" method="POST" action="{{ route('metas.update', $key) }}">
    @csrf
    @method('PATCH')
    <div class="form-group">
      <label for="value" class="form-text">Value</label>
      <input class="form-control" id="value" type="text" name="value" value="{{ old('value', $value) }}" autofocus>
      @if ($errors->has('value'))
      <p class="help-text">
        <strong>{{ $errors->first('value') }}</strong>
      </p>
      @endif
    </div>

    <div class="form-buttons">
      <button type="submit" class="btn btn-success">
        Update
      </button>
    </div>
  </form>
</div>
@endsection
