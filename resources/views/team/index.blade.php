@extends('layouts.app')

@section('pageTitle', 'Teams')

@section('content')
<div class="container">
  <p>The {{ config('branding.space_type') }} is run <strong>entirely</strong> by teams of volunteers.</p>
  <p>Please consider joining a team and helping with the running of the {{ config('branding.space_type') }}.</p>
  <p><a href="{{ route('teams.how-to-join') }}" class="btn btn-primary">How to join a Team</a></p>
  @foreach ($teams as $team)
  <div class="card mb-3">
    <h5 class="card-header">{{ $team->getDisplayName() }}
      @if (Auth::user()->hasRole($team))
      <span class="h6"> (You are on this team)</span>
      @endif
    </h5>
    <div class="card-body">

      <div class="row text-muted">
        <div class="col-md-4">Email: {{ $team->getEmail() }}</div>
        @feature('slack')
        <div class="col-md-4">Slack: {{ $team->getSlackChannel() }}</div>
        @endfeature
        @feature('discord')
        <div class="col-md-4">Discord: {{ $team->getDiscordChannel() }}</div>
        @endfeature
      </div>
      <hr />

      <p class="card-text">{!! $team->getDescription() !!}</p>
      <a class="btn btn-primary" href="{{ route('teams.show', $team->getId()) }}"><i class="far fa-eye" aria-hidden="true"></i> View</a>
      @can('role.edit.all')
      <a class="btn btn-primary" href="{{ route('roles.edit', $team->getId()) }}"><i class="fas fa-pencil" aria-hidden="true"></i> Edit</a>
      @endcan
      @if (Auth::user()->hasRole($team) || Gate::allows('role.edit.all'))
      @can('team.edit.description')
      <a class="btn btn-primary" href="{{ route('teams.edit', $team->getId()) }}"><i class="fas fa-pencil" aria-hidden="true"></i> Edit Description</a>
      @endcan
      @endif
      @can('role.grant.team')
      <add-user-to-team-modal :role-id="{{ $team->getId() }}" role-name="{{ $team->getDisplayName() }}"></add-user-to-team-modal>
      @endcan
    </div>
  </div>
  @endforeach
  @can('team.create')
  <hr>
  <a href="{{ route('teams.create') }}" class="btn btn-primary btn-block"><i class="fa fa-plus"></i> Add New Team</a>
  @endcan
</div>
@endsection
