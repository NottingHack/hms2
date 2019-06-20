<div {{-- class="col-lg-3 mb-3" --}}>
  <div class="card">
    <div class="card-header">
      Common Tasks
    </div>
    <div class="list-group list-group-flush">
      <a href="{{ route('users.show', $user->getId()) }}" class="list-group-item list-group-item-action">Update Details</a>
      <a href="{{ route('users.changePassword') }}" class="list-group-item list-group-item-action">Change Password</a>
      <a href="{{ route('bank-transactions.index') }}" class="list-group-item list-group-item-action">Standing Order details</a>
      <a href="{{ route('projects.index') }}" class="list-group-item list-group-item-action">Print Do-No-Hack Label</a>
      {{-- <a href="#" class="list-group-item list-group-item-action">Request Indcution</a> --}}
      <a href="{{ route('contactUs') }}" class="list-group-item list-group-item-action">Contact Trustees</a>
      <a href="{{  Meta::get('rules_html') }}" class="list-group-item list-group-item-action" target="_blank">View the Hackspace Rules</a>
    </div>
  </div>
</div>
