@extends('layouts.app')

@section('content')
<h2>Key: {{ $key }}</h2>
<form role="form" method="POST" action="{{ route('metas.update', $key) }}">
  {{ csrf_field() }}
  {{ method_field('PATCH') }}
  <div class="row">
    <label for="value" class="form-label">Value</label>
    <div class="form-control">
      <input id="value" type="text" name="value" value="{{ old('value', $value) }}" required autofocus>
      @if ($errors->has('value'))
      <p class="help-text">
        <strong>{{ $errors->first('value') }}</strong>
      </p>
      @endif
    </div>
  </div>

  <div class="row">
    <div class="form-buttons">
      <button type="submit" class="button">
        Update
      </button>
    </div>
  </div>
</form>

@endsection
