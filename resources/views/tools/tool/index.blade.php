@extends('layouts.app')

@section('pageTitle', 'Tools')

@section('content')
<div class="container">
  <div class="table-responsive no-more-tables">
    <table class="table table-striped table-hover">
      <thead>
        <tr>
          <th class="d-none d-md-table-cell">&nbsp;</th>
          <th>Tool</th>
          <th>Status</th>
          <th class="d-none d-md-table-cell">Cost per hour</th>
          <th>Next booking</th>
          @can(['tools.edit', 'tools.maintainer.grant'])
          <th>Actions</th>
          @endcan
        </tr>
      </thead>
      <tbody>
        @foreach($tools as $tool)
        <tr>
          <td class="d-none d-md-table-cell" style="width:25px"><span style="color: #195905"><i class="fal fa-calendar-alt" aria-hidden="true"></i></span></td>
          <td data-title="Tool"><a href="{{ route('bookings.index', $tool->getId()) }}">{{ $tool->getName() }}</a></td>
          <td data-title="Status">
            {{ $tool->getStatusString() }}
            @if($tool->getStatus() == \HMS\Entities\Tools\ToolState::DISABLED && ! is_null($tool->getStatusText()))
            <br>{{ $tool->getStatusText() }}
            @endif
          </td>
          <td data-title="Cost per hour" class="d-none d-md-table-cell">@format_pennies($tool->getPph())</td>
          <td data-title="Next booking">{{ $nextBookings[$tool->getId()] ? $nextBookings[$tool->getId()]->getStart()->format('jS F Y @ H:i') : "None" }}</td>
          @canany(['tools.edit', 'tools.maintainer.grant'])
          <td data-title="Actions" class="actions">
            @can('tools.edit')
            <a class="btn btn-primary btn-sm btn-sm-spacing" href="{{ route('tools.show', $tool->getId()) }}"><i class="far fa-eye" aria-hidden="true"></i> View Settings</a>
            @endcan
            {{-- @can('tools.maintainer.grant')
            <a class="btn btn-primary btn-sm btn-sm-spacing" href=""><i class="fas fa-plus" aria-hidden="true"></i> Appoint Maintainer</a>
            @endcan --}}
          </td>
          @endcanany
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

@can('tools.create')
<br>
<div class="container">
  <a href="{{ route('tools.create') }}" class="btn btn-primary btn-block"><i class="fas fa-plus" aria-hidden="true"></i> Add new tool</a>
</div>
@endcan
@endsection
