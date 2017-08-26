@extends('layouts.app')

@section('pageTitle', $project->getProjectName())

@section('content')
<dl>
    <dt>
        Name
    </dt>
    <dd>
        {{ $project->getProjectName() }}
    </dd>
    <dt>
        Description
    </dt>
    <dd>
        {{ $project->getDescription() }}
    </dd>
    <dt>
        Start Date
    </dt>
    <dd>
        {{ $project->getStartDate()->toDateString() }}
    </dd>
    @if ($project->getCompleteDate())
    <dt>
        Complete Date
    </dt>
    <dd>
        {{ $project->getCompleteDate()->toDateString() }}
    </dd>
    @endif
    <dt>
        Status
    </dt>
    <dd>
        {{ $project->getStateString() }}
    </dd>
</dl>
@endsection
