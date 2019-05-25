<div {{-- class="col-lg-4 mb-3" --}}>
  <div class="card">
    <div class="card-header">
      Members Boxes
    </div>
    <ul class="list-group list-group-flush">
      <li class="list-group-item">{{ Auth::user() == $user ? 'You have' : $uses->getFirstname() . ' has' }} {{ $boxCount }} box{{ $boxCount != 1 ? 'es' : '' }}</li>
    </ul>
    <div class="card-footer">
    @if ($boxCount <= Meta::get('member_box_limit'))
    @if ($user == Auth::user())
    @can('box.buy.self')
      <a href="{{ route('boxes.create') }}" class="btn btn-primary"><i class="fas fa-shopping-cart" aria-hidden="true"></i> Buy new box</a>
    @endcan
    @else
    @can('box.issue.all')
      <a href="{{ route('users.boxes.issue', $user->getId()) }}" class="btn btn-primary"><i class="fas fa-plus" aria-hidden="true"></i> Issue new box</a>
    @endcan
    @endif
    @else
      <a href="{{ route('users.boxes', $user->getId()) }}" class="btn btn-primary">Manage Boxes</a>
    @endif
    </div>
  </div>
</div>
