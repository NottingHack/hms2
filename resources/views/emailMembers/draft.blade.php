@extends('layouts.app')

@section('pageTitle', 'Email to members')

@section('content')
<div class="container">
  <p>Use the form below to draft an email to all members</p>
  <p>Note the formatting template will add "Hello name", at the top and "This email was sent by {{ config('branding.space_name') }} Trustees to you as a current member of {{ config('branding.space_name') }}" as a footer</p>
  <form class="form-group" id="emailMembers-draft-from" role="form" method="POST" action="{{  route('email-members.review') }}">
    @csrf

    <div class="form-group">
      <label for="subject" class="form-label">Subject</label>
      <input id="subject" class="form-control" type="text" name="subject" value="{{ old('subject', $subject) }}" required autofocus>
      @if ($errors->has('subject'))
      <span class="help-block">
        <strong>{{ $errors->first('subject') }}</strong>
      </span>
      @endif
    </div>

    <div class="form-group">
      <label for="emailContent" class="form-label">Content</label>
      <textarea id="emailContent" name="emailContent" class="form-control" rows="10" required>{{ old('emailContent', $emailContent) }}</textarea>
      @if ($errors->has('emailContent'))
      <p class="help-text">
        <strong>{{ $errors->first('emailContent') }}</strong>
      </p>
      @endif
    </div>

    <div class="form-group">
      <div class="card">
        <button type="submit" class="btn btn-primary">
          Review email
        </button>
      </div>
    </div>
  </form>
</div>
@endsection
