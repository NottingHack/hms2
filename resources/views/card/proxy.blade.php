@can('governance.proxy.designateProxy')
@isset($meeting)
<div class="card">
  <div class="card-header h5 bg-info">@if ($meeting->isExtraordinary()) Extraordinary @else Annual @endif General Meeting</div>
  <div class="card-body">
    <p>
      An @if ($meeting->isExtraordinary()) EGM @else AGM @endif has been scheduled for:<br>
      <strong>{{ $meeting->getStartTime()->toDayDateTimeString() }}</strong>
    </p>
    @if ($principal)
    Your have designated <strong>{{ $principal->getProxy()->getFullname() }}</strong> as your proxy.
    @elseif ($proxies)
    You have accepted one or more proxy designations.
    @else
    Not going to make it?<br>
    Please consider designating a proxy.
    @endif
  </div>
  <div class="card-footer">
    @if ($principal)
    <a href="{{ route('governance.proxies.link', ['meeting' => $meeting->getId()]) }}" class="btn btn-primary mb-1"><i class="fas fa-user-friends" aria-hidden="true"></i> Designate a different Proxy</a>
    <a href="javascript:void(0);" onclick="$(this).find('form').submit();"  class="btn btn-danger">
      <form action="{{ route('governance.proxies.destroy', ['meeting' => $meeting->getId()]) }}" method="POST" style="display: none">
        @method('DELETE')
        @csrf
      </form>
      <i class="fas fa-trash" aria-hidden="true"></i> Cancel Proxy
    </a>
    @elseif($proxies)
    <a href="{{ route('governance.proxies.index-for-user', ['meeting' => $meeting->getId()]) }}" class="btn btn-primary mb-1"><i class="far fa-eye" aria-hidden="true"></i> View Proxies</a>
    @else
    <a href="{{ route('governance.proxies.link', ['meeting' => $meeting->getId()]) }}" class="btn btn-primary"><i class="fas fa-user-friends" aria-hidden="true"></i> Designate a Proxy</a>
    @endif
  </div>
</div>
@endisset
@endcan
