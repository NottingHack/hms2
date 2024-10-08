@extends('layouts.app')

@section('pageTitle', $team->getDisplayName())

@section('content')
<div class="container">
  <h2>About the {{ $team->getDisplayName() }}</h2>
  <hr>
  <div class="card float-right">
    <h5 class="card-header">{{ $team->getDisplayName() }} {{-- small and float right (You are on this team) --}}</h5>
    <table class="table">
      <tbody>
        <tr>
          <th scope="row">Email:</th>
          <td>{{ $team->getEmail() }}</td>
        </tr>
        @feature('slack')
        <tr>
          <th scope="row">Slack Channel:</th>
          <td>{{ $team->getSlackChannel() }}</td>
        </tr>
        @endfeature
        @feature('discord')
        <tr>
          <th scope="row">Discord Channel:</th>
          <td>{{ $team->getDiscordChannel() }}</td>
        </tr>
        @endfeature
        {{-- <tr>
          <th scope="row">Members:</th>
          <td>1</td>
        </tr> --}}
      </tbody>
    </table>
    @if (Auth::user()->hasRole($team) || Gate::allows('role.edit.all') || Gate::allows('role.grant.team'))
    <div class="card-footer">
      @if (Auth::user()->hasRole($team) || Gate::allows('role.edit.all'))
      @can('team.edit.description')
      <a class="btn btn-primary" href="{{ route('teams.edit', $team->getId()) }}"><i class="fas fa-pencil" aria-hidden="true"></i> Edit Description</a>
      @endcan
      @endif
      @feature('roundcube_login')
      @if ($team->getEmailPassword())
      @if (Auth::user()->hasRole($team) || Gate::allows('team.login-email.all'))
      @can('team.login-email')
      <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn btn-primary">
        <form action="{{ str(config('services.roundcube_login_helper.url'))->trim('/') }}/login-team" method="POST" style="display: none">
          <input type="hidden" name="team-email" value="{{ $team->getEmail() }}">
        </form>
        <i class="fas fa-inbox" aria-hidden="true"></i> Login To Team Email
      </a>
      @endcan
      @endif
      @endif
      @endfeature
      @can('role.grant.team')
      <add-user-to-team-modal :role-id="{{ $team->getId() }}" role-name="{{ $team->getDisplayName() }}"></add-user-to-team-modal>
      @endcan
    </div>
    @endif
  </div>
  <p>{!! $team->getDescription() !!}</p>
  <hr>
  <h2>Members:</h2>
  <ul class="d-inline-block list-group">
    @foreach ($users as $user)
    <li class="list-group-item">
      <span class="align-middle">
        {{ $user->getFullname() }}&nbsp;
        @can('role.grant.team')
        <div class="btn-group float-right" role="group">
          <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn btn-danger btn-sm" aria-label="delete">
            <form action="{{ route('roles.removeUserFromTeam', ['role' => $team->getId(), 'user' => $user->getId()]) }}" method="POST" style="display: inline">
              @method('DELETE')
              @csrf
            </form>
            <i class="fas fa-trash fa-sm" aria-hidden="true"></i> Remove From Team
          </a>
        </div>
        @endcan
      </span>
    </li>
    @endforeach
  </ul>
  <div classs="pagination-links center">
    {{ $users->links() }}
  </div>
</div>
@endsection
