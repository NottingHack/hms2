@extends('layouts.app')

@section('pageTitle', 'Zone Occupants')

@section('content')
<div class="container">
  <p>This shows the number of current members in each area of the hackspace</p>

  <p>These are very rough numbers based on RFID entries and exits where available. We don't track exits at all doors and tailgating can no be account for. Every 12 hours we reset any members in a zone for over 24 hours back to off-site.</p>
  <table class="table">
    <thead>
      <th>Zone</th>
      <th>Occupants</th>
    </thead>
    <tbody>
      @foreach ($zones as $zone)
      <tr>
        <td>{{ $zone->getDescription() }}</td>
        <td>{{ count($zone->getZoneOccupancts()) }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
