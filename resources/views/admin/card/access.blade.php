@can('rfidTags.view.all')
<div class="card">
  <div class="card-header">Space access</div>
  <ul class="list-group list-group-flush">
    <li class="list-group-item">{{ Auth::user() == $user ? 'You have' : $user->getFirstname() . ' has' }} {{ count($user->getRfidTags()) }} RFID cards.</li>
    @can('pins.view.all')
    @if ($user->getPin())
    <li class="list-group-item">Pin <b>{{ $user->getPin()->getPin() }}</b> is currently set to <i>{{ $user->getPin()->getStateString() }}.</i></li>
    @endcan
    @endif
  </ul>
  <div class="card-footer">
    @can('rfidTags.edit.all')
    <a href="{{ route('users.rfid-tags', $user->getId()) }}" class="btn btn-primary mb-1">Manage RFID Cards</a>
    @endcan
    @can('pins.reactivate')
    @if ($user->getPin())
    @if ($user->getPin()->getState() == \HMS\Entities\Gatekeeper\PinState::CANCELLED)
    <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn btn-primary mb-1">
      <form action="{{ route('pins.reactivate', $user->getPin()->getId()) }}" method="POST" style="display: none">
        @method('PATCH')
        @csrf
      </form>
      <i class="fas fa-sync-alt" aria-hidden="true"></i> Reactivate Pin
    </a>
    @endif
    @endif
    @endcan
    @can('profile.view.all')
    <a href="{{ route('users.admin.access-logs', $user->getId()) }}" class="btn btn-primary mb-1"><i class="fas fa-list" aria-hidden="true"></i> View Access Logs</a>
    @endcan
  </div>
</div>
@endcan
