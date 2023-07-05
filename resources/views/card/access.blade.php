@can('accessCodes.view')
<div class="card">
  <div class="card-header">Space access</div>
  <ul class="list-group list-group-flush">
    @if (Meta::get('access_street_door'))
    <li class="list-group-item">Street Door: {{ Meta::get('access_street_door') }}</li>
    @endif
    @if (Meta::get('access_inner_door'))
    <li class="list-group-item">Inner Door: {{ Meta::get('access_inner_door') }}</li>
    @endif
    @if ($roden = Meta::get('access_roden_street_door'))
    <li class="list-group-item">Roden Street Door: {{ $roden }}</li>
    @endif
    @if ($bins = Meta::get('access_roden_house_bins'))
    <li class="list-group-item">Roden House Bins: {{ $bins }}</li>
    @endif
    @if ($toilet = Meta::get('access_roden_house_toilet'))
    <li class="list-group-item">Roden House Toilet: {{ $toilet }}</li>
    @endif
    @if ($ashley_street = Meta::get('access_roden_house_ashley_street'))
    <li class="list-group-item">Roden House Ashley Street Door: {{ $ashley_street }}</li>
    @endif
    <li class="list-group-item">{{ Auth::user() == $user ? 'You have' : $user->getFirstname() . ' has' }} {{ count($user->getRfidTags()) }} RFID cards.</li>
    @canany(['pins.view.all', 'pins.view.self'])
    @if ($user->getPin())
    @if ((Features::isEnabled('members_enroll_pin') && $user->getPin()->getState() == \HMS\Entities\Gatekeeper\PinState::ENROLL) || Gate::allows('pins.view.all'))
    <li class="list-group-item">Pin <b>{{ $user->getPin()->getPin() }}</b> is currently set to <i>{{ $user->getPin()->getStateString() }}.</i></li>
    @endif
    @endif
    @endcan
  </ul>
  <div class="card-footer">
    <a href="{{ route('users.rfid-tags', $user->getId()) }}" class="btn btn-primary mb-1">Manage RFID Cards</a>
    @can('pins.reactivate')
    @if ($user->getPin())
    @if ($user->getPin()->getState() == \HMS\Entities\Gatekeeper\PinState::CANCELLED)
    <a href="{{ route('pins.reactivate', $user->getPin()->getPin()) }}" class="btn btn-primary mb-1">Reactivate Pin</a>
    @endif
    @endif
    @endcan
    @can('gatekeeper.temporaryAccess.grant.self')
    <a href="{{ route('gatekeeper.accessCodes') }}" class="btn btn-primary mb-1">Request Access</a>
    @endcan
  </div>
</div>
@endcan
