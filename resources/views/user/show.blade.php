@extends('layouts.app')

@section('pageTitle', $user->getFullname())

@section('content')
<div class="container">
  @content('user.show', 'main')
  <p>To change your details, please click the Edit button below.</p>
  <div class="table-responsive">
    <table class="table table-bordered">
      <tbody>
        <tr>
          <th>Username:</th>
          <td>{{ $user->getUsername() }}</td>
        </tr>
        <tr>
          <th>First name:</th>
          <td>{{ $user->getFirstname() }}</td>
        </tr>
        <tr>
          <th>Last name:</th>
          <td>{{ $user->getLastname() }}</td>
        </tr>
        <tr>
          <th>Email:</th>
          <td>{{ $user->getEmail() }}</td>
        </tr>
        @if ($user->getProfile())
        <tr>
          <th>Unlock text:</th>
          <td>{{ $user->getProfile()->getUnlockText() }}</td>
        </tr>
        <tr>
          <th>Join Date:</th>
          <td>@if ($user->getProfile()->getJoinDate()){{ $user->getProfile()->getJoinDate()->toDateString() }}@endif</td>
        </tr>
        <tr>
          <th>Address 1:</th>
          <td>{{ $user->getProfile()->getAddress1() }}</td>
        </tr>
        <tr>
          <th>Address 2:</th>
          <td>{{ $user->getProfile()->getAddress2() }}</td>
        </tr>
        <tr>
          <th>Address 3:</th>
          <td>{{ $user->getProfile()->getAddress3() }}</td>
        </tr>
        <tr>
          <th>City:</th>
          <td>{{ $user->getProfile()->getAddressCity() }}</td>
        </tr>
        <tr>
          <th>County:</th>
          <td>{{ $user->getProfile()->getAddressCounty() }}</td>
        </tr>
        <tr>
          <th>Post Code:</th>
          <td>{{ $user->getProfile()->getAddressPostCode() }}</td>
        </tr>
        <tr>
          <th>Contact Number:</th>
          <td>{{ $user->getProfile()->getContactNumber() }}</td>
        </tr>
        @feature('discord')
        <tr>
          <th>Discord Username:</th>
            <td>
              {{ $user->getProfile()->getDiscordUsername() }}
              @if ($user->getProfile()->getDiscordUserSnowflake())
              <i>({{ $user->getProfile()->getDiscordUserSnowflake() }})</i>
              @endif
            </td>
          </tr>
        @endfeature
        @endif
      </tbody>
    </table>
  </div>

  @if ($user->hasRoleByName(HMS\Entities\Role::MEMBER_APPROVAL))
  <a href="{{ route('membership.edit', Auth::user()->getId()) }}" class="btn btn-primary btn-block" >Update Details</a>
  @elseif (($user == Auth::user() && Auth::user()->can('profile.edit.self')) || ($user->getId() != Auth::user()->getId() && Auth::user()->can('profile.edit.all')))
  <a href="{{ route('users.edit', $user->getID()) }}" class="btn btn-info btn-block"><i class="fas fa-pencil" aria-hidden="true"></i> Edit</a>
  @endif
</div>
@endsection
