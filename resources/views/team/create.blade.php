@extends('layouts.app')

@section('pageTitle', 'Create Team')

@section('content')
<div class="container">
  <p>Creating a new team will also create a new mailbox if one does not exist</p>
  <form role="form" method="POST" action="{{ route('teams.store') }}">
    @csrf

    <div class="form-group">
      <label for="name" class="form-label">Name</label>
      <div class="input-group mb-2">
        <div class="input-group-prepend">
          <div class="input-group-text">team.</div>
        </div>
        <input class="form-control" id="name" type="text" name="name" value="{{ old('name') }}" required autofocus>
      </div>
      @if ($errors->has('name'))
      <p class="help-text">
        <strong>{{ $errors->first('name') }}</strong>
      </p>
      @endif
    </div>

    <div class="form-group">
      <label for="displayName" class="form-label">Display Name</label>
      <input class="form-control" id="displayName" type="text" name="displayName" value="{{ old('displayName') }}" required>
      @if ($errors->has('displayName'))
      <p class="help-text">
        <strong>{{ $errors->first('displayName') }}</strong>
      </p>
      @endif
    </div>

    <div class="form-group">
      <label for="description" class="form-label">Description</label>
      <textarea class="form-control" id="description" name="description" rows="10" required>{{ old('description') }}</textarea>
      @if ($errors->has('description'))
      <p class="help-text">
        <strong>{{ $errors->first('description') }}</strong>
      </p>
      @endif
    </div>

    <div class="form-group">
      <label for="email" class="form-label">Email</label>
      <div class="input-group mb-2">
        <input class="form-control" id="email" type="text" name="email" value="{{ old('email') }}" required>
        <div class="input-group-append">
          <div class="input-group-text">{{ config('branding.email_domain') }}</div>
        </div>
      </div>
      @if ($errors->has('email'))
      <p class="help-text">
        <strong>{{ $errors->first('email') }}</strong>
      </p>
      @endif
    </div>

    <div class="form-group">
      <label for="email-password" class="form-label">Email Password</label>
      <input class="form-control" id="email-password" type="text" name="emailPassword" required>
      @if ($errors->has('emailPassword'))
      <p class="help-text">
        <strong>{{ $errors->first('emailPassword') }}</strong>
      </p>
      @endif
    </div>

    <div class="form-group">
      <label for="slackChannel" class="form-label">Slack channel</label>
      <div class="input-group mb-2">
        <div class="input-group-prepend">
          <div class="input-group-text">#</div>
        </div>
        <input class="form-control" id="slackChannel" type="text" name="slackChannel" value="{{ old('slackChannel') }}" required>
      </div>
      @if ($errors->has('slackChannel'))
      <p class="help-text">
        <strong>{{ $errors->first('slackChannel') }}</strong>
      </p>
      @endif
    </div>

    <div class="form-group">
      <label for="discordChannel" class="form-label">Discord channel</label>
      <input class="form-control" id="discordChannel" type="text" name="discordChannel" value="{{ old('discordChannel') }}">
      @if ($errors->has('discordChannel'))
      <p class="help-text">
        <strong>{{ $errors->first('discordChannel') }}</strong>
      </p>
      @endif
    </div>

    <div class="form-group">
      <label for="discordPrivateChannel" class="form-label">Discord channel (Private)</label>
      <input class="form-control" id="discordPrivateChannel" type="text" name="discordPrivateChannel" value="{{ old('discordPrivateChannel') }}">
      @if ($errors->has('discordPrivateChannel'))
      <p class="help-text">
        <strong>{{ $errors->first('discordPrivateChannel') }}</strong>
      </p>
      @endif
    </div>


    <button type="submit" class="btn btn-primary btn-block">Add Team</button>
  </form>
</div>
@endsection
