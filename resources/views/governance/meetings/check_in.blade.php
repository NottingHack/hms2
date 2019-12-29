@extends('layouts.app')

@section('pageTitle')
{{ $meeting->getTitle() }} Check-in
@endsection

@section('content')
<div class="container">
  <h2>Meeting Starts at {{ $meeting->getStartTime()->toTimeString() }}</h2>
  <hr>
  <div class="card-deck">
    <div class="card">
      <h4 class="card-header">Quorum Requirements</h4>
      <table class="table">
        <tbody>
          <tr>
            <th scope="row">Current Member Count:</th>
            <td>{{ $meeting->getCurrentMembers() }}</td>
          </tr>
          <tr>
            <th scope="row">Voting Member Count:</th>
            <td>{{ $meeting->getVotingMembers() }}</td>
          </tr>
          <tr>
            <th scope="row">Quorum Required:</th>
            <td>{{  $meeting->getQuorum()}}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="card">
      <h4 class="card-header">Current Attendance</h4>
      <table class="table">
        <tbody>
          <tr>
            <th scope="row">
              <span class="align-middle">
                Proxies Registered:&nbsp;
                <div class="btn-group float-right" role="group" aria-label="View Proxies Registered">
                  <a href="{{ route('governance.proxies.index', ['meeting' => $meeting->getId()]) }}" class="btn btn-primary btn-sm"><i class="far fa-eye" aria-hidden="true"></i></a>
                </div>
              </span>
            </th>
            <td>{{ $meeting->getProxies()->count() }}</td>
          </tr>
          <tr>
            <th scope="row">
            <span class="align-middle">
                Attendees in the room:&nbsp;
                <div class="btn-group float-right" role="group" aria-label="View Proxies Registered">
                  <a href="{{ route('governance.meetings.attendees', ['meeting' => $meeting->getId()]) }}" class="btn btn-primary btn-sm"><i class="far fa-eye" aria-hidden="true"></i></a>
                </div>
              </span>
            </th>
            <td>{{ $meeting->getAttendees()->count() }}</td>
          </tr>
          <tr>
            <th scope="row">Proxies Represented:</th>
            <td>{{ $representedProxies }}</td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="card">
      <h4 class="card-header">Total Checked-in</h4>
      <div class="card-body align-items-center d-flex justify-content-center @if($checkInCount >= $meeting->getQuorum()) bg-success @else bg-danger @endif">
        <h1 class="card-text">{{ $checkInCount }}</h1>
      </div>
    </div>
  </div>
  <hr>
  <h2>Member Check-in</h2>
  <p>
    Use the search bellow to find a member to check in.<br>
    You can search via Name, Username, Email or Post Code.<br>
    Search via Username or Email will give the smallest set of results.
  </p>
  <form role="form" method="POST" action="{{ route('governance.meetings.check-in-user', $meeting->getId()) }}">
    @csrf

    <member-select-two name="user_id" :current-only="true"></member-select-two>
    <br>
    <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-user-check fa-lg" aria-hidden="true"></i> Check-in</button>
  </form>
</div>

@endsection
