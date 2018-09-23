@extends('layouts.app')

@section('pageTitle', 'New Member Details Review')

@section('content')
<div class="container">
  <p>Please review the details below and check they are all sensible.</p>

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
      <tr>
        <th>Date of Birth:</th>
        <td>@if($user->getProfile()->getDateOfBirth()){{ $user->getProfile()->getDateOfBirth()->toDateString() }}@endif</td>
      </tr>
    </tbody>
  </table>

  <div id="approve" class="card" data-reveal>
    <h2>Approval</h2>
    <p>To approve these member details please select if a new bank reference should be created or if this account should be link to another member's account.</p>
    <form role="form" method="POST" action="{{ route('membership.approve', $user->getId()) }}">
      {{ csrf_field() }}
      <label for="value" class="form-label">Account</label>
      <div class="form-group">
        <div class="form-tickbox-row">
          <input name="new-account" type="radio" id="Yes" value="1" class="js-programmatic-disable" checked="checked" />
          <label for="Yes">Create a new account reference</label>
        </div>
        <div class="form-tickbox-row">
          <input name="new-account" type="radio" id="No" value="0" class="js-programmatic-enable" />
          <label for="No">Link to an existing account</label>
        </div>
        <select name="existing-account" class="js-data-existing-account-ajax" disabled="disabled" data-width="100%">
        </select>
        <hr>
        <button type="submit" class="btn btn-success">Approve Details</button>
        <button type="button" class="btn btn-danger" data-close>Cancel</button>
      </div>
    </form>
  </div>

  <div id="reject" class="card" data-reveal>
    <h2>Rejection</h2>
    <p>To reject these member details please provide the reason why and ask the member to update them using the email box below.</p>
    <form role="form" method="POST" action="{{ route('membership.reject', $user->getId()) }}">
      {{ csrf_field() }}
      <label for="reason" class="form-label">Reason</label>
      <div class="form-group">
        <textarea class="from-control" rows="4" cols="50" id="reason" name="reason" required>{{ old('reason', '') }}</textarea>
        @if ($errors->has('reason'))
        <p class="help-text">
          <strong>{{ $errors->first('reason') }}</strong>
        </p>
        @endif
      </div>
      <hr>
      <div class="form-group">
        <button type="submit" class="btn btn-danger">Reject Details</button>
        <button type="button" class="btn btn-info" data-close>Cancel</button>
      </div>
    </form>
  </div>
</div>
@endsection
