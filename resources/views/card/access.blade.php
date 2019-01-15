<div {{-- class="col-lg-4 mb-3" --}}>
  <div class="card">
    <div class="card-header">
      Space access
    </div>
    <ul class="list-group list-group-flush">
      <li class="list-group-item">Street Door: {{ Meta::get('access_street_door') }}
      <li class="list-group-item">Inner Door: {{ Meta::get('access_inner_door') }}</li>
      <li class="list-group-item">{{ Auth::user() == $user ? 'You have' : $uses->getFirstname() . ' has' }} {{ count($user->getRfidTags()) }} RFID cards</li>
    </ul>
    <div class="card-footer">
      <a href="{{ route('users.rfid-tags', $user->getId()) }}" class="btn btn-primary">Manage RFID Cards</a>
      @can('pins.reactivate')
      @if ($user->getPin())
      <a href="{{ route('pins.reactivate', $user->getPin()->getPin()) }}" class="btn btn-primary">Reactivate Pin</a>
      @endif
      @endcan
    </div>
  </div>
</div>

