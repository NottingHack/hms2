@extends('layouts.app')

@section('pageTitle', $project->getProjectName())

@section('content')
<div class="container">
  <div class="table-responsive">
    <table class="table table-bordered">
      <tbody>
        @if ($project->getUser() != \Auth::user())
        <tr>
          <th>Owner</th>
          <td>{{ $project->getUser()->getFullname() }} <a class="float-right btn-sm btn-primary mb-1" href="{{ route('users.admin.show', $project->getUser()->getId()) }}"><i class="fa fa-eye"></i></a></td>
        </tr>
        @endif
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
  </div>

  <div class="card border-light">
    @if ( ($project->getUser() == \Auth::user() && \Auth::user()->can('project.edit.self')) || ($project->getUser() != \Auth::user() && \Auth::user()->can('project.edit.all')) )
    <a href="{{ route('projects.edit', $project->getId()) }}" class="btn btn-sm btn-primary mb-1"><i class="fas fa-pencil fg-la" aria-hidden="true"></i> Edit Project</a>
    @endif
  </div>

  <div class="card border-light">
    @feature('label_printer')
    @if ( ($project->getUser() == \Auth::user() && \Auth::user()->can('project.printLabel.self')) || ($project->getUser() != \Auth::user() && \Auth::user()->can('project.printLabel.all')) )
    @if (SiteVisitor::inTheSpace() && $project->getState() == \HMS\Entities\Members\ProjectState::ACTIVE)
    <a href="{{ route('projects.print', $project->getId()) }}" class="btn btn-sm btn-primary mb-1"><i class="fas fa-print fa-lg" aria-hidden="true"></i> Print Do-Not-Hack Label</a>
    @endif
    @endif
    @endfeature
  </div>

  <div class="card border-light">
    @if ($project->getState() == \HMS\Entities\Members\ProjectState::ACTIVE)
    @if ($project->getUser() == \Auth::user() && \Auth::user()->can('project.edit.self'))
    <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn btn-sm btn-primary mb-1">
      <form action="{{ route('projects.markComplete', $project->getId()) }}" method="POST" style="display: none">
        @method('PATCH')
        @csrf
      </form>
      <i class="fas fa-check fa-lg" aria-hidden="true"></i> Mark Complete
    </a>
    @elseif (\Auth::user()->can('project.edit.all'))
    <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn btn-sm btn-primary mb-1">
      <form action="{{ route('projects.markAbandoned', $project->getId()) }}" method="POST" style="display: none">
        @method('PATCH')
        @csrf
      </form>
      <i class="fas fa-frown fa-lg" aria-hidden="true"></i> Mark Abandoned
    </a>
    @endif
    @endif
  </div>

  <div class="card border-light">
    @if ( ($project->getUser() == \Auth::user() && \Auth::user()->can('project.printLabel.self')) || ($project->getUser() != \Auth::user() && \Auth::user()->can('project.printLabel.all')) )
    @if ($project->getState() != \HMS\Entities\Members\ProjectState::ACTIVE)
    <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn btn-sm btn-primary mb-1">
      <form action="{{ route('projects.markActive', $project->getId()) }}" method="POST" style="display: none">
        @method('PATCH')
        @csrf
      </form>
      <i class="far fa-play fa-lg" aria-hidden="true"></i> Resume Project
    </a>
    @endif
    @endif
  </div>
</div>
@endsection
