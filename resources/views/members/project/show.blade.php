@extends('layouts.app')

@section('pageTitle', $project->getProjectName())

@section('content')
<div class="container">
<table class="table table-bordered">
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
    
    
<div class="card border-light">
    
@if ( ($project->getUser() == \Auth::user() && \Auth::user()->can('project.edit.self')) || ($project->getUser() != \Auth::user() && \Auth::user()->can('project.edit.all')) )
<a href="{{ route('projects.edit', $project->getId()) }}" class="btn btn-sm btn-primary btn-sm-spacing"><i class="fa fa-edit fg-la" aria-hidden="true"></i> Edit Project</a>
@endif
    
</div>
    
<div class="card border-light">

@if ( ($project->getUser() == \Auth::user() && \Auth::user()->can('project.printLabel.self')) || ($project->getUser() != \Auth::user() && \Auth::user()->can('project.printLabel.all')) )
  @if (SiteVisitor::inTheSpace() && $project->getState() == \HMS\Entities\Members\Project::PROJCET_ACTIVE)
  <a href="{{ route('projects.print', $project->getId()) }}" class="btn btn-sm btn-primary btn-sm-spacing"><i class="fa fa-print fa-lg" aria-hidden="true"></i> Print Do-Not-Hack Label</a>
  @endif
@endcan
    
    </div>
    
<div class="card border-light">
    
@if ($project->getState() == \HMS\Entities\Members\Project::PROJCET_ACTIVE)
  @if ($project->getUser() == \Auth::user() && \Auth::user()->can('project.edit.self'))
  <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn btn-sm btn-primary btn-sm-spacing">
    <form action="{{ route('projects.markComplete', $project->getId()) }}" method="POST" style="display: none">
      {{ method_field('PATCH') }}
      {{ csrf_field() }}
    </form>
    <i class="fa fa-check fa-lg" aria-hidden="true"></i> Mark Complete
  </a>
  @elseif (\Auth::user()->can('project.edit.all'))
  <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn btn-sm btn-primary btn-sm-spacing">
    <form action="{{ route('projects.markAbandoned', $project->getId()) }}" method="POST" style="display: none">
      {{ method_field('PATCH') }}
      {{ csrf_field() }}
    </form>
    <i class="fa fa-frown-o fa-lg" aria-hidden="true"></i> Mark Abandoned
  </a>
  @endif
@endif
    </div>
<div class="card border-light">
@if ( ($project->getUser() == \Auth::user() && \Auth::user()->can('project.printLabel.self')) || ($project->getUser() != \Auth::user() && \Auth::user()->can('project.printLabel.all')) )
  @if ($project->getState() != \HMS\Entities\Members\Project::PROJCET_ACTIVE)
  <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn btn-sm btn-primary btn-sm-spacing">
    <form action="{{ route('projects.markActive', $project->getId()) }}" method="POST" style="display: none">
      {{ method_field('PATCH') }}
      {{ csrf_field() }}
    </form>
    <i class="fa fa-play fa-lg" aria-hidden="true"></i> Resume Project
  </a>
  @endif
@endif
    </div>
    </div>
@endsection
