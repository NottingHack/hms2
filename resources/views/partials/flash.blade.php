@foreach ((array) session('flash_notification') as $message)
    @if ($message['overlay'])
        @include('partials.flashModal', [
            'modalClass' => 'flash-modal',
            'title'      => $message['title'],
            'body'       => $message['message']
        ])
    @else
        <div class="callout {{ array_key_exists('level', $message) ? $message['level'] : '' }}" data-closable>
            {!! $message['message'] !!}
            <button class="close-button" aria-label="Dismiss alert" type="button" data-close>
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
@endforeach

{{ session()->forget('flash_notification') }}
