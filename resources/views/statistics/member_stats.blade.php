@extends('layouts.app')

@section('pageTitle', 'Membership Statistics')

@section('content')
<div class="container">
  <p>This shows the number of current members using the space over various time periods.</p>

  <p>Note 1: A member is classed as "using the space" if they have used their RFID card to open the front door, use an access controlled tool, use the vending machine or note acceptor.</p>
  <table class="table">
    <tr>
      <th>Last Day:</th>
      <td>{{ $memberStats->last_day }}</td><td>({{ round($memberStats->last_day / $memberStats->total_current_members * 100, 0) }}% of total membership)</td>
    </tr>
    <tr>
      <th>Last Week:</th>
      <td>{{ $memberStats->last_week }}</td><td>({{ round($memberStats->last_week  / $memberStats->total_current_members * 100, 0) }}% of total membership)</td>
    </tr>
    <tr>
      <th>Last Month:</th>
      <td>{{ $memberStats->last_month }}</td><td>({{ round($memberStats->last_month / $memberStats->total_current_members * 100, 0) }}% of total membership)</td>
    </tr>
    <tr>
      <th>Last Quarter:</th>
      <td>{{ $memberStats->last_quarter }}</td><td>({{ round($memberStats->last_quarter / $memberStats->total_current_members * 100, 0) }}% of total membership)</td>
    </tr>
    <tr>
      <th>Last Six Months:</th>
      <td>{{ $memberStats->last_six_month }}</td><td>({{ round($memberStats->last_six_month / $memberStats->total_current_members * 100, 0) }}% of total membership)</td>
    </tr>
    <tr>
      <th>Last Year:</th>
      <td>{{ $memberStats->last_year }}</td><td>({{ round($memberStats->last_year / $memberStats->total_current_members * 100, 0) }}% of total membership)</td>
    </tr>
    <tr>
      <th>Anytime:</th>
      <td>{{ $memberStats->anytime }}</td><td>({{ round($memberStats->anytime / $memberStats->total_current_members * 100, 0) }}% of total membership)</td>
    </tr>
    <tr>
      <th>Total Current Members:</th>
      <td>{{ $memberStats->total_current_members }}</td>
      <td></td>
    </tr>
    <tr>
      <th>Voting Members:</th>
      <td>{{ $votingMembers }}</td>
      <td></td>
    </tr>
  </table>
</div>
@endsection
