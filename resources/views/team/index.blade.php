@extends('layouts.app')

@section('pageTitle', 'Teams')

@section('content')
<div class="container">
  <p>Words about all the teams that exist to help people getting involved in running the space (link to how to join)</p>
  @foreach ($teams as $team)
  <div class="card mb-3">
    <h5 class="card-header">{{ $team->getDisplayName() }}
      @if(Auth::user()->hasRole($team))
      <span class="h6"> (You are on this team)</span>
      @endif
    </h5>
    <div class="card-body">
      <h6 class="card-subtitle mb-2 text-muted">Email: {{ $team->getEmail() }}</h6>
      <h6 class="card-subtitle mb-2 text-muted">Slack Channel: {{ $team->getSlackChannel() }}</h6>
      <p class="card-text">{!! $team->getDescription() !!}</p>
      <a class="btn btn-primary" href="{{ route('teams.show', $team->getId()) }}"><i class="fas fa-eye" aria-hidden="true"></i> View</a>
      @can('role.edit.all')
      <a class="btn btn-primary" href="{{ route('roles.edit', $team->getId()) }}"><i class="fas fa-pencil" aria-hidden="true"></i> Edit</a>
      @endcan
      @if(Auth::user()->hasRole($team) || Gate::allows('role.edit.all'))
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
</div>
@endsection
