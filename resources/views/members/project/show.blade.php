@extends('layouts.app')

@section('pageTitle', $project->getProjectName())

@section('content')
<table>
  <tbody>
    <tr>
      <th>Name</th>
      <td>{{ $project->getProjectName() }}</td>
    </tr>
    <tr>
      <th>Description</th>
      <td>{{ $project->getDescription() }}</td>
    </tr>
    <tr>
      <th>Start Date</th>
      <td>{{ $project->getStartDate()->toDateString() }}</td>
    </tr>
    @if ($project->getCompleteDate())
    <tr>
      <th>Complete Date</th>
      <td>{{ $project->getCompleteDate()->toDateString() }}</td>
    </tr>
    @endif
    <tr>
      <th>Status</th>
      <td>{{ $project->getStateString() }}</td>
    </tr>
  </tbody>
</table>

@if ( ($project->getUser() == \Auth::user() && \Auth::user()->can('project.edit.self')) || ($project->getUser() != \Auth::user() && \Auth::user()->can('project.edit.all')) )
<a href="{{ route('projects.edit', $project->getId()) }}" class="button">Edit Project</a>
@endif

@if ( ($project->getUser() == \Auth::user() && \Auth::user()->can('project.printLabel.self')) || ($project->getUser() != \Auth::user() && \Auth::user()->can('project.printLabel.all')) )
  @if (SiteVisitor::inTheSpace() && $project->getState() == \HMS\Entities\Members\Project::PROJCET_ACTIVE)
    <a href="{{ route('projects.print', $project->getId()) }}" class="button">Print Do-Not-Hack Label</a>
  @endif
@endcan

@if ($project->getState() == \HMS\Entities\Members\Project::PROJCET_ACTIVE)
  @if ($project->getUser() == \Auth::user() && \Auth::user()->can('project.edit.self'))
    <a href="{{ route('projects.markComplete', $project->getId()) }}" class="button">Mark Complete</a>
  @elseif (\Auth::user()->can('project.edit.all'))
    <a href="{{ route('projects.markAbandoned', $project->getId()) }}" class="button alert">Mark Abandoned</a>
  @endif
@endif

@if ( ($project->getUser() == \Auth::user() && \Auth::user()->can('project.printLabel.self')) || ($project->getUser() != \Auth::user() && \Auth::user()->can('project.printLabel.all')) )
  @if ($project->getState() != \HMS\Entities\Members\Project::PROJCET_ACTIVE)
    <a href="{{ route('projects.markActive', $project->getId()) }}" class="button">Resume Project</a>
  @endif
@endif
@endsection
