@canany('profile.view.limited', 'profile.view.all')
<div class="card">
  <div class="card-header">Teams</div>
  <ul class="list-group list-group-flush">
    @forelse ($teams as $team)
    <li class="list-group-item">
      <span class="align-middle">
        {{ $team->getDisplayName() }}&nbsp;
        <div class="btn-group float-right" role="group">
          @can('team.view')
          <a href="{{ route('teams.show', $team->getId()) }}" class="btn btn-primary btn-sm btn-sm-spacing"><i class="far fa-eye" aria-hidden="true"></i></a>
          @endcan
          @can('role.grant.team')
          <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn btn-danger btn-sm btn-sm-spacing" aria-label="delete">
            <form action="{{ route('roles.removeUser', ['role' => $team->getId(), 'user' => $user->getId()]) }}" method="POST" style="display: inline">
              @method('DELETE')
              @csrf
            </form>
            <i class="fas fa-trash" aria-hidden="true"></i>
          </a>
          @endcan
        </div>
      </span>
    </li>
    @empty
    <li class="list-group-item">Not on any teams.</li>
    @endforelse
  </ul>
  @can('role.grant.team')
  <div class="card-footer">
    <a href="#" class="btn btn-primary mb-1"><i class="fas fa-plus" aria-hidden="true"></i> Add to a Team</a>
  </div>
  @endcan
</div>
@endcan
