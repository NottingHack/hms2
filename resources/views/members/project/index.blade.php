@extends('layouts.app')

@section('pageTitle')
Projects for {{ $user->getFirstname() }}
@endsection

@section('content')
@can('project.create.self')
<div>
    <a href="{{ route('projects.create') }}" class="button"><i class="fa fa-plus" aria-hidden="true"></i> Add new project</a>
</div>
@endcan

<table>
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
      <td>{{ $project->getProjectName() }}</td>
      <td>{{ $project->getDescription() }}</td>
      <td>{{ $project->getStartDate()->toDateString() }}</td>
      <td>{{ $project->getCompleteDate() ? $project->getCompleteDate()->toDateString() : '' }}</td>
      <td>{{ $project->getStateString() }}</td>
      <td>
      @can('project.view.self')
        <a href="{{ route('projects.show', $project->getId()) }}">View Project</a><br/>
      @endcan
      @can('project.printLabel.self')
        @if (SiteVisitor::inTheSpace() && $project->getState() == \HMS\Entities\Members\Project::PROJCET_ACTIVE)
          <a href="{{ route('projects.print', $project->getId()) }}">Print Do-Not-Hack Label</a><br />
        @endif
      @endcan
      @can('project.edit.self')
        @if ($project->getState() == \HMS\Entities\Members\Project::PROJCET_ACTIVE)
          @if ($project->getUser() == \Auth::user())
            <a href="{{ route('projects.markComplete', $project->getId()) }}">Mark Complete</a><br />
          @else
            <a href="{{ route('projects.markAbandoned', $project->getId()) }}">Mark Abandoned</a><br />
          @endif
        @endif
        @if ($project->getState() != \HMS\Entities\Members\Project::PROJCET_ACTIVE)
          <a href="{{ route('projects.markActive', $project->getId()) }}">Resume Project</a>
        @endif
      @endcan
      </td>
    </tr>
  @endforeach
</tbody>
</table>
@endsection
