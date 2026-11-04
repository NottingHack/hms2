@extends('layouts.app')

@section('pageTitle', 'Project Removal Request')

@section('content')
<div class="container">
  <p>Please enter the reason for requesting removal. This will be included in the email after the project details.</p>
  <form class="form-group" role="form" method="POST" action="{{ route('projects.tort', $project->getId()) }}">
    @csrf

    <div class="form-group">
      <label for="tortReason" class="form-label">Removal Reason</label>
      <textarea id="description" name="tortReason" class="form-control" placeholder="Removal reason" rows="10" required>{{ old('reason', $project->getTortReason()) }}</textarea>
      @if ($errors->has('tortReason'))
        <p class="help-text">
          <strong>{{ $errors->first('tortReason') }}</strong>
        </p>
      @endif
    </div>

    <button type="submit" class="btn btn-warning btn-block">Request Removal</button>
  </form>
</div>
@endsection
