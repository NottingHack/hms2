@extends('layouts.app')

@section('pageTitle', $user->getFullName())

@section('content')
<div class="container">
  <p>The Hackspace only uses these details to get in touch with you if needed, or if we are legally mandated to pass them on.</p>
  <p>We highly recommend you keep your details updated, especially your email address, as this is our main method of communication, including when door codes are changed and other important information.</p>
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
          <td>@if($user->getProfile()->getJoinDate()){{ $user->getProfile()->getJoinDate()->toDateString() }}@endif</td>
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
        @endif
      </tbody>
    </table>
  </div>

  @if (($user == Auth::user() && Auth::user()->can('profile.edit.self')) || ($user->getId() != Auth::user()->getId() && Auth::user()->can('profile.edit.all')))
  <a href="{{ route('users.edit', $user->getID()) }}" class="btn btn-info btn-block"><i class="fas fa-pencil" aria-hidden="true"></i> Edit</a>
  @endif
</div>
@endsection
