@extends('layouts.app')

@section('pageTitle', 'Account Removal '.$user->getFirstname())

  @section('content')
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-8">
          <div class="card">
            <div class="card-header">
              <h5>Account Removal</h5>
            </div>

            <form id="delete-account-form" role="form" method="POST" action="{{ route('users.deleteAccount.deleted') }}">
              @csrf
              @method('PUT')
              <div class="card-body">

                <p class="card-text">
                  As described in our privacy policy, it is possible
                  to remove your account from the hackspace. This
                  happens automatically after being an ex-member for
                  six years. If you decide you want perform the
                  account removal immediately, you need to understand
                  the following.
                </p>

                <ul class="card-text">

                  <li>
                    If you wish to resume your membership to the
                    hackspace, you must reattend a tour regardless of
                    how long you were a member prior to making this
                    account removal request.
                  </li>
                  <li>
                    The hackspace will retainq your full name and
                    address will be retained for the duration of ten
                    years from the date of this request. This is a
                    legal obligation of being a member of a Limited by
                    Guarentee company.
                  </li>
                  <li>
                    Information relating to your use of tools and
                    access to the building will be annonymised
                    immediately, but retained indefinitely.
                  </li>
                  <li>
                    Payment transactions are retained for the duration
                    of seven years, as required by HMRC for tax
                    purposes. Transactions older than this will
                    automatically be anonymised.
                  </li>
                </ul>

                <p class="card-text">
                  If you wish to remove your account, please confirm your password
                  below.
                </p>

                <input placeholder="Current Password" class="form-control" id="currentPassword" type="password" name="currentPassword" required autofocus autocomplete="current-password">
                @if ($errors->has('currentPassword'))
                  <span class="help-block">
                    <strong>{{ $errors->first('currentPassword') }}</strong>
                  </span><br>
                @endif

              </div>
              <div class="card-footer">
                <button type="submit" class="btn btn-danger btn-block">Remove Account</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  @endsection
