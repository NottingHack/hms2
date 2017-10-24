@extends('layouts.app')

@section('pageTitle', 'Edit Meta Value')

@section('content')
<div class="container">
<p>Editing value for <strong>{{ $key }}</strong></p>
    <hr>
<form role="form" method="POST" action="{{ route('metas.update', $key) }}">
  {{ csrf_field() }}
  {{ method_field('PATCH') }}
  <div class="form-group">
    <label for="value" class="form-text">Value</label>
      <input class="form-control" id="value" type="text" name="value" value="{{ old('value', $value) }}" required autofocus>
      @if ($errors->has('value'))
      <p class="help-text">
        <strong>{{ $errors->first('value') }}</strong>
      </p>
      @endif
    </div>

    <div class="form-buttons">
      <button class="btn btn-success" type="submit" class="button">
        Update
      </button>
  </div>
</form>
</div>
@endsection
