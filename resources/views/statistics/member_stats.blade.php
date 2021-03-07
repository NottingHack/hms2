@extends('layouts.app')

@section('pageTitle', 'Membership Statistics')

@section('content')
<div class="container">
  <p>This shows the number of current members using the space over various time periods.</p>

  <p>Note 1: A member is classed as "using the space" if they have used their RFID card to open the front door, use an access controlled tool, use the vending machine or note acceptor.</p>
  <table class="table">
    <tr>
      <th>Last Day:</th>
      <td>{{ $memberStats->last_day }}</td><td>({{ $memberStats->total_current_members ? round($memberStats->last_day / $memberStats->total_current_members * 100, 0) : 0 }}% of total membership)</td>
    </tr>
    <tr>
      <th>Last Week:</th>
      <td>{{ $memberStats->last_week }}</td><td>({{ $memberStats->total_current_members ? round($memberStats->last_week  / $memberStats->total_current_members * 100, 0) : 0 }}% of total membership)</td>
    </tr>
    <tr>
      <th>Last Month:</th>
      <td>{{ $memberStats->last_month }}</td><td>({{ $memberStats->total_current_members ? round($memberStats->last_month / $memberStats->total_current_members * 100, 0) : 0 }}% of total membership)</td>
    </tr>
    <tr>
      <th>Last Quarter:</th>
      <td>{{ $memberStats->last_quarter }}</td><td>({{ $memberStats->total_current_members ? round($memberStats->last_quarter / $memberStats->total_current_members * 100, 0) : 0 }}% of total membership)</td>
    </tr>
    <tr>
      <th>Last Six Months:</th>
      <td>{{ $memberStats->last_six_month }}</td><td>({{ $memberStats->total_current_members ? round($memberStats->last_six_month / $memberStats->total_current_members * 100, 0) : 0 }}% of total membership)</td>
    </tr>
    <tr>
      <th>Last Year:</th>
      <td>{{ $memberStats->last_year }}</td><td>({{ $memberStats->total_current_members ? round($memberStats->last_year / $memberStats->total_current_members * 100, 0) : 0 }}% of total membership)</td>
    </tr>
    <tr>
      <th>Anytime:</th>
      <td>{{ $memberStats->anytime }}</td><td>({{ $memberStats->total_current_members ? round($memberStats->anytime / $memberStats->total_current_members * 100, 0) : 0 }}% of total membership)</td>
    </tr>
    <tr class="table-success">
      <th>Total Current Members:</th>
      <td>{{ $memberStats->total_current_members }}</td>
      <td></td>
    </tr>
    <tr>
      <th>Voting Members:</th>
      <td>{{ $votingMembers }}</td><td>({{ $memberStats->total_current_members ? round($votingMembers / $memberStats->total_current_members * 100, 0) : 0 }}% of total membership)</td>
    </tr>
  </table>
  <h3>Conversions</h3>
  <table class="table">
    <thead>
      <tr>
        <th class="w-15"></th>
        <th class="w-10">Last 7 days</th>
        <th class="w-10">Last month</th>
        <th>Description</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <th>Invites Sent</th>
        <td>{{ $invitesSentLastWeek }}</td>
        <td>{{ $invitesSentLastMonth }}</td>
        <td>People that have registered their interest in joining the space in the last week/month.</td>
      </tr>
      <tr>
        <th>Invites Outstanding</th>
        <td>{{ $invitesOutstandingLastWeek }}</td>
        <td>{{ $invitesOutstandingLastMonth }}</td>
        <td>Interests there where registered in the last week/month but have not yet created a HMS account.</td>
      </tr>
      <tr>
        <th>Awaiting Payment</th>
        <td>{{ $awaitingPaymentLastWeek }}</td>
        <td>{{ $awaitingPaymentLastMonth }}</td>
        <td>Members that have been thought the membership sign up process in the last week/month, but payment has not yet arrived. There interest could have been registered any time.</td>
      </tr>
      <tr>
        <th>New Members</th>
        <td>{{ $newMembersLastWeek }}</td>
        <td>{{ $newMembersLastMonth }}</td>
        <td>First payment arrived in the last week/month. Note this could be from someone that had got to previous stage at any time.</td>
      </tr>
    </tbody>
  </table>
</div>
@endsection
