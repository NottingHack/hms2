@extends('layouts.app')

@section('pageTitle', $tool->getDisplayName().' Settings')

@section('content')
<div class="container">
  <h1>{{ $tool->getName() }}</h1>
  <hr>
  <h4>Your Access Levels:
    @if (Auth::user()->hasRoleByName('tools.' . $tool->getPermissionName() . '.user'))
    <span class="badge badge-pill badge-booking-normal">User</span>
    @endif
    @if (Auth::user()->hasRoleByName('tools.' . $tool->getPermissionName() . '.inductor'))
    <span class="badge badge-pill badge-booking-induction">Inductor</span>
    @endif
    @if (Auth::user()->hasRoleByName('tools.' . $tool->getPermissionName() . '.maintainer'))
    <span class="badge badge-pill badge-booking-maintenance">Maintainer</span>
    @endif
  </h4>
  <div class="table-responsive">
    <table class="table table-bordered table-hover">
      <tbody>
        <tr>
          <th>Current Status</th>
          <td>{{ $tool->getStatus() }}</td>
        </tr>
        <tr>
          <th>Induction required</th>
          <td>{{ $tool->isRestricted() ? "Yes" : "No" }}</td>
        </tr>
        <tr>
          <th>Cost per hour</th>
          <td>@money($tool->getPph(), 'GBP')</td>
        </tr>
        <tr>
          <th>Default booking length</th>
          <td>{{ $tool->getBookingLength() }} Minutes</td>
        </tr>
        <tr>
          <th>Maximum booking length</th>
          <td>{{ $tool->getLengthMax() }} Minutes</td>
        </tr>
        <tr>
          <th>Maximum number of simultaneous bookings per user</th>
          <td>{{ $tool->getBookingsMax() }}</td>
        </tr>
      </tbody>
    </table>
  </div>

  @can('tools.edit')
  <a href="{{ route('tools.edit', $tool->getId()) }}" class="btn btn-info btn-block"><i class="fas fa-pencil" aria-hidden="true"></i> Edit</a>
  <br>
  @endcan

  @can('tools.destroy')
  <button type="button" class="btn btn-danger btn-block" data-toggle="confirmation" data-placement="bottom">
    <form action="{{ route('tools.destroy', $tool->getId()) }}" method="POST" style="display: inline">
      @method('DELETE')
      @csrf
    </form>
    <i class="fas fa-trash fa-lg" aria-hidden="true"></i> Remove
  </button>
  <br>
  @endcan

  @canany(['tools.' . $tool->getPermissionName() . '.maintain', 'tools.maintainer.grant'])
  <p>
  <a href="{{ route('tools.users-for-grant', ['tool' => $tool->getId(), 'grant' => HMS\Tools\ToolManager::MAINTAINER]) }}" class="btn btn-primary btn-block"><i class="fas fa-eye"></i> View Maintainers</a>
  @endcan
  @canany(['tools.' . $tool->getPermissionName() . '.maintain', 'tools.inductor.grant' , 'tools.' . $tool->getPermissionName() . '.induct'])
  <a href="{{ route('tools.users-for-grant', ['tool' => $tool->getId(), 'grant' => HMS\Tools\ToolManager::INDUCTOR]) }}" class="btn btn-primary btn-block"><i class="fas fa-eye"></i> View Inductors</a>
  @endcan
  @can('tools.user.grant')
  <a href="{{ route('tools.users-for-grant', ['tool' => $tool->getId(), 'grant' => HMS\Tools\ToolManager::USER]) }}" class="btn btn-primary btn-block"><i class="fas fa-eye"></i> View Users</a>
  @endcan
  </p>
</div>
@endsection
