@extends('layouts.app')

@section('pageTitle', "Member's Boxes")

@section('content')
<div class="container">
  <p>All members are entitled to a member's box in the storage room, but we have limited space.</p>
  <table class="table">
    <tr>
      <th>Total Spaces:</th>
      <td>{{ $totalSpaces }}</td><td>This is the number of toal shelf spaces we have accross the two "Member's storage" rooms.</td>
    </tr>
    <tr>
      <th>Avalible Spaces:</th>
      <td>{{ $totalSpaces - $inUse }}</td><td>This is the number of avalible shelf spaces we have accross the two "Member's storage" rooms, this number needs to be above 0 for you to buy a box.</td>
    </tr>
    <tr>
      <th>In Use:</th>
      <td>{{ $inUse }}</td><td>Current number of boxes in use by the members.</td>
    </tr>
    <tr>
      <th>Removed:</th>
      <td>{{ $removed }}</td><td>Number of Boxes that members have removed from the space and HMS.</td>
    </tr>
    <tr>
      <th>Abandoned:</th>
      <td>{{ $abandoned }}</td><td>Number of boxes that have been Abandoned by ex members and the Trustees have removed.</td>
    </tr>
    <tr>
      <th>Total Boxes:</th>
      <td>{{ $total }}</td><td>Total number of boxes registers in HMS.</td>
    </tr>

  </table>
</div>
@endsection
