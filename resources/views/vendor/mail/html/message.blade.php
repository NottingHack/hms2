@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => config('app.url')])
<img width="135px" height="135px"
srcset="{{ url('/images/' . config('branding.theme', 'nottinghack') . '_with_white@3x.png') }} 3x, {{ url('/images/' . config('branding.theme', 'nottinghack') . '_with_white@2x.png') }} 2x, {{ url('/images/' . config('branding.theme', 'nottinghack') . '_with_white.png') }} 1x"
src="{{ url('/images/' . config('branding.theme', 'nottinghack') . '_with_white@3x.png') }}"
>
@endcomponent
@endslot

{{-- Body --}}
{{ $slot }}

{{-- Subcopy --}}
@isset($subcopy)
@slot('subcopy')
@component('mail::subcopy')
{{ $subcopy }}
@endcomponent
@endslot
@endisset

{{-- Footer --}}
@slot('footer')
@component('mail::footer')
© {{ date('Y') }} {{ config('branding.space_name') }}. @lang('All rights reserved.')
@endcomponent
@endslot
@endcomponent
