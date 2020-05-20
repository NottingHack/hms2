@extends('layouts.app')

@section('pageTitle', 'Add a Meeting')

@section('content')
<div class="container">
  <form role="form" method="POST" action="{{ route('governance.meetings.store') }}">
    @csrf

    <div class="form-group">
      <label for="title" class="form-text">Title</label>
      <input class="form-control @error('title') is-invalid @enderror" id="title" type="text" name="title" value="{{ old('title') }}" required autofocus>
      @error('title')
      <span class="invalid-feedback" role="alert">
        <strong>{{ $errors->first('title') }}</strong>
      </span>
      @enderror
    </div>

    <div class="form-group">
      <label for="startTime" class="form-text">Start Time</label>
      <input class="form-control @error('startTime') is-invalid @enderror" id="startTime" type="datetime" name="startTime" value="{{ old('startTime') }}" placeholder="YYYY-MM-DD HH:MM:SS" required>
      @error('startTime')
      <span class="invalid-feedback" role="alert">
        <strong>{{ $errors->first('startTime') }}</strong>
      </span>
      @enderror
    </div>

    <div class="form-group form-check">
      <input id="extraordinary" class="form-check-input @error('extraordinary') is-invalid @enderror" type="checkbox" name="extraordinary" {{ old('extraordinary', 0) ? 'checked="checked"' : '' }}>
      <label class="form-check-label" for="extraordinary">
        Is this an Extraordinary General Meeting?
      </label>
      @error('extraordinary')
      <span class="invalid-feedback" role="alert">
        <strong>{{ $errors->first('extraordinary') }}</strong>
      </span>
      @enderror
    </div>

    <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-plus"></i> Add Meeting</button>
  </form>
</div>

@endsection
