@extends('layouts.app')

@section('pageTitle', 'Tools')

@section('content')
<div class="container">
  <p>
    To book a tool click on the <span style="color: #195905"><i class="fal fa-calendar-alt" aria-hidden="true"></i></span> or name.<br>
    Use of some tools is restricted and needs an induction first.
  </p>
  <div class="table-responsive no-more-tables">
    <table class="table table-striped table-hover">
      <thead>
        <tr>
          <th class="d-none d-md-table-cell">&nbsp;</th>
          <th>Tool</th>
          <th>Status</th>
          <th class="d-none d-md-table-cell">Cost per hour</th>
          <th>Next booking</th>
          @canany(['tools.edit', 'tools.maintainer.grant', 'tools.inductor.grant' , 'tools.' . $tool->getPermissionName() . '.inductor.grant', 'tools.' . $tool->getPermissionName() . '.use'])
          <th>Actions</th>
          @endcan
        </tr>
      </thead>
      <tbody>
        @foreach($tools as $tool)
        <tr>
          <td class="d-none d-md-table-cell" style="width:25px"><a href="{{ route('bookings.index', $tool->getId()) }}"><span style="color: #195905"><i class="fal fa-calendar-alt" aria-hidden="true"></i></span></a></td>
          <td data-title="Tool"><a href="{{ route('bookings.index', $tool->getId()) }}"><span class="d-md-none" style="color: #195905"><i class="fal fa-calendar-alt" aria-hidden="true"></i>&nbsp;</span>{{ $tool->getName() }}</a></td>
          <td data-title="Status">
            {{ $tool->getStatusString() }}
            @if($tool->getStatus() == \HMS\Entities\Tools\ToolState::DISABLED && ! is_null($tool->getStatusText()))
            <br>{{ $tool->getStatusText() }}
            @endif
          </td>
          <td data-title="Cost per hour" class="d-none d-md-table-cell">@format_pennies($tool->getPph())</td>
          <td data-title="Next booking">{{ $nextBookings[$tool->getId()] ? $nextBookings[$tool->getId()]->getStart()->format('jS F Y @ H:i') : "None" }}</td>
          @canany(['tools.edit', 'tools.maintainer.grant', 'tools.inductor.grant' , 'tools.' . $tool->getPermissionName() . '.inductor.grant', 'tools.' . $tool->getPermissionName() . '.use'])
          <td data-title="Actions" class="actions">
            @can('tools.edit')
            <a class="btn btn-primary btn-sm mb-1" href="{{ route('tools.show', $tool->getId()) }}"><i class="far fa-eye" aria-hidden="true"></i> View Settings</a>
            @endcan
            @can('tools.maintainer.grant')
            <tool-grant-modal :tool-id="{{ $tool->getId() }}" tool-name="{{ $tool->getName() }}" grant-type="{{ HMS\Tools\ToolManager::MAINTAINER }}" :small="true"></tool-grant-modal>
            @endcan
            @canany(['tools.inductor.grant' , 'tools.' . $tool->getPermissionName() . '.inductor.grant'])
            <tool-grant-modal :tool-id="{{ $tool->getId() }}" tool-name="{{ $tool->getName() }}" grant-type="{{ HMS\Tools\ToolManager::INDUCTOR }}" :small="true"></tool-grant-modal>
            @endcan
            @if($tool->isRestricted())
            @cannot('tools.' . $tool->getPermissionName() . '.use')
            <a class="btn btn-primary btn-sm mb-1" href="{{ Meta::get('induction_request_html') }}" target="_blank">Request an Induction</a>
            @endcannot
            @endif
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
