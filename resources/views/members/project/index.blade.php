@extends('layouts.app') @section('pageTitle') Projects for {{ $user->getFirstname() }} @endsection @section('content') @can('project.create.self')
<div class="container">
    <div class="card">
        <a href="{{ route('projects.create') }}" class="btn btn-primary"><i class="fa fa-plus" aria-hidden="true"></i> Add new project</a>
    </div>
</div>
@endcan

<br>
<div class="container">
    <table class="table table-bordered table-hover table-responsive">
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
                <td style="width:40%">{{ $project->getDescription() }}</td>
                <td>{{ $project->getStartDate()->toDateString() }}</td>
                <td>{{ $project->getCompleteDate() ? $project->getCompleteDate()->toDateString() : '' }}</td>
                <td>{{ $project->getStateString() }}</td>
                <td>
                    @can('project.view.self')
                    <a href="{{ route('projects.show', $project->getId()) }}" class="btn btn-primary btn-sm btn-sm-spacing"><i class="fa fa-eye" aria-hidden="true"></i> View Project</a><br/> @endcan @can('project.printLabel.self') @if (SiteVisitor::inTheSpace() && $project->getState() == \HMS\Entities\Members\Project::PROJCET_ACTIVE)
                    <a href="{{ route('projects.print', $project->getId()) }}" class="btn btn-primary btn-sm btn-sm-spacing"><i class="fa fa-print" aria-hidden="true"></i> Print Do-Not-Hack Label</a><br /> @endif @endcan @can('project.edit.self') @if ($project->getState() == \HMS\Entities\Members\Project::PROJCET_ACTIVE) @if ($project->getUser() == \Auth::user())
                    <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn btn-primary btn-sm">
            <form action="{{ route('projects.markComplete', $project->getId()) }}" method="POST" style="display: none">
              {{ method_field('PATCH') }}
              {{ csrf_field() }}
            </form>
            <i class="fa fa-check" aria-hidden="true"></i> Mark Complete
          </a> @else
                    <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn btn-primary btn-sm btn-sm-spacing">
            <form action="{{ route('projects.markAbandoned', $project->getId()) }}" method="POST" style="display: none">
              {{ method_field('PATCH') }}
              {{ csrf_field() }}
            </form>
            <i class="fa fa-frown-o" aria-hidden="true"></i> Mark Abandoned
          </a><br /> @endif @endif @if ($project->getState() != \HMS\Entities\Members\Project::PROJCET_ACTIVE)
                    <a href="javascript:void(0);" onclick="$(this).find('form').submit();">
          <form action="{{ route('projects.markActive', $project->getId()) }}" method="POST" style="display: none">
            {{ method_field('PATCH') }}
            {{ csrf_field() }}
          </form>
          <i class="fa fa-play" aria-hidden="true"></i> Resume Project
        </a> @endif @endcan
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
