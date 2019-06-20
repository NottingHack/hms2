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
        <tr>
          <th scope="row">Slack Channel:</th>
          <td>{{ $team->getSlackChannel() }}</td>
        </tr>
        {{-- <tr>
          <th scope="row">Members:</th>
          <td>1</td>
        </tr> --}}
      </tbody>
    </table>
    @if(Auth::user()->hasRole($team) || Gate::allows('role.edit.all' || Gate::allows('role.grant.team')))
    <div class="card-footer">
      @if(Auth::user()->hasRole($team) || Gate::allows('role.edit.all'))
      @can('team.edit.description')
      <a class="btn btn-primary" href="{{ route('teams.edit', $team->getId()) }}"><i class="fas fa-pencil" aria-hidden="true"></i> Edit Description</a>
      @endcan
      @endif
      @can('role.grant.team')
      <add-user-to-team-modal :role-id="{{ $team->getId() }}" role-name="{{ $team->getDisplayName() }}"></add-user-to-team-modal>
      @endcan
    </div>
    @endif
  </div>
  <p>{!! $team->getDescription() !!}</p>
  @if(Auth::user()->hasRole($team) || Gate::allows('role.view.all'))
  <hr>
  <h2>Members:</h2>
  <ul class="d-inline-block list-group">
    @foreach ($users as $user)
    <li class="list-group-item">
      <span class="align-middle">
        {{ $user->getFullName() }}&nbsp;
        @can('role.grant.team')
        <div class="btn-group float-right" role="group">
          <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn btn-danger btn-sm" aria-label="delete">
            <form action="{{ route('roles.removeUser', ['roleId' => $team->getId(), 'userId' => $user->getId()]) }}" method="POST" style="display: inline">
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
  @endif
</div>
@endsection
