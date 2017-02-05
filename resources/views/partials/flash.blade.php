@if (session('flash_notification.message'))
<div class="callout {{ Session::has('flash_notification.level') ? session('flash_notification.level') : '' }}" data-closable>
  {!! session('flash_notification.message') !!}
  <button class="close-button" aria-label="Dismiss alert" type="button" data-close>
    <span aria-hidden="true">&times;</span>
  </button>
</div>
@endif