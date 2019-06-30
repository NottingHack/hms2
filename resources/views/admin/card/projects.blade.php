@can('project.view.all')
<div class="card">
  <div class="card-header">Active Projects</div>
  <ul class="list-group list-group-flush">
    @forelse ($projects as $project)
    <li class="list-group-item">
      <span class="align-middle">
        {{ $project->getProjectName() }}&nbsp;
        <div class="btn-group float-right" role="group" aria-label="Manage Project">
          <a href="{{ route('projects.show', $project->getId()) }}" class="btn btn-primary btn-sm btn-sm-spacing"><i class="far fa-eye" aria-hidden="true"></i></a>
          @can('project.printLabel.self')
          @if (SiteVisitor::inTheSpace() && $project->getState() == \HMS\Entities\Members\ProjectState::ACTIVE)
          <a href="{{ route('projects.print', $project->getId()) }}" class="btn btn-primary btn-sm btn-sm-spacing"><i class="fas fa-print" aria-hidden="true"></i></a>
          @endif
          @endcan
        </div>
      </span>
    </li>
    @empty
    <li class="list-group-item">No Projects yet.</li>
    @endforelse
  </ul>
  @can('project.create.all')
  <div class="card-footer">
    <a href="{{ route('projects.create') }}" class="btn btn-primary"><i class="fas fa-plus" aria-hidden="true"></i> Add a Project</a>
  </div>
  @endcan
  </div>
@endcan
