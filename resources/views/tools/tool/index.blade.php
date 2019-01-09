@extends('layouts.app')

@section('pageTitle', 'Tools')

@section('content')
<div class="container">
  <div class="table-responsive">
    <table class="table table-striped table-hover">
      <thead>
        <tr>
          <th>&nbsp;</th>
          <th>Tool</th>
          <th>Status</th>
          <th>Cost per hour</th>
          <th>Next booking</th>
          @can(['tools.edit', 'tools.maintainer.grant'])
          <th>Actions</th>
          @endcan
        </tr>
      </thead>
      <tbody>
        @foreach($tools as $tool)
        <tr>
          <td style="width:25px"><span style="color: #195905"><i class="fal fa-calendar-alt" aria-hidden="true"></i></span></td>
          <td><a href="{{ route('bookings.index', $tool->getId()) }}">{{ $tool->getName() }}</a></td>
          <td>
            {{ $tool->getStatusString() }}
            @if($tool->getStatus() == \HMS\Entities\Tools\ToolState::DISABLED && ! is_null($tool->getStatusText()))
            <br>{{ $tool->getStatusText() }}
            @endif
          </td>
          <td>@format_pennies($tool->getPph())</td>
          <td>{{ $nextBookings[$tool->getId()] ? $nextBookings[$tool->getId()]->getStart()->format('jS F Y @ H:i') : "None" }}</td>
          @can(['tools.edit', 'tools.maintainer.grant'])
          <td>
            @can('tools.edit')
            <a class="btn btn-primary btn-sm btn-sm-spacing" href="{{ route('tools.show', $tool->getId()) }}" class="button"><i class="far fa-eye" aria-hidden="true"></i> View Settings</a>
            @endcan
            {{-- @can('tools.maintainer.grant')
            <a class="btn btn-primary btn-sm btn-sm-spacing" href="" class="button"><i class="fas fa-plus" aria-hidden="true"></i> Appoint Maintainer</a>
            @endcan --}}
          </td>
          @endcan
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

@can('tools.create')
<br>
<div class="container">
  <div class="card">
    <a href="{{ route('tools.create') }}" class="btn btn-primary"><i class="fas fa-plus" aria-hidden="true"></i> Add new tool</a>
  </div>
</div>
@endcan
@endsection
