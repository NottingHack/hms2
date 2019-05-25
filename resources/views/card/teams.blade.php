<div {{-- class="col-lg-4 mb-3" --}}>
  <div class="card">
    <div class="card-header">
      Your Teams
    </div>
    <ul class="list-group list-group-flush">
      @forelse ($teams as $team)
      <li class="list-group-item">
        {{ $team->getDisplayName() }}&nbsp;
        <div class="btn-group float-right" role="group">
          @can('team.view')
          <a href="{{ route('roles.show', $team->getId()) }}" class="btn btn-primary btn-sm btn-sm-spacing"><i class="far fa-eye" aria-hidden="true"></i></a>
          @endcan
        </div>
      </li>
      @empty
      <li class="list-group-item">Not on any teams.</li>
      @endforelse
    </ul>
    <div class="card-footer">
      <a href="#" class="btn btn-primary">How to join a Team</a>
      <a href="#" class="btn btn-primary">View all teams</a>
    </div>
  </div>
</div>

