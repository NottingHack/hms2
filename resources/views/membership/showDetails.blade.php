@extends('layouts.app')

@section('pageTitle', 'New Member Details for Review')

@push('scripts')
<script>
  {{-- work around to push and run after jQuery is loaded since --}}
  window.addEventListener('DOMContentLoaded', function() {
    (function($) {
      $(".js-programmatic-enable").on("click", function () {
        $(".js-programmatic-state select").prop("disabled", false);
      });

      $(".js-programmatic-disable").on("click", function () {
        $(".js-programmatic-state select").prop("disabled", true);
      });
    })(jQuery);
  });
</script>
@endpush

@section('content')
<div class="container">
  @foreach($rejectedLogs as $rejectedLog)
  @if ($loop->first)
  <p>These details have been previously rejected</p>
  <div class="table-responsive no-more-tables">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th class="w-15">Date</th>
          <th>Reason</th>
          <th>Rejected By</th>
          <th class="w-10">User Updated</th>
        </tr>
      </thead>
      <tbody>
  @endif
        <tr>
          <td data-title="Date">{{ $rejectedLog->getCreatedAt()->toDateTimeString() }}</td>
          <td data-title="Reason">{{ $rejectedLog->getReason() }}</td>
          <td data-title="Rejected By">{{ $rejectedLog->getRejectedBy()->getFullname() }}</td>
          <td data-title="ser Updated">{{ $rejectedLog->getUserUpdatedAt() ? $rejectedLog->getUserUpdatedAt()->toDateTimeString() : 'Not Yet'}}</td>
        </tr>
  @if ($loop->last)
      </tbody>
    </table>
  </div>
  @endif
  @endforeach
  <p>Please review the details below and check they are all sensible.</p>
  <div class="table-responsive">
    <table class="table table-bordered table-hover">
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
          <td>{{ $user->getProfile()->getAddressPostCode() }}&nbsp;<a href="https://www.openstreetmap.org/search?query={{ $user->getProfile()->getAddressPostCode() }}" target="_blank">Check on OpenStreetMap</a></td>
        </tr>
        <tr>
          <th>Contact Number:</th>
          <td>{{ $user->getProfile()->getContactNumber() }}</td>
        </tr>
      </tbody>
    </table>
  </div>

  <button type="button" class="btn btn-success" data-toggle="modal" data-target="#approveDetails">
    Approve Details
  </button>

  <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#rejectDetails">
    Reject Details
  </button>

  <!-- Approve Modal -->
  <div class="modal fade existing-account-select2" id="approveDetails" tabindex="-1" role="dialog" aria-labelledby="Approve Details" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="ApproveTitle">Approve Details</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>To approve these member details please select if a new bank reference should be created or if this account should be link to another member's account.</p>

          <form role="form" method="POST" action="{{ route('membership.approve', $user->getId()) }}">
            @csrf
            <label for="value" class="form-label">Account</label>
            <div class="form-group">
              <div class="form-tickbox-row">
                <input name="new-account" type="radio" id="Yes" value="1" class="js-programmatic-disable" checked="checked">
                <label for="Yes">Create a new account reference</label>
              </div>
              <div class="form-tickbox-row">
                <input name="new-account" type="radio" id="No" value="0" class="js-programmatic-enable">
                <label for="No">Link to an existing account</label>
              </div>
              <member-select-two class="js-programmatic-state" name="existing-account" :with-account="true" :return-account-id="true" :disabled="true"></member-select-two>
            </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-success">Approve Details</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
              </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Reject Modal -->
  <div class="modal fade" id="rejectDetails" tabindex="-1" role="dialog" aria-labelledby="Reject Details" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="ApproveTitle">Reject Details</h5>
        </div>
        <form role="form" method="POST" action="{{ route('membership.reject', $user->getId()) }}">
          @csrf
          <div class="modal-body">
            <p>To reject these member details please provide the reason why and ask the member to update them using the email box below.</p>
            <label for="reason" class="form-label">Reason</label>
            <div class="form-group">
              <textarea class="form-control" rows="4" id="reason" name="reason" required>{{ old('reason', '') }}</textarea>
              @if ($errors->has('reason'))
              <p class="help-text">
                <strong>{{ $errors->first('reason') }}</strong>
              </p>
              @endif
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-danger">Reject Details</button>
            <button type="button" class="btn btn-info" data-dismiss="modal">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
