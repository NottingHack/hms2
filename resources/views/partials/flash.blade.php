@foreach (session('flash_notification', collect())->toArray() as $message)
  @if ($loop->first)
  <div class="container">
  @endif
  @if ($message['overlay'])
  @include('partials.flashModal', [
    'modalClass' => 'flash-modal',
    'title'      => $message['title'],
    'body'       => $message['message']
  ])
  @else
  <div class="alert alert-{{ $message['level'] }}" data-closable>
    {!! $message['message'] !!}
    <button class="close" aria-label="Dismiss alert" data-dismiss="alert" type="button" data-close>
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  @endif
  @if ($loop->last)
  </div>
  @endif
@endforeach
{{ session()->forget('flash_notification') }}
