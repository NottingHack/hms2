@extends('layouts.app')

@section('pageTitle')
Projects for {{ $user->getFirstname() }}
@endsection

@section('content')
<div class="container">
  @content('members.project.index', 'main')
</div>
@can('project.create.self')
<div class="container">
  <a href="{{ route('projects.create') }}" class="btn btn-primary btn-block"><i class="fas fa-plus" aria-hidden="true"></i> Add new project</a>
  </div>
@endcan

<br>
<div class="container">
  <div class="table-responsive no-more-tables">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th>Project Name</th>
          <th>Description</th>
          <th>Start Date</th>
          <th>Complete Date</th>
          <th>State</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($projects as $project)
        <tr>
          <td data-title="Project Name">{{ $project->getProjectName() }}</td>
          <td data-title="Description" class="w-35">{{ $project->getDescriptionTrimed() }}</td>
          <td data-title="Start Date">{{ $project->getStartDate()->toDateString() }}</td>
          <td data-title="Complete Date">{{ $project->getCompleteDate() ? $project->getCompleteDate()->toDateString() : '' }}&nbsp;</td>
          <td data-title="State">{{ $project->getStateString() }}</td>
          <td data-title="Actions" class="actions">
            @can('project.view.self')
            <a href="{{ route('projects.show', $project->getId()) }}" class="btn btn-primary btn-sm mb-1"><i class="far fa-eye" aria-hidden="true"></i> View Project</a><br>
            @endcan
            @feature('label_printer')
            @can('project.printLabel.self')
            @if (SiteVisitor::inTheSpace() && $project->getState() == \HMS\Entities\Members\ProjectState::ACTIVE)
            <a href="{{ route('projects.print', $project->getId()) }}" class="btn btn-primary btn-sm mb-1"><i class="fas fa-print" aria-hidden="true"></i> Print Do-Not-Hack Label</a><br>
            @endif
            @endcan
            @endfeature
            @can('project.edit.self')
            @if ($project->getState() == \HMS\Entities\Members\ProjectState::ACTIVE)
            @if ($project->getUser() == \Auth::user())
            <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn btn-primary btn-sm mb-1">
              <form action="{{ route('projects.markComplete', $project->getId()) }}" method="POST" style="display: none">
                @method('PATCH')
                @csrf
              </form>
              <i class="fas fa-check" aria-hidden="true"></i> Mark Complete
            </a>
            @else
            <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn btn-primary btn-sm mb-1">
              <form action="{{ route('projects.markAbandoned', $project->getId()) }}" method="POST" style="display: none">
                @method('PATCH')
                @csrf
              </form>
              <i class="fas fa-frown" aria-hidden="true"></i> Mark Abandoned
            </a><br>
            @endif
            @endif
            @if ($project->getState() != \HMS\Entities\Members\ProjectState::ACTIVE)
            <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn btn-primary btn-sm mb-1">
              <form action="{{ route('projects.markActive', $project->getId()) }}" method="POST" style="display: none">
                @method('PATCH')
                @csrf
              </form>
              <i class="far fa-play" aria-hidden="true"></i> Resume Project
            </a>
            @endif
            @endcan
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
