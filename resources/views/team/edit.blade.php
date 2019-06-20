@extends('layouts.app')

@section('pageTitle', 'Edit Team')

@section('content')
<div class="container">
  <h1>{{ $team->getDisplayName() }}</h1>
  <hr>
</div>

<div class="container">
  <form role="form" method="POST" action="{{ route('teams.update', $team->getId()) }}">
    @csrf
    @method('PUT')

    <div class="form-group">
      <label for="description" class="form-label">Description</label>
      <textarea class="form-control" id="description" name="description" rows="10">{{ old('description', $team->getDescription()) }}</textarea>
      @if ($errors->has('description'))
      <p class="help-text">
        <strong>{{ $errors->first('description') }}</strong>
      </p>
      @endif
    </div>
    <div class="form-group">
      <button type="submit" class="btn btn-primary btn-block">Save</button>
    </div>
  </form>
</div>
@endsection
