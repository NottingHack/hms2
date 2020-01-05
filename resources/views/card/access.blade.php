@can('accessCodes.view')
<div class="card">
  <div class="card-header">Space access</div>
  <ul class="list-group list-group-flush">
    <li class="list-group-item">Street Door: {{ Meta::get('access_street_door') }}</li>
    <li class="list-group-item">Inner Door: {{ Meta::get('access_inner_door') }}</li>
    <li class="list-group-item">{{ Auth::user() == $user ? 'You have' : $user->getFirstname() . ' has' }} {{ count($user->getRfidTags()) }} RFID cards.</li>
    @can('pins.view.all')
    @if ($user->getPin())
    <li class="list-group-item">Pin <b>{{ $user->getPin()->getPin() }}</b> is currently set to <i>{{ $user->getPin()->getStateString() }}.</i></li>
    @endcan
    @endif
  </ul>
  <div class="card-footer">
    <a href="{{ route('users.rfid-tags', $user->getId()) }}" class="btn btn-primary mb-1">Manage RFID Cards</a>
    @can('pins.reactivate')
    @if ($user->getPin())
    @if ($user->getPin()->getState() == \HMS\Entities\GateKeeper\PinState::CANCELLED)
    <a href="{{ route('pins.reactivate', $user->getPin()->getPin()) }}" class="btn btn-primary mb-1">Reactivate Pin</a>
    @endif
    @endif
    @endcan
  </div>
</div>
@endcan
