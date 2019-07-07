<div class="card">
  <div class="card-header">Your Teams</div>
  <ul class="list-group list-group-flush">
    @forelse ($teams as $team)
    <li class="list-group-item">
      <span class="align-middle">
        {{ $team->getDisplayName() }}&nbsp;
        <div class="btn-group float-right" role="group">
          @can('team.view')
          <a href="{{ route('teams.show', $team->getId()) }}" class="btn btn-primary btn-sm btn-sm-spacing"><i class="far fa-eye" aria-hidden="true"></i></a>
          @endcan
        </div>
      </span>
    </li>
    @empty
    <li class="list-group-item">Not on any teams.</li>
    @endforelse
  </ul>
  <div class="card-footer">
    <a href="{{ route('teams.how-to-join') }}" class="btn btn-primary mb-1">How to join a Team</a>
    <a href="{{ route('teams.index') }}" class="btn btn-primary mb-1">View all teams</a>
  </div>
</div>
