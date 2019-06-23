@can('box.view.all')
<div class="card">
  <div class="card-header">Members Boxes</div>
  {{-- TODO: change this to a list view? --}}
  <ul class="list-group list-group-flush">
    <li class="list-group-item">{{ Auth::user() == $user ? 'You have' : $user->getFirstname() . ' has' }} {{ $boxCount }} box{{ $boxCount != 1 ? 'es' : '' }}.</li>
  </ul>
  <div class="card-footer">
    @if ($boxCount <= Meta::get('member_box_individual_limit'))
    @can('box.issue.all')
    <a href="{{ route('users.boxes.issue', $user->getId()) }}" class="btn btn-primary mb-1"><i class="fas fa-plus" aria-hidden="true"></i> Issue new box</a>
    @endcan
    @endif
    <a href="{{ route('users.boxes', $user->getId()) }}" class="btn btn-primary mb-1">Manage Boxes</a>
  </div>
</div>
@endcan
