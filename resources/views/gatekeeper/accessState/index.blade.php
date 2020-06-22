@extends('layouts.app')

@section('pageTitle', 'Access State')

@section('content')
<div class="container">
  <h2>Buildings</h2>
  <hr>
  <buildings-table></buildings-table>
  <br>
  <h2>Self Book Global Settings</h2>
  <hr>
  <div class="table-responsive">
    <table class="table table-bordered table-hover">
      <tbody>
        <tr>
          <th>Max booking length:</th>
          <td>{{ $maxLength }} (minutes)</td>
        </tr>
        <tr>
          <th>Max concurrent bookings per user:</th>
          <td>{{ $maxConcurrentPerUser }}</td>
        </tr>
        <tr>
          <th>Max guests per user:</th>
          <td>{{ $maxGuestsPerUser }}</td>
        </tr>
        <tr>
          <th>Min period between bookings:</th>
          <td>{{ $minPeriodBetweenBookings }} (minutes)</td>
        </tr>
        <tr>
          <th>Booking info text:</th>
          <td>{{ $bookingInfoText }}</td>
        </tr>
      </tbody>
    </table>
    <a href="{{ route('gatekeeper.access-state.edit') }}" class="btn btn-info btn-block"> <i class="fas fa-pencil" aria-hidden="true"></i> Edit</a>
  </div>
</div>
@endsection
