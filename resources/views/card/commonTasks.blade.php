<div class="card">
  <div class="card-header">Common Tasks</div>
  <div class="list-group list-group-flush">
    <a href="{{ route('users.show', $user->getId()) }}" class="list-group-item list-group-item-action">Update Details</a>
    <a href="{{ route('users.changePassword') }}" class="list-group-item list-group-item-action">Change Password</a>
    @if ($user->getAccount())
    <a href="{{ route('banking.bank-transactions.index') }}" class="list-group-item list-group-item-action">Membership Payment Details</a>
    @endif
    @feature('label_printer')
    <a href="{{ route('projects.index') }}" class="list-group-item list-group-item-action">Print Do-Not-Hack Label</a>
    @endfeature
    {{-- <a href="#" class="list-group-item list-group-item-action">Request Induction</a> --}}
    <a href="{{ route('contactUs') }}" class="list-group-item list-group-item-action">Contact Trustees</a>
    <a href="{{  Meta::get('rules_html') }}" class="list-group-item list-group-item-action" target="_blank">View the {{ ucfirst(config('branding.space_type')) }} Rules</a>
  </div>
</div>
